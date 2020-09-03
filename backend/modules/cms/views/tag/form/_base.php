<?php

use yii\helpers\Html;
use backend\modules\cms\models\Tag;

?>
<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'image')->widget(\backend\modules\media\widgets\TextInput::className()) ?>

<?= $form->field($model, 'sorting')->textInput() ?>

<?= $form->field($model, 'status')->dropDownList(Tag::statusList()) ?>
