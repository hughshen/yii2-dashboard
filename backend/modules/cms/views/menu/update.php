<?php

use yii\helpers\Html;

$title = $model->translatedField('title', $model->title);
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Menus'), 'url' => ['index', 'parent' => $this->context->parent]];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['view', 'id' => $model->id, 'parent' => $this->context->parent]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="menu-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
