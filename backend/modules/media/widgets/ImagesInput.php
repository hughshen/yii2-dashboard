<?php

namespace backend\modules\media\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

class ImagesInput extends BaseInput
{
    public $columnSize = 2;

    protected function defaultValue()
    {
        return [
            'link' => '',
            'image' => '',
            'title' => '',
            'caption' => '',
        ];
    }

    protected function genThumbItem($name, $value)
    {
        $input = Html::beginTag('div', ['class' => "col-md-{$this->columnSize}"]);
        $input .= Html::beginTag('div', [
            'class' => 'thumbnail images-thumbnail',
            'data-id' => $this->id,
            'data-input' => "#{$this->id}-{offset}-image",
            'data-preview' => "#{$this->id}-preview-{offset}",
        ]);

        // Image
        $input .= Html::img($value['image'], ['id' => "{$this->id}-preview-{offset}"]);

        // Caption
        $input .= Html::beginTag('div', ['class' => 'caption']);
        $input .= Html::beginTag('div', [
            'role' => 'group',
            'class' => 'btn-group btn-group-xs btn-group-justified',
        ]);
        // Remove
        $input .= Html::a('<span class="glyphicon glyphicon-trash"></span>', 'javascript:;', [
            'class' => 'btn btn-danger btn-remove',
            'role' => 'button',
            'title' => Yii::t('app', 'Remove'),
        ]);
        // Link
        $input .= Html::a('<span class="glyphicon glyphicon-link"></span>', 'javascript:;', [
            'class' => 'btn btn-success btn-link btn-prompt',
            'role' => 'button',
            'title' => Yii::t('app', 'Link'),
            'data-input' => "#{$this->id}-{offset}-link",
        ]);
        // Title
        $input .= Html::a('<span class="glyphicon glyphicon-header"></span>', 'javascript:;', [
            'class' => 'btn btn-success btn-title btn-prompt',
            'role' => 'button',
            'title' => Yii::t('app', 'Title'),
            'data-input' => "#{$this->id}-{offset}-title",
        ]);
        // Caption
        $input .= Html::a('<span class="glyphicon glyphicon-pencil"></span>', 'javascript:;', [
            'class' => 'btn btn-success btn-caption btn-prompt',
            'role' => 'button',
            'title' => Yii::t('app', 'Caption'),
            'data-input' => "#{$this->id}-{offset}-caption",
        ]);
        // Toggle
        $input .= Html::a('<span class="glyphicon glyphicon-picture"></span>', 'javascript:;', [
            'id' => "{$this->id}-toggle-{offset}",
            'class' => 'btn btn-success btn-toggle',
            'role' => 'button',
            'title' => Yii::t('app', 'Image'),
            'data-input' => "#{$this->id}-{offset}-image",
            'data-preview' => "#{$this->id}-preview-{offset}",
        ]);
        // Append
        $input .= Html::a('<span class="glyphicon glyphicon-plus"></span>', 'javascript:;', [
            'class' => 'btn btn-primary btn-append',
            'role' => 'button',
            'title' => Yii::t('app', 'Append'),
        ]);
        $input .= Html::endTag('div');
        $input .= Html::endTag('div');

        // Input
        foreach ($value as $k => $v) {
            $input .= Html::hiddenInput("{$name}[{offset}][{$k}]", $v, [
                'id' => "{$this->id}-{offset}-{$k}",
            ]);
        }

        $input .= Html::endTag('div');
        $input .= Html::endTag('div');

        return $input;
    }

    protected function registerClientScript()
    {
        $this->registerInitScript();

        $view = $this->getView();
        $view->registerJs("
        ;(function() {
            var root = $('#{$this->id}-images').eq(0);
            var itemTpl = `{$this->genThumbItem($this->inputName, $this->defaultValue())}`;
            var itemOffset = root.find('.images-thumbnail').length;

            function setPreview(btn, url) {
                var thumb = $(btn.parents('.images-thumbnail').eq(0).attr('data-preview'));
                thumb.attr('src', url);
            }

            function setInputValue(btn, value) {
                var input = $(btn.parents('.images-thumbnail').eq(0).attr('data-input'));
                input.val(value);
            }

            root.on('click', '.btn-remove', function() {
                if (root.find('.images-thumbnail').length < 2) {
                    setPreview($(this), '');
                    setInputValue($(this), '');
                } else {
                    $(this).parents('.images-thumbnail').parent().eq(0).remove();
                }
            });
            
            root.on('click', '.btn-prompt', function() {
                var inputEle = $($(this).attr('data-input')).eq(0);
                var inputVal = inputEle.val();
                
                var modalText = `<div class='modal fade' id='media-images-modal' role='dialog' aria-hidden='true'>
                    <div class='modal-dialog modal-dialog-centered' role='document'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                            </div>
                            <div class='modal-body'>
                                <textarea 
                                    rows=4
                                    class='form-control' 
                                    id='media-prompt-input' 
                                    style='resize: none;'>` + inputVal + `</textarea>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-dismiss='modal'>" . Yii::t('app', 'Close') . "</button>
                                <button type='button' class='btn btn-primary' id='update-images-input'>" . Yii::t('app', 'Save') . "</button>
                            </div>
                        </div>
                    </div>
                </div>`;
                $('body').append(modalText);
                
                var modalEle = $('#media-images-modal').eq(0);

                modalEle.on('hide.bs.modal', function(e) {
                    modalEle.remove();
                });
                
                modalEle.on('click', '#update-images-input', function() {
                    var areaEle = modalEle.find('textarea').eq(0);
                    inputEle.val(areaEle.val());
                    modalEle.modal('hide');
                });
                
                modalEle.modal('show');
            });

            root.on('click', '.btn-toggle', function() {
                MediaManager.setToggle('#' + $(this).attr('id'), true);
            });
            
            root.on('click', '.btn-append', function() {
                var itemNew = itemTpl.replaceAll('{offset}', itemOffset);
                root.append(itemNew);
                itemOffset += 1;
            });
        })();
        ", \yii\web\View::POS_END);
    }

    public function run()
    {
        $this->parseInputParams();
        if (!is_array($this->inputValue)) {
            $this->inputValue = @json_decode($this->inputValue, true);
        }
        if (!is_array($this->inputValue) || empty($this->inputValue) || !isset($this->inputValue[0]['image'])) {
            $this->inputValue = [$this->defaultValue()];
        }

        $this->registerClientScript();

        $input = Html::beginTag('div', ['class' => 'row media-input-wrap', 'id' => "{$this->id}-images"]);
        foreach ($this->inputValue as $key => $value) {
            $item = $this->genThumbItem($this->inputName, $value);
            $input .= str_replace('{offset}', $key, $item);
        }
        $input .= Html::endTag('div');

        return $input;
    }
}
