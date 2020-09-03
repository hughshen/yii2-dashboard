<?php

namespace backend\widgets\tinymce;

use yii\web\AssetBundle;

class TinyMCEAsset extends AssetBundle
{
    public $css = [
        'css/custom.css',
    ];
    public $js = [
        'js/custom.js',
    ];
    public $publishOptions = [
        'forceCopy' => YII_DEBUG ? true : false,
    ];
    public $depends = [
        'backend\widgets\tinymce\TinyMCEPluginAsset',
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
    }
}
