<?php

namespace backend\modules\media\controllers;

use Yii;
use yii\web\Response;
use yii\web\UploadedFile;
use backend\modules\media\models\UploadForm;

class ApiController extends \backend\controllers\BackendController
{
    public $json;

    public function init()
    {
        parent::init();

        $this->json = [
            'status' => 0,
            'message' => '',
        ];

        Yii::$app->response->getHeaders()->set('Vary', 'Accept');
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionUpload()
    {
        if (!Yii::$app->request->isPost) {
            $this->json['message'] = Yii::t('app', 'Invalid http method');
            return $this->json;
        }

        $model = new UploadForm();
        $model->setFileSystem($this->module->fs);

        try {
            $model->path = Yii::$app->request->post('path', '');
            $model->files = UploadedFile::getInstances($model, 'files');
            $paths = $model->upload();

            $this->json['status'] = 1;
            $this->json['message'] = Yii::t('app', 'Upload success');
            $this->json['paths'] = $paths;
        } catch (\Exception $e) {
            $this->json['message'] = $e->getMessage();
        }

        return $this->json;
    }

    public function actionDelete()
    {
        $paths = Yii::$app->request->post('paths');
        $paths = is_array($paths) ? $paths : (is_string($paths) ? [$paths] : []);

        if (!empty($paths)) {
            foreach ($paths as $path) {
                $this->module->fs->delete($path);
            }

            $this->json['status'] = 1;
            $this->json['message'] = Yii::t('app', 'Delete success');
        } else {
            $this->json['message'] = Yii::t('app', 'Empty files');
        }

        return $this->json;
    }
}
