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
    #linkurl_add_html{
        padding-top:10px;
    }
    .control-label{
        text-align:right;
    }
    .droplist{
        padding:5px;
    }
</style>
<div id="linkurl_add_html">
    <div class="linkurl_add_content">
        <form action="" id="linkurl_add_form">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">选择类型:</label>
                <div class="col-xs-10">
                <?=Html::activeDropDownList($model,"type_id",ArrayHelper::map($types,"id","type_name"),["class"=>"form-control droplist"])?>
                    <span class="help-block m-b-none error"  name="type_id"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">链接名称:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="Linkurl[link_name]">
                    <span class="help-block m-b-none error"  name="link_name"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">链接地址:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="Linkurl[link_url]">
                    <span class="help-block m-b-none error"  name="link_url"></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 control-label">
                    <button class="btn btn-sm btn-primary" id="linkurl_add_post" type="button">添加</button>
                    <button class="btn btn-sm btn-info" id="linkurl_back" type="button">取消</button>

                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        var user_add_info=(function(){
            return {
                add_url:"<?=Url::to(["linkurl/add"])?>",
            };
        })();

        $("#linkurl_add_post").click(function(){
            var data=$("#linkurl_add_form").serialize();
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
        $("#linkurl_back").click(function(){
            base_action.close_parent_window();
        });
    });
</script>

