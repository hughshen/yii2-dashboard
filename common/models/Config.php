<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class Config extends \yii\db\ActiveRecord
{
    use \common\traits\CacheTrait;

    const ALL_CACHE_KEY = 'ALL_CONFIG';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['option_name'], 'required'],
            [['option_name'], 'string', 'max' => 255],
            [['option_value'], 'string'],
            ['autoload', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'option_name' => Yii::t('app', 'Option'),
            'option_value' => Yii::t('app', 'Value'),
        ];
    }

    protected static function _query($condition = [])
    {
        return self::find()->select(['option_name', 'option_value'])->where($condition);
    }

    protected static function _combine($data)
    {
        return ArrayHelper::map((array)$data, 'option_name', 'option_value');
    }

    public static function allData($condition = [])
    {
        return Yii::$app->cache->getOrSet(self::ALL_CACHE_KEY, function() use ($condition) {
            $data = self::_query()->asArray()->all();
            return self::_combine($data);
        });
    }

    public static function byName($name)
    {
        $data = self::allData();
        return isset($data[$name]) ? $data[$name] : null;
    }
}
