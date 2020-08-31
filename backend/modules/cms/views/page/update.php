<?php

use yii\helpers\Html;

$title = $model->translatedField('title', $model->title);
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="page-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
