<?php

namespace backend\widgets\tinymce;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use backend\modules\media\components\Media;

class TinyMCEEditor extends \yii\widgets\InputWidget
{
    public $clientOptions = [];

    public $fileUploadUrl;
    public $fileDeleteUrl;
    public $fileManagerUrl;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->id;
        $this->value = $this->hasModel() ? $this->model->{$this->attribute} : $this->value;

        if (!$this->fileUploadUrl) {
            $this->fileUploadUrl = Url::to(['/media/api/upload']);
        }
        if (!$this->fileDeleteUrl) {
            $this->fileDeleteUrl = Url::to(['/media/api/delete']);
        }
        if (!$this->fileManagerUrl) {
            $this->fileManagerUrl = Url::to(['/media/manager/popup']);
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            return Html::activeTextarea($this->model, $this->attribute, ['id' => $this->id]);
        } else {
            return Html::textarea($this->name, $this->value, ['id' => $this->id]);
        }
    }

    protected function registerClientScript()
    {
        $view = $this->getView();
        TinyMCEAsset::register($view);

        $allowExtensions = Media::ALLOW_EXTENSIONS;
        $allowMimeTypes = implode(',', Media::ALLOW_MIME_TYPES);

        $view->registerJs('
        initTinyMCE({
            selector: "#' . $this->id . '",
        }, {
            uploadUrl: "' . $this->fileUploadUrl . '",
            deleteUrl: "' . $this->fileDeleteUrl . '",
            managerUrl: "' . $this->fileManagerUrl . '",
            allowExtensions: ' . Json::encode($allowExtensions) . ',
            allowMimeTypes: "' . $allowMimeTypes . '",        
        });
        ', \yii\web\View::POS_END);
    }
}
