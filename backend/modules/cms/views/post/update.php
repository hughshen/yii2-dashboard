<?php

use yii\helpers\Html;

$title = $model->translatedField('title', $model->title);
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="post-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
