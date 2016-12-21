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
use app\models\Linktype;
use app\models\Linkurl;
class EmailtemplateController extends BaseController
{
    public $enableCsrfValidation=false;
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
        $model=new Emailtemplate();
        if(Yii::$app->request->isPost){
            if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()){
                exit(json_encode(["status"=>10]));
            }
            $model->addError("status",20);
            exit(json_encode($model->getErrors()));
        }
        return $this->render("add",["linktype"=>(new Linktype)->get_all(),"model"=>$model]);
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
        return $this->render("edit",["model"=>$model,"data"=>$model->get_one($id),"linktype"=>(new Linktype)->get_all()]);
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

    /**
     * 根据typeid来获取链接类型
     * @param $id
     */
    public function actionGet_link_bytypeid($id)
    {
        exit(json_encode((new Linkurl())->get_by_typeid($id)));
    }

    /**
     * 执行全局action方法
     * @return array
     */
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'cliff363825\kindeditor\KindEditorUploadAction',
                'maxSize' => 2097152,
            ],
        ];
    }
}