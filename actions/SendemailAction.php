<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 10:03
 */
namespace app\actions;
use yii\base\Action;
use yii\db\Query;
use yii;
use app\models\Emailsendconfig;
use app\models\NosubscribersEmail;
use yii\helpers\ArrayHelper;
use app\models\Account;
use app\models\SendErrorLog;
use app\models\UnsendEmail;
use app\models\Emailtemplate;
use app\models\EmailSendRecord;
use yii\helpers\Url;
class SendemailAction extends Action
{
    //定义表前缀
    const MX = "mx_domain_mx_";
    const WHOIS = "mx_domain_whois_";

    /**
     * 执行入口
     */
    public function run()
    {
        switch(Yii::$app->request->get("flag")){
            //这个是读取配置开始发送邮件
            case "send_email":
                $start_id = Yii::$app->request->get("start_id");
                if (empty($start_id)) {
                    Yii::error("请传递参数", "edm");
                    return;
                }
                $this->index($start_id);
                break;
        }
    }

    /**
     * 开启发邮件
     * @param $start_id
     */
    public function index($start_id)
    {
//        session_write_close();     //--------------
//        $this->open_ob_start();
        //读取配置项
        $config_arr = Emailsendconfig::find()->where(["id" => $start_id])->asArray()->one();
        if (empty($config_arr)) {
            Yii::error("没有配置数据", "edm");
            return;
        }
        //获取省份信息
        $province_info = $this->get_db_config($config_arr["province_id"]);
        if (empty($province_info)) {
            Yii::error("无法获取省份信息", "edm");
            return;
        }
        list($province, $db) = $province_info;
        //获取模板
        if (empty($config_arr["template_id"])) {
            Yii::error("无法获取模板", "edm");
            return;
        }
        $this->send_email([$config_arr, $province, $db]);
    }

    /**
     * 开始发送邮件
     * @param $arr
     */
    public function send_email($arr)
    {
        list($config_arr, $province, $db) = $arr;
        //获取不发送邮件数组
        $nosend_arr = $this->nosend_email_action();
        //获取黑名单邮件 不发送
        $unsend_arr = $this->unsend_email_action();
        //定义条件
        $where = [];
        //获取品牌
        if ($config_arr["brand_id"] > 0) {
            $where["b.brand_id"] = intval($config_arr["brand_id"]);
        }
        //获取账号总数
        $account_count = (new Account())->get_count();
        //获取所有的数据总数
        $count = (new Query())->from(self::WHOIS . $province . " as a")->join("left join", self::MX . $province . " as b", "a.id=b.id")->where($where)->count("*", Yii::$app->db2);
        //如果当前配置信息总的邮件数量没有的话 更新
        if (empty($config_arr["count_number"])) {
            $this->save_config_count($config_arr["id"],$count);
        }
        //账号的起始stemp
        $start_account = $config_arr["send_account_id"];
        //数据的起始stemp
        $data_offset = $config_arr["send_record_page"];
        while (1) {
            //如果账号发送到最后一个 开始轮回
            if ($start_account >= $account_count) {
                $start_account = 0;
            }
            //判断数据是否发送完毕
            if ($data_offset > $count) {
                Yii::error("数据发送完毕", "edm");
                return;
            }
            //取出要发送数据的账号
            $account_send_info = Account::find()->asArray()->offset($start_account)->limit(1)->one();
            //取出要发送的数据
            $data = (new Query())->from(self::WHOIS . $province . " as a")->select(["a.id","a.contact_email","a.registrant_name"])->leftJoin(self::MX . $province . " as b", "a.id=b.id")->offset($data_offset)->limit(1)->where($where)->one(Yii::$app->db2);
            //如果在不发送名单中 不发送
            if (in_array($data["contact_email"], $nosend_arr)) {
                //记录下来
                $this->insert_error_log([$account_send_info, $data]);
                break;
            }
            //黑名单用户也过滤掉
            if (in_array($data["contact_email"], $unsend_arr)) {
                //记录下来
                $this->insert_error_log([$account_send_info, $data]);
                break;
            }
            //取出coonfig中对应的模板信息
            $template_info = $this->get_template_by_id($config_arr["template_id"]);
            if (empty($template_info)) {
                Yii::error("模板对应数据无法获得", "edm");
                break;
            }
            //插入发送记录
            $record=[
                $config_arr["template_id"],$data["id"],$config_arr["province_id"],$config_arr["detail"],$config_arr["id"],$data["contact_email"]
            ];
            $record_add_id=$this->save_to_record($record);
            //整理要发送的内容
            $send_info=$this->replace_content([$data["registrant_name"],$template_info["title"],$template_info["content"],$record_add_id]);
            //加密md5串
            $md5_str=md5($data['registrant_name']."registrant_name");
            $customer_id=$data["id"];
            $table_name=$config_arr["province_id"];
            //在最后添加图片和退订
            $send_info[1]=$send_info[1]."\n <img width='1' height='1' src='".$send_info[2]."'>\n".$this->exit_send_email([$customer_id,$table_name,$md5_str]);;
            //发送邮件数组信息
            $email_send_arr=[
                $data["contact_email"],//发送地址
                $account_send_info["account_name"], //谁发的账号
                $account_send_info["account_password"],     //谁发的密码
                $account_send_info["host"],
                $send_info[0],                                             //标题
                $send_info[1],                                           //内容
                "强比科技",//随机获取一个用户接收回复邮件 邮件
            ];
            file_put_contents("email.log",print_r($email_send_arr,true),FILE_APPEND);
            //发邮件失败 记录错误信息
            if(!$this->send($email_send_arr)){
                $this->error_log([$account_send_info["account_name"],$account_send_info["account_password"],$account_send_info["email_type"],$data["contact_email"]]);
            }
            //将账号、数据查询后移
            $start_account++;
            $data_offset++;
            $this->save_for_send_num($config_arr["id"],$start_account,$data_offset,$account_send_info["account_name"]);
            break;//---------------------------------------------
        }
    }

