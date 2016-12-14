<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/6
 * Time: 9:34
 */
namespace app\models;

use yii\db\ActiveRecord;
use yii;
use yii\db\Query;
use app\models\Emailtemplate;

class Emailsendconfig extends ActiveRecord
{
    /**
     * 表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%email_send_config}}";
    }

    /**
     * 获取省份
     * @return array
     */
    public static function provinces()
    {
        return [
            '山东' => 'shandong_1',
            '北京' => 'beijing_1',
            '河南' => 'henan_1',
            '山西' => 'shanxi_1',
            '河北' => 'hebei_1',
            'CN库1' => 'cn1_1',
            'CN库2' => 'cn2_2',
            'CN库3' => 'cn3_1',
            'CN库4' => 'cn4_2',
            'CN库5' => 'cn5_1',
            '广东' => 'guangdong_2',
            '江苏' => 'jiangsu_2',
            '浙江' => 'zhejiang_2',
            '四川' => 'sichuan_2',
            '湖北' => 'hubei_2',
            '辽宁' => 'liaoning_1',
            '湖南' => 'hunan_2',
            '福建' => 'fujian_2',
            '上海' => 'shanghai_2',
            '安徽' => 'anhui_1',
            '陕西' => 'shanxi2_1',
            '内蒙古' => 'neimenggu_1',
            '广西' => 'guangxi_1',
            '江西' => 'jiangxi_1',
            '天津' => 'tianjin_1',
            '重庆' => 'chongqing_1',
            '黑龙江' => 'heilongjiang_1',
            '吉林' => 'jilin_1',
            '云南' => 'yunnan_2',
            '贵州' => 'guizhou_1',
            '新疆' => 'xinjiang_2',
            '甘肃' => 'gansu_1',
            '海南' => 'hainan_2',
            '宁夏' => 'ningxia_1',
            '青海' => 'qinghai_1',
            '西藏' => 'xizang_2',
            '香港' => 'hongkong_1',
            '澳门' => 'aomen_1',
            '台湾' => 'taiwan_1',
            '其他' => 'other_1',
        ];
    }

    /**
     * 定义验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ["province_id", "required", "message" => "请选择省份"],
            ["brand_id", "required", "message" => "请选择品牌"],
            ["template_id", "required", "message" => "请选择模板"],
            ["detail", "required", "message" => "请输入描述"],
        ];
    }

    /**
     * 获取品牌数组
     * @return array
     */
    public function brands()
    {
        return (new Query())->select(["id", "name"])->from("mx_brand")->all(Yii::$app->db2);
    }

    /**
     * 获取指定品牌id
     * @param $id
     * @return array
     */
    public function get_brand_one($id)
    {
        return (new Query())->select(["name"])->where(["id" => intval($id)])->from("mx_brand")->one(Yii::$app->db3);
    }

    /**
     * 前置修改
     * @param bool $insert
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->addtime = time();
            }
            $provinces = array_flip($this->provinces());
            $this->province_name = $provinces[$this->province_id];
            $this->updatetime = time();
            $this->brand_name = $this->get_brand_one($this->brand_id)["name"];
            $model_temp=Emailtemplate::findOne($this->template_id);
            $temp_data=$model_temp->getAttributes();
            $this->template_name = $temp_data["title"];
            $this->template_detail = $temp_data["detail"];
            $this->updatetime = time();
            return true;
        }
        return false;
    }

    /**
     * 获取所有数据
     * @param $arr
     * @return array
     */
    public function get_all($arr)
    {
        list($page,$rows,$offset)=$arr;
        $count=self::find()->count();
        $allpagenum=ceil($count/$page);
        $data=self::find()->offset($offset)->limit($rows)->asArray()->all();
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
            $v["addtime"]=date("Y-m-d H:i:s",$v["addtime"]);
            $table.=<<<FLAG
            <tr>
                <td class="select_check">$i</td>
                <td>{$v["detail"]}</td>
                <td>{$v["brand_name"]}</td>
                <td>{$v["province_name"]}</td>
                <td>{$v["send_record_page"]}</td>
                <td>{$v["count_number"]}</td>
                <td>{$v["id"]}</td>
                <td>{$v["template_detail"]}</td>
                <td>{$v["send_account_name"]}</td>
                <td>{$v["addtime"]}</td>
                <td><a href="javascript:void(0)" _id={$v["id"]} class="user_edit"  onclick="base_action.edit_action({$v["id"]})"  >编辑</a>&nbsp;&nbsp;</td>
            </tr>
FLAG;
        }
        return $table;
    }
}