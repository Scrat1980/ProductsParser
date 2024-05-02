<?php

namespace app\controllers;

use PhpMimeMailParser\Parser;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
//                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionParser()
    {
        $path = '../../asos.eml';
        $parser = new Parser();
        $parser->setStream(fopen($path, "r"));
        $body = $parser->getMessageBody('htmlEmbedded');
        $matches = [];
        preg_match_all('/<img[^>]+products[^>]+alt=\"[^>]+\"[^>]*>((?!\/tbody).)*/', $body,$matches);


//        echo "<pre>";
//        var_dump($matches[0]);
//        echo "</pre>";
//        die;

        return $this->render(
            'parser'
            ,
            [
                'content' =>
                    $body
//                    count($matches)
            ]
        );
    }
}
