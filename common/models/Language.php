<?php

namespace common\models;

use Yii;

class Language extends \yii\db\ActiveRecord
{
    use \common\traits\CacheTrait;
    use \common\traits\CrudModelTrait;

    const ALL_CACHE_KEY = 'ALL_LANGUAGE';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_language}}';
    }

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
            [['sorting','is_default'], 'default', 'value' => 0],
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
        if (!$this->validate()) {
            return false;
        }

        if (!$this->isNewRecord) {
            $this->updated_at = time();
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->save()) {
                $transaction->commit();
                self::deleteCache(self::ALL_CACHE_KEY);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Successfully'));
                return $this;
            } else {
                $this->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed'));
                return false;
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return false;
    }

    public function deleteModel()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->delete();
            $transaction->commit();
            self::deleteCache(self::ALL_CACHE_KEY);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully.'));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function allData()
    {
        return Yii::$app->cache->getOrSet(self::ALL_CACHE_KEY, function() {
            return self::find()
                ->where(['status' => 1])
                ->orderBy('is_default DESC, sorting ASC')
                ->asArray()
                ->all();
        });
    }
}
