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
<style>
    #emailignore_save_html{
        padding-top:10px;
    }
    .control-label{
        text-align:right;
    }
</style>
<div id="emailignore_save_html">
    <div class="emailignore_save_content">
        <form action="" id="emailignore_edit_form">
            <input type="hidden" name="id" value="<?=$id?>">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">域名:</label>
                <div class="col-xs-10">
                    <?=Html::activeInput("text",$model,"match_str",["class"=>"form-control"])?>
                    <span class="help-block m-b-none error"  name="email" ></span>
                </div>
            </div>
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">描述:</label>
                <div class="col-xs-10">
                    <?=Html::activeInput("text",$model,"detail",["class"=>"form-control"])?>
                    <span class="help-block m-b-none error"  name="detail"></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 control-label">
                    <button class="btn btn-sm btn-primary" id="emailignore_edit_post" type="button">修改</button>
                    <button class="btn btn-sm btn-info" id="emailignore_back" type="button">取消</button>

                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        var user_edit_info=(function(){
            return {
                edit_url:"<?=Url::to(["editdata"])?>",
                varible:{}
            };
        })();
        $(".error").each(function(index,item){
            user_edit_info.varible[$(item).attr("name")]=item;
        });
        $("#emailignore_edit_post").click(function(){
            var data=$("#emailignore_edit_form").serialize();
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
        $("#emailignore_back").click(function(){
            base_action.close_parent_window();
        });
    });
</script>

