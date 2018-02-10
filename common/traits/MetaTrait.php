<?php

namespace common\traits;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\Meta;

trait MetaTrait
{
    public static function pureTableName($tableName = '')
    {
        if (empty($tableName)) $tableName = static::tableName();
        return str_replace(['{{%', '}}'], ['', ''], $tableName);
    }

    public static function metaCombine($data = [])
    {
        if (empty($data) || !is_array($data)) return [];

        if (isset($data['meta'])) {
            $data['meta'] = ArrayHelper::map((array)$data['meta'], 'meta_key', 'meta_value');
        } else {
            foreach ($data as $key => $val) {
                $meta = isset($val['meta']) ? $val['meta'] : [];
                $data[$key]['meta'] = ArrayHelper::map((array)$meta, 'meta_key', 'meta_value');
            }
        }
        
        return $data;
    }

    public function deleteAllMeta()
    {
        Meta::deleteAll(['table_name' => static::pureTableName(), 'table_id' => $this->id]);
    }

    public function saveMeta($data)
    {
        if (is_array($data) && !empty($data)) {
            Meta::deleteAll(['table_name' => static::pureTableName(), 'table_id' => $this->id, 'meta_key' => array_keys($data)]);
            $insertData = [];
            foreach ($data as $key => $val) {
                $insertData[] = [static::pureTableName(), $this->id, $key, $val];
            }
            Yii::$app->db->createCommand()->batchInsert(Meta::tableName(), ['table_name', 'table_id', 'meta_key', 'meta_value'], $insertData)->execute();
        }
    }

    public function getMeta()
    {
        return $this->hasMany(Meta::className(), ['table_id' => 'id'])->andOnCondition(['table_name' => static::pureTableName()]);
    }

    public function metaList()
    {
        $data = Meta::find()->where(['table_name' => static::pureTableName(), 'table_id' => $this->id])->asArray()->all();
        if (empty($data)) return [];
        return ArrayHelper::map($data, 'meta_key', 'meta_value');
    }
}
