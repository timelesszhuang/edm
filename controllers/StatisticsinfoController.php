<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 14:01
 */
namespace app\controllers;
use app\models\EmailSendRecord;
class StatisticsinfoController extends BaseController
{
    public $enableCsrfValidation = false;
    /**
     * 信息展示主页面
     * @return string
     */
    public function actionIndex()
    {
        $model=new EmailSendRecord();
        var_dump($model->today_read_num());die;
        return $this->renderPartial("index");
    }

    /**
     * 获取今天发送了多少封邮件
     * @return int|string
     */
    public function actionGet_today_record()
    {
        $model=new EmailSendRecord();
        return $model->total_by_today();
    }

    /**
     * 获取昨天发送了多少邮件
     * @return int|string
     */
    public function actionGet_yesterday_record()
    {
        $model=new EmailSendRecord();
        return $model->yesterday_by_today();
    }







}