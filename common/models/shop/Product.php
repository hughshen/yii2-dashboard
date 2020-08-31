<?php

namespace common\models\shop;

use Yii;

class Product extends \yii\db\ActiveRecord
{
    use \common\traits\SeoTrait;
    use \common\traits\SlugTrait;
    use \common\traits\CacheTrait;
    use \common\traits\TranslateTrait;
    use \common\traits\ExtraDataTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_product}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationships()
    {
        return $this->hasMany(Relationship::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable(Relationship::tableName(), ['product_id' => 'id'])->andOnCondition(['status' => 1])->orderBy('sorting ASC, created_at DESC');
    }

    /**
     * Return status list
     */
    public static function statusList()
    {
        return [
            '1' => Yii::t('app', 'Show'),
            '0' => Yii::t('app', 'Hide'),
        ];
    }
}