    /**
     * 生成退订链接
     * @param $arr
     * @return string
     */
    public function exit_send_email($arr)
    {
        list($customer_id,$table_name,$md5_str)=$arr;
        return "<a href='http://email.salesmen.cn/index.php/Home/Sendemailimg/Unsubscribe_email/customer_id/$customer_id/customer_table/$table_name/registrant_name/$md5_str' target='_blank'>退订邮件</a>";
    }
    /**
     * 开启缓冲区并刷新数据到前台
     */
    public function open_ob_start()
    {
        ignore_user_abort(true);//在关闭连接后，继续运行php脚本
        /******** background process ********/
        set_time_limit(0); //no time limit，不设置超时时间（根据实际情况使用）
    }
    /**
     * 邮件发生错误日志
     * @param $arr
     */
    public function error_log($arr)
    {
        list($account,$account_pwd,$email_type,$email)=$arr;
        $model=SendErrorLog::find();
        $model->account_name=$account;
        $model->account_password=$account_pwd;
        $model->email_type=$email_type;
        $model->email=$email;
        $model->error_msg="邮件发送失败";
        $model->addtime=time();
        $model->updatetime=time();
        $model->save();
    }
    /**
     *同步发送信息
     * @param $id
     * @param $start_account
     * @param $data_offset
     */
    public function save_for_send_num($id,$start_account,$data_offset,$account_pwd)
    {
        $model=Emailsendconfig::findOne($id);
        $model->send_account_id=$start_account;
        $model->send_record_page=$data_offset;
        $model->send_account_name=$account_pwd;
        $model->save();
    }
    /**
     * 发送邮件 兼容多个邮箱类型
     * @param $send_info
     * @return bool
     * @throws yii\base\InvalidConfigException
     */
    public function send($send_info)
    {
        list($email,$account,$account_pwd,$host,$title,$content,$from_name)=$send_info;
        //如果发送邮箱或发生账号为空退出
        if(empty($email) || empty($account)){
            Yii::error("send email empty or sender account empty","edm");
            return false;
        }
        Yii::$app->set('mailer',[
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $host,
                'username' => $account,
                'password' => $account_pwd,
                'port' => '25',
                'encryption' => 'tls',
            ],
            'messageConfig'=>[
                'charset'=>'UTF-8',
                'from'=>[$account]
            ],
        ]);
        $mail=Yii::$app->mailer->compose();
        $mail->setTo("15863549041@126.com");//----------------------------
        $mail->setSubject($title);
        $mail->setHtmlBody($content);
        return $mail->send();
    }
    /**
     * 替换内容
     * @param $data
     * @param $template_info
     * @param $record_add_id
     * @return string
     */
    public function replace_content($arr)
    {
        list($registrant_name,$title,$content,$record_add_id)=$arr;
        //随机字符串
        $rand_abc=chr(rand(97,122)).chr(rand(65,90)).chr(rand(97,122)).chr(rand(65,90));
        //标题
        $title = str_replace("{{name}}", $registrant_name,$title).$rand_abc;
        //内容
        $content = str_replace("{{name}}",$registrant_name,$content);
        //替换链接id
        $content=str_replace("{{id}}",$record_add_id,$content);
        //图片链接地址
        $url=Url::toRoute("Sendemailtool/make_detect_img",["id/$record_add_id"]);
        return [
            $title,$content,$url
        ];
    }

    /**
     * 添加发送记录
     * @param $record
     */
    public function save_to_record($record)
    {
        list($template_id,$mx_id,$province_id,$detail,$config_id,$email)=$record;
        $ip="http://salesman.cc/index.php/Home/Sendemailimg/get_remote_addr";
        $model=new EmailSendRecord();
        $model->template_id=$template_id;
        $model->send_id=$mx_id;
        $model->table_name=$province_id;
        $model->send_config_detail=$detail;
        $model->send_config_id=$config_id;
        $model->send_email=$email;
        $model->sender_ip=$this->send_curl_request($ip);
        $model->addtime=time();
        $model->updatetime=time();
        if(!$model->save()){
            Yii::error("record插入记录失败");
            exit(0);
        }
        return $model->attributes["id"];
    }
    /**
     * 根据id获取模板信息
     * @param $id
     * @return array|null|yii\db\ActiveRecord
     */
    public function get_template_by_id($id)
    {
        $model=Emailtemplate::findOne($id);
        return $model->find()->asArray()->one();
    }
    /**
     * 更改配置文件的发送记录数根据id
     * @param $id
     */
    public function save_config_count($id,$count)
    {
        $model_config = Emailsendconfig::findOne($id);
        $model_config->count_number = intval($count);
        $model_config->save();
    }

    /**
     * 获取所有黑名单中的邮件数组
     * @return array
     */
    public function unsend_email_action()
    {
        $unsend_email_arr=UnsendEmail::find()->select(["email"])->asArray()->all();
        if(!empty($unsend_email_arr)){
            $unsend_arr=array_column($unsend_email_arr,"email");
        }else{
            $unsend_arr=[];
        }
        return $unsend_arr;
    }
    /**
     * 获取客户退订邮件数组
     * @return $this|array
     */
    public function nosend_email_action()
    {
        $model_nosend=new NosubscribersEmail();
        $nosend_arr=$model_nosend->get_all();
        if(!empty($nosend_arr)){
            //格式化数组
            $nosend_arr=array_column($nosend_arr,"email");
        }else{
            $nosend_arr=[];
        }
        return $nosend_arr;
    }
    /**
     * 将插入到log表操作独立出来
     * @param $arr
     */
    public function insert_error_log($arr)
    {
        list($account_send_info,$data)=$arr;
        $model_errorlog=new SendErrorLog();
        $model_errorlog->account_name=$account_send_info["account_name"];
        $model_errorlog->account_password=$account_send_info["account_password"];
        $model_errorlog->email_type=$account_send_info["email_type"];
        $model_errorlog->email=$data["contact_email"];
        $model_errorlog->error_msg="黑名单用户";
        $model_errorlog->addtime=time();
        $model_errorlog->updatetime=time();
        $model_errorlog->save();
    }
    /**
     *根据省市信息 获取执行哪一个数据库查询信息
     * @access public
     * @param $province
     * @return string
     */
    public function get_db_config($province)
    {
        $suffix = substr($province, strlen($province) - 1, 1);
        $province = substr($province, 0, strlen($province) - 2);
        switch ($suffix) {
            case '1':
                return [$province, 'db'];
                break;
            case '2':
                return [$province, 'db2'];
                break;
            default:
                exit;
        }
    }
    /**
     * 发送curl请求
     * @access protected
     * @param $url
     * @param array $data 文件修改 $data 文件修改
     * @param string $flag 标志是 get post
     */
    protected function send_curl_request($url, $data = array(), $flag = 'post')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($flag == 'get') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        } else {
            curl_setopt($ch, CURLOPT_POST, 1);           // 发送一个常规的Post请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);            // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循
        $temp = curl_exec($ch);
        if (curl_errno($ch)) {
            file_put_contents('error.log', '微信推送消息错误：' . curl_error($ch) . "\r\n", FILE_APPEND);
        }
        curl_close($ch);
        return $temp;
    }

}