<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

use common\models\Config;
use common\models\Page;
use backend\modules\cms\models\Menu;

/**
 * Frontend controller
 */
class FrontendController extends Controller
{
    use \common\traits\LanguageTrait;

    public $menu = [];
    public $menus = [];
    public $pages = [];
    public $config = [];

    public $seo = [];

    public function init()
    {
        parent::init();

        $this->initLanguage();
        $this->initMenus();
        $this->initPages();
        $this->initConfig();
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

    protected function initLanguage()
    {
        $this->setSiteLanguage('frontendLanguage', 'frontendLanguageList');
    }

    protected function initConfig()
    {
        $this->config = Config::allData();
    }

    protected function initSeo()
    {
        $this->seo['title'] = $this->configByName('site_title_' . Yii::$app->language);
        $this->seo['keywords'] = $this->configByName('site_keywords_' . Yii::$app->language);
        $this->seo['description'] = $this->configByName('site_description_' . Yii::$app->language);

        // No index ?
        if ((int)$this->configByName('site_noindex') === 1 && stripos(Yii::$app->request->getHostName(), $this->configByName('site_domain')) === false) {
            $this->seo['robots'] = 'noindex, nofollow';
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
        return Menu::byId($mid);
    }

    public function configByName($name)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        } else {
            return null;
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

    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function shuffleData($data, $limit = 2)
    {
        if (!is_array($data) || empty($data)) return [];

        shuffle($data);
        $newData = [];
        $offset = 0;
        foreach ($data as $val) {
            if ($offset >= $limit) break;
            $newData[$offset] = $val;
            $offset++;
        }

        return $newData;
    }

    public static function wordSplit($string, $length = null)
    {
        if ((int)$length > 0) {
            $length = (int)$length;
            
            if (function_exists('mb_strlen')) {
                $l = mb_strlen($string, 'utf-8');
            } else {
                $l = iconv_strlen($string, 'utf-8');
            }
            
            if ($l > $length) {
                if (function_exists('mb_substr')) {
                    $string = mb_substr($string, 0, $length);
                } else {
                    $string = iconv_substr($string, 0, $length);
                }
            }
        }

        return $string;
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
