<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 用户注册";

Yii::app()->session['nav'] = "site/register";
Yii::app()->session['nav_msg'] = "新用户注册";
Yii::app()->session['board_name'] = "注册须知";
Yii::app()->session['board_msg']  = "1、用户名只能是以下字符:字母、数字或下划线. 长度必须在6-16个字符之间。(可以是QQ号，手机号或微信号)。<br>";
Yii::app()->session['board_msg'] .= "2、密码长度必须在6-16之间，为了保证你的安全，请使用尽量复杂的密码。<br>";
Yii::app()->session['board_msg'] .= "3、请认真填写邮箱地址，一旦忘记账号，忘记密码或账号被盗，邮箱是你唯一能够找回自己账号的途径。";

?>

<div class="content">


<form name="register" action="http://www.wifi.com/index.php?r=site/doregister" method="post">
    <input type="hidden" name="ListID" value="27917287443">

    <label>用户名: (<span class="tip">字母或数字组成;最少4个字符</span>)
        <input type="text" name="user_name"  maxlength="16" id="user_name">
    </label>

    <label>密码: (<span class="tip">6到18个字符</span>)
        <input type="password" name="password" maxlength="16" id="password">
    </label>

    <label>重复密码:
        <input type="password" name="password2" maxlength="16" id="password2">
    </label>

    <label>找回密码邮箱:(<span class="tip">找回密码的唯一途径</span>)
        <input type="text" name="email" maxlength="64" id="email">
    </label>

    <input name="accept_protocol" type="checkbox">
    <span class="protocol">我同意Air-WIFI<a href="service_protocol.html">服务协议</a> </span>
    <div class="button"><a onclick="javascript:RegisterAction();">提交</a></div>
</form>

<script>

function RegisterAction()
{
    var user_name = document.register.user_name.value;
    var pwd = document.register.password.value;
    var pwd2 = document.register.password2.value;
    var email = document.register.email.value;

    if (user_name.search(/^[A-z0-9_.]{4,16}$/) < 0) {
        alert("用户名只能是以下字符:字母、数字和下划线. 长度必须在4-16个字符之间。(可以是QQ号，手机号或微信号)。\n");
        return false;
    }

    if (pwd.length < 6 || pwd.length > 16) {
        alert("密码长度必须在6-16之间。\n");
        return false;
    }

    if (pwd != pwd2) {
        alert("两次密码输入不一致\n");
        document.register.password.value = "";
        document.register.password2.value = "";
        return false;
    }

    if (email.search(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/) < 0) {
        alert("邮箱格式不正确。\n");
        return false;
    }

    document.register.submit();
}

</script>


</div>
