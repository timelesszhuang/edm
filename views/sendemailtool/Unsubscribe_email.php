<html>
<head>
    <title>邮件退订</title>
            <!--百度cdn 加速jquery-->
        <script src="http://libs.baidu.com/jquery/1.11.3/jquery.min.js"></script>
</head>
<body style="margin: auto; background: rgb(238, 238, 238) none repeat scroll 0% 0%; padding-top: 60px;"
      class="auto-1477709855787-parent auto-1477709855671-parent">
<div class="f-pf g-headwrap" id="j-fixed-head">
    <div id="j-appbanner" class="u-appbannerwrap"></div>
    <div class="g-hd f-bg1 m-yktNav " id="j-topnav">
        <div class="g-flow" style="position:relative;">
            <form>
                <input type="hidden" name="id" value="<?=$id?>">
                <input type="hidden" name="customer_table" value="<?=$customer_table?>">
            <div class="f-pr f-cb" style="width:400px;height:400;background:#fff;margin:0 auto;">
                <h2 style="text-align:center;color:green;font-weight:bold;padding-top:15px;">邮件退订</h2>
               <div style="text-align:center;margin-top:100px;">
                    继续订阅<input type="radio" name="customer_subscribe" value="1" checked>&nbsp;&nbsp;退订<input type="radio" value="0" name="customer_subscribe">
               </div>
               <div style="position:absolute;bottom:20%;left:47%;">
                <button type="button" style="border:0;width:100px;height:60px;cursor:pointer;" id="submit_customer_subscribe">确定</button>
               </div>
            </div>
        </form>
        </div>
    </div>
</div>
</body>
</html>

<script>
    $(function () {
        var modal_id = 'index_menulist';
        //表单的初始化
        $('#submit_customer_subscribe').click(function(){
                $.ajax({
                    url:"__MODULE__/Sendemailimg/check_unsubscribe_email",
                    type:"post",
                    data:$("form").serialize(),
                    dataType:"json",
                    success:function(data){
                        alert(data.msg);
                    }
                });
        });
    });
 
</script>

