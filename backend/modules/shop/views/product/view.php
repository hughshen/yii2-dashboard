<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->translatedField('title', $model->title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
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
            'price',
            'quantity',
            'weight',
            'slug',
            // 'title',
            // 'content:ntext',
            // 'description:ntext',
            [
                'attribute' => 'title',
                'value' => $model->translatedField('title', $model->title),
            ],
            [
                'attribute' => 'description',
                'value' => $model->translatedField('description', $model->description),
            ],
            'image',
            // 'images:ntext',
            'view_count',
            'sorting',
            'extra_data:ntext',
            'status',
            // 'created_at',
            // 'updated_at',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->updated_at);
                }
            ],
        ],
    ]) ?>

</div>
