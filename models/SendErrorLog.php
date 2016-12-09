<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 16:06
 */
namespace app\models;
use yii\db\ActiveRecord;
class SendErrorLog extends ActiveRecord
{
    /**
     * 定义表名
     * @return string
     */
     public static function tableName()
     {
         return "{{%send_error_log}}";
     }




}