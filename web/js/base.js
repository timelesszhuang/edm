/**
 * Created by Administrator on 2016/11/23.
 */
var base_action = (function () {
    //公共对象
    var add_param={};
    var edit_param={};
    var del_param={};
    return {
        //添加操作
        show_addmodel: function (add_info) {
            if (add_info.width === undefined) {
                add_info.width = "500px";
            }
            if (add_info.height == undefined) {
                add_info.height = "280px"
            }
            add_param.callback=add_info.callback;
            add_param.current_num=add_info.current_num;
            add_param.allrows=add_info.allrows;
            add_param.flag=add_info.flag;

            layer.open({
                type: 2,
                title: add_info.title,
                maxmin: true,
                shadeClose: true, //点击遮罩关闭层
                area: [add_info.width, add_info.height],
                content: add_info.content
            });
        },
        //修改model
        show_editmodel: function (add_info) {
            if (edit_param.width === undefined) {
                add_info.width = "500px";
            }
            if (edit_param.height == undefined) {
                add_info.height = "280px"
            }
            edit_param.callback=add_info.callback;
            edit_param.current_num=add_info.current_num;
            edit_param.allrows=add_info.allrows;
            edit_param.flag=add_info.flag;
            layer.open({
                type: 2,
                title: add_info.title,
                maxmin: true,
                shadeClose: true, //点击遮罩关闭层
                area: [add_info.width, add_info.height],
                content: add_info.content
            });
        },
        //修改操作
        edit_action: function (id) {
            var info=edit_param;
            info.content=info.url + "&id=" + id;
            base_action.show_editmodel(info);
        },
        set_params: function (info) {
            if (edit_param.width === undefined) {
                edit_param.width = info.width;
            }
            if (edit_param.height == undefined) {
                edit_param.height = info.height;
            }
            edit_param.title=info.title;
            edit_param.url=info.url;
            edit_param.callback=info.callback;
            edit_param.current_num=info.current_num;
            edit_param.allrows=info.allrows;
            edit_param.flag=info.flag;
        },
        //删除操作
        del_action:function(id){
            //询问框
            layer.confirm('是否确定删除？', {
                title:'<span class="glyphicon glyphicon-remove"></span> &nbsp;删除',
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    url: del_param.url + "&id=" + id,
                    dataType: "json",
                    success: function (data) {
                        if (data.status == 10) {
                            layer.msg('删除成功');
                            del_param.callback(del_param.current_num,del_param.allrows,del_param.flag);
                        }else{
                            layer.msg('删除失败');
                        }
                    }
                });
            }, function(){

            });

        },
        set_del_param:function(info){
            del_param={
                url:info.url,
                callback:info.callback,
                current_num:info.current_num,
                allrows:info.allrows,
                flag:info.flag
            };
        },
        callback_addaction:function(){
            add_param.callback(add_param.current_num,add_param.allrows,add_param.flag);
        },
        callback_editaction:function(){
            edit_param.callback(edit_param.current_num,edit_param.allrows,edit_param.flag);
        },
        //关闭父窗口
        close_parent_window:function(){
            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
            parent.layer.close(index);
        },
        //遍历error 并清空
        each_error_flash:function(){
            var info=[];
            $(".error").each(function(index,item){
                $(item).html("");
                info[$(item).attr("name")]=item;
            });
            return info;
        },//发送ajax操作
        send_ajax_and_back:function(obj){
            $.ajax({
                url:obj.url,
                type:obj.type,
                data:obj.data,
                dataType:"json",
                success:function(data){
                    obj.func(data);
                }
            });
        }
    }
})();