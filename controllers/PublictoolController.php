<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 11:36
 */
namespace app\controllers;

use yii\web\Controller;
use yii;
class PublictoolController extends Controller
{

    /**
     * 定义公有方法
     * @return array
     */
    public function actions()
    {
        return [
            "sendemail"=>[
                "class"=>"app\actions\SendemailAction"
            ]
        ];
    }
    
    public function actionSend()
    {
        Yii::$app->set('mailer',[
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.qq.com',
        'username' => '3423929165@qq.com',
        'password' => 'Qiangbi123',
        'port' => '465',
        'encryption' => 'tls',
                        ],
            'messageConfig'=>[
        'charset'=>'UTF-8',
        'from'=>['3423929165@qq.com'=>'白狼栈']
    ],
        ]);

        $mail=Yii::$app->mailer->compose();
        $mail->setTo('15863549041@126.com');
        $mail->setSubject("邮件测试");
        $mail->setHtmlBody("htmlbody");
        var_dump($mail->send());
//            var_dump($mail);die;
    }

}