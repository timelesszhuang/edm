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
class SendemailAction extends Action
{
    //句柄
    private $handle;
    const MX = "mx_domain_mx_";
    const WHOIS = "mx_domain_whois_";

    /**
     * 执行入口
     */
    public function run()
    {
        //实则句柄
        $this->handle = new Query();
        $start_id = Yii::$app->request->get("start_id");
        if (empty($start_id)) {
            Yii::error("请传递参数", "edm");
            return;
        }
        $this->index($start_id);
    }

    /**
     * 开启发邮件
     * @param $start_id
     */
    public function index($start_id)
    {
//        session_write_close();
        header("Content-type:text/html;charset=utf-8");
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
            $this->save_config_count($config_arr["id"]);
        }
        //账号的起始stemp
        $start_account = $config_arr["send_account_id"];
        //数据的起始stemp
        $data_offset = $config_arr["send_record_page"];
        while (1) {
            //如果账号发送到最后一个 开始轮回
            if ($start_account > $account_count) {
                $start_account = 1;
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
            //在最后添加图片
            $send_info[1]=$send_info[1]."\n <img width='1' height='1' src='".$send_info[2]."'>\n <a href='http://email.salesmen.cn/index.php/Home/Sendemailimg/Unsubscribe_email/customer_id/$customer_id/customer_table/$table_name/registrant_name/$md5_str' target='_blank'>退订邮件</a>";
            //发送邮件数组信息
            $email_send_arr=[
                $data["contact_email"],//发送地址
                $account_send_info["account_name"], //谁发的账号
                $account_send_info["account_password"],     //谁发的密码
                $send_info[0],                                             //标题
                $send_info[1],                                           //内容
                "liurui@qiangbi.net",//随机获取一个用户接收回复邮件 姓名
                "liurui@qiangbi.net",//随机获取一个用户接收回复邮件 邮件
                $path,
                $template_info["description"]
            ];
            //发邮件
            $domain_tool->send_qiyu_email($email_send_arr);
        }


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
        //标题
        $title = str_replace("{{name}}", $registrant_name,$title);
        //内容
        $content = str_replace("{{name}}",$registrant_name,$content);
        //替换链接id
        $content=str_replace("{{id}}",$record_add_id,$content);
        //图片链接地址
        $url="http://email.salesmen.cn/index.php/Home/Sendemailimg/Index/id/".$record_add_id;
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
        $model=new EmailSendRecord();
        $model->template_id=$template_id;
        $model->send_id=$mx_id;
        $model->table_name=$province_id;
        $model->send_config_detail=$detail;
        $model->send_config_id=$config_id;
        $model->send_email=$email;
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
    public function save_config_count($id)
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


}