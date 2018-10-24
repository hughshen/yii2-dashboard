<?php

namespace common\models\cms;

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
    public function init()
    {
        parent::init();
        $this->type = static::typeName();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cms_category}}';
    }

    /**
     * Return type name
     */
    public static function typeName()
    {
        return 'category';
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
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['id' => 'post_id'])->viaTable(Relationship::tableName(), ['category_id' => 'id'])->andOnCondition(['status' => Post::STATUS_PUBLISH, 'type' => Post::typeName()])->orderBy('sorting ASC, created_at DESC');
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
