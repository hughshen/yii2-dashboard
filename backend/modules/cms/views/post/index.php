<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use backend\modules\cms\models\Post;

$this->title = Yii::t('app', 'Posts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Post'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'title',
            [
                'attribute' => 'title',
                'value' => function($model) {
                    return $model->translatedField('title', $model->title);
                }
            ],
            'slug',
            // 'excerpt',
            [
                'attribute' => 'excerpt',
                'value' => function($model) {
                    return $model->translatedField('excerpt', $model->excerpt);
                }
            ],
            [
                'attribute' => 'status',
                'filter' => Post::statusList(),
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
