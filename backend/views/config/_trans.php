<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\Config;

$langs = Yii::$app->session->get('backendLanguageList');

$fields = [];
$fields[] = [
    'label' => Yii::t('app', 'Site Title'),
    'field' => 'site_title',
];
$fields[] = [
    'label' => Yii::t('app', 'Site Keywords'),
    'field' => 'site_keywords',
];
$fields[] = [
    'label' => Yii::t('app', 'Site Description'),
    'field' => 'site_description',
    'textarea' => true,
];
$fields[] = [
    'label' => Yii::t('app', 'Site Copyright'),
    'field' => 'site_copyright',
];
$fields[] = [
    'label' => Yii::t('app', 'Site Tagline'),
    'field' => 'site_tagline',
];

$tabsData = [];
foreach ($langs as $langKey => $lang) {
    
    $tabContent = '';
    foreach ($fields as $val) {
        $name = $val['field'] . '_' . $lang['locale'];
        $value = isset($config[$name]) ? $config[$name] : (isset($config[$val['field']]) ? $config[$val['field']] : null);
        $name = $this->context->arrayName . '[' . $name . ']';
        $tabContent .= Html::beginTag('div', ['class' => 'form-group translate-group']);
        $tabContent .= Html::tag('label', $val['label'], ['class' => 'control-label']);
        if (isset($val['textarea']) && $val['textarea'] === true) {
            $tabContent .= Html::textarea($name, $value, ['class' => 'form-control', 'rows' => 3]);
        } else {
            $tabContent .= Html::textInput($name, $value, ['class' => 'form-control']);
        }
        $tabContent .= Html::endTag('div');
    }

    $tabsData[] = [
        'label' => $lang['title'],
        'content' => $tabContent,
        'active' => Yii::$app->language == $langKey,
    ];
}

?>
<?= Tabs::widget([
    'items' => $tabsData
]) ?>
