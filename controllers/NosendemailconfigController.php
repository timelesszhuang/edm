<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/5
 * Time: 15:00
 */
namespace app\controllers;
use yii;
use app\models\UnsendEmail;
class NosendemailconfigController extends BaseController
{
    /**
     * 不发送邮件配置页面展示
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderPartial("index");
    }

    /**
     * 返回添加展示页面
     * @return string
     */
    public function actionAdd()
    {
        if(Yii::$app->request->isPost){
            $model=new UnsendEmail();
            if($model->load(Yii::$app->request->post()) && $model->save()){
                exit(json_encode(["status"=>10]));
            }
            $model->addError("status",20);
            exit(json_encode($model->getErrors()));
        }
        return $this->renderPartial("add");
    }

    /**
     * 获取所有列表
     */
    public function actionList()
    {
        $model = new UnsendEmail();
        exit(json_encode($model->get_list($this->get_page_info())));
    }

    /**
     * 修改页面展示
     * @return string
     */
    public function actionEdit()
    {
        $id=Yii::$app->request->get("id");
        $data=UnsendEmail::findOne($id);
        return $this->renderPartial("edit",["data"=>$data]);
    }

    /**
     * 修改数据
     */
    public function actionEditdata()
    {
        $id=Yii::$app->request->post("id");
        $model=UnsendEmail::findOne($id);
        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()){
            exit(json_encode(["status"=>10]));
        }
        $model->addError("status",20);
        exit(json_encode($model->getErrors()));
    }

    /**
     * 删除操作
     */
    public function actionDel()
    {
        $id=Yii::$app->request->get("id");
        $model=new UnsendEmail();
        if($model->del_all([$id])){
            exit(json_encode(["status"=>10]));
        }
        exit(json_encode(["status"=>20]));
    }
}