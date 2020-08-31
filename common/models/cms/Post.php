<?php

namespace common\models\cms;

use Yii;

class Post extends \yii\db\ActiveRecord
{
    use \common\traits\SeoTrait;
    use \common\traits\SlugTrait;
    use \common\traits\CacheTrait;
    use \common\traits\TranslateTrait;
    use \common\traits\ExtraDataTrait;

    const STATUS_PUBLISH = 'publish';
    const STATUS_DRAFT = 'draft';

    public $publish_at;

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
        return '{{%cms_post}}';
    }

    /**
     * Return type name
     */
    public static function typeName()
    {
        return 'post';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationships()
    {
        return $this->hasMany(Relationship::className(), ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable(Relationship::tableName(), ['post_id' => 'id'])->andOnCondition(['status' => 1, 'type' => Category::typeName()])->orderBy('sorting ASC, created_at DESC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable(Relationship::tableName(), ['post_id' => 'id'])->andOnCondition(['status' => 1, 'type' => Tag::typeName()])->orderBy('sorting ASC, created_at DESC');
    }

    /**
     * Return status list
     */
    public static function statusList()
    {
        return [
            self::STATUS_PUBLISH => Yii::t('app', 'Publish'),
            self::STATUS_DRAFT => Yii::t('app', 'Draft'),
        ];
    }
}
