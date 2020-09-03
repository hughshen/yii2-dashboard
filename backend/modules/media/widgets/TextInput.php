<?php

namespace backend\modules\media\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

class TextInput extends \yii\bootstrap\InputWidget
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

        $viewStyle = 'width: 100px; height: 100px; overflow: hidden; margin-bottom: 10px;';
        if (!$inputValue) {
            $viewStyle .= ' display: none;';
        }

        $input = '';
        $input .= Html::beginTag('div', ['class' => 'image-input-wrap', 'style' => 'position: relative;']);
        // View
        $input .= Html::tag('div', Html::tag('div', Html::img($inputValue, [
            'style' => 'height: 100px; width: auto;',
        ])), [
            'id' => $this->id . '-view',
            'class' => 'image-input-view',
            'style' => $viewStyle,
        ]);
        // View end
        $input .= Html::textInput($inputName, $inputValue, ['class' => 'form-control', 'id' => $this->id, 'style' => 'padding-left: 45px;']);
        $input .= Html::tag('span', '<i class="glyphicon glyphicon-picture"></i>', ['class' => 'btn btn-success media-manager-toggle', 'style' => 'margin-bottom: 0px; position: absolute; bottom: 0; left: 0;']);
        $input .= Html::endTag('div');

        return $input;
    }

    protected function registerClientScript()
    {
        $view = $this->getView();
        MediaAsset::register($view);

        $view->registerJs('
        initMediaManager({
            target: "#' . $this->id . '",
            targetView: "#' . $this->id . '-view",
            managerUrl: "' . Url::to(['/media/manager/popup']) . '",
        });
        ', \yii\web\View::POS_END);
    }
}