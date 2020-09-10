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
            'id' => $this->id . '-preview',
            'class' => 'image-input-preview',
            'style' => $viewStyle,
        ]);
        // View end
        $input .= Html::textInput($inputName, $inputValue, [
            'id' => $this->id,
            'class' => 'form-control',
            'style' => 'padding-left: 45px;',
        ]);
        $input .= Html::tag('span', '<i class="glyphicon glyphicon-picture"></i>', [
            'id' => $this->id . '-toggle',
            'class' => 'btn btn-success media-manager-toggle',
            'style' => 'margin-bottom: 0px; position: absolute; bottom: 0; left: 0;',
            'data-input' => "#{$this->id}",
            'data-preview' => "#{$this->id}-preview",

        ]);
        $input .= Html::endTag('div');

        return $input;
    }

    protected function registerClientScript()
    {
        $view = $this->getView();
        MediaAsset::register($view);

        $view->registerJs('
        ;MediaManager.init({
            title: "' . Yii::t('app', 'Media Manager') . '",
            mediaUrl: "' . Url::to(['/media/manager/popup']) . '",
        });
        ', \yii\web\View::POS_END, 'media-manager-init-script');

        $toggleId = "#{$this->id}-toggle";
        $view->registerJs("
        ;$('{$toggleId}').on('click', function() {
            MediaManager.setToggle('{$toggleId}', true);
        });
        ", \yii\web\View::POS_END);
    }
}
