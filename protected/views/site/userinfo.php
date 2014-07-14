<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 我的账户";

Yii::app()->session['nav'] = "site/userinfo";
Yii::app()->session['nav_msg'] = "我的账户";
Yii::app()->session['board_name'] = "信息栏";
Yii::app()->session['board_msg']  = "月总流量: 本月可以使用的总流量.包含本月已经使用的和还没有使用的.<br>忙时流量: 7:00-23:00期间使用的总流量.<br>";
Yii::app()->session['board_msg'] .= "闲时流量: 23:00-7:00期间使用的总流量.<br>总使用量: 本月实际已经使用的流量.<br>";
Yii::app()->session['board_msg'] .= "计费流量: 本月计费的流量数。比如你本月用了100MB忙时流量，300MB闲时流量，按照目前闲时流量按照1/3折算，则本月算你用的流量数为100+(300/3) = 200MB.<br>";
Yii::app()->session['board_msg'] .= "剩余流量：目前剩余可用的流量数。请保持账户有剩余流量，流量用完后将会被下线或限速到3KB/秒";

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
            <td class="notice_info"><strong><?php echo $user_name;?></strong></td>
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
            <td><a href="index.php?r=service/movieTicketsDetail">详情</a></td>
        </tr>

        <tr>
            <td>账户余额</td>
            <td><?php echo sprintf("%.2f",$balance);?> 元</td>
            <td>
                <a href="index.php?r=service/disCharge">充值</a>,
                <a href="index.php?r=site/balanceDetail">明细</a>
            </td>
        </tr>

        <tr>
            <td>我的电影</td>
            <td ><a href = "index.php?r=site/videoList">电影列表</a></td>
            <td></td>
        </tr>

        <tr>
            <td>月总流量</td>
            <td><?php print(sprintf("%.1f", $traffic_bill) + sprintf("%.1f", $traffic_remain));?>MB</td>
            <td><a href="index.php?r=service/userPacketDetail">详情</a></td>
        </tr>


    </tbody>
</table>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2">本月流量使用明细</th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td>忙时流量</td>
            <td><?php echo sprintf("%.1f", $traffic_busy);?>MB</td>
        </tr>

        <tr>
            <td>闲时流量</td>
            <td><?php echo sprintf("%.1f", $traffic_idle);?>MB</td>
        </tr>

        <tr>
            <td>总使用量</td>
            <td> <?php echo sprintf("%.1f", $traffic_idle + $traffic_busy + $traffic_internal);?>MB</td>
        </tr>


        <tr>
            <td>计费流量</td>
            <td><span class="notice_info"><?php echo sprintf("%.1f", $traffic_bill);?>MB</span></td>
        </tr>

        <tr>
            <td>剩余流量</td>
            <td><span class="notice_info"><?php echo sprintf("%.1f", $traffic_remain);?>MB</span></td>
            </td>
        </tr>

    </tbody>
</table>

</dir>
