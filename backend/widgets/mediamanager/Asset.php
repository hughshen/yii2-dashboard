<?php

namespace backend\widgets\mediamanager;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $css = [];
    public $js = [
        'js/custom.js',
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
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
    }
}
