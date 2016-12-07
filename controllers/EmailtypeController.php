<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 16:35
 */
namespace app\controllers;

use yii;
use app\models\Emailtype;

class EmailtypeController extends BaseController
{
    /**
     * 主页面展示
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderPartial("index");
    }

    /**
     * 添加
     * @return string
     */
    public function actionAdd()
    {
        if (Yii::$app->request->isPost) {
            $model = new Emailtype();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                exit(json_encode(["status" => 10]));
            }
            $model->addError("status", 20);
            exit(json_encode($model->getErrors()));
        }
        return $this->renderPartial("add");
    }

    /**
     * 获取列表
     */
    public function actionList()
    {
        $model = new Emailtype();
        exit(json_encode($model->get_list($this->get_page_info())));
    }

    /**
     * 展示修改页面
     * @param $id
     * @return string
     */
    public function actionEdit($id)
    {
        $model = new Emailtype();
        return $this->renderPartial("edit", ["data" => $model->get_one($id)]);
    }

    /**
     * 修改数据
     */
    public function actionEditdata()
    {
        $id = Yii::$app->request->post("id");
        $model = Emailtype::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            exit(json_encode(["status" => 10]));
        }
        $model->addError("status", 20);
        exit(json_encode($model->getErrors()));
    }

    /**
     * 删除操作
     * @param $id
     */
    public function actionDel($id)
    {
        $model=new Emailtype();
        if($model->delete_all([$id])){
            exit(json_encode(["status"=>10]));
        }
        exit(json_encode(["status"=>20]));
    }

}