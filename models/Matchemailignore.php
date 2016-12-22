<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/22
 * Time: 8:52
 */
namespace app\models;

use yii\db\ActiveRecord;

class Matchemailignore extends ActiveRecord
{
    /**
     * 设置表名
     * @return string
     */
    public static function tabName()
    {
        return "{{%matchemailignore}}";
    }

    /**
     * 获取列表
     * @param $arr
     * @return array
     */
    public function get_list($arr)
    {
        list($page, $rows, $offset) = $arr;
        $count = self::find()->count();
        $allpagenum = ceil($count / $page);
        $data = self::find()->offset($offset)->limit($rows)->orderBy("id desc")->asArray()->all();
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
                <td>{$v["match_str"]}</td>
                <td>{$v["detail"]}</td>
                <td>{$v["addtime"]}</td>
                <td><a href="javascript:void(0)" _id={$v["id"]} class="user_edit"  onclick="base_action.edit_action({$v["id"]})"  >编辑</a>&nbsp;&nbsp;<a href="javascript:void(0)"  _id={$v["id"]} class="user_del" onclick="base_action.del_action({$v["id"]})">删除</a></td>
            </tr>
FLAG;
        }
        return $table;
    }

    /**
     * 定义规则
     * @return array
     */
    public function rules()
    {
        return [
            ["match_str", "required", "message" => "请输入邮箱后缀"],
            ["match_str", "unique", "message" => "邮箱后缀重复"],
            ["detail", "required", "message" => "请输入描述"]
        ];
    }

    /**
     * 前置修改
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if($this->isNewRecord){
                $this->addtime=time();
            }
            $this->updatetime=time();
            return true;
        }
        return false;
    }

    /**
     * 批量删除
     * @param $id
     * @return bool|int
     */
    public function delete_all($id)
    {
        if(is_array($id)){
            return $this->deleteAll(["id"=>$id]);
        }
        return false;
    }

}