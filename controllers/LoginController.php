<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 11:05
 */
namespace app\controllers;
use yii\web\Controller;
use app\models\LoginForm;
use yii\captcha\Captcha;
use yii;
class LoginController extends Controller
{
    /**
     *登陆操作
     * @return string
     */
    public function actionIndex()
    {
        $model=new LoginForm;
        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate() && $model->login()){
             return Yii::$app->response->redirect(["index/index"]);
        }
        return $this->renderPartial("index",["model"=>$model]);
    }

    /**
     * 方法
     * @return array
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'maxLength' => 4,//生成验证码最大个数
                'minLength' => 4,//生成验证码最小个数
                'width' => 80,//验证码的宽度
                'height' => 40//验证码的高度
            ]

        ];
    }



}