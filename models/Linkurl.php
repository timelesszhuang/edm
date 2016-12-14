<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 10:43
 */
namespace app\models;

use yii\db\ActiveRecord;
use app\models\Linktype;
use yii\helpers\ArrayHelper;
use yii;
use yii\helpers\Url;

class Linkurl extends ActiveRecord
{
    private $change_click_url=false;
    /**
     * 设置表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%link_url}}";
    }

    /**
     * 生成验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ["type_id", "required", "message" => "请选择类型名称"],
            ["link_name", "required", "message" => "请输入链接名称"],
            ["link_name", "unique", "message" => "链接名称重复"],
            ["link_url", "required", "message" => "请输入链接地址"],
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
                //用于后置操作时 判断是否是insert操作
                $this->change_click_url=true;
            }
            $arr = ArrayHelper::map(Linktype::get_all(), "id", "type_name");
            $this->type_name = $arr[$this->type_id];
            $this->updatetime = time();
            return true;
        }
        return false;
    }

    /**
     * 后置操作  给新添加的记录修改click_page
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->attributes["id"] && $this->change_click_url) {
            $url="http://email.salesmen.cn/index.php?r=sendemailtool%2Fjump_web&link_id=".$this->attributes["id"]."&record_id={{id}}";
            self::updateAll(["click_url" => $url], ["id" => $this->attributes["id"]]);
        }
    }

    /**
     * 获取列表
     * @param $arr
     * @return array
     */
    public function get_list($arr)
    {
        list($page,$rows,$offset)=$arr;
        $count=self::find()->count();
        $allpagenum=ceil($count/$page);
        $data=self::find()->offset($offset)->limit($rows)->orderBy("id desc")->asArray()->all();
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
        $i=0;
        $table='';
        foreach($data as $k=>$v){
            $i++;
            $v["click_url"]=urldecode($v["click_url"]);
            $v["addtime"]=date("Y-m-d H:i:s",$v["addtime"]);
            $table.=<<<FLAG
            <tr>
                <td class="select_check">$i</td>
                <td>{$v["link_name"]}</td>
                <td>{$v["type_name"]}</td>
                <td>{$v["link_url"]}</td>
                <td>{$v["click_url"]}</td>
                <td>{$v["read_number"]}</td>
                <td>{$v["addtime"]}</td>
                <td><a href="javascript:void(0)" _id={$v["id"]} class="user_edit"  onclick="base_action.edit_action({$v["id"]})"  >编辑</a>&nbsp;&nbsp;<a href="javascript:void(0)"  _id={$v["id"]} class="user_del" onclick="base_action.del_action({$v["id"]})">删除</a></td>
            </tr>
FLAG;
        }
        return $table;
    }

    /**
     * 修改单条数据
     * @param $id
     * @return array|null|ActiveRecord
     */
    public function get_one($id)
    {
        return self::find()->where(["id"=>$id])->asArray()->one();
    }

    /**
     * 删除
     * @param $id
     * @return bool
     */
    public function delete_all($id)
    {
        if(!empty($id)){
            return self::deleteAll(["id"=>$id]);
        }
        return false;
    }

    /**
     * 根据type_id来获取数据
     * @param $typeid
     * @return array|yii\db\ActiveRecord[]
     */
    public function get_by_typeid($typeid){
        return self::find()->where(["type_id"=>$typeid])->asArray()->all();
    }
}