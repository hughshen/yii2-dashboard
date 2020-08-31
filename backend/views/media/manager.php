<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

?>
<div class="row">
    <div class="col-md-12">
        <?= $this->render('_box', ['list' => $list, 'mediaName' => true, 'disableUrl' => true]) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= LinkPager::widget([
            'pagination' => $pages,
            'prevPageLabel' => '&lsaquo;',
            'nextPageLabel' => '&rsaquo;',
            'firstPageLabel' => '&laquo;',
            'lastPageLabel' => '&raquo;',
            'maxButtonCount' => 4,
        ]) ?>
    </div>
</div>