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
<style>
    #template_add_html{
        padding-top:10px;
    }
    .control-label{
        text-align:right;
    }
    .droplist{
        padding:5px;
        margin-bottom:5px;
    }
</style>
<div id="template_add_html">
    <div class="template_add_content">
        <form action="" id="template_add_form">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">邮件标题:</label>
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
                <label for="" class="col-xs-2 control-label">链接类型:</label>
                <div class="col-xs-10">
                    <select id="link_type_change" class="form-control droplist">
                    <option value="0">选择链接类型</option>
                        <?php foreach($linktype as $k=>$v){ ?>
                        <option value="<?=$v['id']?>"><?=$v['type_name']?></option>
                           <?php }?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-xs-2 control-label">选择链接:</label>
                <div class="col-xs-10">
                        <select id="link_select" class="form-control droplist">
                            <option value="0">选择链接</option>
                        </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-xs-2 control-label">生成链接:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" id="make_link">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-xs-2 control-label">内容:</label>
                <div class="col-xs-10">
                    <?= \cliff363825\kindeditor\KindEditorWidget::widget([
                        'name' => 'Emailtemplate[content]',
                        'options' => [], // html attributes
                        'clientOptions' => [
                            'width' => '587px',
                            'height' => '350px',
                            'themeType' => 'default', // optional: default, simple, qq
                            'langType' => \cliff363825\kindeditor\KindEditorWidget::LANG_TYPE_ZH_CN,
                            'uploadJson'=> Url::to(["emailtemplate/upload"])
                            ],
                    ]); ?>
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
                link_type_url:"<?=Url::to(["emailtemplate/get_link_bytypeid"])?>",
                ajax_callback:function(data){
                    if(data){
                        var append_arr=[];
                        data.forEach(function(item,index){
                            append_arr.push('<option value="'+item.click_url+'">'+item.link_name+'</option>');
                        });
                        $("#link_select").append(append_arr.join(""));
                    }
                }
            };
        })();

        //切换链接
        $("#link_select").change(function(){
            var val=$(this).val();
            if(val<1){
                return;
            }
            $("#make_link").val(val);
        });
        //切换链接类型
        $("#link_type_change").change(function(){
                var val=$(this).val();
                if(val<1){
                    return;
                }
        var obj={
            url:user_add_info.link_type_url,
            type:"get",
            data:{
                id:val
            },
            func:user_add_info.ajax_callback
        };
        base_action.send_ajax_and_back(obj);
        });



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

