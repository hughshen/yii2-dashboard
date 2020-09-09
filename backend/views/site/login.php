<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

\yiister\gentelella\assets\Asset::register($this);

$this->title = 'Login';
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <style>
            .login_wrapper {
                margin-top: 0;
            }

            .login_content {
                padding-top: 100px;
            }

            .captcha-area .form-group img {
                float: right;
                height: 40px;
            }

            .captcha-area .form-group input {
                max-width: 65%;
            }

            .help-block {
                text-align: left;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group input {
                height: 40px;
                margin-bottom: 0 !important;
            }
        </style>
    </head>
    <body class="login">
    <?php $this->beginBody() ?>
    <div class="login_wrapper">
        <div class="form login_form">
            <section class="login_content">
                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <h1><?= Html::encode($this->title) ?></h1>

                <?= $form->field($model, 'username')->textInput([
                    'autofocus' => true,
                    'placeholder' => $model->getAttributeLabel('username')
                ])->label(false) ?>

                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => $model->getAttributeLabel('password')
                ])->label(false) ?>

                <div class="captcha-area">
                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => $model->getAttributeLabel('verifyCode')
                        ],
                    ])->label(false) ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-lg btn-primary btn-block', 'name' => 'login-button']) ?>
                </div>

                <div class="clearfix"></div>

                <?php ActiveForm::end(); ?>
            </section>
        </div>
    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>