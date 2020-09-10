<?php

namespace backend\modules\media\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

class TextInput extends BaseInput
{
    public function run()
    {
        $this->parseInputParams();

        $this->registerClientScript();

        $input = '';
        $input .= Html::beginTag('div', ['class' => 'media-input-wrap']);

        // Preview
        $input .= Html::tag('div', Html::img($this->inputValue, ['class' => 'img-thumbnail']), [
            'id' => $this->id . '-preview',
            'class' => 'media-input-preview',
            'style' => !$this->inputValue ? 'display: none;' : '',
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
        $input .= Html::textInput($this->inputName, $this->inputValue, [
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
        $this->registerInitScript();

        $view = $this->getView();
        $toggleId = "#{$this->id}-toggle";
        $view->registerJs("
        ;$('{$toggleId}').on('click', function() {
            MediaManager.setToggle('{$toggleId}', true);
        });
        ", \yii\web\View::POS_END);
    }
}
