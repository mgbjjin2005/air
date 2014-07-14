<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 交易记录";

Yii::app()->session['nav'] = "site/userinfo";
Yii::app()->session['nav_msg'] = "交易记录";
Yii::app()->session['board_name'] = "信息栏";
Yii::app()->session['board_msg'] = $name."致力于为大家提供高速的WIFI服务；使用".$name."不仅可以畅游互联网, 更有超过1000部超高清国内外大片可以不用等待立即观看。赶紧过来体验吧^_^";


?>

<div class="content">
<?php foreach($ret as $row): ?>
<table class="table table-bordered table-striped">
        <tr>
            <td>时间</td>
            <td><?php print($row['create_date']);?></td>
        </tr>

        <tr>
            <td>描述</td>
            <td><?php echo $row['msg'];?></td>
        </tr>

        <tr>
            <td>金额变化</td>
            <td><?php print($row['change_quota']);?>元</td>
        </tr>

</table>
 <?php endforeach; ?>

 </div>
