<?php

use yii\helpers\Html;
use common\widgets\ExtraFieldInput;

?>
<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'site_logo',
        'inputType' => 'image',
        'valueData' => $config,
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'site_email',
        'valueData' => $config,
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'site_domain',
        'valueData' => $config,
        'arrayName' => $this->context->arrayName,
    ],
]) ?>

<?= ExtraFieldInput::widget([
    'options' => [
        'fieldName' => 'site_noindex',
        'valueData' => $config,
        'inputType' => 'dropdown',
        'valueList' => [
            '1' => 'Yes',
            '0' => 'No',
        ],
        'defaultValue' => '0',
        'arrayName' => $this->context->arrayName,
    ],
]) ?>
