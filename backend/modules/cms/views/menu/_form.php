<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

?>

<div class="menu-form">

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
                'label' => Yii::t('app', 'Meta'),
                'content' => $model->renderExtraTabContent(),
            ],
        ],
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs("
;(function($) {
$('#ExtraFieldstype').on('change', function() {
    console.log($(this).val());
    $.ajax({
        url: '" . Url::to(['type-list']) . "',
        type: 'post',
        data: {type: $(this).val()},
        dataType: 'json',
        success: function(data) {
            var options = '<option value>----</option>';
            $.each(data, function(key, val) {
                options += '<option value=\"' + val.id + '\">' + val.value + '</option>';
            });
            $('#ExtraFieldstype_id').html(options);
        },
    });
});
})(jQuery);
", \yii\web\View::POS_END);
?>
