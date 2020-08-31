<?php

namespace backend\models;

use Yii;

class Config extends \common\models\Config
{
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
}
