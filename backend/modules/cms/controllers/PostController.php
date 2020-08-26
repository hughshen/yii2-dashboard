<?php

namespace backend\modules\cms\controllers;

use Yii;
use backend\modules\cms\models\Post;
use backend\modules\cms\models\search\PostSearch;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends \backend\controllers\BackendController
{
    use \common\traits\CrudControllerTrait;

    protected function getSearchClassName()
    {
        return PostSearch::className();
    }

    protected function getModelClassName()
    {
        return Post::className();
    }
}
