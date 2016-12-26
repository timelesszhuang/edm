<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/7
 * Time: 14:48
 */
namespace app\actions;
use yii\base\Action;
use yii\db\Query;
use yii;
use app\models\Account;
class ImporttableAction extends Action
{
    /**
     * 从一个表向另一个表导入数据
     * Yii批量添加还需要clone一下model才可以，不然只会插入一条数据。
     */
    public function import_table1()
    {
        $data=(new Query())->select(["id","account_name"])->from("sm_account")->all(Yii::$app->db);
        foreach($data as $k=>$v){
            if(strstr($v["account_name"],"99crm.cn")!==false){
                $model=Account::findOne(['id'=>$v["id"]]);
                $data=[
                    "email_type"=>1,
                    "host"=>"smtp.qiye.163.com",
                    "email_type_name"=>"试用"
                ];
                $model->setAttributes($data,false);
                $model->save(false);
            }
//            $_model = clone $model;
//            $_model->setAttributes(["account_name"=>$v["account_name"]."@126-m.com","account_password"=>$v["password"],"email_type"=>1]);
//            $_model->save();
        }
    }

    /**
     * 统一执行方法
     */
    public function run()
    {
//        $this->import_table1();
    }

}