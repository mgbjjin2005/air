<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 信息";

Yii::app()->session['nav'] = "site/userinfo";
Yii::app()->session['nav_msg'] = "信息";
Yii::app()->session['board_name'] = "公告栏";
Yii::app()->session['board_msg'] = $name."致力于为大家提供高速的WIFI服务；使用Air-WIFI不仅可以畅游互联网, 更有超过1000部超高清国内外大片可以不用等待立即观看。赶紧过来体验吧^_^";

$msg = Yii::app()->session["msg"];
$ret_url = Yii::app()->session["ret_url"];
$button_msg = Yii::app()->session["button_msg"];

?>

<div class="error_msg">
    <?php echo $msg;?>
</div>    
    <div class="button"><a  href="<?php echo $ret_url;?>"><?php echo $button_msg;?></a></div>
</div>
