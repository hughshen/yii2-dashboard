<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use backend\models\search\ManagerSearch;

/**
 * ManagerController implements the CRUD actions for User model.
 */
class ManagerController extends BackendController
{
    use \common\traits\CrudControllerTrait;

    protected function getSearchClassName()
    {
        return ManagerSearch::className();
    }

    protected function getModelClassName()
    {
        return User::className();
    }

    protected function initModel($model)
    {
        if ($model->isNewRecord) {
            $model->setScenario('backend_create');
            $model->role = User::ROLE_MANAGER;
            $model->role_group = User::GROUP_BACKEND;
        }

        return $model;
    }
}
