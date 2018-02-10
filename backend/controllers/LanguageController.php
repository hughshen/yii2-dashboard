<?php

namespace backend\controllers;

use Yii;

use common\models\Language;
use backend\models\search\LanguageSearch;

/**
 * LanguageController implements the CRUD actions for Language model.
 */
class LanguageController extends BackendController
{
    use \common\traits\CrudControllerTrait;

    protected function getSearchClassName()
    {
        return LanguageSearch::className();
    }

    protected function getModelClassName()
    {
        return Language::className();
    }
}
