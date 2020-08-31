<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\modules\cms\models\Page;

$this->title = Yii::t('app', 'Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Page'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'title',
                'value' => function ($model) {
                    return $model->translatedField('title', $model->title);
                }
            ],
            'slug',
            [
                'attribute' => 'status',
                'filter' => Page::statusList(),
            ],
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
