<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/9
 * Time: 10:15
 */
namespace app\controllers;
use yii\web\Controller;
use yii;
use app\models\EmailSendRecord;
class MakeemailimgController extends Controller
{
    public function actionMakeimg()
    {
        //获取邮箱记录id
        $email_id=Yii::$app->request->get("id");
        $email_one_data=EmailSendRecord::find()->where(["id"=>$email_id])->asArray()->one();

        $this->sendemailimg();
    }

    /**
     * 发送邮件时生成图片
     */
    public function actionSendemailimg()
    {
        $width = 1;//图片宽度
        $height = 1;//图片高度
        $H1 = 12;
        @header("Content-Type:image/png");
        $im = imagecreate($width, $height);//创建图片
        $grey = imagecolorallocate($im, 204, 204, 204);//创建灰色
        $yellow = imagecolorallocate($im, 203, 255, 0);//创建黄色
        $red = imagecolorallocate($im, 255, 0, 0);//创建黄色
        $black = imagecolorallocate($im, 0, 0, 0);//创建字体颜色黑色
        $white = imagecolorallocate($im, 255, 255, 255);//创建颜色白色
        $fontfile = 'arial.ttf';
        $size = 10;//字体大小
        $angle = 0;//字体旋转角度
        $x = 0;//第一个字左下角的X坐标位置
        $y = 4;//第一个字左下解的Y坐标位置
        $text = '-24hr      -18hr            -12hr            -6hr        now';
        imagefilledrectangle($im, 0, 0, $width, $height, $white);//本函数将图片的封闭长方形区域着色。参数 x1、y1 及 x2、y2 分别为矩形对角线的坐标。参数 col 表示欲涂上的颜色。
        imageline($im, 0, 0, $width, 0, $black);
        for ($i = 0; $i < 28; $i++) {
            imageline($im, $i, 1, $i, $H1, $red);
        }
        imageline($im, 0, 0, $width, 0, $black);//top边框
        imageline($im, $width - 1, 0, $width - 1, $H1, $black);//right边框
        imageline($im, 0, $H1, 290, $H1, $black);//bottom边框
        imageline($im, 0, 0, 0, $H1, $black);//left边框
        imagettftext($im, $size, $angle, 0, 24, $black, $fontfile, $text);//指定图片,$size文字大小,$angle文字旋转角度,注意($x,$y)是文串第一个字符的basepoint(约等于左下角的坐标),$fontfile使用的字体文件的路径位置,$text文字内容.
        imagepng($im);
        imagedestroy($im);
    }





}