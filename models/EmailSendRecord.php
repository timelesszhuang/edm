<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 19:24
 */
namespace app\models;
use yii\db\ActiveRecord;
class EmailSendRecord extends ActiveRecord
{
    /**
     * 设置表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%email_send_record}}";
    }








}