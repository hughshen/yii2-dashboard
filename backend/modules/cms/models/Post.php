<?php

namespace backend\modules\cms\models;

use Yii;

class Post extends \common\models\cms\Post
{
    use \common\traits\CrudModelTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['author', 'view_count', 'sorting', 'created_at', 'updated_at'], 'integer'],
            [['slug', 'title', 'guid', 'image'], 'string', 'max' => 255],
            [['content', 'excerpt', 'extra_data', 'images'], 'string'],
            ['type', 'string', 'max' => 16],
            ['type', 'default', 'value' => static::typeName()],
            [['author', 'parent', 'view_count', 'sorting'], 'default', 'value' => 0],
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
            'image' => Yii::t('app', 'Image'),
            'images' => Yii::t('app', 'Images'),
            'view_count' => Yii::t('app', 'View Count'),
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
        $this->setSlug($this->title);

        if (!$this->validate()) {
            throw new \yii\base\Exception(implode('<br>', (array)$this->getFirstErrors()));
        };

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->setExtraFieldArray(Yii::$app->request->post('ExtraFields', []));
            $this->saveExtraData();
            $this->save();

            $this->setTags(Yii::$app->request->post('Categories', []));
            $this->setCategories(Yii::$app->request->post('Categories', []));
            $this->saveTranslate(Yii::$app->request->post('Translate'));

            $transaction->commit();

            return $this;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Delete model
     */
    public function deleteModel($fake = true)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($fake) {
                $this->deleted_at = time();
                $this->update(false);
            } else {
                $this->unlinkAll('categories', true);
                $this->deleteAllTranslate();
                $this->delete();
            }

            $transaction->commit();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set tags
     */
    public function setTags($data)
    {
        if (!$this->isNewRecord) $this->unlinkAll('tags', true);
        foreach ($data as $key => $cid) {
            $model = Tag::findOne($cid);
            $this->link('tags', $model);
        }
    }

    /**
     * Set categories
     */
    public function setCategories($data)
    {
        if (!$this->isNewRecord) $this->unlinkAll('categories', true);
        foreach ($data as $key => $cid) {
            $model = Category::findOne($cid);
            $this->link('categories', $model);
        }
    }
}
