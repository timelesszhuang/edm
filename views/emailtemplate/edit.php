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
    #template_save_html{
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
<div id="template_save_html">
    <div class="template_save_content">
        <form action="" id="template_edit_form">
            <input type="hidden" name="id" value="<?=$data["id"]?>">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">标题:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="Emailtemplate[title]" value='<?=$data["title"]?>'>
                    <span class="help-block m-b-none error"  name="title" ></span>
                </div>
            </div>
            <div class="form-group">
                <label for="user_name" class="col-xs-2 control-label">描述:</label>
                <div class="col-xs-10">
                    <input type="text" class="form-control" name="Emailtemplate[detail]" value='<?=$data["detail"]?>'>
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
                    <textarea name="Emailtemplate[content]"  id="template_content"><?=$data["content"]?></textarea>
                    <span class="help-block m-b-none error" name="content"></span>
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
                edit_url:"<?=Url::to(["emailtemplate/editdata"])?>",
                varible:{},
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
        //表单的初始化
        KindEditor.create('#template_content', {
            allowFileManager: true,
            uploadJson: '',
            fileManagerJson: '',
            minWidth:552,
            minHeight:200,
            afterBlur: function () {
                this.sync();
                //这一句的作用：当失去焦点时执行 this.sync()
                //这个函数作用是同步KindEditor的值到textarea文本框。
            }
        });
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
                url:user_edit_info.link_type_url,
                type:"get",
                data:{
                    id:val
                },
                func:user_edit_info.ajax_callback
            };
            base_action.send_ajax_and_back(obj);
        });
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

