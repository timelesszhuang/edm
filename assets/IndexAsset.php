<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 9:09
 */
namespace app\assets;
use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $basePath="@webroot";
    public $baseUrl="@web";
    public $css=[
        'css/font-awesome.css',
        "css/animate.css",
        "css/style.css"
    ];
    public $js=[
        "js/jquery.min.js",
        "js/bootstrap.min.js",
        "js/plugins/metisMenu/jquery.metisMenu.js",
        "js/plugins/slimscroll/jquery.slimscroll.min.js",
        "js/plugins/layer/layer.min.js",
        "js/hAdmin.js",
        "js/index.js"
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}