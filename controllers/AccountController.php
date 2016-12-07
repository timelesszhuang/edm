<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 15:56
 */
namespace app\controllers;
use yii;
use app\models\Account;
use app\models\Emailtype;
class AccountController extends BaseController
{
    /**
     * 主页面展示
     */
    public function actionIndex()
    {
        return $this->renderPartial("index");
    }

    /**
     * 添加
     * @return mixed
     */
    public function actionAdd()
    {
        $model=new Account();
        if(Yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) && $model->save()){
                exit(json_encode(["status",10]));
            }
            $model->addError("status",20);
            exit(json_encode($model->getErrors()));
        }
        return $this->renderPartial("add",["model"=>$model,"types"=>(new Emailtype())->get_all()]);
    }

    /**
     * 获取列表数据
     */
    public function actionList()
    {
        $model = new Account();
        exit(json_encode($model->get_list($this->get_page_info())));
    }

    /**
     * 编辑页面
     * @return string
     */
    public function actionEdit()
    {
        $id=Yii::$app->request->get("id");
        $model=new Account();
        $data=$model->find()->where(["id"=>$id])->asArray()->one();
        $model->email_type=$data["email_type"];
        return $this->renderPartial("edit",["data"=>$data,"model"=>$model,"types"=>(new Emailtype())->get_all()]);
    }

    /**
     * 修改数据
     */
    public function actionEditdata()
    {
        if(Yii::$app->request->isPost){
            $id=Yii::$app->request->post("id");
            $model=Account::findOne($id);
            if($model->load(Yii::$app->request->post()) && $model->save()){
                exit(json_encode(["status"=>10]));
            }
            $model->addError("status",20);
            exit(json_encode($model->getErrors()));
        }
    }

    /**
     * 根据id删除
     * @param $id
     */
    public function actionDel($id)
    {
        $model=new Account();
        if($model->del_all([$id])){
            exit(json_encode(["status"=>10]));
        }
        exit(json_encode(["status"=>20]));
    }
}