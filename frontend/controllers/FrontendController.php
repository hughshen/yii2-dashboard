<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Config;
use frontend\models\Menu;

/**
 * Frontend controller
 */
class FrontendController extends Controller
{
    use \common\traits\LanguageTrait;

    public $config = [];

    public $menu = [];

    public $menus = [];

    public $pages = [];

    public $seo = [];

    public function init()
    {
        parent::init();
        $this->initConfig();
        $this->initLanguage();
        $this->initMenus();
        $this->initPages();
        $this->initSeo();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge($this->baseActions(), $this->addedActions());
    }

    public function baseActions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function addedActions()
    {
        return [];
    }

    protected function initConfig()
    {
        $this->config = Config::allData();
    }

    protected function initLanguage()
    {
        $this->setSiteLanguage('frontendLanguage', 'frontendLanguageList');
    }

    protected function initSeo()
    {
        $this->seo['title'] = $this->configByName('site_title', true);
        $this->seo['keywords'] = $this->configByName('site_keywords', true);
        $this->seo['description'] = $this->configByName('site_description', true);

        // No index?
        if (
            (int)$this->configByName('site_noindex') === 1 &&
            stripos(Yii::$app->request->getHostName(), $this->configByName('site_domain')) === false
        ) {
            $this->seo['robots'] = 'noindex, nofollow';
        }
    }

    public function render($view, $params = [])
    {
        $this->registerSeo();

        $content = $this->getView()->render($view, $params, $this);
        return $this->renderContent($content);
    }

    protected function initMenus()
    {
        $this->menu = $this->getMenu(Yii::$app->request->get('mid'));
        $this->menus = Menu::frontendList();
    }

    protected function initPages()
    {

    }

    protected function getMenu($mid)
    {
        $menu = Menu::byId($mid);
        $menu = Menu::buildData($menu);

        return $menu;
    }

    public function groupMenus($group)
    {
        return Menu::groupList($group, $this->menus);
    }

    public function configByName($name, $translated = false)
    {
        if ($translated === true) {
            $name .= '_' . Yii::$app->language;
        }

        if (isset($this->config[$name])) {
            return $this->config[$name];
        } else {
            return null;
        }
    }

    protected function registerSeo()
    {
        foreach ($this->seo as $key => $val) {
            if (!$val) continue;
            if ($key === 'title') {
                $this->getView()->title = $val;
            } elseif (!is_numeric($key)) {
                $this->getView()->registerMetaTag(['name' => $key, 'content' => $val]);
            } else {
                $this->getView()->registerMetaTag($val);
            }
        }
    }

    public function noIndexMetaTag()
    {
        $this->view->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
    }

    public function isHome()
    {
        return (Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id == 'index');
    }

    public static function currentUrl()
    {
        return Yii::$app->request->getHostInfo() . Yii::$app->request->getUrl();
    }

    public static function showImage($url = '', $withTag = true, $tagOptions = [], $randomInt = false)
    {
        if ($url && stripos($url, 'http') !== 0) {
            $url = Yii::$app->request->getHostInfo() . '/' . trim($url, '/');
        }

        if ($randomInt) {
            $url .= '?v=' . mt_rand(1100, 9999);
        }

        if ($withTag) {
            return Html::img($url, $tagOptions);
        }

        return $url;
    }
}
