<?php

namespace backend\modules\cms\models;

use Yii;

class Post extends \yii\db\ActiveRecord
{
    use \common\traits\CacheTrait;
    use \common\traits\SlugTrait;
    use \common\traits\TranslateTrait;
    use \common\traits\ExtraDataTrait;
    use \common\traits\CrudModelTrait;

    const STATUS_PUBLISH = 'publish';
    const STATUS_DRAFT = 'draft';
    const STATUS_TRASH = 'trash';

    public $publish_at;

    public $seo_title;
    public $seo_keywords;
    public $seo_description;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->type = static::typeName();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cms_post}}';
    }

    /**
     * Return type name
     */
    public static function typeName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['author', 'sorting', 'created_at', 'updated_at'], 'integer'],
            [['slug', 'title', 'guid'], 'string', 'max' => 255],
            [['content', 'excerpt', 'extra_data'], 'string'],
            ['type', 'string', 'max' => 16],
            ['type', 'default', 'value' => static::typeName()],
            [['author', 'parent', 'sorting'], 'default', 'value' => 0],
            ['status', 'default', 'value' => 'publish'],
            [['created_at', 'updated_at'], 'default', 'value' => time()],
            // Add
            ['publish_at', 'string'],
            [['seo_title', 'seo_keywords', 'seo_description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent' => Yii::t('app', 'Parent'),
            'author' => Yii::t('app', 'Author'),
            'slug' => Yii::t('app', 'Slug'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'excerpt' => Yii::t('app', 'Excerpt'),
            'guid' => Yii::t('app', 'Guid'),
            'type' => Yii::t('app', 'Type'),
            'extra_data' => Yii::t('app', 'Extra Data'),
            'sorting' => Yii::t('app', 'Sorting'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            // Extra
            'publish_at' => Yii::t('app', 'Publish At'),
            'seo_title' => Yii::t('app', 'SEO Title'),
            'seo_keywords' => Yii::t('app', 'SEO Keywords'),
            'seo_description' => Yii::t('app', 'SEO Description'),
        ];
    }

    /**
     * If has trash
     */
    public function hasTrash()
    {
        return false;
    }

    /**
     * Slug prefix
     */
    public function slugPrefix()
    {
        return '';
    }

    public function extraFields()
    {
        $this->extractExtraData();
        return [
            [
                'fieldName' => 'view_count',
                'inputLabel' => Yii::t('app', 'View Count'),
                'valueData' => $this->extraData,
                'defaultValue' => $this->getExtraValue('view_count'),
            ],
        ];
    }

    /**
     * Save model
     */
    public function saveModel()
    {
        if ($this->isNewRecord) {
            $this->author = Yii::$app->user->id;
        } else {
            $this->updated_at = time();
        }

        $this->combineTranslate(Yii::$app->request->post('Translate'));
        $this->setSlug($this->slugPrefix() . $this->title);

        if (!$this->validate()) {
            Yii::$app->session->setFlash('error', implode('<br>', (array)$this->getFirstErrors()));
            return false;
        };

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->setExtraFieldArray(Yii::$app->request->post('ExtraFields', []));
            $this->saveExtraData();
            $this->save();

            $this->setCategories(Yii::$app->request->post('Categories', []));
            $this->saveTranslate(Yii::$app->request->post('Translate'));

            $transaction->commit();

            Yii::$app->session->setFlash('success', 'Updated successfully.');

            return $this;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return false;
    }

    /**
     * Delete model
     */
    public function deleteModel()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->hasTrash()) {
                $this->moveToTrash();
                $this->save();
            } else {
                $this->unlinkAll('categories', true);
                $this->deleteAllTranslate();
                $this->delete();
            }

            $transaction->commit();

            Yii::$app->session->setFlash('success', 'Deleted successfully.');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationships()
    {
        return $this->hasMany(Relationship::className(), ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable(Relationship::tableName(), ['post_id' => 'id'])->andOnCondition(['status' => 1, 'type' => Category::typeName()])->orderBy('sorting ASC, created_at DESC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable(Relationship::tableName(), ['post_id' => 'id'])->andOnCondition(['status' => 1, 'type' => Tag::typeName()])->orderBy('sorting ASC, created_at DESC');
    }

    /**
     * Set categories data
     */
    public function setCategories($data)
    {
        if (!$this->isNewRecord) $this->unlinkAll('categories', true);
        foreach ($data as $key => $cid) {
            $model = Category::findOne($cid);
            $this->link('categories', $model);
        }
    }

    /**
     * Fake delete
     */
    public function moveToTrash()
    {
        $this->deleted_at = time();
        $this->status = self::STATUS_TRASH;
    }

    /**
     * Return status list
     */
    public static function statusList()
    {
        return [
            self::STATUS_PUBLISH => Yii::t('app', 'Publish'),
            self::STATUS_DRAFT => Yii::t('app', 'Draft'),
            // self::STATUS_TRASH => Yii::t('app', 'Trash'),
        ];
    }
}
