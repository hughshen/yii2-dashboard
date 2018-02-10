<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\widgets\TranslateInput;
use backend\modules\cms\models\Menu;

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

<hr>

<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'sorting')->textInput() ?>

<?= $form->field($model, 'status')->dropDownList(Menu::statusList()) ?>
