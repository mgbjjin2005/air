<?php
Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = "电影测试";
?>

<video poster='<?php echo $media['m_pic_path']?>' width="200" height="300" controls="controls">
    <source src="<?php echo $media_info['m_video_path']?>">
</video>
