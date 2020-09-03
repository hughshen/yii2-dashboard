<?php

use yii\helpers\Html;
use backend\modules\cms\models\Category;

?>
<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'image')->widget(\backend\widgets\mediamanager\Widget::className()) ?>

<?= $form->field($model, 'sorting')->textInput() ?>

<?= $form->field($model, 'status')->dropDownList(Category::statusList()) ?>
