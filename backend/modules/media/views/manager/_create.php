<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="create-form">

    <?php $form = ActiveForm::begin(['action' => ['create']]); ?>

    <?= $form->field($model, 'path')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'folder')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
