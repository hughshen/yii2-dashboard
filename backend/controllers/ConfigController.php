<?php

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;

use common\models\Config;

class ConfigController extends BackendController
{
    public $arrayName;

    public function init()
    {
        parent::init();
        $this->arrayName = 'Config';
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'config' => Config::allData()
        ]);
    }

    public function actionFlushCache()
    {
        Yii::$app->cache->flush();
        if (Yii::$app->cache instanceof \yii\caching\FileCache) {
            Yii::$app->fileCacheFrontend->flush();
        }

        Yii::$app->session->setFlash('success', 'Deletes all values from cache.');

        return $this->redirect(['index']);
    }

    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {

            $exists = Config::allData();
            $updated = false;

            foreach (Yii::$app->request->post($this->arrayName, []) as $name => $value) {
                if (isset($exists[$name])) {
                    if ((string)$exists[$name] !== (string)$value) {
                        $model = $this->findModel($name);
                        $model->option_value = $value;
                        $model->save();
                        $updated = true;
                    }
                } else {
                    $model = new Config();
                    $model->option_name = $name;
                    $model->option_value = $value;
                    $model->autoload = 1;
                    $model->save();
                    $updated = true;
                }
            }
            
            if ($updated) {
                Config::flushCache();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Site config update success.'));
            } else {
                Yii::$app->session->setFlash('info', Yii::t('app', 'Nothing changed.'));
            }
        }

        return $this->redirect(['index']);      
    }

    protected function findModel($option_name)
    {
        if (($model = Config::findOne(['option_name' => $option_name])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * SMTP test
     */
    public function actionSmtpTest()
    {
        $data = [];
        $data['status'] = 0;
        $data['msg'] = '';

        if (Yii::$app->request->isPost) {
            $to = Yii::$app->request->post('to');

            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $data['msg'] = Yii::t('app', 'Invalid email address');
            }

            if (empty($data['msg'])) {
                $mailer = new \common\components\Mailer;
                $mailer->to = $to;
                $mailer->subject = 'Test Subject';
                $mailer->body = '<b>Test</b> Message';
                $result = $mailer->send();

                if (is_bool($result) && $result === true) {
                    $data['status'] = 1;
                    $data['msg'] = Yii::t('app', 'Test email has been sent, please check your mailbox.');
                } elseif (is_string($result)) {
                    $data['msg'] = $result;
                } else {
                    $data['msg'] = Yii::t('app', 'Message could not be sent.');
                }
            }
        }

        return $this->asJson($data);
    }
}
