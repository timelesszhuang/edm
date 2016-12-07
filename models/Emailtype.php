<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 16:38
 */
namespace app\models;

use yii\db\ActiveRecord;

class Emailtype extends ActiveRecord
{
    /**
     * 设置表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%email_type}}";
    }

    /**
     * 定义规则
     * @return array
     */
    public function rules()
    {
        return [
            ["email_name", "required", "message" => "请输入邮箱类型"],
            ["email_name", "unique", "message" => "名称重复"],
            ["host", "required", "message" => "请输入host"]
        ];
    }

    /**
     * 前置修改 添加
     * @param bool $insert
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
     * 获取列表
     * @param $arr
     * @return array
     */
    public function get_list($arr)
    {
        list($page, $rows, $offset) = $arr;
        $data = self::find()->asArray()->offset($offset)->limit($rows)->all();
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
                <td>{$v["email_name"]}</td>
                <td>{$v["host"]}</td>
                <td>{$v["addtime"]}</td>
                <td><a href="javascript:void(0)" _id={$v["id"]} class="user_edit"  onclick="base_action.edit_action({$v["id"]})"  >编辑</a>&nbsp;&nbsp;<a href="javascript:void(0)"  _id={$v["id"]} class="user_del" onclick="base_action.del_action({$v["id"]})">删除</a></td>
            </tr>
FLAG;
        }
        return $table;
    }

    /**
     * 查询单条
     * @param $id
     * @return array|null|ActiveRecord
     */
    public function get_one($id)
    {
        return self::find()->where(["id" => $id])->asArray()->one();
    }

    /**
     * 删除操作
     * @param $arr
     * @return bool
     */
    public function delete_all($arr)
    {
        if (!empty($arr)) {
            return self::deleteAll(["id"=>$arr]);
        }
        return false;
    }

    /**
     * 获取所有数据
     * @return array|\yii\db\ActiveRecord[]
     */
    public function get_all()
    {
        return self::find()->asArray()->all();
    }
}