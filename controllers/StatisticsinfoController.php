<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 14:01
 */
namespace app\controllers;

use app\models\EmailSendRecord;
use app\models\Emailsendconfig;
use yii\helpers\Url;
use yii;
class StatisticsinfoController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * 信息展示主页面
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderPartial("index",["data"=>$this->get_projects()]);
    }

    /**
     * 获取最近的邮件发送信息
     * @return int|string
     */
    public function actionGet_info_total()
    {
        $model = new EmailSendRecord();
        //今天发送多少邮件
        $today_count = $model->total_by_today();
        //昨天发送多少邮件
        $yesterday_count = $model->yesterday_by_today();
        //今天有多少阅读量
        $today_read = $model->today_read_num();
        //昨天阅读量
        $yesterday_read = $model->yesterday_read_num();
        exit(json_encode([
            "today_count" => $today_count,
            "yesterday_count" => $yesterday_count,
            "today_read" => $today_read,
            "yesterday_read" => $yesterday_read
        ]));
    }

    /**
     * 获取今天发送多少邮件
     * @return int|string
     */
    public function actionGet_today_count()
    {
        $model = new EmailSendRecord();
        //今天发送多少邮件
        return $model->total_by_today();
    }

    /**
     * 获取所有的项目
     * @return array|\yii\db\ActiveRecord[]
     */
    public function get_projects()
    {
        $model = new Emailsendconfig();
        return $model->get_record();
    }
}