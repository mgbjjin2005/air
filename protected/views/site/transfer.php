<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 转账给好友";

Yii::app()->session['nav'] = "site/transfer";
Yii::app()->session['nav_msg'] = "转账给好友";
Yii::app()->session['board_name'] = "转账步骤";
Yii::app()->session['board_msg']  = "1、先填写好友的用户名，然后点\"信息查询\"按钮，此时会显示好友的邮箱信息。<br>";
Yii::app()->session['board_msg'] .= "2、确认好友的信息无误后，输入转账金额(转账金额在0.1元到200元之间)，点击转账会跳到再次确认页面，确认无误点击转账即可完成转账。<br>";

?>

<div class="content">

<form name="transfer" action="index.php?r=site/transfer" method="post">
    <input type="hidden" name="submit_category" value="" />
    <input type="hidden" name="balance" value="<?php print($user_balance)?>" />
    <input type="hidden" name="user_name" value="<?php print($user_name)?>" />
    <table class="table table-bordered table-striped">
        <tr>
            <td>我的账号</td>
            <td colspan="2"><span class="notice_info"><strong><?php print($user_name);?></strong></td>
        </tr>

        <tr>
            <td>账户余额</td>
            <td colspan="2"><?php print($user_balance)?>元</td>
        </tr>

        <tr>
            <td>好友账号</td>
            <td style="padding:1px 1px 1px 1px">
                <input type="text" name="friend_name" style="width:120px; margin:0px 0px 0px 4px" 
                 maxlength="16" value = "<?php print($friend_name); ?>">
            </td>
            <td> <a onclick="javascript:friend_query()">查询</a> </td>
        </tr>

        <tr> 
            <td> 好友邮箱</td>
            <td colspan="2">
                <span id="friend_email" class="notice_info"><?php print("$friend_email") ?></span>
            </td>
        </tr>

        <tr> 
            <td>转账金额</td> 
            <td style="padding:1px 1px 1px 1px ">
                <input type="text" name="transfer_quota" maxlength="6"  style="width:50px;margin:0px 0px 0px 4px" 
                value="<?php print("$transfer_quota")?>">
            </td>
            <td>元</td>
        </tr>

        <tr>
            <td></td>
            <td colspan="2"><a onclick="javascript:do_transfer()"><strong>转账</strong></a></td> 
        </tr>

    </table>

</form>

</div>

<script>

function friend_query()
{
    document.transfer.submit_category.value = "query";
    var friend_name = document.transfer.friend_name.value;
    var user_name = document.transfer.user_name.value;

    if (friend_name.search(/^[A-z0-9_.]{2,16}$/) < 0) {
        alert("请先填写正确的好友账号。\n");
        return false;
    }

    if (user_name == friend_name) {
        alert("不能给自己转账哦。\n");
        return false;
    }

    document.transfer.submit();
}


function do_transfer()
{
    document.transfer.submit_category.value = "do_transfer";
    var friend_name = document.transfer.friend_name.value;
    var friend_email = document.all["friend_email"].innerHTML;
    var transfer_quota = document.transfer.transfer_quota.value;
    var balance = document.transfer.balance.value;
    var user_name = document.transfer.user_name.value;

    if (friend_name.search(/^[A-z0-9_.]{2,16}$/) < 0) {
        alert("请先填写正确的好友账号。\n");
        return false;
    }

    if (user_name == friend_name) {
        alert("不能给自己转账哦。\n");
        return false;
    }

    if (friend_email.search( /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/) < 0) {
        alert("邮箱格式不正确，是不是忘记点击好友账号后面的查询了?\n");
        return false;
    }

    if (!(transfer_quota >= 0.1 && transfer_quota <= 200)) {
        alert("请填写正确的转账金额\n");
        return false;
    }

    if (transfer_quota > balance) {
        alert("你的账户余额不够哦\n");
        return false;
    }


    var msg = "请确认转账信息:\n我的账号:"+user_name+"\n好友账号:"+friend_name+"\n好友邮箱:"+friend_email+"\n转账金额:"+transfer_quota+"元";
    if (confirm(msg)) {
        document.transfer.submit();
    }
}

</script>

