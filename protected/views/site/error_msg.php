<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 错误页";

Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = "错误信息";
Yii::app()->session['board_name'] = "公告栏";
Yii::app()->session['board_msg'] = $name."致力于为大家提供高速的WIFI服务；使用Air-WIFI不仅可以畅游互联网, 更有超过1000部超高清国内外大片可以不用等待立即观看。赶紧过来体验吧^_^";

?>

<div class="error_msg">
    <?php echo Yii::app()->session['msg']; ?>
</div>    
