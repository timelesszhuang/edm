<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 11:12
 */
namespace app\assets;
use yii\web\AssetBundle;

class LoginAsset extends AssetBundle
{
    public $basePath = "@webroot";
    public $baseUrl = "@web";
    public $css = [
        'css/font-awesome.css',
        "css/animate.css",
        "css/style.css"
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];


}