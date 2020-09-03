<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\modules\cms\models\Page;

?>
<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'image')->widget(\backend\modules\media\widgets\TextInput::className()) ?>

<?= $form->field($model, 'view_count')->textInput() ?>

<?= $form->field($model, 'sorting')->textInput() ?>

<?= $form->field($model, 'status')->dropDownList(Page::statusList()) ?>
