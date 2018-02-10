<?php

namespace common\models;

use Yii;

class Translate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_translate}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table_name', 'table_id', 'table_field', 'language'], 'required'],
            [['table_id', 'created_at', 'updated_at'], 'integer'],
            [['field_value'], 'string'],
            [['table_name', 'language'], 'string', 'max' => 32],
            [['table_field'], 'string', 'max' => 255],
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
            'table_name' => Yii::t('app', 'Table Name'),
            'table_id' => Yii::t('app', 'Table ID'),
            'table_field' => Yii::t('app', 'Table Field'),
            'field_value' => Yii::t('app', 'Field Value'),
            'language' => Yii::t('app', 'Language'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
