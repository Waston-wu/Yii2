<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

//$this->title = '登录';
//$this->params['breadcrumbs'][] = $this->title;
$this->context->layout = 'header'; //设置使用的布局文件
$this->title = 'Welcome to YII2 - 登录';

?>
<div style="padding:20px;width:30%;margin:0 auto;" ng-app="loginPageApp" ng-controller="loginPageCtrl">
    <h1 style="text-align: center;margin-bottom: 20px;">欢迎登录</h1>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">邮箱</label>
            <div class="layui-input-block">
                <input list="mail_input_list" id="mail_input" type="text" name="email" ng-model="email" lay-verify="required|email" placeholder="请输入邮箱" autocomplete="off" class="layui-input">
                <datalist id="mail_input_list"></datalist>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-block">
                <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">验证码</label>
            <div class="layui-input-block">
                <div class="l-captcha" data-site-key="<?php echo Yii::$app->params['luosimao_site_key']?>" data-width="100%;"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="formDemo" ng-click="login_save()">登录</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
</div>

<script>
    // 邮箱输入联想功能(邮箱对象，邮箱列表对象)
    mail_input_list($("#mail_input"),$("#mail_input_list"));
    //Demo
    layui.use('form', function(){

    });
    var app = angular.module('loginPageApp', []);
    app.controller('loginPageCtrl', function($scope,$http) {
        // 登录按钮-保存
        $scope.login_save = function()
        {
            var form = layui.form;
            //监听提交
            form.on('submit(formDemo)', function(data){
                if($("#lc-captcha-response").val() == '')
                {
                    layui.layer.msg('请进行人机验证', {icon: 5,anim: 6});return false;
                }
                data.field._csrf = '<?= Yii::$app->request->csrfToken ?>';
                $http({
                    method: 'post',
                    data:$.param(data.field),
                    headers:{'Content-Type': 'application/x-www-form-urlencoded'},
                    url: 'index.php?r=site/login_save',
                }).then(function successCallback(response) {
                    layui.layer.msg(response.data.message);
                    if(response.data.code < 0)LUOCAPTCHA.reset(); // 重置验证码
                    if(response.data.code == 0)window.location = 'index.php?r=site/index';
                }, function errorCallback(response) {
                    // 请求失败执行代码
                    console.log(response);
                });
                return false;
            });
        }
    });
</script>
