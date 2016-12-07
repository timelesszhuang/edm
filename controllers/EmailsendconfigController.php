<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/5
 * Time: 16:51
 */
namespace app\controllers;
use yii;
use app\models\Emailsendconfig;
use app\models\Emailtemplate;
class EmailsendconfigController extends BaseController
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
     * 添加页面展示 接收数据
     * @return mixed
     */
    public function actionAdd()
    {
        $model=new Emailsendconfig();
        if(Yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) && $model->save()){
                exit(json_encode(["status"=>10]));
            }
            $model->addError("status",20);
            exit(json_encode($model->getErrors()));
        }
        $template=(new Emailtemplate)->get_all();
        return $this->renderPartial("add",["model"=>$model,"provinces"=>Emailsendconfig::provinces(),"brands"=>$model->brands(),"template"=>$template]);
    }

    /**
     * 获取所有列表数据
     */
    public function actionList()
    {
        $model=new Emailsendconfig();
        exit(json_encode($model->get_all($this->get_page_info())));
    }

    /**
     * 修改展示页面
     * @return mixed
     */
    public function actionEdit()
    {
        $id=Yii::$app->request->get("id");
        $model=new Emailsendconfig();
        $data=$model->find()->where(["id"=>$id])->asArray()->one();
        $model->province_id=$data["province_id"];
        $model->brand_id=$data["brand_id"];
        $model->template_id=$data["template_id"];
        $template=(new Emailtemplate)->get_all();
        return $this->renderPartial("edit",["data"=>$data,"model"=>$model,"provinces"=>Emailsendconfig::provinces(),"brands"=>$model->brands(),"template"=>$template]);
    }

    /**
     * 修改数据
     */
    public function actionEditdata()
    {
        $id=Yii::$app->request->post("id");
        $model=Emailsendconfig::findOne($id);
        if($model->load(Yii::$app->request->post()) && $model->save()){
            exit(json_encode(["status"=>10]));
        }
        $model->addError("status",20);
        exit(json_encode($model->getErrors()));
    }

}