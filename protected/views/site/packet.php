<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$title = "套餐介绍";
$this->pageTitle = $name." ".$title;

Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = $title;
Yii::app()->session['board_name'] = $title;
Yii::app()->session['board_msg']  = "1、套餐为包月形式，新的套餐办理后，老的套餐会在下月自动失效。</br>";
Yii::app()->session['board_msg'] .= "2、套餐的生效周期为当月办理，下月开始生效。</br>";
Yii::app()->session['board_msg'] .= "3、套餐流量的有效期为一个月，月底如果没有使用完会被清零。所以亲请根据需要选择适合自己的套餐。</br>";
Yii::app()->session['board_msg'] .= "4、每个月最后一天为出账期，不能办理套餐业务。</br>";
Yii::app()->session['board_msg'] .= "4、套餐具有连续性。办理后每个月最后一天会自动扣下个月的套餐费，扣费不成功(余额不足)套餐不生效。</br>";
Yii::app()->session['board_msg'] .= "5、如果下个月不想再使用任何套餐，请一定记得在本月最后一天之前办理";
Yii::app()->session['board_msg'] .= "&lt取消我的套餐&gt业务，防止系统误扣套餐费，给你带来不必要的损失。</br>";
Yii::app()->session['board_msg'] .= "6、如果本月流量不够需要增加本月的流量，请选择对应的加油包</br>";

?>

<div class="content">
<table class="table table-bordered table-striped">
    <colgroup>
        <col class="span1">
        <col class="span1">
        <col class="span1">
    </colgroup>
    <thead>
        <tr>
            <th colspan="3">套餐列表</th>
        </tr>
        <tr>
            <th>价格</th>
            <th>流量</th>
            <th>办理</th>
        </tr>
    </thead>
    <tbody>

    <?php foreach($set_t as $row): ?>
        <tr>
            <td><?php echo sprintf("%.2f", $row["price"]);?> 元</td>
            <td><?php echo sprintf("%.2f", $row["traffic"]);?>MB</td>
            <td>办理</td>
        </tr>
     <?php endforeach; ?> 

        <tr>
            <td colspan="2">取消我的套餐</td>
            <td>办理</td>
        </tr>
    </tbody>
</table>

</div>

