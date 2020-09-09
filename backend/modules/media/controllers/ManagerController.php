<?php

namespace backend\modules\media\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\data\Pagination;
use backend\modules\media\models\UploadForm;
use backend\modules\media\models\CreateForm;

class ManagerController extends \backend\controllers\BackendController
{
    public function actionIndex($path = '', $search = '')
    {
        $uploadModel = new UploadForm();
        $uploadModel->setFileSystem($this->module->fs);
        $uploadModel->path = $path;

        $createModel = new CreateForm();
        $createModel->setFileSystem($this->module->fs);
        $createModel->path = $path;

        list($data, $pages) = $this->getDataByPath($path, $search);

        return $this->render('index', [
            'list' => array_splice($data, $pages->offset, $pages->limit),
            'pages' => $pages,
            'uploadModel' => $uploadModel,
            'createModel' => $createModel,
        ]);
    }

    public function actionUpload()
    {
        $model = new UploadForm();
        $model->setFileSystem($this->module->fs);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            try {
                $model->files = UploadedFile::getInstances($model, 'files');
                $model->upload();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Upload success'));
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->getReferrer());
        }

        return $this->throwNotFound();
    }

    public function actionCreate()
    {
        $model = new CreateForm();
        $model->setFileSystem($this->module->fs);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            try {
                $model->create();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Create folder success'));
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->getReferrer());
        }

        return $this->throwNotFound();
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

        usort($data, function ($a, $b) {
            $a = ($a['type'] === 'dir' ? '00000' : '') . $a['basename'];
            $b = ($b['type'] === 'dir' ? '00000' : '') . $b['basename'];
            return strnatcmp($a, $b);
        });

        $pages = new Pagination(['totalCount' => count($data)]);

        return [$data, $pages];
    }
}