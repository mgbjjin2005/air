<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 我的账户";

Yii::app()->session['nav'] = "userinfo";
Yii::app()->session['nav_msg'] = "我的账户";
Yii::app()->session['board_name'] = "信息栏";
Yii::app()->session['board_msg'] = $name."致力于为大家提供高速的WIFI服务；使用".$name."不仅可以畅游互联网, 更有超过1000部超高清国内外大片可以不用等待立即观看。赶紧过来体验吧^_^";

?>

<div class="content">

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="3">账户信息总览</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>用户名称</td>
            <td><?php echo $user_name;?></td>
            <td></td>
        </tr>
        <!--
        <tr>
            <td>本月消费</td>
            <td>35.0 元</td>
        </tr>
        -->
        <tr>
            <td>电影豆</td>
            <td><?php echo sprintf("%.2f", $movie_tickets);?>豆</td>
            <td><a href="index.php?r=service/movieTicketsDetail"> 详情</a></td>
        </tr>

        <tr>
            <td>账户余额</td>
            <td><?php echo sprintf("%.2f",$balance);?> 元</td>
            <td>
                <a href="index.php?r=charge">充值</a>
                </br>
                <a href="index.php?r=charge/chargeDetail">历史记录</a>
            </td>
        </tr>

        <tr>
            <td>绑定状态</td>
            <td>已绑定 </td>
            <td><a href="index.php?r=site/bindinfo">详情</a></td>
        </tr>

    </tbody>
</table>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>本月流量</th>
            <th><a href="index.php?r=service/userPacketDetail">当前开通详情</a></th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td>已用忙时流量</td>
            <td><?php echo sprintf("%.2f", $traffic_busy);?>MB</td>
        </tr>

        <tr>
            <td>已用闲时流量</td>
            <td><?php echo sprintf("%.2f", $traffic_idle);?>MB</td>
        </tr>

        <tr>
            <td>已用电影流量</td>
            <td><?php echo sprintf("%.2f",$traffic_internal)?>MB</td>
        </tr>

        <tr>
            <td>已使用总流量</td>
            <td> <?php echo sprintf("%.2f", $traffic_idle + $traffic_busy + $traffic_internal);?>MB</td>
        </tr>


        <tr>
            <td>计费流量</td>
            <td><?php echo sprintf("%.2f", $traffic_bill);?>MB</td>
        </tr>

        <tr>
            <td>剩余流量</td>
            <td><?php echo sprintf("%.2f", $traffic_remain);?>MB</td>
            </td>
        </tr>

    </tbody>
</table>

</dir>
