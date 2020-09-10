<?php

namespace backend\modules\media\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

class GroupInput extends BaseInput
{
    public $splitChar = ',';

    public $columnSize = 2;

    public function run()
    {
        $this->parseInputParams();

        $this->registerClientScript();

        if (!is_array($this->inputValue)) {
            if ($this->splitChar) {
                $this->inputValue = explode($this->splitChar, $this->inputValue);
            } else {
                $this->inputValue = [$this->inputValue];
            }
        } else {
            $this->inputValue = array_values(array_filter($this->inputValue));
        }
        $this->inputValue = empty($this->inputValue) ? [''] : $this->inputValue;

        $input = Html::beginTag('div', ['class' => 'row media-input-wrap', 'id' => "{$this->id}-group"]);
        foreach ($this->inputValue as $key => $value) {
            $item = $this->genThumbItem($this->inputName, $value);
            $item = str_replace('{offset}', $key, $item);

            $input .= $item;
        }
        $input .= Html::endTag('div');

        return $input;
    }

    protected function registerClientScript()
    {
        $this->registerInitScript();

        $view = $this->getView();
        $view->registerJs("
        ;(function() {
            root = $('#{$this->id}-group').eq(0);
            itemTpl = `{$this->genThumbItem($this->inputName, '')}`;
            itemOffset = root.find('.group-thumbnail').length;

            function setPreview(btn, url) {
                thumb = $(btn.parents('.group-thumbnail').eq(0).attr('data-preview'));
                thumb.attr('src', url);
            }

            function setInputValue(btn, value) {
                input = $(btn.parents('.group-thumbnail').eq(0).attr('data-input'));
                input.val(value);
            }

            root.on('click', '.btn-remove', function() {
                if (root.find('.group-thumbnail').length < 2) {
                    setPreview($(this), '');
                    setInputValue($(this), '');
                } else {
                    $(this).parents('.group-thumbnail').parent().eq(0).remove();
                }
            });
            
            root.on('click', '.btn-toggle', function() {
                MediaManager.setToggle('#' + $(this).attr('id'), true);
            });
            
            root.on('click', '.btn-append', function() {
                itemNew = itemTpl.replaceAll('{offset}', itemOffset);
                root.append(itemNew);
                itemOffset += 1;
            });
        })();
        ", \yii\web\View::POS_END);
    }

    protected function genThumbItem($name, $value)
    {
        $input = Html::beginTag('div', ['class' => 'col-md-' . $this->columnSize]);
        $input .= Html::beginTag('div', [
            'class' => 'thumbnail group-thumbnail',
            'data-id' => $this->id,
            'data-input' => "#{$this->id}-{offset}",
            'data-preview' => "#{$this->id}-preview-{offset}",
        ]);

        // Image
        $input .= Html::img($value, ['id' => "{$this->id}-preview-{offset}"]);

        // Caption
        $input .= Html::beginTag('div', ['class' => 'caption']);
        $input .= Html::beginTag('div', ['class' => 'btn-group btn-group-justified', 'role' => 'group']);
        $input .= Html::a('<span class="glyphicon glyphicon-trash"></span>', 'javascript:;', [
            'class' => 'btn btn-danger btn-remove',
            'role' => 'button',
        ]);
        $input .= Html::a('<span class="glyphicon glyphicon-picture"></span>', 'javascript:;', [
            'id' => "{$this->id}-toggle-{offset}",
            'class' => 'btn btn-success btn-toggle',
            'role' => 'button',
            'data-input' => "#{$this->id}-{offset}",
            'data-preview' => "#{$this->id}-preview-{offset}",
        ]);
        $input .= Html::a('<span class="glyphicon glyphicon-plus"></span>', 'javascript:;', [
            'class' => 'btn btn-primary btn-append',
            'role' => 'button',
        ]);
        $input .= Html::endTag('div');
        $input .= Html::endTag('div');

        // Input
        $input .= Html::hiddenInput($name . '[{offset}]', $value, [
            'id' => $this->id . '-{offset}',
            'class' => 'form-control',
        ]);

        $input .= Html::endTag('div');
        $input .= Html::endTag('div');

        return $input;
    }
}
