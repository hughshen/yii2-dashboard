<?php

namespace backend\modules\media\models;

use Yii;
use yii\base\Model;

class PathModel extends Model
{
    /**
     * @var string
     */
    public $path;

    /**
     * File system
     */
    protected $fs;

    /**
     * Set file system
     *
     * @param $fs
     */
    public function setFileSystem($fs)
    {
        $this->fs = $fs;
    }

    /**
     * Check path
     *
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function checkPath()
    {
        if (!$this->fs) {
            throw new \yii\base\InvalidConfigException();
        }

        $path = $this->fs->normalizePath($this->path);
        if (!empty($path) && !$this->fs->has($path)) {
            throw new \yii\base\Exception(Yii::t('app', 'Invalid path'));
        }

        return $path;
    }
}
