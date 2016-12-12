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
                <label for="user_name" class="col-xs-2 control-label">选择类型:</label>
                <div class="col-xs-10">
                    <?=Html::activeDropDownList($model,"email_type",ArrayHelper::map($types,"id","email_name"),["class"=>"form-control droplist"])?>
                    <span class="help-block m-b-none error"  name="email_type"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">账号名称:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="Account[account_name]" value="<?=$data['account_name']?>">
                    <span class="help-block m-b-none error"  name="account_name"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">账号密码:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="Account[account_password]" value="<?=$data['account_password']?>">
                    <span class="help-block m-b-none error"  name="account_password"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label col-xs-2">使用者:</label>
                <div class="col-xs-10">
                    <input type="text" name="Account[name]" class="form-control" value="<?=$data['name']?>">
                    <span class="help-block error" name="name"></span>
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
                edit_url:"<?=Url::to(["account/editdata"])?>",
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

