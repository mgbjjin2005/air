<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 影视购买记录";

Yii::app()->session['nav'] = "site/userinfo";
Yii::app()->session['nav_msg'] = "影视购买记录(30天)";
Yii::app()->session['board_name'] = "信息栏";
Yii::app()->session['board_msg'] = $name."致力于为大家提供高速的WIFI服务；使用".$name."不仅可以畅游互联网, 更有超过1000部超高清国内外大片可以不用等待立即观看。赶紧过来体验吧^_^";


?>

<div class="content">
<?php foreach($ret_expire as $row): ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2"> 
                <a href="index.php?r=tv/toWatch&m_alias=<?php echo $row['m_alias']?>"><?php print(air_format_str($row['name'], 15)); ?></a>
            </th>
            
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>价格</td>
            <td><?php print($row['price']);?> 豆/元</td>
        </tr>

        <tr>
            <td>购买时间</td>
            <td><?php print($row['buy_date']);?></td>
        </tr>

        <tr>
            <td>过期时间</td>
            <td><?php print($row['expire_date']);?></td>
        </tr>

    </tbody>
</table>
 <?php endforeach; ?>

 
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2">已过期电影列表</th>
            
        </tr>
    </thead>
    <tbody>

    <?php foreach($ret_history as $row): ?>
        <tr>
            <td colspan = 2">
                <a href="index.php?r=tv/toWatch&m_alias=<?php echo $row['m_alias']?>"><?php print(air_format_str($row['name'], 15)); ?></a>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
</table> 


 </div>
