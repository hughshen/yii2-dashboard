<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\widgets\ExtraFieldInput;
use backend\modules\cms\models\Category;
use backend\modules\cms\models\Tag;

$ids = ArrayHelper::getColumn($model->getRelationships()->asArray()->all(), 'category_id');
?>
<?= ExtraFieldInput::widget([
    'options' => [
        'inputId' => 'post-category',
        'inputName' => 'Categories[]',
        'inputType' => 'checkboxlist',
        'defaultValue' => $ids,
        'valueList' => ArrayHelper::map(Category::categoryList(), 'id', 'title'),
        'inputLabel' => 'Categories',
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'inputId' => 'post-tag',
        'inputName' => 'Categories[]',
        'inputType' => 'checkboxlist',
        'defaultValue' => $ids,
        'valueList' => ArrayHelper::map(Tag::categoryList(), 'id', 'title'),
        'inputLabel' => 'Tags',
    ],
]) ?>
