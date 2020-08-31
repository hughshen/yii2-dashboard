<?php

namespace backend\modules\cms\controllers;

use Yii;
use backend\modules\cms\models\Category;
use backend\modules\cms\models\CategorySearch;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends \backend\controllers\BackendController
{
    use \common\traits\CrudControllerTrait;

    public $parent;

    public function init()
    {
        parent::init();
        $this->parent = Yii::$app->request->get('parent', 0);
    }

    protected function getSearchClassName()
    {
        return CategorySearch::class;
    }

    protected function getModelClassName()
    {
        return Category::class;
    }

    protected function initModel($model)
    {
        if ($model->isNewRecord) {
            $model->parent = $this->parent;
        }

        return $model;
    }

    protected function getQueryParams()
    {
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['CategorySearch']['parent'] = $this->parent;

        return $queryParams;
    }

    protected function defaultRedirect()
    {
        return $this->redirect(['index', 'parent' => $this->parent]);
    }
}