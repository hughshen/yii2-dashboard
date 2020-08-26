<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use common\models\Language;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $menuItems = [
        ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']],
        [
            'label' => Yii::t('app', 'Site'),
            'items' => [
                ['label' => Yii::t('app', 'User'), 'url' => ['/user/index']],
                ['label' => Yii::t('app', 'Manager'), 'url' => ['/manager/index']],
                ['label' => Yii::t('app', 'Config'), 'url' => ['/config/index']],
                ['label' => Yii::t('app', 'Language'), 'url' => ['/language/index']],
                ['label' => Yii::t('app', 'Media'), 'url' => ['/media/index']],
            ],
        ],
        [
            'label' => Yii::t('app', 'CMS'),
            'items' => [
                ['label' => Yii::t('app', 'Post'), 'url' => ['/cms/post/index']],
                ['label' => Yii::t('app', 'Page'), 'url' => ['/cms/page/index']],
                ['label' => Yii::t('app', 'Tag'), 'url' => ['/cms/tag/index']],
                ['label' => Yii::t('app', 'Category'), 'url' => ['/cms/category/index']],
                ['label' => Yii::t('app', 'Menu'), 'url' => ['/cms/menu/index']],
            ],
        ],
        [
            'label' => Yii::t('app', 'Shop'),
            'items' => [
                ['label' => Yii::t('app', 'Product'), 'url' => ['/shop/product/index']],
                ['label' => Yii::t('app', 'Category'), 'url' => ['/shop/category/index']],
            ],
        ],
    ];

    // Language switcher
    $languageMenu = [];
    $languageMenu['label'] = Yii::t('app', 'Language');
    foreach (Yii::$app->controller->languageList as $langKey => $lang) {
        $languageMenu['items'][] = [
            'label' => $lang['title'],
            'url' => Yii::$app->controller->buildSiteLocaleUrl($lang['code']),
            'active' => Yii::$app->language == $langKey,
        ];
    }
    $menuItems[] = $languageMenu;

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                Yii::t('app', 'Logout ({name})', ['name' => Yii::$app->user->identity->username]),
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>
<?php
$this->registerJs("
;(function($) {
$('.copy-text').on('click', function() {
    try {
        var text = $(this).attr('data-text');
        var inputNode = document.createElement('input');
        inputNode.style = 'position: absolute; left: -10000px; top: -10000px;';
        inputNode.value = text;
        document.body.appendChild(inputNode);

        inputNode.select();
        var res = document.execCommand('copy');
        var msg = res ? 'successful' : 'unsuccessful';
        alert('Copying text command was ' + msg);
        document.body.removeChild(inputNode);
    } catch (e) {
        console.log('Oops, unable to copy');
    }
})
})(jQuery);
", \yii\web\View::POS_END);
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
