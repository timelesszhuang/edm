<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 15:58
 */
namespace app\models;

use yii\db\ActiveRecord;
use app\models\Emailtype;
class Account extends ActiveRecord
{
    /**
     * 设置表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%account}}";
    }

    /**
     * 请输入验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ["account_name", "required", "message" => "请输入账号"],
            ["account_name", "unique", "message" => "账号重复"],
            ["account_password", "required", "message" => "请输入密码"],
            ["email_type", "required", "message" => "请选择邮箱类型"]
        ];
    }

    /**
     * 前置修改和新增
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($this->isNewRecord){
                $this->addtime=time();
            }
            $emile_type=(new Emailtype())->get_one($this->email_type);
            $this->host=$emile_type["host"];
            $this->updatetime=time();
            $this->email_type_name=$emile_type["email_name"];
            return true;
        }
        return false;
    }

    /**
     * 获取列表
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
     * @return string
     */
    public function formatter_data(&$data)
    {
        $i = 0;
        $table = '';
        foreach ($data as $k => $v) {
            $i++;
            $v["addtime"] = date("Y-m-d H:i:s", $v["addtime"]);
            $table .= <<<FLAG
            <tr>
                <td class="select_check">$i</td>
                <td>{$v["account_name"]}</td>
                <td>{$v["account_password"]}</td>
                <td>{$v["email_type_name"]}</td>
                <td>{$v["host"]}</td>
                <td>{$v["addtime"]}</td>
                <td><a href="javascript:void(0)" _id={$v["id"]} class="user_edit"  onclick="base_action.edit_action({$v["id"]})"  >编辑</a>&nbsp;&nbsp;<a href="javascript:void(0)"  _id={$v["id"]} class="user_del" onclick="base_action.del_action({$v["id"]})">删除</a></td>
            </tr>
FLAG;
        }
        return $table;
    }

    /**
     * 批量删除
     * @param $id
     * @return int
     */
    public function del_all($id)
    {
        return self::deleteAll(["id"=>$id]);
    }
}