<?php

namespace backend\modules\shop\controllers;

use Yii;
use yii\web\NotFoundHttpException;

use backend\modules\shop\models\Category;
use backend\modules\shop\models\search\CategorySearch;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends \backend\controllers\BackendController
{
    public $parent;

    public function init()
    {
        parent::init();
        $this->parent = Yii::$app->request->get('parent', 0);
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();

        $queryParams = Yii::$app->request->queryParams;
        $queryParams['CategorySearch']['parent'] = $this->parent;
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if (Yii::$app->request->isPost && $this->saveModel($model)) {
            return $this->redirect(['index', 'parent' => $this->parent]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Category model.
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
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->deleteModel($id);

        return $this->redirect(['index', 'parent' => $this->parent]);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
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
