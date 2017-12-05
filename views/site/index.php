<?php
/* @var $this yii\web\View */
$this->title = 'Welcome to YII2';
$this->context->layout = 'header'; //设置使用的布局文件
?>
<div class="layui-carousel" id="carousel_img">
    <div carousel-item>
        <div><img width="100%" src="/basic/web/img/carousel/1.jpg"></div>
        <div><img width="100%" src="/basic/web/img/carousel/2.jpg"></div>
        <div><img width="100%" src="/basic/web/img/carousel/3.jpg"></div>
        <div><img width="100%" src="/basic/web/img/carousel/4.jpg"></div>
        <div><img width="100%" src="/basic/web/img/carousel/5.jpg"></div>
    </div>
</div>
<script>
    // 轮播图初始化
    layui.use('carousel', function(){
        var carousel = layui.carousel;
        //建造实例
        carousel.render({
            elem: '#carousel_img'
            ,width: '100%' //设置容器宽度
            ,height: '100%' //设置容器宽度
            ,arrow: 'hover' //始终显示箭头
            ,anim: 'fade' //切换动画方式
        });
    });
</script>