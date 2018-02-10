<?php

namespace common\widgets\tinymce;

use yii\web\AssetBundle;

class TinyMCEPluginAsset extends AssetBundle
{
    public $sourcePath = '@vendor/tinymce/tinymce/';
    public $js = [
        'tinymce.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
