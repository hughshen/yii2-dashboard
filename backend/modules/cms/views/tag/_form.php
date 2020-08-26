<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\TranslateInput;
use backend\modules\cms\models\Tag;

?>

<div class="tag-form">

    <?php $form = ActiveForm::begin(); ?>

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

    <?= $form->field($model, 'status')->dropDownList(Tag::statusList()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
