<?php

namespace common\models\shop;

use Yii;
use yii\helpers\ArrayHelper;

class Category extends \yii\db\ActiveRecord
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
        return '{{%shop_category}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationships()
    {
        return $this->hasMany(Relationship::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable(Relationship::tableName(), ['category_id' => 'id'])->andOnCondition(['status' => 1])->orderBy('sorting ASC, created_at DESC');
    }

    /**
     * Get parent category
     */
    public function getParent()
    {
        return $this->hasOne(static::className(), ['id' => 'parent']);
    }

    /**
     * Get parent categories
     */
    public function getParents()
    {
        return $this->hasMany(static::className(), ['id' => 'parent']);
    }

    /**
     * Get child category
     */
    public function getChild()
    {
        return $this->hasOne(static::className(), ['parent' => 'id']);
    }

    /**
     * Get children categories
     */
    public function getChildren()
    {
        return $this->hasMany(static::className(), ['parent' => 'id']);
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
