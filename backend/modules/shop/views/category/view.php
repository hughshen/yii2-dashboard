<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->translatedField('title', $model->title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Categories'), 'url' => ['index', 'parent' => $this->context->parent]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id, 'parent' => $this->context->parent], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id, 'parent' => $this->context->parent], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'parent',
            // 'title',
            // 'description',
            [
                'attribute' => 'title',
                'value' => $model->translatedField('title', $model->title),
            ],
            [
                'attribute' => 'description',
                'value' => $model->translatedField('description', $model->description),
            ],
            'slug',
            'sorting',
            'status',
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($model) {
                    return date('Y-m-d H:i:s', $model->updated_at);
                }
            ],
        ],
    ]) ?>

</div>
