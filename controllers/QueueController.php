<?php

namespace app\controllers;

use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class QueueController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        echo 'Welcome to Queue';
    }

    public function actionQueue()
    {
        $redis = new \Predis\Client(\Yii::$app->params['redis']);
        $email = Yii::$app->request->get('email');
        if($email){
            $redis->rpush('sendEmail', $email);
        }
    }

    public function sendEmail($email)
    {

    }
}
