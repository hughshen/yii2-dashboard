<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\widgets\LinkPager;
use backend\modules\media\widgets\MediaAsset;

MediaAsset::register($this);

$this->title = Yii::t('app', 'Media');
$this->params['breadcrumbs'][] = $this->title;

$path = Yii::$app->request->get('path');
$search = Yii::$app->request->get('search');

?>
<div class="media-search">
    <div class="row">
        <div class="col-md-12">
            <?= Html::beginForm(['index'], 'get') ?>
            <?= Html::hiddenInput('path', $path) ?>
            <div class="row">
                <div class="col-md-2">
                    <?= Html::textInput('search', $search, ['class' => 'form-control']) ?>
                </div>
                <div class="col-md-10">
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('app', 'Reset'), ['index'], ['class' => 'btn btn-default']) ?>
                    <?= Html::button(Yii::t('app', 'Create'), [
                        'class' => 'btn btn-success',
                        'data-toggle' => 'modal',
                        'data-target' => '#create-modal',
                    ]) ?>
                    <?= Html::button(Yii::t('app', 'Upload'), [
                        'class' => 'btn btn-success',
                        'data-toggle' => 'modal',
                        'data-target' => '#upload-modal',
                    ]) ?>
                </div>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            Modal::begin([
                'id' => 'upload-modal',
                'header' => Html::tag('h2', Yii::t('app', 'Upload')),
            ]);
            echo $this->render('_upload', [
                'model' => $uploadModel,
            ]);
            Modal::end();
            ?>
        </div>
        <div class="col-md-12">
            <?php
            Modal::begin([
                'id' => 'create-modal',
                'header' => Html::tag('h2', Yii::t('app', 'Create')),
            ]);
            echo $this->render('_create', [
                'model' => $createModel,
            ]);
            Modal::end();
            ?>
        </div>
    </div>
</div>
<hr>
<div class="media-area">
    <div class="row">
        <div class="col-md-12">
            <?= $this->render('_box', ['list' => $list, 'caption' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            echo LinkPager::widget([
                'pagination' => $pages,
                'prevPageLabel' => '&lsaquo;',
                'nextPageLabel' => '&rsaquo;',
                'firstPageLabel' => '&laquo;',
                'lastPageLabel' => '&raquo;',
                'maxButtonCount' => 4,
            ]);
            ?>
        </div>
    </div>
</div>
