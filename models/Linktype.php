<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 9:38
 */
namespace app\models;

use yii\db\ActiveRecord;

class Linktype extends ActiveRecord
{
    /**
     * 设置表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%link_type}}";
    }

    /**
     * 定义验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ["type_name", "required", "message" => "请输入类型名称"],
            ["detail", "required", "message" => "请输入描述"],
        ];
    }

    /**
     * 前置修改和添加
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->addtime = time();
            }
            $this->updatetime = time();
            return true;
        }
        return false;
    }

    /**
     * 获取数据表格
     * @param $arr
     * @return array
     */
    public function get_list($arr)
    {
        list($page, $rows, $offset) = $arr;
        $count=self::find()->count();
        $allpagenum=ceil($count/$page);
        $data = self::find()->asArray()->offset($offset)->limit($rows)->all();
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
     * @return string
     */
    public function formatter_data(&$data)
    {
        $i = 0;
        $table = '';
        foreach ($data as $k => $v) {
            $i++;
            $v["addtime"] = date("Y-m-d H:i:s",$v["addtime"]);
            $table .= <<<FLAG
            <tr>
            <td class="select_checkbox">$i</td>
            <td>{$v["type_name"]}</td>
            <td>{$v["detail"]}</td>
            <td>{$v["addtime"]}</td>
            <td><a href="javascript:void(0)" _id={$v["id"]} class="user_edit"  onclick="base_action.edit_action({$v["id"]})"  >编辑</a>&nbsp;&nbsp;<a href="javascript:void(0)"  _id={$v["id"]} class="user_del" onclick="base_action.del_action({$v["id"]})">删除</a></td>
            </tr>
FLAG;

        }
        return $table;
    }

    /**
     * 获取一条数据
     * @param $id
     * @return array|null|ActiveRecord
     */
    public function get_one($id)
    {
        return self::find()->where(["id"=>$id])->asArray()->one();
    }

    /**
     * 批量删除
     * @param $del
     */
    public function delete_all($del)
    {
        if(!empty($del)){
            return self::deleteAll(["id"=>$del]);
        }
    }

    /**
     * 获取所有类型
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function get_all()
    {
        return self::find()->asArray()->select("id,type_name")->all();
    }
}