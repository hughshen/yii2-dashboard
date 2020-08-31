<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use backend\models\UserSearch;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BackendController
{
    use \common\traits\CrudControllerTrait;

    protected function getSearchClassName()
    {
        return UserSearch::className();
    }

    protected function getModelClassName()
    {
        return User::className();
    }

    protected function initModel($model)
    {
        if ($model->isNewRecord) {
            $model->setScenario('backend_create');
            $model->role = User::ROLE_USER;
            $model->role_group = User::GROUP_FRONTEND;
        }

        return $model;
    }
}
