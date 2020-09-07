<?php

namespace backend\modules\media;

use Yii;
use backend\modules\media\components\FileSystem;

/**
 * Media module definition class
 *
 * Inspired by https://github.com/iutbay/yii2-mm
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\media\controllers';

    /**
     * Filesystem
     */
    public $fs;

    /**
     * Filesystem component name
     * @var string
     */
    public $fsComponent = 'fs';

    /**
     * Directory separator
     * @var string
     */
    public $directorySeparator = '/';

    /**
     * Allow extensions
     * @var array
     */
    public $allowExtensions = [];

    /**
     * Allow mime types
     * @var array
     */
    public $allowMimeTypes = [];

    /**
     * URL prefix
     * @var string
     */
    public $urlPrefix = '/uploads/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset(Yii::$app->{$this->fsComponent})) {
            throw new \yii\base\InvalidConfigException();
        }

        $this->fs = new FileSystem([
            'fs' => Yii::$app->{$this->fsComponent},
            'directorySeparator' => $this->directorySeparator,
            'urlPrefix' => $this->urlPrefix,
        ]);
    }
}
