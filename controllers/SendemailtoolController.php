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
use app\models\EmailSendRecord;
use app\models\Linkurl;
class SendemailtoolController extends Controller
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
    /**
     * 发送邮件时,点击链接时的二次跳转
     */
    public function actionJump_web()
    {
        //链接id
        $link_id=Yii::$app->request->get("link_id");
        //获取邮箱记录id
        $email_id=Yii::$app->request->get("e_id");
        $model_erecord=EmailSendRecord::findOne(["id"=>$email_id]);
        if (!empty($model_erecord->getAttributes())) {
            $this->save_link_record([$email_id,$link_id]);
        }
        //link_info表中添加数据
        $model_linkurl=Linkurl::findOne($link_id);
        $link_url_one=$model_linkurl->getAttributes();
        $model_linkurl->read_number=intval($link_url_one["read_number"]) + 1;
        $model_linkurl->save();
        header("Location:" . $link_url_one["link_url"]);
    }
    /**
     * 查看邮件时修改link链接查看记录
     * @param $arr
     */
    public function actionSave_link_record($arr)
    {
        list($email_id,$link_id)=$arr;
        $model_erecord=EmailSendRecord::findOne(["id"=>$email_id]);
        $data_one=$model_erecord->getAttributes();
        //如果是第一次
        if(empty($data_one["link_serialize"])){
            $save_link=[$link_id=>1];
        }else{
            $save_link=unserialize($data_one["link_serialize"]);
            if(array_key_exists($link_id,$save_link)){
                $save_link[$link_id]++;
            }else{
                $save_link=[$link_id=>1];
            }
        }
        $link_plus=$data_one["link_num"]+1;
        $model_erecord->read_num=$link_plus;
        $model_erecord->link_serialize=serialize($save_link);
        $model_erecord->save();
    }

    /**
     * 查看邮件时修改数据
     * @param $arr
     */
    public function save_record($arr)
    {
        list($ip,$data,$model) = $arr;
        $ip_info = $this->get_ip_info($ip);
        $save_data = [];
        $save_data["read_num"]=$data["read_num"] + 1;
        $save_data["lasttime"]=time();
        $save_data["updatetime"]=time();
        $ip_info=$ip_info["data"]["area"]."-".$ip_info["data"]["region"]."-".$ip_info["data"]["city"]."-".$ip_info["data"]["isp"];
        //如果阅读次数数组是空的话
        if(empty($data["read_num_serialize"])){
            $save_data["read_num_serialize"] = serialize([0=>["time"=>time(),"ip_info"=>$ip_info,"ip"=>$ip,"user-agent"=>$_SERVER["HTTP_USER_AGENT"]]]);
        }else{
            $time_history=unserialize($data["read_num_serialize"]);
            $time_history[]=["time"=>time(),"ip_info"=>$ip_info,"ip"=>$ip,"user-agent"=>$_SERVER["HTTP_USER_AGENT"]];
            $save_data["read_num_serialize"] = serialize($time_history);
        }
        $model->setAttributes($save_data,false);
        $model->save();
    }
    /**
     * 获取ip 接口
     */
    public function get_ip_info($ip)
    {
        $curl = curl_init(); //这是curl的handle
        //下面是设置curl参数
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=$ip";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_HEADER, 0); //don't show header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //相当关键，这句话是让curl_exec($ch)返回的结果可以进行赋值给其他的变量进行，json的数据操作，如果没有这句话，则curl返回的数据不可以进行人为的去操作（如json_decode等格式操作）
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);
        //这个就是超时时间了
        $data = curl_exec($curl);
        return json_decode($data, true);
    }
    /**
     * 发送邮件时生成探针图片 并修改访问记录
     */
    public function actionMake_detect_img()
    {
        //获取邮箱记录id
        $email_id=Yii::$app->request->get("id");
        $model_record=EmailSendRecord::findOne($email_id);
        $email_one_data=$model_record->getAttributes();
        if(!!$email_one_data){
            $this->modifly_email_record([$_SERVER['REMOTE_ADDR'],$email_one_data,$model_record]);
        }
        $this->make_img();
    }
    /**
     * 查看邮件时修改数据
     * @param $arr
     */
    public function modifly_email_record($arr)
    {
        list($ip,$data,$model) = $arr;
        $ip_info = $this->get_ip_info($ip);
        $save_data = [];
        $save_data["read_num"]=$data["read_num"] + 1;
        $save_data["lasttime"]=time();
        $save_data["updatetime"]=time();
        //序列化
        if (empty($data["read_num_serialize"])) {
            $ip_info=$ip_info["data"]["area"]."-".$ip_info["data"]["region"]."-".$ip_info["data"]["city"]."-".$ip_info["data"]["isp"];
            $save_data["read_num_serialize"] = serialize([0=>["ip_info"=>$ip_info,"time"=>time(),"ip"=>$ip,"user-agent"=>$_SERVER["HTTP_USER_AGENT"]]]);
        } else {
            $time_history=unserialize($data["read_num_serialize"]);
            $time_history[]=["time"=>time(),"ip_info"=>$ip_info,"ip"=>$ip,"user-agent"=>$_SERVER["HTTP_USER_AGENT"]];
            $save_data["read_num_serialize"] = serialize($time_history);
        }
        $model->setAttributes($save_data,false);
    }
    /**
     * 生成探针图片
     */
    public function make_img()
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