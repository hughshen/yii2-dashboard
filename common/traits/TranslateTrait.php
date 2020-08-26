<?php

namespace common\traits;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\Language;
use common\models\Translate;

trait TranslateTrait
{
    // abstract public static function tableName();
    // abstract public function getPrimaryKey($asArray = false);

    public static function transTableName($tableName = '')
    {
        if (empty($tableName)) $tableName = static::tableName();
        return str_replace(['{{%', '}}'], ['', ''], $tableName);
    }

    public function getTranslate()
    {
        return $this->hasMany(Translate::className(), ['table_id' => 'id'])->andOnCondition(['table_name' => static::transTableName()]);
    }

    public function getTranslated()
    {
        return $this->hasMany(Translate::className(), ['table_id' => 'id'])->andOnCondition(['table_name' => static::transTableName(), 'language' => Yii::$app->language]);
    }

    public function translatedField($field, $default = null)
    {
        $query = Translate::find()->where([
            'table_name' => static::transTableName(),
            'table_id' => $this->getPrimaryKey(),
            'table_field' => $field,
            'language' => Yii::$app->language,
        ])->asArray()->one();

        if ($query) {
            return $query['field_value'];
        } else {
            return $default;
        }
    }

    public function saveTranslate($data)
    {
        if (is_array($data) && $data) {
            foreach ($data as $langKey => $val) {
                if (is_array($val) && $val) {
                    foreach ($val as $field => $value) {
                        $model = Translate::findOne([
                            'table_name' => static::transTableName(),
                            'table_id' => $this->getPrimaryKey(),
                            'table_field' => $field,
                            'language' => $langKey,
                        ]);
                        if ($model) {
                            $model->field_value = $value;
                            $model->updated_at = time();
                            $model->save();
                        } else {
                            $model = new Translate();
                            $model->table_name = static::transTableName();
                            $model->table_id = $this->getPrimaryKey();
                            $model->table_field = $field;
                            $model->field_value = $value;
                            $model->language = $langKey;
                            $model->save();
                        }
                    }
                }
            }
        }
    }

    public function combineTranslate($data)
    {
        if (is_array($data) && $data) {
            $combined = [];
            $langKeys = array_keys(Yii::$app->controller->languageList);
            if ($langKeys) {
                $default = $langKeys['0'];
                if (isset($data[$default]) && is_array($data[$default])) {
                    foreach ($data[$default] as $field => $value) {
                        $combined[$field] = $value;
                    }
                }
            }
            if ($combined) {
                foreach ($combined as $key => $val) {
                    try {
                        $this->{$key} = $val;
                    } catch (\Exception $e) {}
                }
            }
        }
    }

    public function deleteAllTranslate()
    {
        Translate::deleteAll([
            'table_name' => static::transTableName(),
            'table_id' => $this->getPrimaryKey(),
        ]);
    }

    public static function leftJoinTranslate($query)
    {
        $tableName = static::tableName();
        $transTable = Translate::tableName();
        $pureTable = static::transTableName($tableName);
        $query->leftJoin($transTable, "{$tableName}.id = {$transTable}.table_id AND {$transTable}.table_name = '{$pureTable}'");

        return $query;
    }

    public static function fieldFilterTranslate($query, $field, $value)
    {
        $transTable = Translate::tableName();
        $language = Yii::$app->language;

        if ($value) {
            $query->andWhere("{$transTable}.table_field = '{$field}' AND {$transTable}.field_value LIKE '%{$value}%' AND {$transTable}.language = '{$language}'");
        }

        return $query;
    }

    public static function combineTranslatedData($data)
    {
        if (!is_array($data) || !$data) return $data;

        // Single
        $isSingle = !isset($data['0']);

        if ($isSingle) {
            $data = [$data];
        }

        $newData = [];
        foreach ($data as $key => $val) {
            if (isset($val['translated']) && $val['translated']) {
                foreach ($val['translated'] as $k => $v) {
                    $val[$v['table_field']] = $v['field_value'];
                }
                unset($val['translated']);
            }
            $newData[] = $val;
        }

        if ($isSingle) {
            return $newData[0];
        } else {
            return $newData;
        }
    }
}
