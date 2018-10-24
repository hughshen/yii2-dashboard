<?php

namespace backend\modules\shop\models;

use Yii;

class Product extends \common\models\shop\Product
{
    use \common\traits\CrudModelTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['price', 'weight'], 'number'],
            [['quantity', 'view_count', 'sorting', 'status', 'created_at', 'updated_at', 'deleted_at'], 'integer'],
            [['content', 'description', 'images', 'extra_data'], 'string'],
            [['slug', 'title', 'image'], 'string', 'max' => 255],
            [['price', 'weight'], 'default', 'value' => 0.00],
            [['quantity'], 'default', 'value' => 0],
            [['view_count', 'sorting'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],
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
            'price' => Yii::t('app', 'Price'),
            'quantity' => Yii::t('app', 'Quantity'),
            'weight' => Yii::t('app', 'Weight'),
            'slug' => Yii::t('app', 'Slug'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'description' => Yii::t('app', 'Description'),
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
}
