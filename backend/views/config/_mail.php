<?php

use yii\helpers\Url;
use yii\helpers\Html;
use common\widgets\ExtraFieldInput;

?>
<div class="form-group">
    <label class="control-label"><?= Yii::t('app', 'Mailer Test') ?></label>
    <div style="position: relative;">
        <input type="text" id="smtp-test-input" class="form-control" style="padding-left: 45px;">
        <span class="btn btn-success" id="smtp-test-toggle" style="position: absolute; bottom: 0; left: 0;">
            <i class="glyphicon glyphicon-send"></i>
        </span>
    </div>
</div>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'smtp_host',
        'valueData' => $config,
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'smtp_user',
        'valueData' => $config,
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'smtp_pass',
        'valueData' => $config,
        'inputType' => 'password',
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'smtp_auth',
        'valueData' => $config,
        'inputType' => 'dropdown',
        'valueList' => [
            '1' => 'Yes',
            '0' => 'No',
        ],
        'defaultValue' => '1',
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'smtp_secure',
        'valueData' => $config,
        'inputType' => 'dropdown',
        'valueList' => [
            '' => 'None',
            'ssl' => 'SSL',
            'tls' => 'TLS',
        ],
        'defaultValue' => 'ssl',
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'smtp_port',
        'valueData' => $config,
        'defaultValue' => 25,
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'smtp_sender',
        'valueData' => $config,
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'smtp_receiver',
        'valueData' => $config,
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?php
$this->registerJs("
;(function($) {
$('#smtp-test-toggle').on('click', function() {
    var input = $('#smtp-test-input');
    var to = input.val();
    if (to.length) {
        $.ajax({
            url: '" . Url::to(['smtp-test']) . "',
            type: 'post',
            data: {to: to},
            dataType: 'json',
            beforeSend: function() {
                input.attr('disabled', 'disabled');
            },
            success: function(data) {
                if (data.status) input.val('');
                alert(data.msg);
                input.removeAttr('disabled');
            }
        });
    }
});
})(jQuery);
", \yii\web\View::POS_END);
?>
