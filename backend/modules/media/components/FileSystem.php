<?php

namespace backend\modules\media\components;

use Yii;
use yii\helpers\Url;
use yii\helpers\FileHelper;

class FileSystem extends \yii\base\Component
{

    /**
     * Filesystem
     */
    public $fs;

    /**
     * @var string
     */
    public $directorySeparator = '/';

    /**
     * @var boolean
     */
    public $local;

    /**
     * @var string
     */
    public $urlPrefix = '';

    /**
     * Image extensions
     * @var array
     */
    public $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];

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
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->fs instanceof \creocoder\flysystem\Filesystem) {
            throw new \yii\base\InvalidConfigException();
        }

        $this->local = ($this->fs instanceof \creocoder\flysystem\LocalFilesystem);
    }

    /**
     * @param string $path
     * @return string
     */
    public function normalizePath($path)
    {
        return FileHelper::normalizePath($path, $this->directorySeparator);
    }

    /**
     * @param string $path
     * @param boolean $recursive
     * @param string $filter
     * @return array
     */
    public function listContents($path = '', $recursive = false, $filter = '')
    {
        $contents = $this->fs->listContents($path, $recursive);
        return $this->filterContents($contents, $recursive, $filter);
    }

    /**
     * @param array $contents
     * @param boolean $recursive
     * @param string $filter
     * @return array
     */
    protected function filterContents($contents, $recursive = false, $filter = '')
    {
        $new = [];
        foreach ($contents as $f) {
            if ($recursive && isset($f['type']) && $f['type'] === 'dir') {
                continue;
            }

            if (isset($f['basename'])) {
                if (preg_match('#^\.#', $f['basename']))
                    continue;

                if (!empty(trim($filter)) && !preg_match("/.*{$filter}.*/i", $f['basename']))
                    continue;
            }

            if ($f['type'] === 'file') {
                // Set file url
                $f['url'] = $this->urlPrefix . trim($f['path']);

                // If is image
                $f['image'] = 0;
                if (in_array(strtolower($f['extension']), $this->imageExtensions)) {
                    $f['image'] = 1;
                }
            }

            $new[] = $f;
        }

        return $new;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->fs, $method], $parameters);
    }
}
