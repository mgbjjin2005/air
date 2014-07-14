<?php

$name = Yii::app()->name;
$title = "流量开通详情";
$this->pageTitle = $name." 流量开通详情";

Yii::app()->session['nav'] = "site/userinfo";
Yii::app()->session['nav_msg'] = "流量开通详情";
Yii::app()->session['board_name'] = "信息栏";
Yii::app()->session['board_msg'] = $name."致力于为大家提供高速的WIFI服务；使用".$name."不仅可以畅游互联网, 更有超过1000部超高清国内外大片可以不用等待立即观看。赶紧过来体验吧^_^";


?>

<div class="content">
<?php foreach($detail as $row): ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2"
            >
                    <?php if ($row['packet_category']=="packet") : ?>
                    套餐类型:
                    <?php else:?>
                    加油包类型:
                    <?php endif;?><?php echo $row['packet_desc']; ?></th>

        </tr>
    </thead>
    <tbody>
        <tr>
            <td>流量总额</td>
            <td><?php echo sprintf("%.2f", $row["quota"]);?> MB</td>
        </tr>
        <tr>
            <td>剩余</td>
            <td><?php echo sprintf("%.2f", $row["remain"]);?> MB</td>
        </tr>
        
        <tr>
            <td>状态</td>
            <td><?php echo $row["state_desc"] ;?></td>
        </tr>
        <tr>
            <td>有效期</td>
            <td>
                <?php echo $row["start_date"];?></br>至</br>
                <?php echo $row["stop_date"];?>
            </td>
        </tr>


    </tbody>
</table>
 <?php endforeach; ?>
</div>
