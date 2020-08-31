<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Languages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-index">

    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Language'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'title',
            'code',
            'locale',
            // 'image',
            // 'is_default',
            'sorting',
            'status',
            // 'created_at',
            // 'updated_at',

            [
                'class' => 'backend\widgets\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
