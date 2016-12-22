<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/22
 * Time: 8:49
 */
namespace app\controllers;

use app\models\Matchemailignore;
use yii;

class EmailignoreController extends BaseController
{
    /**
     * 主页面展示
     * @return string
     */
    public function actionIndex()
    {
        return $this->render("index");
    }

    /**
     * 获取列表
     */
    public function actionList()
    {
        $model = new Matchemailignore();
        exit(json_encode($model->get_list($this->get_page_info())));
    }

    /**
     * 添加操作
     * @return string
     */
    public function actionAdd()
    {
        $model = new Matchemailignore();
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                exit(json_encode(["status" => 10]));
            }
            $model->addError("status", 20);
            exit(json_encode($model->getErrors()));
        }
        return $this->render("add", ["model" => $model]);
    }

    /**
     * 删除操作
     * @param $id
     */
    public function actionDel($id)
    {
        $model = new Matchemailignore();
        if ($model->delete_all([$id])) {
            exit(json_encode(["status" => 10]));
        }
        exit(json_encode(["status" => 20]));
    }

    /**
     * 修改页面展示
     * @return string
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get("id");
        $model = Matchemailignore::findOne($id);
        return $this->render("edit", ["model" => $model, "id" => $id]);
    }

    /**
     * 修改操作
     */
    public function actionEditdata()
    {
        $id = Yii::$app->request->post("id");
        $model = Matchemailignore::findOne(["id"=>$id]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
                exit(json_encode(["status"=>10]));
        }
        $model->addError("status",20);
        exit(json_encode($model->getErrors()));
    }
    
}