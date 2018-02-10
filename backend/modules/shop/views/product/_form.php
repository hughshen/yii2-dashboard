<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app', 'Base'),
                'content' => $this->render('form/_base', [
                    'form' => $form,
                    'model' => $model,
                ]),
                'active' => true,
            ],
            [
                'label' => Yii::t('app', 'Categories'),
                'content' => $this->render('form/_cats', [
                    'model' => $model
                ]),
            ],
        ],
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
