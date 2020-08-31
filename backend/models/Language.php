<?php

namespace backend\models;

use Yii;

class Language extends \common\models\Language
{
    use \common\traits\CrudModelTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'code', 'locale'], 'required'],
            [['sorting', 'is_default', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'image'], 'string', 'max' => 255],
            [['code', 'locale'], 'string', 'max' => 16],
            [['sorting', 'is_default'], 'default', 'value' => 0],
            ['status', 'default', 'value' => 1],
            [['created_at', 'updated_at'], 'default', 'value' => time()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'code' => Yii::t('app', 'Code'),
            'locale' => Yii::t('app', 'Locale'),
            'image' => Yii::t('app', 'Image'),
            'sorting' => Yii::t('app', 'Sorting'),
            'is_default' => Yii::t('app', 'Is Default'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function saveModel()
    {
        if (!$this->isNewRecord) {
            $this->updated_at = time();
        }

        if (!$this->validate()) {
            throw new \yii\base\Exception(implode('<br>', (array)$this->getFirstErrors()));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->is_default == 1) {
                Yii::$app->db->createCommand()
                    ->update(self::tableName(), ['is_default' => 0], ['!=', 'id', $this->id])
                    ->execute();
            }

            $this->save();
            $transaction->commit();

            self::deleteCache(self::ALL_CACHE_KEY);

            return $this;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function deleteModel()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->delete();
            $transaction->commit();
            self::deleteCache(self::ALL_CACHE_KEY);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
