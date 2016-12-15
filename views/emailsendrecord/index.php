<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 14:37
 */
use yii\helpers\Url;
echo $this->render("_public");
?>

<style>
    #header {
        margin-top: 20px;
    }
</style>
<div id="header">
    <div class="row">
        <div class="col-sm-12">
            <div class="col-sm-6 ">
                <button class="btn btn-outline btn-primary" id="email_record_add">添加</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class='col-md-12' style="padding:20px !important;">
        <table class="table table-condensed table-hover table-bordered" style="">
            <thead>
            <tr class="success">
                <th class="select_checkbox"></th>
                <th>当前配置描述</th>
                <th>阅读次数</th>
                <th>链接阅读次数</th>
                <th>邮件地址</th>
                <th>发送ip</th>
                <th>发送时间</th>
                <th>最后查看时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="datalist_show_div">
            </tbody>
        </table>
    </div>
</div>
<!--分页操作-->
<div class="row" style="margin-right:60px;">
    <div class="col-sm-12 row" style="text-align:center;">
        <div class="pull-right">
            <ul class="pagination pagination-sm">
                <li><a href="javascript:void(0);" aria-label="Previous" id="previous"><span
                            aria-hidden="true">上一页</span></a></li>
                <li><a href="javascript:void(0);" aria-label="Next" id="next"><span aria-hidden="true">下一页</span></a>
                </li>
            </ul>
            <ul class="pagination pagination-sm">
                <li><span>共 <span style="color: #010001" class="page_allnum">0</span> 页</span></li>
                <li><span>共 <span style="color: #010001" class="allrows">0</span> 条记录</span></li>
            </ul>
            <ul class="pagination pagination-sm">
                <li><span>跳转到：<input type="number" value="1" name="cuerrent_page" style="width:40px;"></span>
                </li>
            </ul>
        </div>
    </div>
</div>
<input type="hidden" name="_csrf" id="csrf" value='<?=Yii::$app->request->getCsrfToken() ?>'>
<script type="text/javascript">
    $(function () {
        //下面是关于表格方面的
        var pagesize = 6;
        //初始化之后加载数据
        var current_num = 1;
        var flag = 'init';
        var allrows = 0;
        load_data(current_num, allrows, flag);
        /**
         * 点击前一页
         */
        $('#previous').click(function () {
            var allpage_num = parseInt($('.page_allnum').html());
            var allrows = parseInt($('.allrows').html());
            var current_num = parseInt($('input[name="cuerrent_page"]').val());
            if (current_num === 1) {
                //当前就是第一页 提示
                layer.msg('当前为第一页');
                return;
            }
            if (current_num <= 0) {
                layer.msg('当前页数有误');
                return;
            }
            current_num = current_num - 1;
            $('input[name="cuerrent_page"]').val(current_num);
            var flag = 'previous';
            load_data(current_num, allrows, flag);
        });
        /**
         * 点击下一页
         */
        $('#next').click(function () {
            var allpage_num = parseInt($('.page_allnum').html());
            var allrows = parseInt($('.allrows').html());
            var current_num = parseInt($('input[name="cuerrent_page"]').val());
            if (current_num === allpage_num || allpage_num === 0) {
                layer.msg('当前为最后一页');
                return;
            }
            current_num = current_num + 1;
            $('input[name="cuerrent_page"]').val(current_num);
            var flag = 'next';
            load_data(current_num, allrows, flag);
        });
        /**
         * 页面跳转之后的操作
         */
        $('input[name="cuerrent_page"]').blur(function () {
            var allpage_num = parseInt($('.page_allnum').html());
            var allrows = parseInt($('.allrows').html());
            var jump_num = parseInt($(this).val());
            if (jump_num > allpage_num || jump_num <= 0) {
                layer.msg('请输入有效的数字');
                return;
            }
            var flag = 'jump';
            load_data(jump_num, allrows, flag);
        });

        /**
         * 加载数据
         */
        function load_data(current_num, allrows, flag) {
            var url = '<?=Url::to(["list"]);?>';
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: {
                    page: current_num,
                    rows: pagesize,
                    flag: flag,
                    allrows: allrows,
                    _csrf:$('#csrf').val()
                },
                success: function (data) {
                    if (data.status == '10') {
                        if (flag == 'init') {
                            //初始化 设置总的数量 还有 页数
                            $('.page_allnum').html(data.allpagenum);
                            $('.allrows').html(data.allrows);
                        }
                        $('#datalist_show_div').html(data.table);
                    } else {
                        $.messager.alert('获取列表状态', data.msg, 'error');
                    }
                }
            });
        };
        $("body").undelegate(".mx_check","click");
        //点击查看mx
        $("body").delegate(".mx_check","click",function(){
            var id=$(this).attr("_id");
            layer.open({
                type: 2,
                title: '详情页',
                shadeClose: true,
                shade: true,
                maxmin: true, //开启最大化最小化按钮
                area: ['80%', '80%'],
                content: '<?=Url::to(["emailsendrecord/mx_show"])?>'+"&id="+id
            });
        });
        $("body").undelegate(".click_more","click");
        //点击查看ip
        $("body").delegate(".click_more","click",function(){
            var id=$(this).attr("_id");
            layer.open({
                type: 2,
                title: '详情页',
                shadeClose: true,
                shade: true,
                maxmin: true, //开启最大化最小化按钮
                area: ['80%', '80%'],
                content: '<?=Url::to(["emailsendrecord/get_link_detail"])?>'+"&id="+id
            });
        });
    });
</script>