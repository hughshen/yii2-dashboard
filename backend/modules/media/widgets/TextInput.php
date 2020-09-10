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

        $input = '';
        $input .= Html::beginTag('div', ['class' => 'media-input-wrap']);

        // Preview
        $input .= Html::tag('div', Html::tag('div', Html::img($inputValue)), [
            'id' => $this->id . '-preview',
            'class' => 'media-input-preview',
            'style' => !$inputValue ? 'display: none;' : '',
        ]);

        // Input group
        $input .= Html::beginTag('div', ['class' => 'input-group']);

        // Toggle
        $input .= Html::beginTag('div', ['class' => 'input-group-btn']);
        $input .= Html::tag('button', '<span class="glyphicon glyphicon-picture"></span>', [
            'id' => $this->id . '-toggle',
            'class' => 'btn btn-success media-input-toggle',
            'type' => 'button',
            'data-input' => "#{$this->id}",
            'data-preview' => "#{$this->id}-preview",
        ]);
        $input .= Html::endTag('div');

        // Input
        $input .= Html::textInput($inputName, $inputValue, [
            'id' => $this->id,
            'class' => 'form-control',
        ]);

        $input .= Html::endTag('div');
        // Input group end

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
