<?php
Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = "放映厅";

$name = Yii::app()->name;
$title = "放映厅(".$media_info['m_chs_desc'].")";
$this->pageTitle = $name." ".$title;

Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = $title;
Yii::app()->session['board_name'] = $title;
Yii::app()->session['board_msg']  = "1、如果长时间加载不上或比较卡顿，有可能是你用的浏览器有问题，请换一个浏览器再试(猎豹/UC/百度...)<br>";

?>

<div align = "center">
<video poster='<?php echo $media['m_pic_path']?>' width="200" height="300" controls="controls">
    <source src="<?php echo $media_info['m_video_path']?>">
</video>
<br>
</div>
