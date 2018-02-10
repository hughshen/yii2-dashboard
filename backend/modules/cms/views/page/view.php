<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->translatedField('title', $model->title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'parent',
            'author',
            // 'title',
            // 'content:html',
            // 'excerpt',
            [
                'attribute' => 'title',
                'value' => $model->translatedField('title', $model->title),
            ],
            [
                'attribute' => 'excerpt',
                'value' => $model->translatedField('excerpt', $model->excerpt),
            ],
            [
                'attribute' => 'content',
                'format' => 'html',
                'value' => $model->translatedField('content', $model->content),
            ],
            'slug',
            'guid',
            'type',
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
