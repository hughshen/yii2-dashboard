<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\data\Pagination;
use common\components\Media;
use backend\models\UploadForm;

class MediaController extends BackendController
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

    public function actionManagerList()
    {
        list($list, $listCount) = Media::getFileList(Yii::$app->request->get('folder'));

        $pages = new Pagination(['totalCount' => $listCount, 'defaultPageSize' => 12]);
        $data = Media::getFileData($list, $pages->offset, $pages->limit);

        return $this->renderPartial('manager', [
            'list' => $data,
            'pages' => $pages,
        ]);
    }

    public function actionJsonUpload()
    {
        Yii::$app->response->getHeaders()->set('Vary', 'Accept');
        Yii::$app->response->format = Response::FORMAT_JSON;

        $json = [];
        $json['status'] = 0;
        $json['msg'] = Yii::t('app', 'Fail!');

        $image = Yii::$app->request->post('image');
        $filename = Yii::$app->request->post('filename');

        try {
            $res = Media::saveByData($image, $filename);
            if (is_string($res)) {
                $json['status'] = 1;
                $json['path'] = $res;
                $json['msg'] = Yii::t('app', 'Upload success');
            }
        } catch (\Exception $e) {
            $json['msg'] = $e->getMessage();
        }

        return $json;
    }

    public function actionJsonDelete()
    {
        Yii::$app->response->getHeaders()->set('Vary', 'Accept');
        Yii::$app->response->format = Response::FORMAT_JSON;

        $json = [];
        $json['status'] = 0;
        $json['msg'] = Yii::t('app', 'Error');

        $paths = Yii::$app->request->post('paths');
        $paths = is_array($paths) ? $paths : (is_string($paths) ? [$paths] : []);

        if (!empty($paths)) {
            foreach ($paths as $path) {
                Media::deleteFile($path);
            }
            $json['status'] = 1;
            $json['msg'] = Yii::t('app', 'Delete success');
        } else {
            $json['msg'] = Yii::t('app', 'Empty files');
        }

        return $json;
    }
}