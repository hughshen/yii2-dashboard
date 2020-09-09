<?php

namespace backend\modules\media\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\data\Pagination;
use backend\modules\media\models\UploadForm;

class ManagerController extends \backend\controllers\BackendController
{
    public function actionIndex($path = '', $search = '')
    {
        $model = new UploadForm();
        $model->setFileSystem($this->module->fs);

        if (Yii::$app->request->isPost) {
            try {
                $model->load(Yii::$app->request->post());
                $model->files = UploadedFile::getInstances($model, 'files');

                $path = $this->module->fs->normalizePath($model->path);
                if (!empty($path) && !$this->module->fs->has($path)) {
                    throw new \yii\base\Exception(Yii::t('app', 'Invalid path'));
                }

                $model->upload($this->module->fs);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Upload success'));
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->getReferrer());
        } else {
            $model->path = $path;
        }

        list($data, $pages) = $this->getDataByPath($path, $search);

        return $this->render('index', [
            'list' => array_splice($data, $pages->offset, $pages->limit),
            'pages' => $pages,
            'model' => $model,
        ]);
    }

    public function actionDelete()
    {
        if (Yii::$app->request->isPost) {
            $path = Yii::$app->request->get('path');
            $type = Yii::$app->request->get('type');

            try {
                if ($type === 'dir') {
                    $this->module->fs->deleteDir($path);
                } else {
                    $this->module->fs->delete($path);
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Delete success'));
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->getReferrer());
        }

        $this->throwNotFound();
    }

    public function actionCreate()
    {
        $path = Yii::$app->request->post('path');
        $folder = Yii::$app->request->post('folder');

        if (Yii::$app->request->isPost && !empty(trim($folder))) {
            try {
                $newDirPath = trim(trim($path, '/')) . '/' . trim(trim($folder, '/'));
                $created = $this->module->fs->createDir($newDirPath);
                if ($created === false) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Create folder failed'));
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Create folder success'));
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->redirect(['index', 'path' => $path]);
    }

    public function actionPopup($path = '')
    {
        list($data, $pages) = $this->getDataByPath($path);

        return $this->renderPartial('popup', [
            'list' => array_splice($data, $pages->offset, $pages->limit),
            'pages' => $pages,
        ]);
    }

    public function getDataByPath($path = '', $search = '')
    {
        $path = $this->module->fs->normalizePath($path);
        if (!empty($path) and !$this->module->fs->has($path)) {
            $data = [];
        } else {
            $data = $this->module->fs->listContents($path, false, $search);
        }

        $pages = new Pagination(['totalCount' => count($data)]);

        return [$data, $pages];
    }
}