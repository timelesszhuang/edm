<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 19:24
 */
namespace app\models;

use yii\db\ActiveRecord;
use app\controllers\EmailsendrecordController;
use yii\db\Query;
use yii;

class EmailSendRecord extends ActiveRecord
{
    //定义表前缀
    const MX = "mx_domain_mx_";
    const WHOIS = "mx_domain_whois_";

    /**
     * 设置表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%email_send_record}}";
    }

    /**
     * 获取列表数据
     * @param $arr
     * @return array
     */
    public function get_list($arr)
    {
        list($page, $rows, $offset) = $arr;
        $data = self::find()->asArray()->offset($offset)->limit($rows)->orderBy("id desc")->all();
        $count = self::find()->count();
        $allpagenum = ceil($count / $page);
        $insert_data = $this->formatter_data($data);
        return [
            "status" => 10,
            "table" => $insert_data,
            "allrows" => $allpagenum,
            "allpagenum" => ceil($count / $rows),
        ];

    }

    /**
     * 格式化数据
     * @param $data
     * @return mixed
     */
    public function formatter_data(&$data)
    {
        $i = 0;
        $table = '';
        foreach ($data as $k => $v) {
            $i++;
            $v["addtime"] = date("Y-m-d H:i:s", $v["addtime"]);
            $v["updatetime"] = date("Y-m-d H:i:s", $v["updatetime"]);
            $table .= <<<FLAG
            <tr>
                <td class="select_check">$i</td>
                <td>{$v["send_config_detail"]}</td>
                <td>{$v["read_num"]}</td>
                <td>{$v["link_num"]}</td>
                <td>{$v["send_email"]}</td>
                <td>{$v["sender_ip"]}</td>
                <td>{$v["addtime"]}</td>
                <td>{$v["updatetime"]}</td>
                <td><a href="javascript:void(0)" _id={$v["id"]} class="mx_check">查看mx</a>&nbsp;&nbsp;
                &nbsp;<a href="javascript:void(0)" _id={$v["id"]} class="click_more">点击详情</a>&nbsp;&nbsp;</td>

            </tr>
FLAG;
        }
        return $table;
    }

    /**
     *根据指定id进行查询 并进行大数据链接查询
     * @access public
     * @param $province
     * @return string
     */
    public function join_data_db($table_info_arr,$id)
    {
        list($table, $db) = $table_info_arr;
        $whois_data=(new Query())->from(self::WHOIS.$table)->where(["id"=>$id])->one(Yii::$app->$db);
        $whois_data["registrar_name"] = $this->get_registrar_name($whois_data['registrar_name_id']);
        $whois_data["server_name"] = $this->get_whoisserver($whois_data["whoisserver_id"]);
        $whois_data["createdate"] = date("Y-m-d", $whois_data["createdate"]);
        $whois_data["updatedate"] = date("Y-m-d", $whois_data["updatedate"]);
        $whois_data["expiresdate"] = date("Y-m-d", $whois_data["expiresdate"]);
        //mx
        $mx_data=(new Query())->from(self::MX.$table)->where(["id"=>$id])->one(Yii::$app->$db);
        $mx_data["brand_name"] = $this->get_brand($mx_data["brand_id"]);
        if (!empty($mx_data["old_brand_id"])) {
            $mx_data["old_brand_name"] = $this->get_brand($mx_data["old_brand_id"]);
        }
        return array('whois' => $whois_data, 'mx' => $mx_data);
    }
    /**
     * 格式化brand信息
     * @param $id
     * @return mixed
     */
    public function get_brand($id)
    {
        $brand=(new Query())->from("mx_brand")->where(["id"=>$id])->one(Yii::$app->db3);
        return $brand["name"];
    }
    /**
     * 获取注册机构名称
     * @param $id
     * @return mixed
     */
    public function get_registrar_name($id)
    {
        $data=(new Query())->from("mx_registrar")->where(["id"=>$id])->one(Yii::$app->db3);
        return $data["en_name"];
    }
    /**
     * 根据id获取whois服务器ip
     * @param $id
     */
    public function get_whoisserver($id)
    {
        $data=(new Query())->from("mx_whoisserver")->where(["id"=>$id])->one(Yii::$app->db3);
        return $data["server"];
    }
    /**
     * 获取详情
     * @param $id
     */
    public function get_link_detail($id)
    {
        $data=self::find()->where(["id"=>$id])->asArray()->all();
        $temp=unserialize($data[0]["read_num_serialize"]);
        if($temp){
            array_walk($temp,[$this,"formatter_link_detail"]);
            return $temp;
        }
        return [];
    }
    /**
     * 格式化详情
     * @param $id
     */
    public function formatter_link_detail(&$value,$key)
    {
        if($value["time"]){
            $value["time"]=date("Y-m-d H:i:s",$value["time"]);
        }
        if(strlen($value["ip_info"])<10){
            $value["ip_info"]=$value["ip"];
        }

    }
}