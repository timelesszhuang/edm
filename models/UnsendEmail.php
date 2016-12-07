<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/5
 * Time: 15:27
 */
namespace app\models;

use yii\db\ActiveRecord;

class UnsendEmail extends ActiveRecord
{
    /**
     * 返回表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%unsend_email}}";
    }

    /**
     * 定义验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ["email", "required", "message" => "请填写邮箱"],
            ["detail", "required", "message" => "请填写描述"]
        ];
    }

    /**
     * 前置修改和添加
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
     * 获取数据表格
     * @param $page
     * @param $rows
     * @param $offset
     * @return array
     */
    public function get_list($arr)
    {
        list($page,$rows,$offset)=$arr;
        $data=self::find()->asArray()->offset($offset)->limit($rows)->all();
        $count=self::find()->count();
        $allpagenum = ceil($count / $page);
        $insert_data=$this->formatter_data($data);
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
        $i=0;
        $table='';
        foreach($data as $k=>$v){
            $i++;
            $v["addtime"] = date("Y-m-d H:i:s", $v["addtime"]);
            $table.=<<<FLAG
        <tr>
            <td class="select_checkbox">{$i}</td>
              <td>{$v["email"]}</td>
              <td>{$v["detail"]}</td>
              <td>{$v["addtime"]}</td>
              <td><a href="javascript:void(0)" _id={$v["id"]} class="user_edit"  onclick="base_action.edit_action({$v["id"]})"  >编辑</a>&nbsp;&nbsp;<a href="javascript:void(0)"  _id={$v["id"]} class="user_del" onclick="base_action.del_action({$v["id"]})">删除</a></td>
        </tr>
FLAG;
        }
        return $table;
    }

    /**
     * 删除全部
     * @param $arr
     * @return int
     */
    public function del_all($arr)
    {
        return $this->deleteAll(["id"=>$arr]);
    }
}