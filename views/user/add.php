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
        <form action="" id="user_add_form">
            <div class="form-group">
                <label for="user_name" class="col-sm-4 control-label">用户名:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="User[user_name]">
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
                    <button class="btn btn-sm btn-primary" id="user_add_post" type="button">添加</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        var user_add_info=(function(){
            return {
                add_url:"<?=Url::to(["user/add"])?>",
                varible:{}
            };
        })();
        $(".error").each(function(index,item){
            user_add_info.varible[$(item).attr("name")]=item;
        });
        $("#user_add_post").click(function(){
            var data=$("#user_add_form").serialize();
            $.ajax({
                url:user_add_info.add_url,
                type:"post",
                data:data,
                dataType:"json",
                success:function(data){
                    if(data.status==20){
                        for(var i in data){
                            if(user_add_info.varible[i]!==undefined){
                                $(user_add_info.varible[i]).html(data[i]);
                            }
                        }
                    }else{
                        parent.base_action.callback_addaction();
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                        parent.layer.close(index);
                        //提示层
                        parent.layer.msg('添加成功');
                    }
                }
            });
        });
    });
</script>

