<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/21
 * Time: 18:21
 */
namespace app\controllers;

use yii\web\Controller;
use yii;
class BaseController extends Controller
{
    /**
     * 初始化操作
     */
        public function init()
        {
            parent::init();
            if(!Yii::$app->session->get("user_name")){
                return Yii::$app->response->redirect(['login/index']);
            }
        }

    /**
     * 获取传递过来的page和rows参数
     * @return array
     */
        public function get_page_info()
        {
            $rows=Yii::$app->request->post("rows");
            $page=Yii::$app->request->post("page");
            return [
                $page,$rows,($page-1)*$rows
            ];
        }

}