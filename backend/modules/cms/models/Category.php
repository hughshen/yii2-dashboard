<?php

namespace backend\modules\cms\models;

use Yii;
use yii\helpers\ArrayHelper;

class Category extends \common\models\cms\Category
{
    use \common\traits\CrudModelTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['parent', 'sorting', 'created_at', 'updated_at'], 'integer'],
            [['type'], 'string', 'max' => 32],
            [['slug', 'title', 'image'], 'string', 'max' => 255],
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
            'image' => Yii::t('app', 'Image'),
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
            throw new \yii\base\Exception(implode('<br>', (array)$this->getFirstErrors()));
        };

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->setExtraFieldArray(Yii::$app->request->post('ExtraFields', []));
            $this->saveExtraData();
            $this->save();

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
                $this->updateChildren();
                $this->unlinkAll('posts', true);
                $this->deleteAllTranslate();
                $this->delete();
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
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

        return static::combineTranslatedData($data);
    }
}
