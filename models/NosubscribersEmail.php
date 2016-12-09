<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 11:13
 */
namespace app\models;
use yii\db\ActiveRecord;

class NosubscribersEmail extends ActiveRecord
{
    /**
     * 设置表名
     * @return string
     */
    public static function tableName()
    {
        return "{{%nosubscribers_email}}";
    }

    /**
     * 获取所有不发送邮件列表
     * @return $this
     */
    public function get_all()
    {
        return self::find()->asArray()->all();
    }





}