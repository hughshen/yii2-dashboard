<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\data\Pagination;
use common\models\Media;
use backend\models\UploadForm;

class MediaController extends BackendController
{
    public function actionIndex()
    {
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {

            $model->files = UploadedFile::getInstances($model, 'files');

            if ($model->validate()) {
                Media::saveFiles($model->files, Yii::$app->request->get('folder'));
                Yii::$app->session->setFlash('success', Yii::t('app', 'Upload success.'));

                return $this->redirect(['index', 'folder' => Yii::$app->request->get('folder')]);
            }
        }

        list($list, $listCount) = Media::getList(Yii::$app->request->get('folder'), Yii::$app->request->get('search'));

        $pages = new Pagination(['totalCount' => $listCount]);
        $data = Media::getListData($list, $pages->offset, $pages->limit);

        return $this->render('index', [
            'list' => $data,
            'pages' => $pages,
            'model' => $model,
        ]);
    }

    public function actionDelete()
    {
        if (Yii::$app->request->isPost) {
            Media::deletePath(Yii::$app->request->get('path'));
            Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully.'));

            return $this->redirect(Yii::$app->request->getReferrer());
        }

        $this->throwNotFound();
    }

    public function actionCreateFolder()
    {
        $folder = Yii::$app->request->get('folder');
        $create = Yii::$app->request->get('create');

        if (!$create) return $this->redirect(['index']);

        $new = $folder . '/' . $create;

        if (Media::createFolder($new)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Create folder successfully.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Create folder failed.'));
        }

        $this->redirect(['index', 'folder' => $folder]);
    }

    public function actionManagerList()
    {
        list($list, $listCount) = Media::getList(Yii::$app->request->get('folder'));

        $pages = new Pagination(['totalCount' => $listCount, 'defaultPageSize' => 12]);
        $data = Media::getListData($list, $pages->offset, $pages->limit);

        return $this->renderPartial('manager-list', [
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

        $res = Media::jsonUpload($image, $filename);

        if (is_array($res)) {
            $json['status'] = 1;
            $json['msg'] = $res['msg'];
            $json['path'] = $res['path'];
        } elseif (is_string($res)) {
            $json['msg'] = $res;
        }

        return $json;
    }

    public function actionJsonDelete()
    {
        Yii::$app->response->getHeaders()->set('Vary', 'Accept');
        Yii::$app->response->format = Response::FORMAT_JSON;

        $json = [];
        $json['status'] = 0;
        $json['msg'] = Yii::t('app', 'Fail!');

        $paths = Yii::$app->request->post('paths');
        $paths = is_array($paths) ? $paths : (is_string($paths) ? [$paths] : []);

        if (!empty($paths)) {
            foreach ($paths as $path) {
                $res = Media::deleteFile($path);
            }
            $json['status'] = 1;
            $json['msg'] = Yii::t('app', 'Deleted successfully');
        } else {
            $json['msg'] = Yii::t('app', 'Please select files');
        }

        return $json;
    }
}
