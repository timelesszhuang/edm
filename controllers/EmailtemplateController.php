<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 14:35
 */
namespace app\controllers;
use yii;
use app\models\Emailtemplate;
class EmailtemplateController extends BaseController
{
    /**
     * 模板主页面展示
     */
    public function actionIndex()
    {
        return $this->renderPartial("index");
    }

    /**
     * 模板添加操作
     */
    public function actionAdd()
    {
        if(Yii::$app->request->isPost){
            $model=new Emailtemplate();
            if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()){
                exit(json_encode(["status"=>10]));
            }
            $model->addError("status",20);
            exit(json_encode($model->getErrors()));
        }
        return $this->renderPartial("add");
    }

    /**
     * 列表展示页面
     */
    public function actionList()
    {
        list($page,$rows,$offset)=$this->get_page_info();
        $model=new Emailtemplate();
        exit(json_encode($model->get_list($page,$rows,$offset)));
    }

    /**
     * 修改展示页面
     * @return string
     */
    public function actionEdit()
    {
        $id=Yii::$app->request->get("id");
        $model=Emailtemplate::findOne($id);
        return $this->renderPartial("edit",["data"=>$model->get_one($id)]);
    }

    /**
     * 修改数据
     */
    public function actionEditdata()
    {
        $id=Yii::$app->request->post("id");
        $model=Emailtemplate::findOne($id);
        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()){
            exit(json_encode(["status"=>10]));
        }
        $model->addError("status",20);
        exit(json_encode($model->getErrors()));
    }

    /**
     * 批量删除
     */
    public function actionDel()
    {
        $id=Yii::$app->request->get("id");
        $model=Emailtemplate::findOne($id);
        $status=20;
        if($model->delete_record([$id])){
            $status=10;
        }
        exit(json_encode(["status"=>$status]));
    }
}