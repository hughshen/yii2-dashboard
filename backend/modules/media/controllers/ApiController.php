<?php

namespace backend\modules\media\controllers;

use Yii;
use yii\web\Response;

class ApiController extends \backend\controllers\BackendController
{
    public function actionUpload()
    {
        Yii::$app->response->getHeaders()->set('Vary', 'Accept');
        Yii::$app->response->format = Response::FORMAT_JSON;

        $json = [];
        $json['status'] = 0;
        $json['msg'] = Yii::t('app', 'Fail!');

        $image = Yii::$app->request->post('image');
        $filename = Yii::$app->request->post('filename');

        try {
            $res = $this->module->fs->saveBase64Data($image, $filename);
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

    public function actionDelete()
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
                $this->module->fs->delete($path);
            }
            $json['status'] = 1;
            $json['msg'] = Yii::t('app', 'Delete success');
        } else {
            $json['msg'] = Yii::t('app', 'Empty files');
        }

        return $json;
    }
}