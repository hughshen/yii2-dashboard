<?php

namespace backend\modules\media\models;

use Yii;

class CreateForm extends PathModel
{
    /**
     * @var string
     */
    public $folder;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['folder', 'required'],
            [['path', 'folder'], 'string'],
            [['path', 'folder'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'folder' => Yii::t('app', 'Folder'),
        ];
    }

    /**
     * Create folder
     *
     * @throws \yii\base\Exception
     */
    public function create()
    {
        $path = $this->checkPath();

        if (!$this->validate()) {
            throw new \yii\base\Exception(implode('<br>', (array)$this->getFirstErrors()));
        }

        $newPath = rtrim($path, '/') . '/' . trim($this->folder, '/');
        $created = $this->fs->createDir($newPath);
        if ($created === false) {
            throw new \yii\base\Exception(Yii::t('app', 'Create folder failed'));
        }

        return $created;
    }
}
