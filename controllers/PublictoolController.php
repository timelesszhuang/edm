<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 11:36
 */
namespace app\controllers;

use yii\web\Controller;

class PublictoolController extends Controller
{
    public function actionIndex()
    {
        (new table_import())->import_table1();
    }

    /**
     * 定义公有方法
     * @return array
     */
    public function actions()
    {
        return [
            "table_import"=>[
                "class"=>"app\actions\ImporttableAction"
            ]
        ];
    }

}