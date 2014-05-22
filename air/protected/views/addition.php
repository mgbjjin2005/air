<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$title = "加油包介绍";
$this->pageTitle = $name." ".$title;

Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = $title;
Yii::app()->session['board_name'] = $title;
Yii::app()->session['board_msg']  = "1、加油包的流量为当月办理，立即生效。</br>";
Yii::app()->session['board_msg'] .= "2、加油包的购买次数和时间不受任何限制。随用随买。</br>";
Yii::app()->session['board_msg'] .= "3、加油包有效期为当月，月底如果没有使用完会被清零。所以亲请根据需要选择适合自己的加油包。</br>";
Yii::app()->session['board_msg'] .= "4、加油包为单独的流量购买行为，不具有连续性，如果下个月还需要加油包，需要重新购买，系统不会自动为你购买。</br>";

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
            <th colspan="3">加油包列表</th>
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
            <a href="service_open.html">办理</a>
        </tr>
     <?php endforeach; ?> 

    </tbody>
</table>

</div>

