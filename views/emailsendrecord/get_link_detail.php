<!----该文件是打开窗体之后的页面-->
<?php
$page_id="email_template_bycusid";
echo $this->render("_public");
?>
    <table class="table table-hover table-bordered" style="margin-bottom: 10px !important;">
       <tr>
           <th>IP:</th>
           <th>打开时间:</th>
           <th>客户端信息:</th>
       </tr>
       <?php foreach($data as $v){?>
        <tr>
            <td><?=$v["ip_info"]?></td>
            <td><?=$v["time"]?></td>
            <td><?=$v["user-agent"]?></td>
        </tr>
       <?php }?>
   </table>

