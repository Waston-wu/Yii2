<?php

namespace app\controllers;

use app\models\YUser;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionRegister()
    {
        return $this->render('register');
    }

    /**
     * 发送邮箱验证码
     * @date 2017-12-03
     * @author wusong
     */
    public function actionSend_mail_code()
    {
        if(!(Yii::$app->request->isPost))
        {
            return \yii\helpers\Json::encode(array('code'=>1,'message'=>'非法请求','date'=>array()));die;
        }
        $param = Yii::$app->request->post();
        if(!isset($param['email']) || !$param['email'])
        {
            return \yii\helpers\Json::encode( array('code'=>2,'message'=>'非法请求','data'=>array()));die;
        }
        // 发送邮箱验证码
        $mail = Yii::$app->helper->mail_code_send($param['email']);
        if($mail)
            return \yii\helpers\Json::encode( array('code'=>0,'message'=>'发送成功','data'=>array('down_time'=> Yii::$app->params['mail_code_down'])));
        else
            return \yii\helpers\Json::encode( array('code'=>3,'message'=>'发送失败','data'=>$mail));
    }

    /**
     * 注册信息的保存
     * @date 2017-12-04
     * @author wusong
     */
    public function actionRegister_save()
    {
        if(!(Yii::$app->request->isPost))
        {
            return \yii\helpers\Json::encode(array('code'=>1,'message'=>'非法请求','date'=>array()));die;
        }
        $param = Yii::$app->request->post();
        // 验证参数必填
        if(Yii::$app->helper->param_validate($param,['email','code','password','luotest_response']))
        {
            return \yii\helpers\Json::encode(array('code'=>2,'message'=>'入参非法','data'=>[]));
        }
        // 验证人机验证的验证码
        $luosimao_validate = \yii\helpers\Json::decode(Yii::$app->helper->luosimao_validate($param['luotest_response']));
        if($luosimao_validate['code'] != 0)return \yii\helpers\Json::encode($luosimao_validate);
        // 验证邮箱验证码是否正确
        $mail_code_validate = \yii\helpers\Json::decode(Yii::$app->helper->mail_code_validate($param['email'],$param['code']));
        if($mail_code_validate['code'] != 0)return \yii\helpers\Json::encode($mail_code_validate);
        // 保存信息到数据库信息
        $model = new YUser();
        $model->email = $param['email'];
        $model->password = md5($param['password']);
        try{
            $res = $model->save();
            if($res)
                return \yii\helpers\Json::encode(array('code'=>0,'message'=>'注册成功','data'=>[]));
            else
                return \yii\helpers\Json::encode(array('code'=>4,'message'=>'注册失败','data'=>$res));
        }catch (Exception $e){
            return \yii\helpers\Json::encode(array('code'=>5,'message'=>'注册失败','data'=>$e->getMessage() ));
        }
    }
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
