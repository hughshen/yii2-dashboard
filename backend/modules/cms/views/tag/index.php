<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Tags');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tag-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tag'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'title',
            [
                'attribute' => 'title',
                'value' => function ($model) {
                    return $model->translatedField('title', $model->title);
                }
            ],
            'slug',
            // 'description',
            'sorting',
            'status',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                }
            ],

            ['class' => 'backend\widgets\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
