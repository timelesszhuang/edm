<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 19:03
 */
namespace app\controllers;

use yii;
use app\models\Linktype;

class LinktypeController extends BaseController
{
    /**
     * 链接类型主页展示
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
        if (Yii::$app->request->isPost) {
            $model = new Linktype();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                exit(json_encode(["status" => 10]));
            }
            $model->addError("status", 20);
            exit(json_encode($model->getErrors()));
        }
        return $this->renderPartial("add");
    }

    /**
     * 获取数据表格
     */
    public function actionList()
    {
        $model = new Linktype();
        exit(json_encode($model->get_list($this->get_page_info())));
    }

    /**
     * 修改页面展示
     * @param $id
     * @return string
     */
    public function actionEdit($id)
    {
        $model = Linktype::findOne($id);
        return $this->renderPartial("edit", ["data" => $model->get_one($id)]);
    }

    /**
     * 修改操作
     */
    public function actionEditdata()
    {
        $id=Yii::$app->request->post("id");
        $model=Linktype::findOne($id);
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
        $model=new Linktype();
        if($model->delete_all([$id])){
            exit(json_encode(["status"=>10]));
        }
        exit(json_encode(["status"=>20]));
    }

}