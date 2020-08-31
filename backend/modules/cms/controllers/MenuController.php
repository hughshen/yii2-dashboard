<?php

namespace backend\modules\cms\controllers;

use Yii;
use backend\modules\cms\models\Menu;
use backend\modules\cms\models\MenuSearch;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends \backend\controllers\BackendController
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
        return MenuSearch::class;
    }

    protected function getModelClassName()
    {
        return Menu::class;
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
        $queryParams = Yii::$app->request->getQueryParams();
        $queryParams['MenuSearch']['parent'] = $this->parent;

        return $queryParams;
    }

    protected function defaultRedirect()
    {
        return $this->redirect(['index', 'parent' => $this->parent]);
    }

    public function actionTypeList()
    {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $type = Yii::$app->request->post('type');

            return $this->asJson(Menu::menuIdList($type));
        }

        return $this->asJson([]);
    }
}