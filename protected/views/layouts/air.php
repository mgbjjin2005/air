<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" />
    <script src="<?php echo Yii::app()->request->baseUrl;?>/js/jquery-1.8.2.min.js"></script>
</head>

<body>
<div class="top_menu">
	欢迎你,<a href="index.php?r=site/userinfo"><?php echo CHTML::encode(Yii::app()->session['username']);  ?></a>
    &nbsp;|&nbsp;<a href="site/message">8条未读消息</a>
</div>
<div class="logo">
	<h1>
		<a href="index.php?r=site/index"><?php echo Yii::app()->name;?></a>
		<span class="nav">&gt</span>
		<a href="index.php?r=site/<?php echo Yii::app()->session['nav']; ?>"><?php echo CHTML::encode(Yii::app()->session['nav_msg']); ?></a>
	</h1>
</div>
<div class="body_split"></div>
    <?php echo $content; ?>
<h2 class="board"><?php echo Yii::app()->session['board_name'];?></h2>

<div class="content">
    <p><?php echo Yii::app()->session['board_msg']; ?></p>
</div>

<div class="footer">
    <p>Copyright &copy; <?php echo date('Y') ?> by <a href="http://www.wifi.com"><?php echo Yii::app()->name;?></a></p>
</div>
</body>
</html>
