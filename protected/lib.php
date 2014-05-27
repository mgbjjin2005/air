<?php

function airAutoLogin()
{
    if (!(isset(Yii::app()->session["login_state"]) &&
                Yii::app()->session["login_state"] == "yes"))
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($ip == "10.0.222.222") {
            $msg  = "打开页面出现问题，请参照以下建议处理: <br>";
            $msg .= "1、如果你刚从2G/3G/4G状态切换到wifi状态，请稍等10秒再刷新此页.<br>";
            $msg .= "2、请使用UC/QQ/百度流量器来浏览此网站。<br>";
            Yii::app()->session['msg'] = $msg;
            return false;
        }

        $sql  = "select username,acctstarttime,acctstoptime,callingstationid, framedipaddress from radacct ";
        $sql .= "where framedipaddress = '$ip' and acctstoptime is NULL;";
        $login_ret = Yii::app()->getDbByName("db_radius")->createCommand($sql)->queryAll();
        $count = count($login_ret);
        if($count != 1){
            $msg = "认证过程出现未知问题，请在浏览器中访问10.0.222.222,退出后重新登录后再来访问此页面，count=$count";
            Yii::app()->session['msg'] = $msg;
            return false;
        }
        
        $session = Yii::app()->session;
        foreach ($login_ret as $tuple) {
            Yii::app()->session['username'] = $tuple["username"];
            Yii::app()->session['mac'] = $tuple["callingstationid"];
            Yii::app()->session['ip'] = $tuple["framedipaddress"];
            Yii::app()->session['login_state'] = "yes";
            Yii::app()->session['msg'] = "";
            Yii::app()->session['nav'] = "";
            Yii::app()->session['nav_msg'] = "";
            Yii::app()->session['board_name'] = "";
            Yii::app()->session['board_msg'] = "";

            return true;
        }
    }

    //Yii::app()->session['msg'] = "正常";
    return true;
}

?>
