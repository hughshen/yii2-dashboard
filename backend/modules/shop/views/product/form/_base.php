<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\modules\shop\models\Product;

?>
<?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'quantity')->textInput() ?>

<?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'image')->widget(\backend\widgets\mediamanager\Widget::className()) ?>

<?= $form->field($model, 'view_count')->textInput() ?>

<?= $form->field($model, 'sorting')->textInput() ?>

<?= $form->field($model, 'status')->dropDownList(Product::statusList()) ?>
