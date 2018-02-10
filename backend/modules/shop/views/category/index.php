<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Category'), ['create', 'parent' => $this->context->parent], ['class' => 'btn btn-success']) ?>
        &nbsp;&nbsp;
        <?= HtmL::a(Yii::t('app', 'Previous'), ['index', 'parent' => 0], ['class' => 'btn btn-success']) ?>
        &nbsp;&nbsp;
        <?= HtmL::a(Yii::t('app', 'Top'), ['index', 'parent' => 0], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
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
            // 'description',
            'sorting',
            'status',
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{children} {update} {delete}',
                'buttons' => [
                    'children' => function($url, $model, $key) {
                        $title = Yii::t('yii', 'Children');
                        $options = array_merge([
                            'title' => $title,
                            'aria-label' => $title,
                            'data-pjax' => '0',
                        ]);
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-list"]);
                        $url = Url::to(['index', 'parent' => $model->id]);
                        return Html::a($icon, $url, $options);
                    },
                    'update' => function($url, $model, $key) {
                        $title = Yii::t('yii', 'Update');
                        $options = array_merge([
                            'title' => $title,
                            'aria-label' => $title,
                            'data-pjax' => '0',
                        ]);
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]);
                        $url = Url::to(['update', 'id' => $model->id, 'parent' => $model->parent]);
                        return Html::a($icon, $url, $options);
                    },
                    'delete' => function($url, $model, $key) {
                        $title = Yii::t('yii', 'Delete');
                        $options = array_merge([
                            'title' => $title,
                            'aria-label' => $title,
                            'data-pjax' => '0',
                            'data-confirm' => 'Are you sure you want to delete this item?',
                            'data-method' => 'post',
                        ]);
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]);
                        $url = Url::to(['delete', 'id' => $model->id, 'parent' => $model->parent]);
                        return Html::a($icon, $url, $options);
                    },
                ],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
