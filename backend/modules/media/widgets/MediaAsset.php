<?php

namespace backend\modules\media\widgets;

use yii\web\AssetBundle;

class MediaAsset extends AssetBundle
{
    public $css = [
        'custom.css'
    ];
    public $js = [
        'custom.js',
    ];
    public $publishOptions = [
        'forceCopy' => YII_DEBUG ? true : false,
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../assets';
    }
}
