<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AppAsset;

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
<body class="nav-<?= !empty($_COOKIE['menuIsCollapsed']) && $_COOKIE['menuIsCollapsed'] == 'true' ? 'sm' : 'md' ?>">
<?php $this->beginBody() ?>
<div class="container body">

    <div class="main_container">

        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">

                <div class="navbar nav_title" style="border: 0;">
                    <a href="<?= Yii::$app->homeUrl ?>" class="site_title">
                        <i class="fa fa-paw"></i>&nbsp;<span><?= Yii::$app->name ?></span>
                    </a>
                </div>
                <div class="clearfix"></div>

                <!-- menu prile quick info -->
                <div class="profile clearfix">
                    <div class="profile_info">
                        <span><?= Yii::t('app', 'Welcome') ?>, <br><?= Yii::$app->user->identity->username ?></span>
                    </div>
                </div>
                <!-- /menu prile quick info -->

                <br/>

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <?php
                        $menuItems = [
                            ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']],
                            ['label' => Yii::t('app', 'Config'), 'url' => ['/config/index']],
                            ['label' => Yii::t('app', 'User'), 'url' => ['/user/index']],
                            ['label' => Yii::t('app', 'Manager'), 'url' => ['/manager/index']],
                            ['label' => Yii::t('app', 'Language'), 'url' => ['/language/index']],
                            ['label' => Yii::t('app', 'Media'), 'url' => ['/media/index']],
                            [
                                'label' => Yii::t('app', 'CMS'),
                                'url' => '#',
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
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('app', 'Product'), 'url' => ['/shop/product/index']],
                                    ['label' => Yii::t('app', 'Category'), 'url' => ['/shop/category/index']],
                                ],
                            ],
                        ];

                        echo \backend\widgets\Menu::widget(['items' => $menuItems, 'activateInController' => true]);
                        ?>
                    </div>
                </div>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <div class="sidebar-footer hidden-small">
                    <a data-toggle="tooltip" data-placement="top" title="Settings">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>
                    <a href="<?= Url::to(['/site/logout']) ?>" data-method="post" data-toggle="tooltip"
                       data-placement="top" title="<?= Yii::t('app', 'Logout') ?>">
                        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                    </a>
                </div>
                <!-- /menu footer buttons -->
            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">

            <div class="nav_menu">
                <nav class="" role="navigation">
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                               aria-expanded="false">
                                <?= Yii::$app->user->identity->username ?>
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <?php foreach (Yii::$app->controller->languageList as $langKey => $lang): ?>
                                    <li>
                                        <a href="<?= Yii::$app->controller->buildSiteLocaleUrl($lang['code']) ?>">
                                            <span><?= $lang['title'] ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                                <li>
                                    <?= Html::a(Yii::t('app', 'Change password'), ['/manager/password']) ?>
                                </li>
                                <li>
                                    <a href="<?= Url::to(['/site/logout']) ?>" data-method="post">
                                        <i class="fa fa-sign-out pull-right"></i>
                                        <?= Yii::t('app', 'Logout') ?>
                                    </a>
                                </li>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </div>

        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">

            <div class="clearfix"></div>

            <?= \yiister\gentelella\widgets\FlashAlert::widget() ?>

            <div class="clearfix"></div>

            <?php
            if (Yii::$app->controller->panelContent) {
                \yiister\gentelella\widgets\Panel::begin(['header' => $this->title]);
                echo $content;
                \yiister\gentelella\widgets\Panel::end();
            } else {
                echo $content;
            }
            ?>

        </div>
        <!-- /page content -->
    </div>
</div>
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
