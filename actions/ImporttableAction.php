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
        $data=(new Query())->select(["account_name","password"])->from("sm_mail_user")->all(Yii::$app->db);
        $model=new Account();
        foreach($data as $k=>$v){
            $_model = clone $model;
            $_model->setAttributes(["account_name"=>$v["account_name"]."@126-m.com","account_password"=>$v["password"],"email_type"=>1]);
            $_model->save();
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