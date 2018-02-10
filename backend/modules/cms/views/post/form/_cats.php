<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use common\widgets\ExtraFieldInput;
use backend\modules\cms\models\Category;
use backend\modules\cms\models\Tag;

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

<?= ExtraFieldInput::widget([
    'options' => [
        'inputId' => 'post-tag',
        'inputName' => 'Categories[]',
        'inputType' => 'checkboxlist',
        'defaultValue' => $categoryIds,
        'valueList' => ArrayHelper::map(Tag::categoryList(), 'id', 'title'),
        'inputLabel' => 'Tags',
    ],
]) ?>
