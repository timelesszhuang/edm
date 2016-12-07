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
?>
<?=$this->render("_public");?>
<style>
    #nosendemailconfig_save_html{
        padding-top:10px;
    }
    .control-label{
        text-align:right;
    }
</style>
<div id="nosendemailconfig_save_html">
    <div class="nosendemailconfig_save_content">
        <form action="" id="nosendemailconfig_edit_form">
            <input type="hidden" name="id" value="<?=$data["id"]?>">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">标题:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="UnsendEmail[email]" value='<?=$data["email"]?>'>
                    <span class="help-block m-b-none error"  name="email" ></span>
                </div>
            </div>
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">描述:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="UnsendEmail[detail]" value='<?=$data["detail"]?>'>
                    <span class="help-block m-b-none error"  name="detail"></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 control-label">
                    <button class="btn btn-sm btn-primary" id="nosendemailconfig_edit_post" type="button">修改</button>
                    <button class="btn btn-sm btn-info" id="nosendemailconfig_back" type="button">取消</button>

                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        var user_edit_info=(function(){
            return {
                edit_url:"<?=Url::to(["nosendemailconfig/editdata"])?>",
                varible:{}
            };
        })();
        $(".error").each(function(index,item){
            user_edit_info.varible[$(item).attr("name")]=item;
        });
        $("#nosendemailconfig_edit_post").click(function(){
            var data=$("#nosendemailconfig_edit_form").serialize();
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
        $("#nosendemailconfig_back").click(function(){
            base_action.close_parent_window();
        });
    });
</script>

