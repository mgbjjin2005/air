<?php
$name = Yii::app()->name;
$title = "放映厅(".$media_info['m_chs_desc'].")";
$this->pageTitle = $name." ".$title;

Yii::app()->session['nav'] = "tv";
Yii::app()->session['nav_msg'] = $title;
Yii::app()->session['board_name'] = $title;
Yii::app()->session['board_msg']  = "1、浏览本网站所产生的流量算内部流量，不会计入计费流量。</br>";
Yii::app()->session['board_msg'] .= "2、所有的外语片均有中文字幕，可以放心的选择观看。</br>";
Yii::app()->session['board_msg'] .= "3、不同的浏览器对视频的播放能力差别很大，建议优先尝试使用猎豹/UC/百度浏览器进行观看.</br>";

?>

<div align = "center">
<video poster='<?php echo $media['m_pic_path']?>' width="200" height="300" controls="controls">
    <source src="<?php echo $media_info['m_video_path']?>">
</video>
<br>
</div>
