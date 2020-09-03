<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

$this->title = Yii::t('app', 'Config');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="config-index">

    <div class="config-form">

        <?php $form = ActiveForm::begin([
            'action' => Url::to(['update'])
        ]); ?>

        <?php
        echo Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('app', 'Base'),
                    'content' => $this->render('_base', [
                        'config' => $config,
                    ]),
                    'active' => true,
                ],
                [
                    'label' => Yii::t('app', 'Translate'),
                    'content' => $this->render('_tran', [
                        'config' => $config,
                    ]),
                ],
                [
                    'label' => Yii::t('app', 'Mail'),
                    'content' => $this->render('_mail', [
                        'config' => $config,
                    ]),
                ],
            ],
        ]);
        ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
