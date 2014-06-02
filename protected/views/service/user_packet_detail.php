<?php

$name = Yii::app()->name;
$title = "当前流量详情";
$this->pageTitle = $name." ".$title;


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
