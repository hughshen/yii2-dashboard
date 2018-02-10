<?php

namespace common\widgets\mediamanager;

use Yii;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class Widget extends \yii\bootstrap\InputWidget
{
    public $id;

    public function run()
    {
        if ($this->hasModel()) {
            $this->id = Html::getInputId($this->model, $this->attribute);
            $inputName = Html::getInputName($this->model, $this->attribute);
            $inputValue = $this->model{$this->attribute};
        } else {
            $inputValue = $this->value;
            $inputName = $this->name;
        }

        $this->registerClientScript();

        $input = '';
        $input .= Html::beginTag('div', ['class' => 'image-input-wrap', 'style' => 'position: relative;']);
        $input .= Html::textInput($inputName, $inputValue, ['class' => 'form-control', 'id' => $this->id, 'style' => 'padding-left: 45px;']);
        $input .= Html::tag('span', '<i class="glyphicon glyphicon-picture"></i>', ['class' => 'btn btn-success media-manager-toggle', 'style' => 'position: absolute; bottom: 0; left: 0;']);
        $input .= Html::endTag('div');

        return $input;
    }

    protected function registerClientScript()
    {
        $view = $this->getView();
        Asset::register($view);

        $view->registerJs('
        initMediaManager({
            target: "#' . $this->id . '",
            managerUrl: "' . Url::to(['/media/manager-list']) . '",
        });
        ', \yii\web\View::POS_END);
    }
}
