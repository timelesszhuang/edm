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
use app\models\Nosubscribersemail;
use yii\helpers\ArrayHelper;
use app\models\Account;
use app\models\Emailtemplate;
use app\models\EmailSendRecord;
use app\models\Matchemailignore;
use yii\helpers\Url;

class SendemailAction extends Action
{
    //定义表前缀
    const MX = "mx_domain_mx_";
    const WHOIS = "mx_domain_whois_";
    //通过属性来调用吧
    public $property;
    //是否是黑名单   默认不在黑名单那
    private $is_ignore=false;
    //黑名单信息
    private $ignore_info;
    /**
     * 执行入口
     */
    public function run($param)
    {
        switch ($this->property) {
            //这个是读取配置开始发送邮件
            case "send_email":
                $start_id = Yii::$app->request->get("param");
                if (empty($start_id)) {
                    Yii::error("请传递参数", "edm");
                    return;
                }
                $this->index($start_id);
                break;
            case "get_db_config":
                return $this->get_db_config($param);
                break;
        }
    }

    /**
     * 开启发邮件
     * @param $start_id
     */
    public function index($start_id)
    {
        $this->open_ob_start();
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
        //文件枷锁
        if(file_exists("number.lock")){
            $modify_time=filemtime("number.lock");
            $change_time=time()-$modify_time;
            if($change_time<200){
                return;
            }
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
        //定义条件
        $where = [];
        //获取品牌
        if ($config_arr["brand_id"] > 0) {
            $where["b.brand_id"] = intval($config_arr["brand_id"]);
        }
        //获取账号总数
        $account_count = (new Account())->get_count();
        //获取所有的数据总数
        $count = (new Query())->from(self::WHOIS . $province . " as a")->join("left join", self::MX . $province . " as b", "a.id=b.id")->where($where)->count("*", Yii::$app->$db);
        //如果当前配置信息总的邮件数量没有的话 更新
        if (empty($config_arr["count_number"])) {
            $this->save_config_count($config_arr["id"], $count);
        }
        //取出coonfig中对应的模板信息
        $template_info = $this->get_template_by_id($config_arr["template_id"]);
        if (empty($template_info)) {
            Yii::error("模板对应数据无法获得", "edm");
            return;
        }

        //账号的起始stemp
        $start_account = $config_arr["send_account_id"];
        //数据的起始stemp
        $data_offset = $config_arr["send_record_page"];
        while (1) {
            file_put_contents("number.lock",$data_offset);
            //如果账号发送到最后一个 开始轮回
            if ($start_account >= $account_count) {
                $start_account = 0;
            }
            //取出要发送数据的账号
            $account_send_info = Account::find()->asArray()->offset($start_account)->limit(1)->one();
            //判断数据是否发送完毕  发送完再发送给自己
            if ($data_offset >= $count) {
//                $this->send_self(["", $account_send_info["account_name"], $account_send_info["account_password"], $account_send_info["host"], $template_info['title'] . "数据已经发送完毕,无法再次发送,请重新修改配置", $template_info["content"], "强比科技"]);
                Yii::error("数据已经发送完毕,无法再次发送,请重新修改配置", "edm");
                return;
            }
            //取出要发送的数据
            $data = (new Query())->from(self::WHOIS . $province . " as a")->select(["a.id", "a.domain_name", "a.contact_email", "a.registrant_name", "b.mx"])->leftJoin(self::MX . $province . " as b", "a.id=b.id")->offset($data_offset)->limit(1)->where($where)->one(Yii::$app->$db);
            file_put_contents("email.log", print_r(["data_offset" => $data, "id" => $data["id"]], true), FILE_APPEND);
            //准备要插入的数据
            $record = [
                $config_arr["template_id"], $data["id"], $config_arr["province_id"], $config_arr["detail"], $config_arr["id"], $data["contact_email"]
            ];
            //批量黑名单替换
            if (!empty($this->emailmatchignore($data["contact_email"]))) {
                $this->is_ignore=true;
                $this->ignore_info="域名黑名单";
            }
            //接着匹配单个黑名单
            if(!$this->is_ignore && !empty($this->emailignoreone($data["contact_email"]))){
                $this->is_ignore=true;
                $this->ignore_info="邮箱退订";
            }
            //如果是黑名单用户
            if($this->is_ignore){
                $this->is_ignore=false;
                $this->save_for_send_num($config_arr["id"], ++$start_account, ++$data_offset, $account_send_info["account_name"]);
                $record[3]= $this->ignore_info;
                //插入发送记录
                $this->save_to_record($record);
                continue;
            }
            //插入发送记录
            $record_add_id = $this->save_to_record($record);
            //将账号、数据查询后移   因为swiefmail可能会出错为了防止程序一直down在send函数那里,所以直接跳过这个账号
            $start_account++;
            $data_offset++;
            $this->save_for_send_num($config_arr["id"], $start_account, $data_offset, $account_send_info["account_name"]);
            //整理要发送的内容
            $send_info = $this->replace_content([$data["registrant_name"], $template_info["title"], $template_info["content"], $record_add_id]);
            //加密md5串
            $md5_str = md5($data["contact_email"] . "registrant_name");
            $customer_id = $data["id"];
            $table_name = $config_arr["province_id"];
            //在最后添加图片和退订
            $send_info[1] = $send_info[1] . "\n <img width='1' height='1' src='" . $send_info[2] . "'>\n" . $this->exit_send_email([$customer_id, $data["contact_email"], $md5_str]);;
            //发送邮件数组信息
            $email_send_arr = [
                $data["contact_email"],//发送地址
                $account_send_info["account_name"], //谁发的账号
                $account_send_info["account_password"],     //谁发的密码
                $account_send_info["host"],                 //host
                $send_info[0],                                             //标题
                $send_info[1],                                           //内容
                "强比科技",//
            ];
            //发邮件
            $this->send($email_send_arr);
            //如果mx不为空的话 需要发送给企业admin用户
            if (!empty($data["mx"])) {
                $email_send_arr[0] = "admin@" . $data["domain_name"];
                //发送成功记录下
                if ($this->send($email_send_arr)) {
                    $record[5] = $email_send_arr[0];
                    $this->save_to_record($record);
                }
            }
        }
    }

    /**
     * 匹配单个黑名单 成功返回对象 失败返回空
     * @param $email
     * @return bool|static
     */
    public function emailignoreone($email)
    {
        if (empty($email)) {
            return false;
        }
        return Nosubscribersemail::findOne(["email"=>$email]);
    }

    /**
     * 正则匹配email 返回数组 没找到返回空
     * @param $email
     * @return array|null|yii\db\ActiveRecord
     */
    public function emailmatchignore($email)
    {
        if (empty($email)) {
            return false;
        }
        $arr = explode("@", $email);
        $model = Matchemailignore::find();
        return  $model->where(["REGEXP", "match_str", "^$arr[1]$"])->asArray()->one();
    }

    /**
     * 发送给自己的邮件
     * @param $send_info
     */
    public function send_self($send_info)
    {
        $self_arr = ["liurui@qiangbi.net", "guoping@qiangbi.net", "bjshihuajie@corp.netease.com", "3423929165@qq.com", "2923788170@qq.com"];
        foreach ($self_arr as $k => $v) {
            $send_info[0] = $v;
            $this->send($send_info);
            file_put_contents("email-self.log", print_r($send_info, true), FILE_APPEND);
        }
    }

    /**
     * 生成退订链接
     * @param $arr
     * @return string
     */
    public function exit_send_email($arr)
    {
        list($customer_id, $email, $md5_str) = $arr;
        $url = Yii::$app->params["domain"] . "index.php?r=sendemailtool/unsubscribe_email&customer_id=$customer_id&email=$email&registrant_name=$md5_str";
        return "<a href='" . $url . "' target='_blank'>退订邮件</a>";
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
     *同步发送信息
     * @param $id
     * @param $start_account
     * @param $data_offset
     */
    public function save_for_send_num($id, $start_account, $data_offset, $account_pwd)
    {
        $model = Emailsendconfig::findOne(["id" => intval($id)]);
        $data = [
            "send_account_id" => $start_account,
            "send_record_page" => $data_offset,
            "send_account_name" => $account_pwd
        ];
        $model->setAttributes($data, false);
        $model->save(false);
    }

    /**
     * 发送邮件 兼容多个邮箱类型
     * @param $send_info
     * @return bool
     * @throws yii\base\InvalidConfigException
     */
    public function send($send_info)
    {
        list($email, $account, $account_pwd, $host, $title, $content, $from_name) = $send_info;
        file_put_contents("email-send.log", print_r($send_info, true), FILE_APPEND);
        //如果发送邮箱或发生账号为空退出
        if (empty($email) || empty($account)) {
            Yii::error("send email empty or sender account empty", "edm");
            return false;
        }
        Yii::$app->set('mailer', [
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
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => [$account]
            ],
        ]);
        $mail = Yii::$app->mailer->compose();
        $mail->setTo($email);
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
        list($registrant_name, $title, $content, $record_add_id) = $arr;
        //随机字符串
        $rand_abc = chr(rand(97, 122)) . chr(rand(65, 90)) . chr(rand(97, 122)) . chr(rand(65, 90));
        if (empty($registrant_name)) {
            $registrant_name = "您好";
        }
        //标题
        $title = str_replace("{{name}}", $registrant_name, $title) . "(" . $rand_abc . ")";
        //内容
        $content = str_replace("{{name}}", $registrant_name, $content);
        //替换链接id
        $content = str_replace("{{id}}", $record_add_id, $content);
        //图片链接地址
        $url = Yii::$app->params["domain"] . "index.php?r=sendemailtool%2Fmake_detect_img&id=$record_add_id";
        return [
            $title, $content, $url
        ];
    }

    /**
     * 添加发送记录
     * @param $record
     */
    public function save_to_record($record)
    {
        list($template_id, $mx_id, $province_id, $detail, $config_id, $email) = $record;
        $ip = "http://salesman.cc/index.php/Home/Sendemailimg/get_remote_addr";
        $model = new EmailSendRecord();
        $model->template_id = $template_id;
        $model->send_id = $mx_id;
        $model->table_name = $province_id;
        $model->send_config_detail = $detail;
        $model->send_config_id = $config_id;
        $model->send_email = $email;
        $model->sender_ip = $this->send_curl_request($ip);
        $model->addtime = time();
        $model->updatetime = time();
        if (!$model->save()) {
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
        $model = Emailtemplate::findOne($id);
        return $model->find()->asArray()->one();
    }

    /**
     * 更改配置文件的发送记录数根据id
     * @param $id
     */
    public function save_config_count($id, $count)
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
        $unsend_email_arr = UnsendEmail::find()->select(["email"])->asArray()->all();
        if (!empty($unsend_email_arr)) {
            $unsend_arr = array_column($unsend_email_arr, "email");
        } else {
            $unsend_arr = [];
        }
        return $unsend_arr;
    }

    /**
     * 获取客户退订邮件数组
     * @return $this|array
     */
    public function nosend_email_action()
    {
        $nosend_arr = Nosubscribersemail::find()->asArray()->all();
        if (!empty($nosend_arr)) {
            //格式化数组
            $nosend_arr = array_column($nosend_arr, "email");
        } else {
            $nosend_arr = [];
        }
        return $nosend_arr;
    }

    /**
     * 将插入到log表操作独立出来
     * @param $arr
     */
    public function insert_error_log($arr)
    {
        list($account_send_info, $data) = $arr;
        $model_errorlog = new SendErrorLog();
        $model_errorlog->account_name = $account_send_info["account_name"];
        $model_errorlog->account_password = $account_send_info["account_password"];
        $model_errorlog->email_type = $account_send_info["email_type"];
        $model_errorlog->email = $data["contact_email"];
        $model_errorlog->error_msg = "黑名单用户";
        $model_errorlog->addtime = time();
        $model_errorlog->updatetime = time();
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
                return [$province, 'db3'];
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
