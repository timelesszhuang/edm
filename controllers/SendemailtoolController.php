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
use yii\helpers\Url;
use app\models\Nosubscribersemail;
class SendemailtoolController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * 定义公有方法
     * @return array
     */
    public function actions()
    {
        return [
            "sendemail" => [
                "class" => "app\actions\SendemailAction",
                "property"=>"send_email"
            ],
            "get_db_config"=>[
                "class"=>"app\actions\SendemailAction",
                "property"=>"get_db_config"
            ]
        ];
    }

    /**
     * 发送邮件时,点击链接时的二次跳转
     */
    public function actionJump_web()
    {
        //链接id
        $link_id = Yii::$app->request->get("link_id");
        //获取邮箱记录id
        $email_id = Yii::$app->request->get("record_id");
        $model_erecord = EmailSendRecord::findOne(["id" => $email_id]);
        if (!empty($model_erecord->getAttributes())) {
            $this->save_link_record([$email_id, $link_id,$model_erecord->getAttributes()]);
        }
        //link_info表中添加数据
        $model_linkurl = Linkurl::findOne($link_id);
        $link_url_one = $model_linkurl->getAttributes();
        $save_data=[
            "read_number"=>intval($link_url_one["read_number"]) + 1
        ];
        Linkurl::updateAll($save_data,["id"=>$link_id]);
        $this->redirect($link_url_one["link_url"]);
    }

    /**
     * 查看邮件时修改link链接查看记录
     * @param $arr
     */
    public function save_link_record($arr)
    {
        list($email_id, $link_id,$data_one) = $arr;
        //如果是第一次
        if (empty($data_one["link_serialize"])) {
            $save_link = [$link_id => 1];
        } else {
            $save_link = unserialize($data_one["link_serialize"]);
            if (array_key_exists($link_id, $save_link)) {
                $save_link[$link_id]++;
            } else {
                $save_link = [$link_id => 1];
            }
        }
        $link_plus = $data_one["link_num"] + 1;
        $save_data["link_num"]=$link_plus;
        $save_data["link_serialize"]=serialize($save_link);
        EmailSendRecord::updateAll($save_data,["id"=>$email_id]);
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
        $email_id = Yii::$app->request->get("id");
        $model_record = EmailSendRecord::findOne($email_id);
        $email_one_data = $model_record->getAttributes();
        if (!!$email_one_data) {
            $this->modifly_record([$email_id,$_SERVER['REMOTE_ADDR'], $email_one_data, $model_record]);
        }
        $this->actionMake_img();
    }

    /**
     * 查看邮件时修改数据
     * @param $arr
     */
    public function modifly_record($arr)
    {
        list($id,$ip, $data, $model) = $arr;
        $ip_info = $this->get_ip_info($ip);
        $save_data = [];
        $save_data["read_num"] = $data["read_num"] + 1;
        $save_data["updatetime"] = time();
        //序列化
        if (empty($data["read_num_serialize"])) {
            $ip_info = $ip_info["data"]["area"] . "-" . $ip_info["data"]["region"] . "-" . $ip_info["data"]["city"] . "-" . $ip_info["data"]["isp"];
            $save_data["read_num_serialize"] = serialize([0 => ["ip_info" => $ip_info, "time" => time(), "ip" => $ip, "user-agent" => $_SERVER["HTTP_USER_AGENT"]]]);
        } else {
            $time_history = unserialize($data["read_num_serialize"]);
            $time_history[] = ["time" => time(), "ip_info" => $ip_info, "ip" => $ip, "user-agent" => $_SERVER["HTTP_USER_AGENT"]];
            $save_data["read_num_serialize"] = serialize($time_history);
        }
        $model->updateAll($save_data,["id"=>$id]);
    }

    /**
     * 生成探针图片
     */
    public function actionMake_img()
    {
        $url="images/1.png";
        $img=imagecreatefrompng($url);
        @header("Content-Type:image/png");
        imagepng($img);
    }

    /**
     * 退订
     */
    public function actionUnsubscribe_email()
    {
        $customer_id=Yii::$app->request->get("customer_id");
        $md5_str=Yii::$app->request->get("registrant_name");
        $email=Yii::$app->request->get("email");
        //验证MD5
        if($md5_str==md5($email."registrant_name")){
           return $this->renderPartial("unsubscribe_email",["customer_id"=>$customer_id,"customer_email"=>$email]);
        }
    }

    /**
     * 添加到退订表中
     */
    public function actionCheck_unsubscribe_email()
    {
        header('Access-Control-Allow-Origin:*');
        $id=Yii::$app->request->post("id");
        $email=Yii::$app->request->post("email");
        $customer_subscribe=Yii::$app->request->post("customer_subscribe");
        if(empty($customer_subscribe)){
            //查询是否存在email
            $model_meybe=Nosubscribersemail::findOne(["email"=>$email]);
            //不存在添加
            if(empty($model_meybe)){
                $model=new Nosubscribersemail();
                $data=[
                    "email"=>$email,
                    "email_record_id"=>$id,
                    "addtime"=>time(),
                    "updatetime"=>time()
                ];
                $model->setAttributes($data,false);
                $model->save(false);
            }
        }else{
            Nosubscribersemail::deleteAll(["email"=>$email]);
        }
        exit(json_encode(["msg"=>"修改成功"]));
    }

}