<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/9
 * Time: 11:00
 */
namespace app\controllers;
use yii\web\Controller;
class SendemailtoolController extends Controller
{
    /**
     * 发送邮件
     * @param $Content
     * @param $Subject
     * @param $email
     * @return bool
     * @throws \phpmailerException
     */
//    public function send_qiyu_email($arr)
//    {
//        list($send_email, $account_name, $password, $title, $content, $receipt_name, $receipt_email, $options_arr, $description) = $arr;
//        set_time_limit(0);
//        import('Vendor.PHPMailer.phpmailer');
//        $mail = new \PHPMailer();
//        $mail->IsSmtp();                         // 设置使用 SMTP
//        $mail->Host = "smtp.qiye.163.com";       // 指定的 SMTP 服务器地址
//        $mail->SMTPAuth = true;                  // 设置为安全验证方式
//        $mail->Username = $account_name . "@126-m.com"; // SMTP 发邮件人的用户名
//        $mail->Password = $password;           // SMTP 密码
//        $mail->From = $account_name . "@126-m.com";
//        $mail->FromName = $description;
//        $mail->CharSet = "UTF-8";
//        $mail->AddReplyTo($receipt_email, $receipt_name);//回复给谁
//        $mail->AddAddress($send_email);
//        //发送到谁 写谁$mailaddress
//        $mail->WordWrap = 50;                // set word wrap to 50 characters
//        $mail->IsHTML(true);                    // 设置邮件格式为 HTML
//        $mail->Subject = $title; //邮件主题// 标题
//        $mail->Body = $content;              // 内容
//        if (!empty($options_arr)) {
//            //添加附件
//            for ($i = 0; $i < count($options_arr["name"]); $i++) {
//                $houzui = strrchr($options_arr["path"][$i], ".");
//                $mail->AddAttachment($options_arr["path"][$i], $options_arr["name"][$i] . $houzui);
//            }
//        }
//        if (!$mail->Send()) {
//            $data = [
//                "error_msg" => $mail->ErrorInfo, "send_email" => $send_email, "account_name" => $account_name, "account_email" => $account_name . "@126-m.com", "account_passwd" => $password, "addtime" => time(), "updatetime" => time()
//            ];
//            M("EmailErrorRecord")->add($data);
//        }
//    }



}




