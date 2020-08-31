<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Backend controller
 */
class BackendController extends Controller
{
    use \common\traits\LanguageTrait;

    public $panelContent = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->initLanguage();
    }

    protected function initLanguage()
    {
        $this->setSiteLanguage('backendLanguage', 'backendLanguageList');
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'captcha'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['signup', 'captcha'],
                        'allow' => false,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function throwNotFound()
    {
        throw new NotFoundHttpException(Yii::t('app', 'The requested page or data does not exist'));
    }
}
