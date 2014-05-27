<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 业务办理";

Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = "业务办理";
Yii::app()->session['board_name'] = "公告栏";
Yii::app()->session['board_msg'] = $name."致力于为大家提供高速的WIFI服务；使用Air-WIFI不仅可以畅游互联网, 更有超过1000部超高清国内外大片可以不用等待立即观看。赶紧过来体验吧^_^";

?>

<div class="menu">
    <a href="index.php?r=site/addition">加油包</a>
    <a href="index.php?r=site/packet">套餐变更</a>
    <a href="index.php?r=site/charge">账户充值</a>
    <a href="index.php?r=site/present">WIFI币赠送</a>
</div>


