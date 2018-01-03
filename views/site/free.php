<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

//$this->title = '登录';
//$this->params['breadcrumbs'][] = $this->title;
$this->context->layout = 'header'; //设置使用的布局文件
$this->title = 'Welcome to YII2 - 免费';

?>
<div style="margin:20px 30px;" ng-app="freeListApp" ng-controller="freeListCtrl" >
    <form class="layui-form">
        <div class="layui-inline">
            <label class="layui-form-label">商品标题</label>
            <div class="layui-input-inline">
                <input type="text" name="goods_title" ng-model="goods_title" placeholder="请输入关键字" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">平台</label>
            <div class="layui-input-inline">
                <select name="goods_plat" ng-model="goods_plat">
                    <option value=""></option>
                    <option value="淘宝">淘宝</option>
                    <option value="天猫">天猫</option>
                    <option value="京东">京东</option>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">创建时间</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" name="create_time" id="create_time" placeholder=" - " readonly ng-model="create_time">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label"></label>
            <div class="layui-input-inline">
                <input type="button" class="layui-btn layui-btn-radius" value="搜索" ng-click="get_free_list_btn()" lay-submit lay-filter="formDemo">
            </div>
        </div>
    </form>
    <table id="free_table" lay-filter="test"></table>
</div>

<script>
    var app = angular.module('freeListApp', []);
    app.controller('freeListCtrl', function($scope,$http) {
        layui.use(['table','form'], function(){
            $scope.table = layui.table;
            $scope.laydate = layui.laydate;
            //日期范围
            $scope.laydate.render({
                elem: '#create_time'
                ,range: true
            });
            // 排序初始值
            $scope.order = 'id DESC ';
            // 表格数据
            $scope.free_table = $scope.table.render({
                elem: '#free_table'
                ,height: 500
                ,url: 'index.php?r=site/get_free_goods_list' //数据接口
                ,method: 'post'
                ,page: true //开启分页
                ,cols: [[ //表头
                    {field: 'goods_id', title: '商品id', sort: true, fixed: 'left', width:80,
                        templet: '<div><a href="{{d.goods_link}}" target="_blank" class="layui-table-link">{{d.goods_id}}</a></div>'}
                    ,{field: 'goods_title', title: '标题', sort: true, width:300}
                    ,{field: 'goods_plat', title: '平台', sort: true, width:80, align:'center'}
                    ,{field: 'goods_apply', title: '申请人数', sort: true, width:100, align:'center'}
                    ,{field: 'goods_left', title: '剩余份数', sort: true, width:100, align:'center'}
                    ,{field: 'goods_price', title: '商品价格', sort: true, width:100, align:'center'}
//                    ,{field: 'business_plat', title: '商家平台', sort: true}
                    ,{field: 'business_grade', title: '商家等级', sort: true}
                    ,{field: 'business_sale', title: '商家主营类目',sort: true}
                    ,{field: 'business_sock', title: '商家评分', sort: true, width:100, align:'center'}
                    ,{field: 'create_time', title: '创建时间', sort: true, templet: function(d){
                            return d.create_time.substr(0,16)
                        }
                    }
                    ,{field: 'update_time', title: '更新时间', sort: true, templet: function(d){
                            if(d.update_time == '0000-00-00 00:00:00'){
                                return ''
                            }else{
                                return d.update_time.substr(0,16)
                            }
                        }
                    }
                ]]
            });
            // 排序
            $scope.table.on('sort(test)', function(obj){
                $scope.order = obj.field+' '+obj.type;
                if(obj.type == null)$scope.order = 'id DESC';   // 恢复默认排序
                $scope.table.reload('free_table', {
                    initSort: obj //记录初始排序，如果不设的话，将无法标记表头的排序状态。 layui 2.1.1 新增参数
                    ,where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                        order: $scope.order,
                        goods_title: $scope.goods_title,
                        goods_plat: $scope.goods_plat,
                        create_time: $scope.create_time
                    }
                });
            });
            // 搜索按钮
            $scope.get_free_list_btn = function(){
                var form = layui.form;
                //监听提交
                form.on('submit(formDemo)', function(data){
                    data.field._csrf = '<?= Yii::$app->request->csrfToken ?>';
                    data.field.order = $scope.order;
                    $scope.free_table.reload({
                        where: data.field
                        ,page: {
                            curr: 1 //重新从第 1 页开始
                        }
                    });
                    return false;
                });
            }
        });
    })
</script>