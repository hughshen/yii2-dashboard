<?php
namespace common\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;

class TranslateInput extends \yii\bootstrap\Widget
{
    public $form;
    public $model;
    public $attributes;

    public function run()
    {
        $langs = Yii::$app->session->get('backendLanguageList');
        if ($this->form && $this->model && $this->attributes && $langs) {
            $tabItems = [];

            foreach ($langs as $langKey => $lang) {
                $tabLabel = $lang['title'];
                $tabContent = $this->getTabContent($langKey);
                $tabItems[] = [
                    'label' => $tabLabel,
                    'content' => $tabContent,
                    'active' => Yii::$app->language == $langKey,
                ];
            }

            if ($tabItems) {
                return Tabs::widget([
                   'items' => $tabItems,
                ]);
            }
        }

        return null;
    }

    protected function getTabContent($langKey)
    {
        if (Yii::$app->request->post('Translate')) {
            $translate = Yii::$app->request->post('Translate');
            $translate = isset($translate[$langKey]) ? $translate[$langKey] : [];
        } else {
            $data = $this->model->getTranslate()->andWhere(['language' => $langKey])->asArray()->all();
            $translate = [];
            foreach ($data as $val) {
                $translate[$val['table_field']] = $val;
            }
        }

        $content = '';
        foreach ($this->attributes as $attr => $attrOption) {
            $inputName = "Translate[{$langKey}][{$attr}]";
            $inputId = "{$attr}-{$langKey}";
            if (isset($translate[$attr])) {
                if (is_array($translate[$attr])) {
                    $inputValue = $translate[$attr]['field_value'];
                } else {
                    $inputValue = $translate[$attr];
                }
            } else {
                $inputValue = '';
            }

            if (isset($attrOption['type'])) {
                switch ($attrOption['type']) {
                    case 'editor':
                        $content .= '<div class="form-group field-' . $inputId . '">
                        ' . Html::activeLabel($this->model, $attr, ['for' => $inputId, 'class' => 'control-label']) . '
                        ' . \common\widgets\tinymce\TinyMCEEditor::widget([
                                'name' => $inputName,
                                'value' => $inputValue,
                                'id' => $inputId,
                        ]) . '
                        <div class="help-block"></div>
                        </div>';
                        break;
                    case 'textarea':
                        $content .= $this->form->field($this->model, $attr)->textarea(['name' => $inputName, 'value' => $inputValue, 'id' => $inputId, 'rows' => 3]);
                        break;
                    default:
                        $content .= $this->form->field($this->model, $attr)->textInput(['name' => $inputName, 'value' => $inputValue, 'id' => $inputId]);
                        break;
                }
            }
        }

        return $content;
    }
}
