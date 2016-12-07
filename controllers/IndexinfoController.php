<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 9:33
 */
namespace app\controllers;

use yii\web\Controller;

class IndexinfoController extends Controller
{
    public function actionIndex()
    {
        return $this->renderPartial("index");
    }

}