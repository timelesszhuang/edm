<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> - 邮件信息</title>
    <?php echo $this->render("_public");?>
</head>
<body class="gray-bg">
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="row row-sm text-center">
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="h1 text-info font-thin h1" id="email_yesterday_send">0</div>
                                    <span class="text-muted text-xs">昨天已发送</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-info">
                                    <div class="h1 text-fff font-thin h1" id="email_today_send">0</div>
                                    <span class="text-muted text-xs">今日已发送</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-primary">
                                    <div class="h1 text-fff font-thin h1" id="email_yesterday_read">0</div>
                                    <span class="text-muted text-xs">昨天阅读量</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="font-thin h1" id="email_today_read">0</div>
                                    <span class="text-muted text-xs">今天阅读量</span>
                                    <div class="bottom text-left">
                                        <i class="fa fa-caret-up text-warning m-l-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title" style="border-bottom:none;background:#fff;">
                                <h5>邮件服务器发送状态----每过一秒动态检测是否在发送</h5>
                            </div>
                            <div class="ibox-content" style="border-top:none;">
                                <div id="flot-line-chart-moving" style="height:217px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>所有项目</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="project-list">
                                    <table class="table table-hover">
                                        <tbody>
                                        <?php foreach($data as $k=>$v):?>
                                            <tr>
                                                <td class="project-status">
                                                    <span class="label label-primary"><?=$v["online"]?>
                                                </span></td>
                                                <td class="project-title">
                                                    <a href="javascript:void(0)"><?=$v["detail"]?></a>
                                                    <br>
                                                    <small>创建于 <?=$v["addtime"]?></small>
                                                </td>
                                                <td class="project-title">
                                                    <small style="font-size:13px;">阅读量: <?=$v["read_number"]?></small>
                                                </td>
                                                <td class="project-completion" colspan="2">
                                                        <small>当前进度： <?=$v["schedule"]?>%</small>
                                                        <div class="progress progress-mini">
                                                            <div style="width: <?=$v['schedule']?>%;" class="progress-bar"></div>
                                                        </div>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-2">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>正在发送邮件...</h5>
                    </div>
                    <div class="ibox-content">
                        <ul class="todo-list m-t small-list ui-sortable" id="email_ul">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 全局js -->
    <script src="js/jquery.min.js?v=2.1.4"></script>
    <script src="js/bootstrap.min.js?v=3.3.6"></script>
    <script src="js/plugins/layer/layer.min.js"></script>
    <!-- Flot -->
    <script src="js/plugins/flot/jquery.flot.js"></script>
    <script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="js/plugins/flot/jquery.flot.pie.js"></script>
    <!-- 自定义js -->
<!--    <script src="js/content.js"></script>-->
</body>
</html>
<script>
    $(function(){
        //基本信息
        var info=(function(){
            return {
                    all_info_url:"<?=Url::to(["statisticsinfo/get_info_total"])?>",
                    today_url:"<?=Url::to(["statisticsinfo/get_today_count"])?>",
                    email_info:"<?=Url::to(['statisticsinfo/get_email_info'])?>",
                    data:[],
                    send_num:0,
                    memcache_id:0,
                    insert_arr:[],
                    email_ul:$("#email_ul")
            };
        })();
        //获取今天、昨天数据信息
        $.ajax({
            url:info.all_info_url,
            dataType:"json",
            success:function(data){
                if(data){
                    init_move();
                    $("#email_today_send").html(data.today_count);
                    $("#email_yesterday_send").html(data.yesterday_count);
                    $("#email_yesterday_read").html(data.yesterday_read);
                    $("#email_today_read").html(data.today_read);
                    info.send_num=data.today_count;
                    setInterval(heart_jump,2000);
                }
            }
        });
        //获取正在发送的邮件的名称
        function sending_email_info(){
            $.ajax({
                url:info.email_info,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data){
                        info.insert_arr=[];
                        info.insert_arr.push('<li><a href="javascript::void(0);" class="check-link"><i class="fa fa-square-o"></i></a><span class="m-l-xs">');
                        info.insert_arr.push(data.send_email);
                        info.insert_arr.push('<br/><small class="label label-primary"><i class="fa fa-clock-o"></i>');
                        info.insert_arr.push(data.send_time+"</small></li>");
                        info.insert_arr=info.insert_arr.join("");
                        if(info.memcache_id>0){
                            if(info.memcache_id!=data.id){
                                info.email_ul.html();
                            }
                        }
                        info.email_ul.html(info.insert_arr);
                        info.memcache_id=data.id;
                    }
                }
            });
        }

        //心跳检测
        function heart_jump()
        {
            $.ajax({
                url:info.today_url,
                success:function(data){
                    if(data && data>info.send_num){
                        info.send_num=data;
                        $("#email_today_send").html(data*2);
                        init_move();
                    }
                }
            });
        }

        //移动图表
        function init_move(){
            var container = $("#flot-line-chart-moving");
            var maximum = container.outerWidth() / 2 || 300;
            var data = [];
            function getRandomData() {
                if (data.length) {
                    data = data.slice(1);
                }
                while (data.length < maximum) {
                    var previous = data.length ? data[data.length - 1] : 50;
                    var y = previous + Math.random() * 10 - 5;
                    data.push(y < 0 ? 0 : y > 100 ? 100 : y);
                }
                var res = [];
                for (var i = 0; i < data.length; ++i) {
                    res.push([i, data[i]])
                }
                return res;
            }
            series = [{
                data: getRandomData(),
                lines: {
                    fill: true
                }
            }];
            var plot = $.plot(container, series, {
                grid: {

                    color: "#999999",
                    tickColor: "#f7f9fb",
                    borderWidth:0,
                    minBorderMargin: 20,
                    labelMargin: 10,
                    backgroundColor: {
                        colors: ["#ffffff", "#ffffff"]
                    },
                    margin: {
                        top: 8,
                        bottom: 20,
                        left: 20
                    },
                    markings: function(axes) {
                        var markings = [];
                        var xaxis = axes.xaxis;
                        for (var x = Math.floor(xaxis.min); x < xaxis.max; x += xaxis.tickSize * 2) {
                            markings.push({
                                xaxis: {
                                    from: x,
                                    to: x + xaxis.tickSize
                                },
                                color: "#fff"
                            });
                        }
                        return markings;
                    }
                },
                colors: ["#4fc5ea"],
                xaxis: {
                    tickFormatter: function() {
                        return "";
                    }
                },
                yaxis: {
                    min: 0,
                    max: 110
                },
                legend: {
                    show: true
                }
            });
            setTimeout(function updateRandom() {
                series[0].data = getRandomData();
                plot.setData(series);
                plot.draw();
            }, 40);
        }



    });
</script>
