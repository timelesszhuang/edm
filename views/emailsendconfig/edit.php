<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 9:44
 */
use yii\helpers\Url;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<?=$this->render("_public");?>
<style>
    #template_save_html{
        padding-top:10px;
    }
    .control-label{
        text-align:right;
    }
    .droplist{
        padding:3px;
    }
</style>
<div id="template_save_html">
    <div class="template_save_content">
        <form action="" id="template_edit_form">
            <input type="hidden" name="id" value="<?=$data["id"]?>">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">选择省份:</label>
                <div class="col-xs-10">
                    <?=Html::activeDropDownList($model,"province_id",array_flip($provinces),["class"=>"form-control droplist"])?>
                    <span class="help-block m-b-none error"  name="province_id"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-xs-2 control-label">选择品牌:</label>
                <div class="col-xs-10">
                    <?=Html::activeDropDownList($model,"brand_id",ArrayHelper::map($brands,"id","name"),["class"=>"form-control droplist"])?>
                    <span class="help-block m-b-none error" name="content"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-2 control-label">选择模板:</label>
                <div class="col-xs-10">
                    <?=Html::activeDropDownList($model,"template_id",ArrayHelper::map($template,"id","title"),["class"=>"form-control droplist"])?>
                    <span class="help-block m-b-none error" name="template_id"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">描述:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="Emailsendconfig[detail]" value="<?=$data['detail']?>">
                    <span class="help-block m-b-none error"  name="detail"></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 control-label">
                    <button class="btn btn-sm btn-primary" id="template_edit_post" type="button">修改</button>
                    <button class="btn btn-sm btn-info" id="template_back" type="button">取消</button>

                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        var user_edit_info=(function(){
            return {
                edit_url:"<?=Url::to(["emailsendconfig/editdata"])?>",
                varible:{}
            };
        })();
        $(".error").each(function(index,item){
            user_edit_info.varible[$(item).attr("name")]=item;
        });
        $("#template_edit_post").click(function(){
            var data=$("#template_edit_form").serialize();
            var info=base_action.each_error_flash();
            $.ajax({
                url:user_edit_info.edit_url,
                type:"post",
                data:data,
                dataType:"json",
                success:function(data){
                    if(data.status==20){
                        for(var i in data){
                            if(info[i]!==undefined){
                                $(info[i]).html(data[i]);
                            }
                        }
                    }else{
                        parent.base_action.callback_editaction();
                        base_action.close_parent_window();
                        //提示层
                        parent.layer.msg('修改成功');
                    }
                }
            });
        });
        //取消按钮
        $("#template_back").click(function(){
            base_action.close_parent_window();
        });
    });
</script>

