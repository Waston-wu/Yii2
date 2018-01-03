<?php
/**
 * Created by PhpStorm.
 * User: banma
 * Date: 2017/12/1
 * Time: 18:05
 */
$this->context->layout = 'header'; //设置使用的布局文件
$this->title = 'Welcome to YII2 - 注册';

?>
<div style="padding:20px;width:30%;margin:0 auto;" ng-app="registerPageApp" ng-controller="registerPageCtrl" ng-init="mail_button_text='发送验证码';">
    <h1 style="text-align: center;margin-bottom: 20px;">欢迎注册</h1>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">邮箱</label>
            <div class="layui-input-block">
                <input list="mail_input_list" id="mail_input" type="text" name="email" ng-model="email" lay-verify="required|email" placeholder="请输入邮箱" autocomplete="off" class="layui-input">
                <datalist id="mail_input_list"></datalist>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">邮箱验证码</label>
            <div class="layui-input-inline" style="width:50%;">
                <input type="text" name="code" lay-verify="required" placeholder="请输入邮箱验证码" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-word-aux"><button type="button" id="mail_button" class="layui-btn layui-btn-primary" ng-click="sendMailCode()" ng-bind="mail_button_text"></button></div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">验证码</label>
            <div class="layui-input-block">
                <div class="l-captcha" data-site-key="<?php echo Yii::$app->params['luosimao_site_key']?>" data-width="100%;"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-block">
                <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="formDemo" ng-click="register_save()">注册</button>
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
    var app = angular.module('registerPageApp', []);
    app.controller('registerPageCtrl', function($scope,$http) {
        // 验证邮箱格式的方法
        $scope.validateMail = function(email)
        {
            if(!!!email)
            {
                layui.layer.msg('请填写邮箱', {icon: 5,anim: 6});
                return false;
            }
            var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
            if(!(reg.test(email))){
                layui.layer.msg('邮箱格式不正确', {icon: 5,anim: 6});
                return false;
            }
            return true;
        }
        // 改变按钮样式-关
        $scope.change_button_off = function(obj)
        {
            obj.attr('disabled',true);
            obj.attr('class','layui-btn layui-btn-disabled');
        }
        // 改变按钮样式-开
        $scope.change_button_on = function(obj)
        {
            obj.removeAttr('disabled');
            obj.attr('class','layui-btn layui-btn-primary');
        }
        // 发送验证码页面
        $scope.sendMailCode = function()
        {
            // 1.验证邮箱格式
            var validate_res = $scope.validateMail($scope.email);
            if(!validate_res)return false;
            $scope.change_button_off($("#mail_button"));
            // 2.发送邮箱验证码
            $http({
                method: 'post',
                data:$.param({
                    email:$scope.email,_csrf:'<?= Yii::$app->request->csrfToken ?>'
                }),
                headers:{'Content-Type': 'application/x-www-form-urlencoded'},
                url: 'index.php?r=site/send_mail_code',
            }).then(function successCallback(response) {
                // 请求成功执行代码
                layui.layer.msg(response.data.message);
                if(response.data.code == 0)
                {
                    var mail_down_time = parseInt(response.data.data.down_time);
                    mail_down_time = isNaN(mail_down_time)? '0' : mail_down_time;
                    // 执行倒计时
                    var mail_time_out = window.setInterval(function () {
                        if(mail_down_time == 0)
                        {
                            $scope.change_button_on($("#mail_button"));
                            $("#mail_button").text ( '发送验证码');
                            clearInterval(mail_time_out);
                        }else
                        {
                            $scope.change_button_off($("#mail_button"));
                            $("#mail_button").text ( '（'+ mail_down_time + '）秒');
                            mail_down_time -- ;
                        }
                    },1000);
                }else
                {
                    $scope.change_button_on($("#mail_button"));
                }
            }, function errorCallback(response) {
                // 请求失败执行代码
                console.log(response);
                $scope.change_button_on($("#mail_button"));
            });
        }
        // 注册按钮-保存
        $scope.register_save = function()
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
                    url: 'index.php?r=site/register_save',
                }).then(function successCallback(response) {
                    layui.layer.msg(response.data.message);
                    if(response.data.code < 0)LUOCAPTCHA.reset(); // 重置验证码
                }, function errorCallback(response) {
                    // 请求失败执行代码
                    console.log(response);
                });
                return false;
            });
        }
    });
</script>
