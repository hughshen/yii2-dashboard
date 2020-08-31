<?php

namespace common\models;

use Yii;

class Language extends \yii\db\ActiveRecord
{
    use \common\traits\CacheTrait;

    const ALL_CACHE_KEY = 'ALL_LANGUAGE';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_language}}';
    }

    public static function allData()
    {
        return Yii::$app->cache->getOrSet(self::ALL_CACHE_KEY, function () {
            return self::find()
                ->where(['status' => 1])
                ->orderBy('is_default DESC, sorting ASC')
                ->asArray()
                ->all();
        });
    }
}
