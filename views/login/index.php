<?php
use app\assets\LoginAsset;
use yii\helpers\Html;
//验证码类
use yii\captcha\Captcha;
LoginAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> 登录EDM</title>
    <!--百度cdn 加速jquery-->
    <script src="http://libs.baidu.com/jquery/1.11.3/jquery.min.js"></script>
    <?php $this->head();?>
</head>
<?php $this->beginBody();?>
<body class="gray-bg">
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">E</h1>
        </div>
        <h3>欢迎使用 EDM</h3>
        <?=Html::beginForm("","post",["id"=>"form"])?>
            <div class="form-group">
                <?=Html::activeInput("input",$model,"user_name",["class"=>"form-control","placeholder"=>"用户名"])?>
                 <?=Html::error($model,"user_name",["class"=>"error"])?>
            </div>
            <div class="form-group">
                <?=Html::activePasswordInput($model,"password",["class"=>"form-control","placeholder"=>"密码"])?>
                <?=Html::error($model,"password",["class"=>"error"])?>
            </div>
            <div class="form-group">
                <?=Captcha::widget([
                    'model' => $model,
                    'attribute' => 'verifycode',//验证吗这个地方一定要注意,verifycode是小写不要写成
                    'captchaAction'=>'login/captcha',
                    'template'=>'{input}{image}',
                    'options' => [
                        'class' => 'form-control col-xs-5 input verifycode',
                        "placeholder"=>"输入验证码",
                        'id' => 'verifyCode'
                    ],
                ])?>
                <?=Html::error($model,"verifycode",["class"=>"error"])?>
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">登 录</button>
        <?=Html::endForm();?>
    </div>
</div>
</body>
<?php $this->endBody();?>
</html>
<?php $this->endPage();?>
