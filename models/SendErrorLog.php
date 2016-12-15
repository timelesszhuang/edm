<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/15
 * Time: 14:55
 */
namespace app\models;
use yii\db\ActiveRecord;

class SendErrorLog extends ActiveRecord
{
    /**
     * 设置表名
     */
        public static function tableName()
        {
            return "{{%send_error_log}}";
        }


}