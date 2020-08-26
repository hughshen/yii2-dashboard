<?php

use yii\helpers\Url;
use yii\helpers\Html;
use common\widgets\TranslateInput;
use backend\modules\shop\models\Product;

?>
<?= TranslateInput::widget([
    'form' => $form,
    'model' => $model,
    'attributes' => [
        'title' => [
            'type' => 'text',
        ],
        'content' => [
            'type' => 'editor',
        ],
        'description' => [
            'type' => 'textarea',
        ],
        'seo_title' => [
            'type' => 'text',
        ],
        'seo_keywords' => [
            'type' => 'text',
        ],
        'seo_description' => [
            'type' => 'text',
        ],
    ],
]) ?>

<hr>

<?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'quantity')->textInput() ?>

<?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'image')->widget(\common\widgets\mediamanager\Widget::className()) ?>

<?= $form->field($model, 'view_count')->textInput() ?>

<?= $form->field($model, 'sorting')->textInput() ?>

<?= $form->field($model, 'status')->dropDownList(Product::statusList()) ?>
