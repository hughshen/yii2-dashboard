<?php

namespace backend\modules\media\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\data\Pagination;
use backend\modules\media\components\Media;
use backend\modules\media\models\UploadForm;

class ManagerController extends \backend\controllers\BackendController
{
    public function actionIndex()
    {
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {

            $model->files = UploadedFile::getInstances($model, 'files');

            if ($model->validate()) {
                try {
                    Media::saveFiles($model->files, Yii::$app->request->get('folder'));

                    Yii::$app->session->setFlash('success', Yii::t('app', 'Upload success'));
                    return $this->redirect(['index', 'folder' => Yii::$app->request->get('folder')]);
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            } else if (isset($model->errors['files'])) {
                Yii::$app->session->setFlash('error', $model->errors['files'][0]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Upload failed'));
            }
        }

        list($list, $listCount) = Media::getFileList(Yii::$app->request->get('folder'), Yii::$app->request->get('search'));

        $pages = new Pagination(['totalCount' => $listCount]);
        $data = Media::getFileData($list, $pages->offset, $pages->limit);

        return $this->render('index', [
            'list' => $data,
            'pages' => $pages,
            'model' => $model,
        ]);
    }

    public function actionDelete()
    {
        if (Yii::$app->request->isPost) {
            Media::deletePath(Yii::$app->request->get('path'), Yii::$app->request->get('type'));
            Yii::$app->session->setFlash('success', Yii::t('app', 'Delete success'));

            return $this->redirect(Yii::$app->request->getReferrer());
        }

        $this->throwNotFound();
    }

    public function actionCreateFolder()
    {
        $folder = Yii::$app->request->get('folder');
        $create = Yii::$app->request->get('create');

        if ($create) {
            try {
                $new = $folder . '/' . $create;
                $created = Media::createFolder($new);
                if ($created === true) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Create folder success'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Create folder failed'));
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->redirect(['index', 'folder' => $folder]);
    }

    public function actionPopup()
    {
        list($list, $listCount) = Media::getFileList(Yii::$app->request->get('folder'));

        $pages = new Pagination(['totalCount' => $listCount, 'defaultPageSize' => 12]);
        $data = Media::getFileData($list, $pages->offset, $pages->limit);

        return $this->renderPartial('popup', [
            'list' => $data,
            'pages' => $pages,
        ]);
    }
}