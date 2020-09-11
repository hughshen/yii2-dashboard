<?php

namespace backend\modules\media\models;

use Yii;

class UploadForm extends PathModel
{
    /**
     * @var \yii\web\UploadedFile
     */
    public $files;

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
            ['path', 'trim'],
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

    public function formName()
    {
        return '';
    }

    /**
     * Upload files
     *
     * @throws \yii\base\Exception
     */
    public function upload()
    {
        $this->checkPath();

        if (!$this->validate()) {
            throw new \yii\base\Exception(implode('<br>', (array)$this->getFirstErrors()));
        }

        $paths = [];
        $errors = [];
        foreach ($this->files as $file) {
            $counter = 1;
            $filePath = "{$this->path}/{$file->baseName}.{$file->extension}";
            while ($this->fs->has($filePath)) {
                $filePath = "{$this->path}/{$file->baseName}_{$counter}.{$file->extension}";
                $counter++;
            }
            $paths[] = $this->fs->urlPrefix . $filePath;

            if ($stream = fopen($file->tempName, 'r+')) {
                $write = $this->fs->writeStream($filePath, $stream);
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

        return $paths;
    }
}
