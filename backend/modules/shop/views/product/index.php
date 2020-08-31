<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Product'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'attribute' => 'title',
                'value' => function ($model) {
                    return $model->translatedField('title', $model->title);
                }
            ],
            'slug',
            'price',
            'quantity',
            // 'weight',
            // 'title',
            //'content:ntext',
            //'description:ntext',
            //'image',
            //'images:ntext',
            // 'view_count',
            //'sorting',
            //'extra_data:ntext',
            // 'status',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                }
            ],
            //'created_at',
            //'updated_at',
            //'deleted_at',

            ['class' => 'backend\widgets\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
