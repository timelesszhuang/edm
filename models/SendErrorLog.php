<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/15
 * Time: 11:42
 */
namespace app\models;
use yii\db\ActiveRecord;

class Senderrorlog extends ActiveRecord
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