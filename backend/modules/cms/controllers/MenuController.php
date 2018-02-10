<?php

namespace backend\modules\cms\controllers;

use Yii;
use yii\web\NotFoundHttpException;

use backend\modules\cms\models\Menu;
use backend\modules\cms\models\search\MenuSearch;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends \backend\controllers\BackendController
{
    public $parent;

    public function init()
    {
        parent::init();
        $this->parent = Yii::$app->request->get('parent', 0);
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MenuSearch();

        $queryParams = Yii::$app->request->queryParams;
        $queryParams['MenuSearch']['parent'] = $this->parent;
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Menu model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Menu();

        if (Yii::$app->request->isPost && $this->saveModel($model)) {
            return $this->redirect(['index', 'parent' => $this->parent]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost && $this->saveModel($model)) {
            return $this->redirect(['index', 'parent' => $this->parent]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->deleteModel($id);

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

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Save model
     */
    protected function saveModel($model)
    {
        $model->load(Yii::$app->request->post());

        if ($model->isNewRecord) {
            $model->parent = $this->parent;
        }

        return $model->saveModel();
    }

    /**
     * Delete model
     */
    protected function deleteModel($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->findModel($id);
            $model->updateChildren();
            $model->deleteModel();
            
            $transaction->commit();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
