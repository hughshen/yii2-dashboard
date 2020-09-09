<?php

namespace backend\modules\media\models;

use Yii;
use yii\base\Model;

class UploadForm extends Model
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var \yii\web\UploadedFile
     */
    public $files;

    /**
     * File system
     */
    protected $fs;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $extensions = [];
        $mimeTypes = [];
        if ($this->fs) {
            $extensions = $this->fs->allowExtensions;
            $mimeTypes = $this->fs->allowMimeTypes;
        }

        return [
            ['path', 'string'],
            ['files', 'required'],
            [
                ['files'], 'file',
                'skipOnEmpty' => false,
                'extensions' => $extensions,
                'mimeTypes' => $mimeTypes,
                'maxSize' => 10 * 1024 * 1024,
                'maxFiles' => 10,
                'checkExtensionByMimeType' => false,
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'files' => Yii::t('app', 'Files'),
        ];
    }

    public function setFileSystem($fs)
    {
        $this->fs = $fs;
    }

    /**
     * Upload files
     * @param $fs
     * @throws \yii\base\Exception
     */
    public function upload($fs)
    {
        if (!$this->validate()) {
            throw new \yii\base\Exception(implode('<br>', (array)$this->getFirstErrors()));
        }

        $errors = [];
        foreach ($this->files as $file) {
            $counter = 1;
            $filePath = "{$this->path}/{$file->baseName}.{$file->extension}";
            while ($fs->has($filePath)) {
                $filePath = "{$this->path}/{$file->baseName}_{$counter}.{$file->extension}";
                $counter++;
            }

            if ($stream = fopen($file->tempName, 'r+')) {
                $write = $fs->writeStream($filePath, $stream);
                fclose($stream);
                if (!$write) {
                    $errors[] = "Failed to write file (${filePath})";
                }
            } else {
                $errors[] = "Failed to get file (${filePath})";
            }
        }

        if (count($errors) > 0) {
            throw new \yii\base\Exception(implode('<br>', $errors));
        }
    }
}
