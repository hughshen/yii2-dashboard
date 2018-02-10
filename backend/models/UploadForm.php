<?php

namespace backend\models;

use Yii;
use yii\base\Model;

use common\models\Media;

class UploadForm extends Model
{
    public $files;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['files', 'required'],
            [['files'], 'file', 'maxFiles' => 10, 'maxSize' => Media::MAX_FILE_SIZE, 'extensions' => Media::ALLOW_EXTENSIONS, 'mimeTypes' => Media::ALLOW_MIME_TYPES, 'checkExtensionByMimeType' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'files' => Yii::t('app', 'Files'),
        ];
    }
}
