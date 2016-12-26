<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 9:05
 */
namespace app\controllers;

use yii\web\Controller;

class IndexController extends BaseController
{
    /**
     * 主页面
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderPartial("index");
    }

    public function actions()
    {
        return [
            'importtable'=>[
                 'class'=>'app\actions\ImporttableAction'
            ]
        ];
    }
}