<?php

namespace backend\modules\cms\controllers;

use Yii;
use backend\modules\cms\models\Tag;
use backend\modules\cms\models\search\TagSearch;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends \backend\controllers\BackendController
{
    use \common\traits\CrudControllerTrait;

    protected function getSearchClassName()
    {
        return TagSearch::className();
    }

    protected function getModelClassName()
    {
        return Tag::className();
    }
}
