<?php

use yii\helpers\Html;
use backend\widgets\TranslateInput;

?>
<?= TranslateInput::widget([
    'form' => $form,
    'model' => $model,
    'attributes' => [
        'title' => [
            'type' => 'text',
        ],
        'description' => [
            'type' => 'textarea',
        ],
        'seo_title' => [
            'type' => 'text',
        ],
        'seo_keywords' => [
            'type' => 'text',
        ],
        'seo_description' => [
            'type' => 'text',
        ],
    ],
]) ?>
