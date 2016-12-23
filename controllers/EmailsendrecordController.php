<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/12
 * Time: 19:03
 */
namespace app\controllers;
use app\models\EmailSendRecord;
use yii;
use yii\helpers\Url;
class EmailsendrecordController extends BaseController
{
    /**
     * 邮件发送记录 页面展示
     * @return string
     */
    public function actionIndex()
    {
        return $this->render("index");
    }

    /**
     * 获取列表数据
     */
    public function actionList()
    {
        $model = new EmailSendRecord();
        exit(json_encode($model->get_list($this->get_page_info())));
    }

    /**
     * 查看mx记录
     * @return string
     */
    public function actionMx_show()
    {
        $id=Yii::$app->request->get("id");
        $model= EmailSendRecord::findOne($id);
        $record_one=$model->getAttributes();
        $table_info_arr=Yii::$app->runAction("emailsendrecord/get_db_config",['param'=>$record_one["table_name"]]);
        $whois_mx_arr=$model->join_data_db($table_info_arr,$record_one["send_id"]);
        return $this->renderPartial("mx_show",["mx"=>$whois_mx_arr["mx"],"whois"=>$whois_mx_arr["whois"]]);
    }

    /**
     * 定义共有方法
     * @return array
     */
    public function actions()
    {
        return [
            "get_db_config"=>[
                "class"=>"app\actions\SendemailAction",
                "property"=>"get_db_config"
            ]
        ];
    }
    /**
     * 获取详情
     */
    public function actionGet_link_detail()
    {
        $id=Yii::$app->request->get("id");
        $model=new EmailSendRecord();
        $db_arr=$model->get_link_detail($id);
        return $this->renderPartial("get_link_detail",["data"=>$db_arr]);
    }

}