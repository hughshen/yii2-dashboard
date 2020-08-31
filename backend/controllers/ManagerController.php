<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use backend\models\ManagerSearch;
use backend\models\PasswordForm;

/**
 * ManagerController implements the CRUD actions for User model.
 */
class ManagerController extends BackendController
{
    use \common\traits\CrudControllerTrait;

    protected function getSearchClassName()
    {
        return ManagerSearch::class;
    }

    protected function getModelClassName()
    {
        return User::class;
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

    public function actionPassword()
    {
        $user = $this->findModel(Yii::$app->user->id);
        $model = new PasswordForm();
        $model->setUser($user);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            try {
                $model->changePassword();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Change password success'));
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            return $this->redirect(['password']);
        }

        return $this->render('password', ['model' => $model]);
    }
}