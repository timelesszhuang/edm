<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 9:08
 */
namespace app\assets;

use yii\web\AssetBundle;

class PublicAsset extends AssetBundle
{
    public $basePath = "@webroot";
    public $baseUrl = "@web";
    public $css=[
        "/css/bootstrap.min.united.css",
        "/css/style.css"
    ];
    public $js=[
        "http://libs.baidu.com/jquery/1.11.3/jquery.min.js",
        "/js/layer/layer.js",
        "/js/base.js"
    ];
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}