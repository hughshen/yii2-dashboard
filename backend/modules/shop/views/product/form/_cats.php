<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use common\widgets\ExtraFieldInput;
use backend\modules\shop\models\Category;

$categoryIds = ArrayHelper::getColumn($model->getCategories()->asArray()->all(), 'id');
?>
<?= ExtraFieldInput::widget([
    'options' => [
        'inputId' => 'post-category',
        'inputName' => 'Categories[]',
        'inputType' => 'checkboxlist',
        'defaultValue' => $categoryIds,
        'valueList' => ArrayHelper::map(Category::categoryList(), 'id', 'title'),
        'inputLabel' => 'Categories',
    ],
]) ?>
