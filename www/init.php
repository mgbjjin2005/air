<?php

global $g_args, $g_air_db, $g_radius_db, $g_name;
session_start();

$g_name  = "Air";
$g_name .= "-WIFI";

require_once('include/conn.inc');
require_once('include/common.inc');
require_once('include/html_common.inc');
/*准备数据库连接*/

$g_air_db =  init_db("air");
$g_radius_db = init_db("radius");

/*如果还没有登录*/
if (!(isset($_SESSION["session_state"]) && $_SESSION["session_state"] == "yes")) {
    /*通过ip和radacct表自动关联登录用户*/
    $ip = $_SERVER["REMOTE_ADDR"];
    if ($ip == "10.0.222.222") {
        $msg  = "页面跳转出现未知问题，我们建议你: <br>1、使用UC/QQ/百度流量器打开www.wifi.com<br>";
        $msg .= "2、如果你已经是使用以上浏览器打开，是因为你刚从移动运营商环境切换到".$g_name.",请关闭你的浏览器，重新打开即可。<br>";
        html_print_err_result("$msg");
    }

    /*根据ip地址查用户信息*/
    $sql = " select username,acctstarttime,acctstoptime,callingstationid, framedipaddress from radacct ";
    $sql .= "where framedipaddress = '$ip' and acctstoptime is NULL;";

        $login_ret = air_get_query_result($sql, $g_radius_db);
        $count = count($login_ret);
        if($count != 1){
            html_print_err_result("认证过程出现未知问题，建议访问10.0.222.222,退出后重新登录后再试，count=$count");
        }

    foreach ($login_ret as $tuple) {
        $_SESSION["username"] = $tuple["username"];
        $_SESSION["mac"] = $tuple["mac"];
        $_SESSION["ip"] = $tuple["ip"];
        $_SESSION["login_state"] = "yes";
    }

};

?>
