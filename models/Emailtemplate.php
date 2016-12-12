<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 14:53
 */
namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
class Emailtemplate extends ActiveRecord
{
    /**
     * 表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%email_template}}";
    }

    /**
     * 定义添加规则
     * @return array
     */
    public function rules()
    {
        return [
            ["title", "required", "message" => "请填写标题"],
            ["content", "required", "message" => "请填写内容"],
            ["detail", "required", "message" => "请填写描述"]
        ];
    }

    /**
     * 添加修改的前置操作
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

    public function get_list($page, $rows, $offset)
    {
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
     * @return mixed
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
              <td>{$v["title"]}</td>
              <td>{$v["detail"]}</td>
              <td>{$v["addtime"]}</td>
              <td><a href="javascript:void(0)" _id={$v["id"]} class="user_edit"  onclick="base_action.edit_action({$v["id"]})"  >编辑</a>&nbsp;&nbsp;<a href="javascript:void(0)"  _id={$v["id"]} class="user_del" onclick="base_action.del_action({$v["id"]})">删除</a></td>
        </tr>
FLAG;
        }
        return $table;
    }

    /**
     * 获取单个数据
     * @param $id
     * @return array|null|ActiveRecord
     */
    public function get_one($id)
    {
        return self::find()->where(["id" => $id])->asArray()->one();
    }

    /**
     * 批量删除
     * @param $id
     * @return int
     */
    public function delete_record($id)
    {
        $del = [];
        foreach ($id as $k => $v) {
            $del[] = $v;
        }
        return self::deleteAll(["id" => $del]);
    }

    /**
     * 获取所有的模板信息
     * @return array|\yii\db\ActiveRecord[]
     */
    public function get_all()
    {
        return self::find()->asArray()->all();
    }

    /**
     * 根据指定id获取模板信息
     * @param $id
     * @return $this
     */
    public function get_byid($id)
    {
        return self::find()->where(["id"=>$id])->asArray()->select("id,title,detail")->one();
    }
}