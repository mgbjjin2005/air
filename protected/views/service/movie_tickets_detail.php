<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$title = "当前电影豆详情";
$this->pageTitle = $name." ".$title;


?>

<div class="content">
<?php foreach($detail as $row): ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2" 
            >
                    电影豆来自:<?php echo $row['packet_desc']; ?></th>
            
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>电影豆总额</td>
            <td><?php echo sprintf("%.2f", $row["quota"]);?> 豆</td>
        </tr>
        <tr>
            <td>剩余</td>
            <td><?php echo sprintf("%.2f", $row["remain"]);?>豆</td>
        </tr>
        <!--
        <tr>
            <td>状态</td>
            <td><?php echo $row["state_desc"] ;?></td>
        </tr>-->
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
