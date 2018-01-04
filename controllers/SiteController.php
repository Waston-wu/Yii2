<?php

namespace app\controllers;

use app\models\YUser;
use app\models\YShiyong;
use app\models\YUserLog;
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
    public function actionTest()
    {
        $user = YUser::findOne('1');
        $user->deleted = 0;
        $res = $user->save();
        var_export($res);
    }
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
     * @return string
     * @author wusong
     * @date 2018/1/3 10:52
     * 免费页面
     */
    public function actionFree()
    {
        // 获取平台信息和商品类型
        $model = new YShiyong();
        $goods_plat = $model->find()->select('goods_plat')->groupBy('goods_plat')->asArray()->all();
        $business_sale = $model->find()->select('business_sale')->groupBy('business_sale')->asArray()->all();
        return $this->render('free',['goods_plat'=>$goods_plat, 'business_sale'=>$business_sale]);
    }
    /**
     * @author wusong@bmtrip.com
     * @date 2018/1/3 11:30
     * 获取免费列表
     */
    public function actionGet_free_goods_list()
    {
        $limit = $_REQUEST['limit']?$_REQUEST['limit']:10;
        $page = $_REQUEST['page']?$_REQUEST['page']:1;
        // 搜索条件
        $goods_title = isset($_REQUEST['goods_title'])?$_REQUEST['goods_title']:''; // 商品标题
        $goods_plat = isset($_REQUEST['goods_plat'])?$_REQUEST['goods_plat']:'';    // 平台
        $create_time = isset($_REQUEST['create_time'])?$_REQUEST['create_time']:''; // 创建时间
        $business_time = isset($_REQUEST['business_time'])?$_REQUEST['business_time']:''; // 是否上架
        $business_sale = isset($_REQUEST['business_sale'])?$_REQUEST['business_sale']:''; // 商品类型

        // 排序
        $order = isset($_REQUEST['order'])?$_REQUEST['order']:'id DESC';
        if($create_time){
            $create_time_l = explode(' - ',$create_time)[0].' 00:00:00';
            $create_time_r = explode(' - ',$create_time)[1].' 23:59:59';
        }
        $model = new YShiyong();
        $query = $model->find()->where(1);
        if($goods_title)$query->andwhere(['like', 'goods_title', $goods_title]);    // 商品标题
        if($goods_plat)$query->andwhere(['goods_plat'=>$goods_plat]);   // 商品平台
        if($business_time === '0'){
            $query->andwhere(['<','business_time',date('Y-m-d H:i:s')]);    // 是否上架
        }else if($business_time === '1'){
            $query->andwhere(['>=','business_time',date('Y-m-d H:i:s')]);
        }
        if($business_sale)$query->andwhere(['business_sale'=>$business_sale]);  // 商品类型
        if($create_time)$query->andwhere(['between', 'create_time', $create_time_l, $create_time_r]);   // 创建时间
        // 获取列表
        $list = $query
            ->limit($limit)
            ->offset(($page-1)*$limit)
            ->orderBy($order)
            ->all();
        // 获取总数
        $count = $query->count('id');
        return \yii\helpers\Json::encode( array('code'=>0, 'message'=>'获取数据成功', 'data'=>$list, 'count'=>$count));
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
        // 检测邮箱是否已经注册了
        $userinfo = YUser::find()->where(['email'=>trim($param['email']),'deleted'=>0])->one();
        if($userinfo)return \yii\helpers\Json::encode( array('code'=>4,'message'=>'该邮箱已经被注册','data'=>[]));
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
            $this->recode_user_action('注册',$model->id); // 存储活动记录
            if($res)
                return \yii\helpers\Json::encode(array('code'=>0,'message'=>'注册成功','data'=>[]));
            else
                return \yii\helpers\Json::encode(array('code'=>4,'message'=>'注册失败','data'=>$res));
        }catch (Exception $e){
            return \yii\helpers\Json::encode(array('code'=>5,'message'=>'注册失败','data'=>$e->getMessage() ));
        }
    }

    /**
     * 登录操作
     * @author wusong
     * @date 2017-12-05
     * @return string
     */
    public function actionLogin_save()
    {
        if(!(Yii::$app->request->isPost))
        {
            return \yii\helpers\Json::encode(array('code'=>1,'message'=>'非法请求','date'=>array()));die;
        }
        $param = Yii::$app->request->post();
        // 验证参数必填
        if(Yii::$app->helper->param_validate($param,['email','password','luotest_response']))
        {
            return \yii\helpers\Json::encode(array('code'=>2,'message'=>'入参非法','data'=>[]));
        }
        // 验证人机验证的验证码
        $luosimao_validate = \yii\helpers\Json::decode(Yii::$app->helper->luosimao_validate($param['luotest_response']));
        if($luosimao_validate['code'] != 0)return \yii\helpers\Json::encode($luosimao_validate);
        // 检测用户名和密码是否正确
        $query = YUser::find()
            ->asArray()
            ->select(['id','email'])
            ->where(['email'=>$param['email'],'password'=>md5($param['password']),'deleted'=>0]);
        $res = $query->one();
        if($res)
        {
            // 将用户信息保存到session
            Yii::$app->session->set('userinfo',$res);
            $this->recode_user_action('登录');
            // 更新最后登录IP地址
            $model = YUser::findOne($res['id']);
            $model->last_login_ip_address = ip2long(Yii::$app->helper->getIp())?ip2long(Yii::$app->helper->getIp()):0;
            $model->last_login_time = date("Y-m-d H:i:s");
            $model->save();
            return \yii\helpers\Json::encode(array('code'=>0,'message'=>'登录成功','data'=>$res));
        }else
        {
            return \yii\helpers\Json::encode(array('code'=>3,'message'=>'用户名或密码错误','data'=>[]));
        }
    }

    /**
     * 退出登录操作
     * @author wusong
     * @date 2017-13-05
     */
    public function actionLogin_out()
    {
        // 获取用户session
        $session = Yii::$app->session;
        $userinfo = $session->has('userinfo') ? $session->get('userinfo') : '';
        if($userinfo)
        {
            // 清除session
            $this->recode_user_action('登出');
            $session->remove('userinfo');
        }
        return $this->goHome();
    }

    /**
     * 存储用户操作信息
     * @author wusong
     * @date 2017-12-05
     * @action 操作类型
     */
    public function recode_user_action($action,$userid = '',$old_data='',$new_data='',$remark='')
    {
        $model = new YUserLog();
        $model->createtime = date('Y-m-d H:i:s');   // 创建时间
        $model->userid = $userid ? $userid : Yii::$app->session->get('userinfo')['id'];   // 用户id
        $model->ip_address = ip2long(Yii::$app->helper->getIp())?ip2long(Yii::$app->helper->getIp()):0;  // ip地址
        $model->action = $action;   // 用户操作类型
        $model->old_data = $old_data;   // 旧数据
        $model->new_data = $new_data;   // 新数据
        $model->remark = $remark;   // 备注
        return $model->save();
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
