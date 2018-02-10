<?php

namespace common\traits;

use Yii;
use yii\helpers\ArrayHelper;

use common\models\Language;

trait LanguageTrait
{
    public $language = null;
    public $languageList = [];

    protected function setSiteLanguage($singleKey, $listKey)
    {
        $data = Language::allData();
        if ($data) {
            $localeList = ArrayHelper::getColumn($data, 'locale');
            $codeList = ArrayHelper::getColumn($data, 'code');

            $this->languageList = array_combine($localeList, $data);

            // Get current language code
            $currentCode = Yii::$app->request->get('lang');
            if ($currentCode && in_array($currentCode, $codeList)) {
                $langIndex = array_search($currentCode, $codeList);
                $this->language = $localeList[$langIndex];
            } else {
                $currentKey = Yii::$app->session->get($singleKey);
                if ($currentKey && in_array($currentKey, $localeList)) {
                    $this->language = $currentKey;
                } else {
                    $this->language = $localeList['0'];
                }
            }
        } else {
            $this->languageList = [];
            $this->language = Yii::$app->language;
        }

        Yii::$app->language = $this->language;
        Yii::$app->session->set($singleKey, $this->language);
        Yii::$app->session->set($listKey, $this->languageList);
    }

    public function buildSiteLocaleUrl($code)
    {
        $currentUrl = Yii::$app->request->getHostInfo() . Yii::$app->request->getUrl();
        $cleanUrl = preg_replace('/[&\?]lang=[a-zA-Z]+/', '', $currentUrl);
        if (strpos($cleanUrl, '?') !== false) {
            return $cleanUrl . '&lang=' . $code;
        } else {
            return $cleanUrl . '?lang=' . $code;
        }
    }
}
