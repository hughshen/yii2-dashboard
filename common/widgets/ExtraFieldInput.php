<?php

namespace common\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class ExtraFieldInput extends \yii\bootstrap\Widget
{
    public $options;

    private $_options;

    public function init()
    {
        parent::init();
        $this->initOptions();
    }

    public function run()
    {
        $options = $this->options;

        if (empty($options['fieldName']) && empty($options['inputName'])) return;

        $inputId = !empty($options['inputId']) ? $options['inputId'] : $options['arrayName'] . $options['fieldName'];
        $inputName = !empty($options['inputName']) ? $options['inputName'] : $options['arrayName'] . "[{$options['fieldName']}]";

        if (isset($options['valueData'][$options['fieldName']])) {
            $inputValue = $options['valueData'][$options['fieldName']];
        } elseif ($options['defaultValue'] === '') {
            $inputValue = null;
        } else {
            $inputValue = $options['defaultValue'];
        }

        $html = Html::label($this->getLabel(), $inputId, ['class' => 'control-label']);
        switch ($options['inputType']) {
            case 'text':
                $html .= Html::textInput($inputName, $inputValue, ['class' => 'form-control', 'id' => $inputId]);
                break;
            case 'password':
                $html .= Html::passwordInput($inputName, $inputValue, ['class' => 'form-control', 'id' => $inputId]);
                break;
            case 'textarea':
                $html .= Html::textarea($inputName, $inputValue, ['class' => 'form-control', 'id' => $inputId, 'rows' => 4]);
                break;
            case 'image':
                $html .= \common\widgets\mediamanager\Widget::widget(['name' => $inputName, 'value' => $inputValue, 'id' => $inputId]);
                break;
            case 'dropdown':
                $html .= Html::dropDownList($inputName, $inputValue, $options['valueList'], ['class' => 'form-control', 'id' => $inputId, 'prompt' => $options['promptText']]);
                break;
            case 'checkboxlist':
                $html .= Html::checkboxList($inputName, $inputValue, $options['valueList'], ['id' => $inputId]);
                break;
        }

        return Html::tag('div', $html, ['class' => "form-group field-{$inputId}"]);
    }

    protected function getLabel()
    {
        return (!empty($this->options['inputLabel']) ? $this->options['inputLabel'] : trim(ucwords(str_replace('_', ' ', $this->options['fieldName']))));
    }

    protected function initOptions()
    {
        $this->_options['fieldName'] = '';
        $this->_options['inputId'] = '';
        $this->_options['inputName'] = '';
        $this->_options['inputLabel'] = '';
        $this->_options['inputType'] = 'text';
        $this->_options['defaultValue'] = '';
        $this->_options['valueList'] = [];
        $this->_options['valueData'] = [];
        $this->_options['arrayName'] = 'ExtraFields';
        $this->_options['promptText'] = null;

        $this->options = ArrayHelper::merge($this->_options, $this->options);
    }
}
