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
<style>
    td .layui-table-cell{
        height:50px
    }
</style>
<div style="margin:20px 30px;" ng-app="freeListApp" ng-controller="freeListCtrl" >
    <h4><b>这个列表是用Python抓取<a href="http://www.shiyong.com" target="_blank">试用网</a>的免费试用列表的数据</b></h4>
    <br>
    <form class="layui-form">
        <div class="layui-inline">
            <label class="layui-form-label">商品标题</label>
            <div class="layui-input-inline">
                <input type="text" name="goods_title" ng-model="goods_title" id="goods_title" placeholder="请输入关键字" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">平台</label>
            <div class="layui-input-inline">
                <select name="goods_plat" ng-model="goods_plat" id="goods_plat">
                    <option value=""></option>
                    <?php foreach ($goods_plat as $value):?>
                        <?php echo '<option value="'.$value['goods_plat'].'">'.$value['goods_plat'].'</option>';?>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">商品类型</label>
            <div class="layui-input-inline">
                <select name="business_sale" ng-model="business_sale" id="business_sale">
                    <option value=""></option>
                    <?php foreach ($business_sale as $value):?>
                        <?php echo '<option value="'.$value['business_sale'].'">'.$value['business_sale'].'</option>';?>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">是否上架</label>
            <div class="layui-input-inline">
                <input type="radio" name="business_time" value="" title="不限" checked>
                <input type="radio" name="business_time" value="1" title="是">
                <input type="radio" name="business_time" value="0" title="否">
            </div>
        </div>
<!--        <div class="layui-inline">-->
<!--            <label class="layui-form-label">创建时间</label>-->
<!--            <div class="layui-input-inline">-->
<!--                <input type="text" class="layui-input" name="create_time" id="create_time" placeholder=" - " readonly ng-model="create_time">-->
<!--            </div>-->
<!--        </div>-->
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
            $scope.form = layui.form;
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
                ,height: 700
                ,url: 'index.php?r=site/get_free_goods_list' //数据接口
                ,method: 'post'
                ,page: true //开启分页
                ,cols: [[ //表头
                    {field: 'goods_id', title: '商品id', sort: true, width:80,
                        templet: '<div><a href="{{d.goods_link}}" target="_blank" class="layui-table-link">{{d.goods_id}}</a></div>'}
                    ,{field: 'goods_title', title: '标题', sort: true, width:300}
                    ,{field: 'goods_image', title: '图片', sort: true, width:80, style: 'height:50px',
                        templet: '<div style="height:180px;"><a href="{{d.goods_image}}" target="_blank"><img src="{{d.goods_image}}" height="50px"></div>'}
                    ,{field: 'is_huabei', title: '花呗', sort: true, width:80, align:'center', templet: function(d){
                            return d.is_huabei == '0'?'<i class="layui-icon" style="font-size: 30px; color: #FF5722;">&#x1006;</i>':'<i class="layui-icon" style="font-size: 30px; color: #1E9FFF;">&#xe605;</i>'
                        }
                    }
                    ,{field: 'goods_plat', title: '平台', sort: true, width:80, align:'center'}
                    ,{field: 'goods_apply', title: '申请数', sort: true, width:80, align:'center'}
                    ,{field: 'goods_left', title: '剩余', sort: true, width:70, align:'center'}
                    ,{field: 'goods_price', title: '价格', sort: true, width:90, align:'center'}
                    ,{field: 'business_time', title: '过期时间', sort: true, width:170}
                    ,{field: 'business_grade', title: '商家等级', sort: true}
                    ,{field: 'business_sale', title: '商品类型',sort: true}
                    ,{field: 'business_sock', title: '评分', sort: true, width:80, align:'center'}
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
                    ,where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式） 这里取值不能用ng-model取值，我自测感觉是layui将select和日期选择后值动态赋值angular获取不到
                        order: $scope.order,
                        goods_title: $scope.goods_title,
                        goods_plat: $("#goods_plat").val(),
                        business_sale: $("#business_sale").val(),
                        business_time: $("input[name='business_time']:checked").val(),
                        create_time: $("#create_time").val()
                    }
                });
            });
            // 搜索按钮
            $scope.get_free_list_btn = function(){
                //监听提交
                $scope.form.on('submit(formDemo)', function(data){
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