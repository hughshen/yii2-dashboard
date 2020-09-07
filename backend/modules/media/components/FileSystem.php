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
     * @param $encodedData
     * @param null $fileName
     * @return bool
     * @throws \Exception
     */
    public function saveBase64Data($encodedData, $fileName = null)
    {
        @list($type, $data) = explode(';', $encodedData);
        @list(, $data) = explode(',', $data);

        $data = base64_decode($data);

        if ($type && $data) {
            @list(, $ext) = explode('/', $type);

            if ($ext == 'jpeg') $ext = 'jpg';

            try {
                if (!$ext || !in_array($ext, ['jpg', 'png', 'gif', 'jpeg'])) {
                    throw new \yii\base\Exception(Yii::t('app', 'Only files with these extensions are allowed: ') . implode(', ', ['jpg', 'png', 'gif', 'jpeg']));
                }

                if ($fileName) {
                    $fileName = (string)time();
                }

                $counter = 1;
                $filePath = "{$fileName}.{$ext}";
                while ($this->has($filePath)) {
                    $filePath = "{$fileName}_{$counter}.{$ext}";
                    $counter++;
                }

                $write = $this->write($filePath, $data);
                if (!$write) {
                    throw new \yii\base\Exception("Failed to write file (${filePath})");
                }

                return $this->urlPrefix . trim($filePath);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return false;
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
