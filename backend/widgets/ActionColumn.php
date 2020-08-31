<?php

namespace backend\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

class ActionColumn extends \yii\grid\ActionColumn
{
    public $hasParent = false;

    public $buttonClass = 'btn btn-sm';

    protected function initDefaultButtons()
    {
        $this->myInitDefaultButton('view', 'eye-open', 'primary');
        $this->myInitDefaultButton('update', 'pencil', 'primary');
        $this->myInitDefaultButton('children', 'list', 'primary');
        $this->myInitDefaultButton('delete', 'trash', 'danger', [
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ]);
    }

    protected function myInitDefaultButton($name, $iconName, $buttonType, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $buttonType, $additionalOptions) {
                $urlArr = null;
                switch ($name) {
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        $urlArr = ['view', 'id' => $model->id];
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        $urlArr = ['update', 'id' => $model->id];
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        $urlArr = ['delete', 'id' => $model->id];
                        break;
                    case 'children':
                        $title = Yii::t('app', 'Children');
                        $urlArr = ['index'];
                        break;
                    default:
                        $title = ucfirst($name);
                }

                // url
                if ($urlArr !== null && $this->hasParent === true) {
                    $urlArr['parent'] = $name == 'children' ? $model->id : $model->parent;
                    $url = Url::to($urlArr);
                }

                $options = array_merge([
                    'title' => $title,
                    'class' => "btn btn-sm btn-$buttonType",
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
                return Html::a($icon, $url, $options);
            };
        }
    }
}
