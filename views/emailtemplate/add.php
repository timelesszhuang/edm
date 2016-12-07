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
    #template_add_html{
        padding-top:10px;
    }
    .control-label{
        text-align:right;
    }
</style>
<div id="template_add_html">
    <div class="template_add_content">
        <form action="" id="template_add_form">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">标题:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="Emailtemplate[title]">
                    <span class="help-block m-b-none error"  name="title"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">描述:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="Emailtemplate[detail]">
                    <span class="help-block m-b-none error"  name="detail"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-xs-2 control-label">内容:</label>
                <div class="col-xs-10">
                    <textarea name="Emailtemplate[content]" class="form-control" cols="30" rows="10"></textarea>
                    <span class="help-block m-b-none error" name="content"></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 control-label">
                    <button class="btn btn-sm btn-primary" id="template_add_post" type="button">添加</button>
                    <button class="btn btn-sm btn-info" id="template_back" type="button">取消</button>

                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        var user_add_info=(function(){
            return {
                add_url:"<?=Url::to(["emailtemplate/add"])?>",
            };
        })();

        $("#template_add_post").click(function(){
            var data=$("#template_add_form").serialize();
            var info=base_action.each_error_flash();
            $.ajax({
                url:user_add_info.add_url,
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
                        parent.base_action.callback_addaction();
                        base_action.close_parent_window();
                        //提示层
                        parent.layer.msg('添加成功');
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

