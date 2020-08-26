<?php

namespace backend\modules\cms\controllers;

use Yii;
use backend\modules\cms\models\Page;
use backend\modules\cms\models\search\PageSearch;

/**
 * PageController implements the CRUD actions for Page model.
 */
class PageController extends \backend\controllers\BackendController
{
    use \common\traits\CrudControllerTrait;

    protected function getSearchClassName()
    {
        return PageSearch::className();
    }

    protected function getModelClassName()
    {
        return Page::className();
    }
}
