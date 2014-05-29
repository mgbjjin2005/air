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
    <colgroup>
        <col class="span2">
        <col class="span4">
    </colgroup>
    <thead>
        <tr>
            <th colspan="2">账户信息总览</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>用户名称</td>
            <td><?php echo $user_name;?></td>
        </tr>

        <tr>
            <td>本月消费</td>
            <td>35.0 元</td>
        </tr>

        <tr>
            <td>电影券</td>
            <td><?php echo sprintf("%.2f", $movie_tickets);?>元<a href="index.php?r=site/charge"> 查看有效期</a></td>
        </tr>

        <tr>
            <td>账户余额</td>
            <td><?php echo sprintf("%.2f",$balance);?> 元 <a href="index.php?r=site/charge">充值</a></td>
        </tr>

        <tr>
            <td>绑定状态</td>
            <td>已绑定 <a href="index.php?r=site/bindinfo">了解详情</a></td>
        </tr>

    </tbody>
</table>

<table class="table table-bordered table-striped">
    <colgroup>
        <col class="span2">
        <col class="span4">
    </colgroup>
    <thead>
        <tr>
            <th colspan="2">本月流量信息</th>
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
            <td><?php echo sprintf("%.2f", $traffic_remain);?>MB 
                <a href="index.php?r=site/trafficdetail">查看有效期</a>
            </td>
        </tr>

    </tbody>
</table>

</dir>
