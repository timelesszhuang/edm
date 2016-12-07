<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 14:57
 */
namespace app\models;

use app\models\User;
use yii\base\Model;
use yii;
class  LoginForm extends Model
{
    //定义三个参数需要接收
      public $user_name;
      public $password;
      public $verifycode;
    //用户信息数组
      public $user;
    /**
     * 规则
     * @return array
     */
    public function rules()
    {
        return [
            ["user_name","required","message"=>"请输入名称"],
            ["password","required","message"=>"请输入密码"],
            ["verifycode","required","message"=>"请输入密码"],
            ["user_name","validatePassword"],
            ["verifycode", "captcha", "captchaAction" => "login/captcha", "message" => "验证码错误"],
        ];
    }

    /**
     * 自定义验证方法
     * @param $attribute
     * @param $param
     */
    public function validatePassword($attribute,$param)
    {
        $user_one=User::find()->where(["user_name"=>$this->$attribute])->asArray()->one();
        if(!$user_one || md5($this->password)!==$user_one["password"]){
            return $this->addError("user_name","用户名或密码错误");
        }
        //填充用户数组
        $this->user=$user_one;
    }

    /**
     * 验证成功后的附加操作
     * @return bool
     */
    public function login()
    {
        $this->createSession();
        return true;
    }

    /**
     * 为用户生成session
     */
    public function createSession()
    {
        $session_obj=Yii::$app->session;
        $session_obj->set("user_name",$this->user["user_name"]);
        $session_obj->set("user_id",$this->user["id"]);
    }
}