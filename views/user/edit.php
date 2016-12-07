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
    #user_add_html{
        padding-top:20px;
    }
</style>
<div id="user_add_html">
    <div class="user_add_content">
        <form action="" id="user_edit_form">
            <input type="hidden" name="id" value="<?=$data["id"]?>">
            <div class="form-group">
                <label for="user_name" class="col-sm-4 control-label">用户名:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="User[user_name]" value="<?=$data['user_name']?>">
                    <span class="help-block m-b-none error"  name="user_name"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-4 control-label">密码:</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" name="User[password]">
                    <span class="help-block m-b-none error" name="password"></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-8">
                    <button class="btn btn-sm btn-primary" id="user_edit_post" type="button">修改</button>
                    <button class="btn btn-sm btn-primary" id="user_back" type="button">取消</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        var user_edit_info=(function(){
            return {
                edit_url:"<?=Url::to(["user/editdata"])?>",
                varible:{}
            };
        })();
        $(".error").each(function(index,item){
            user_edit_info.varible[$(item).attr("name")]=item;
        });
        $("#user_edit_post").click(function(){
            var data=$("#user_edit_form").serialize();
            $.ajax({
                url:user_edit_info.edit_url,
                type:"post",
                data:data,
                dataType:"json",
                success:function(data){
                    if(data.status==20){
                        for(var i in data){
                            if(user_edit_info.varible[i]!==undefined){
                                $(user_edit_info.varible[i]).html(data[i]);
                            }
                        }
                    }else{
                        parent.base_action.callback_editaction();
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                        parent.layer.close(index);
                        //提示层
                        parent.layer.msg('修改成功');
                    }
                }
            });
        });
        //取消按钮
        $("#user_back").click(function(){
            base_action.close_parent_window();
        });
    });
</script>

