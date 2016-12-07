<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 10:36
 */
namespace app\controllers;

use yii;
use app\models\Linkurl;
use app\models\Linktype;

class LinkurlController extends BaseController
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
     * 添加操作
     * @return string
     */
    public function actionAdd()
    {
        $model = new Linkurl();
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                exit(json_encode(["status" => 10]));
            }
            $model->addError("status", 20);
            exit(json_encode($model->getErrors()));
        }
        $types = Linktype::get_all();
        return $this->renderPartial("add", ["model" => $model, "types" => $types]);
    }

    /**
     * 获取列表
     */
    public function actionList()
    {
        $model = new Linkurl;
        exit(json_encode($model->get_list($this->get_page_info())));
    }

    /*
     * 修改展示页面
     */
    public function actionEdit($id)
    {
        $model = new Linkurl();
        $types = new Linktype();
        $data=$model->get_one($id);
        $model->type_id=$data["type_id"];
        return $this->renderPartial("edit", ["model" => $model, "types" => $types::get_all(), "data" => $data]);
    }

    /**
     * 修改操作
     */
    public function actionEditdata()
    {
        $id=Yii::$app->request->post("id");
        $model=Linkurl::findOne($id);
        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()){
            exit(json_encode(["status"=>10]));
        }
        $model->addError("status",20);
        exit(json_encode($model->getErrors()));
    }

    /**
     * 删除
     * @param $id
     */
    public function actionDel($id)
    {
        $model=new Linkurl();
        if($model->delete_all([$id])){
            exit(json_encode(["status"=>10]));
        }
        exit(json_encode(["status"=>20]));
    }
}