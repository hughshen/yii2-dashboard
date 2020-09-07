<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="upload-form">

    <?php
    $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
    ]);
    ?>

    <?= $form->field($model, 'path')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'files[]')->fileInput(['multiple' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
