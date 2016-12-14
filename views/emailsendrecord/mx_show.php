<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 14:37
 */
echo $this->render("_public");
?>
        <table class="table table-hover table-bordered" style="margin-bottom: 10px !important;">
            <tbody>
            <tr>
                <td colspan="7">网站标题</td>
            </tr>
            <tr>
                <td class="t-left ">域名：</td>
                <th class='view_content'>
                    <span id="mx_whois_domain"><a href="http://{$whois.domain_name}" target="_blank"><?=$whois['domain_name']?></a></span>&nbsp;
                </th>
                <td class="t-left">网站标题</td>
                <th colspan="7">
                    <?=$whois['wwwtitle']?>
                </th>
            </tr>
            <tr>
                <th colspan="7">mx信息</th>
            </tr>
            <tr>
                <td class="t-left ">优先级别：</td>
                <th class='view_content'><?=$mx['priority']?></th>
                <td class="t-left ">mx：</td>
                <th class='view_content'><?=$mx["mx"]?></th>
                <td class="t-left ">品牌：</td>
                <th class='view_content'><?=$mx["brand_name"]?></th>
            </tr>
            <?php if(!empty($mx["old_priority"]) && !empty($mx["old_mx"] && !empty($mx["old_brand_name"]))){?>
            <tr>
                <td class="t-left ">先前优先级：</td>
                <th class='view_content'><?=$mx['old_priority']?></th>
                <td class="t-left ">先前mx：</td>
                <th class='view_content'><?=$mx['old_mx']?></th>
                <td class="t-left ">先前邮箱品牌：</td>
                <th class='view_content'><?=$mx['old_brand_name']?></th>
            </tr>
            <?php }?>
            <tr>
                <td colspan="7">whois信息</td>
            </tr>
            <tr>

                <td class="t-left ">注册机构：</td>
                <th class='view_content'><?=$whois['registrar_name']?></th>
                <td class="t-left ">联系人邮箱：</td>
                <th class='view_content' colspan="7"><?=$whois['contact_email']?></th>
            </tr>
            <tr>
                <td class="t-left ">whois服务器的ip：</td>
                <th class='view_content'><?=$whois['server_name']?></th>
                <td class="t-left ">域名申请时间：</td>
                <th class='view_content'><?=$whois['createdate']?></th>
                <td class="t-left ">更新时间：</td>
                <th class='view_content'><?=$whois['updatedate']?></th>
            </tr>
            <tr>
                <td class="t-left ">过期时间：</td>
                <th class='view_content'><?=$whois['expiresdate']?></th>
                <td class="t-left ">注册者名称：</td>
                <th class='view_content'><?=$whois['registrant_name']?></th>
                <td class="t-left ">注册者详细地址：</td>
                <th class='view_content'><?=$whois['registrant_street']?></th>
            </tr>
            <tr>
                <td class="t-left ">注册城市：</td>
                <th class='view_content'><?=$whois['registrant_city']?></th>
                <td class="t-left ">注册省市：</td>
                <th class='view_content'><?=$whois['registrant_state']?></th>
                <td class="t-left ">注册者电话：</td>
                <th class='view_content'><?=$whois['registrant_telephone']?></th>
            </tr>
            </tbody>
        </table>