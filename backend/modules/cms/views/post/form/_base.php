<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\widgets\TranslateInput;
use backend\modules\cms\models\Post;

?>
<?= TranslateInput::widget([
    'form' => $form,
    'model' => $model,
    'attributes' => [
        'title' => [
            'type' => 'text',
        ],
        'content' => [
            'type' => 'editor',
        ],
        'excerpt' => [
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

<?= $form->field($model, 'status')->dropDownList(Post::statusList()) ?>
