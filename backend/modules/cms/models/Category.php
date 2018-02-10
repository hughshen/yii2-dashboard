<?php

namespace backend\modules\cms\models;

use Yii;
use yii\helpers\ArrayHelper;

class Category extends \yii\db\ActiveRecord
{
    use \common\traits\CacheTrait;
    use \common\traits\SlugTrait;
    use \common\traits\TranslateTrait;
    use \common\traits\ExtraDataTrait;
    use \common\traits\CrudModelTrait;

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
        return '{{%cms_category}}';
    }

    /**
     * Return type name
     */
    public static function typeName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['parent', 'sorting', 'created_at', 'updated_at'], 'integer'],
            [['type'], 'string', 'max' => 32],
            [['slug', 'title'], 'string', 'max' => 255],
            [['description', 'extra_data'], 'string'],
            ['type', 'default', 'value' => static::typeName()],
            [['parent', 'sorting'], 'default', 'value' => 0],
            ['status', 'default', 'value' => 1],
            [['created_at', 'updated_at'], 'default', 'value' => time()],
            // Add
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
            'slug' => Yii::t('app', 'Slug'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'type' => Yii::t('app', 'Type'),
            'extra_data' => Yii::t('app', 'Extra Data'),
            'sorting' => Yii::t('app', 'Sorting'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            // Extra
            'seo_title' => Yii::t('app', 'SEO Title'),
            'seo_keywords' => Yii::t('app', 'SEO Keywords'),
            'seo_description' => Yii::t('app', 'SEO Description'),
        ];
    }

    /**
     * Save model
     */
    public function saveModel()
    {
        if (!$this->isNewRecord) {
            $this->updated_at = time();
        }

        $this->combineTranslate(Yii::$app->request->post('Translate'));
        $this->setSlug($this->title);

        if (!$this->validate()) {
            Yii::$app->session->setFlash('error', implode('<br>', (array)$this->getFirstErrors()));
            return false;
        };

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->setExtraFieldArray(Yii::$app->request->post('ExtraFields', []));
            $this->saveExtraData();
            $this->save();

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
            $this->unlinkAll('posts', true);
            $this->deleteAllTranslate();
            $this->delete();

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
        return $this->hasMany(Relationship::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['id' => 'post_id'])->viaTable(Relationship::tableName(), ['category_id' => 'id'])->andOnCondition(['status' => Post::STATUS_PUBLISH, 'type' => Post::typeName()])->orderBy('sorting ASC, created_at DESC');
    }

    /**
     * Get parent category
     */
    public function getParentCategory()
    {
        return $this->hasOne(static::className(), ['id' => 'parent']);
    }

    /**
     * Update children when delete record
     */
    public function updateChildren()
    {
        Yii::$app->db->createCommand()
            ->update(self::tableName(), ['parent' => $this->parent], ['parent' => $this->id])
            ->execute();
    }

    /**
     * Return status list
     */
    public static function statusList()
    {
        return [
            '1' => Yii::t('app', 'Show'),
            '0' => Yii::t('app', 'Hide'),
        ];
    }

    /**
     * Get category list
     */
    public static function categoryList($type = null)
    {
        if ($type === null) $type = static::typeName();
        
        $data = self::find()
            ->with(['translated'])
            ->where(['type' => $type])
            ->orderBy('sorting ASC, id DESC')
            ->asArray()
            ->all();

        return static::combineTranslatedData($data);;
    }
}
