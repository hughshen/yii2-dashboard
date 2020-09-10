<?php

namespace backend\modules\media\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

class BaseInput extends \yii\bootstrap\InputWidget
{
    public $id;

    protected $inputName;
    protected $inputValue;

    protected function registerClientScript()
    {
        $this->registerInitScript();
    }

    protected function registerInitScript()
    {
        $view = $this->getView();
        MediaAsset::register($view);

        $view->registerJs('
        ;MediaManager.init({
            title: "' . Yii::t('app', 'Media Manager') . '",
            mediaUrl: "' . Url::to(['/media/manager/popup']) . '",
        });
        ', \yii\web\View::POS_END, 'media-manager-init-script');
    }

    protected function parseInputParams()
    {
        if ($this->hasModel()) {
            $this->id = Html::getInputId($this->model, $this->attribute);
            $this->inputName = Html::getInputName($this->model, $this->attribute);
            $this->inputValue = $this->model{$this->attribute};
        } else {
            $this->inputName = $this->name;
            $this->inputValue = $this->value;
        }
    }
}
