<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/21
 * Time: 17:49
 */
namespace app\controllers;
use yii;
use app\models\User;

class UserController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * 用户列表页面
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderPartial("index");
    }

    /**
     * 获取用户json列表
     */
    public function actionList()
    {
        $model = new User();
        $page = Yii::$app->request->post("page");
        $rows = Yii::$app->request->post("rows");
        exit(json_encode($model->get_all($page, $rows)));
    }

    /**
     * 添加用户相关操作  ajax数据
     * @return string
     */
    public function actionAdd()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->renderPartial("add");
        }
        $model = new User();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            exit(json_encode(["status" => 10]));
        }
        $model->addError("status", 20);
        exit(json_encode($model->errors));
    }

    /**
     * 获取要修改的数据并展示出来
     * @param $id
     * @return string
     */
    public function actionEdit($id)
    {
        $model = new User();
        $data = $model->get_edit($id);
        return $this->renderPartial("edit", ["data" => $data]);
    }

    /**
     * 修改数据
     */
    public function actionEditdata()
    {
        $id = Yii::$app->request->post("id");
        $model = User::findOne($id);
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()) {
            exit(json_encode(["status",10]));
        }
        $model->addError("status",20);
        exit(json_encode($model->errors));
    }

    /**
     * 删除操作
     * @param $id
     */
    public function actionDel($id)
    {
        $delete_arr=[$id];
        $model = User::findOne($id);
        $status=20;
        if($model->deleteIn($delete_arr)){
            $status=10;
        }
        exit(json_encode(["status"=>$status]));
    }

}