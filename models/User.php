<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 11:37
 */
namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

class User extends ActiveRecord
{
    //管理员id =1
    const ADMIN_ID=1;
    /**
     * 表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%user}}";
    }

    /**
     * 规则
     * @return array
     */
    public function rules()
    {
        return [
            ["user_name", "unique", "message" => "用户名重复"],
            ["user_name", "required", "message" => "请输入用户名"],
            ["password", "string", "min" => 6, "tooShort" => "密码长度不能小于6位", "skipOnEmpty" => false, "when" => function ($model) {
                return ($model->isNewRecord) || ($model->password != "");
            }],
        ];
    }

    /**
     * 前置修改
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->addtime = time();
            }
            if (empty($this->password)) {
                unset($this->password);
            } else {
                $this->password = md5($this->password);
            }
            $this->updatetime = time();
            return true;
        }
        return false;
    }

    /**
     * 查询所有数据
     * @return array|\yii\db\ActiveRecord[]
     */
    public function get_all($page, $rows)
    {
        $offset = ($page - 1) * $rows;
        $data = self::find()->asArray()->offset($offset)->limit($rows)->all();
        $count = self::find()->count("*");
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
     * 格式化为表格数据
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
                <td class="select_checkbox">{$i}</td>
                    <td>{$v["user_name"]}</td>
                    <td>{$v["addtime"]}</td>
                    <td><a href="javascript:void(0)" _id={$v["id"]} class="user_edit"  onclick="base_action.edit_action({$v["id"]})"  >编辑</a>&nbsp;&nbsp;<a href="javascript:void(0)"  _id={$v["id"]} class="user_del" onclick="base_action.del_action({$v["id"]})">删除</a></td>
                </tr>
FLAG;
        }
        return $table;
    }

    /**
     * 返回要修改数据
     * @param $id
     * @return array|null|ActiveRecord
     */
    public function get_edit($id)
    {
        return self::find()->where(["id" => $id])->asArray()->one();
    }

    /**
     * 删除操作
     * @param $selected
     * @return int
     */
    public function deleteIn($selected)
    {
        $all_arr=[];
        foreach($selected as $k=>$v){
            if($v==self::ADMIN_ID){
                return false;
            }
            $all_arr[]=$v;
        }
        return self::deleteAll(["id"=>$all_arr]);
    }
}