<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app', 'Translate'),
                'content' => $this->render('form/_tran', [
                    'form' => $form,
                    'model' => $model,
                ]),
                'active' => true,
            ],
            [
                'label' => Yii::t('app', 'Base'),
                'content' => $this->render('form/_base', [
                    'form' => $form,
                    'model' => $model,
                ]),
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
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
