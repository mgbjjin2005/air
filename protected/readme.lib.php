<?php
/*该文件为函数使用说明，不会被实际使用*/

function airAutoLogin()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $username = "";
    if (isset($_COOKIE['username'])) {
        $username = $_COOKIE['username'];
    }

    if ($ip == "10.0.222.222") {
        if ($username == "") {
            $msg  = "自动登录过程出现问题，请参考如下方法解决此问题 <br>";
            $msg .= "1、如果你刚从2G/3G/4G状态切换到wifi状态，请稍等10秒再刷新此页.<br>";
            $msg .= "2、请使用猎豹/UC//百度流量器来浏览此网站。<br>";
            $msg .= "3、如果上面都不成功，请断掉wifi重新连接，对此给您带来的不便我们深表歉意。<br>";
            Yii::app()->session['msg'] = $msg;
            return false;
        }

        $sql  = "select username,acctstarttime,acctstoptime,callingstationid, framedipaddress from radacct ";
        $sql .= "where username = '$username' and acctstoptime is NULL;";

    } else {
        $sql  = "select username,acctstarttime,acctstoptime,callingstationid, framedipaddress from radacct ";
        $sql .= "where framedipaddress = '$ip' and acctstoptime is NULL;";
    }

    $login_ret = Yii::app()->getDbByName("db_radius")->createCommand($sql)->queryAll();
    $count = count($login_ret);
    if($count < 1) {
        $msg = "认证过程出现未知问题，请在浏览器中访问10.0.222.222,退出后重新登录后再来访问此页面，count=$count";
        Yii::app()->session['msg'] = $msg;
        return false;
    };

    $session = Yii::app()->session;
    foreach ($login_ret as $tuple) {
        $username = $tuple["username"];
        Yii::app()->session['username'] = $tuple["username"];
        Yii::app()->session['mac'] = $tuple["callingstationid"];
        Yii::app()->session['ip'] = $tuple["framedipaddress"];
        Yii::app()->session['login_state'] = "yes";
        Yii::app()->session['msg'] = "";
        Yii::app()->session['nav'] = "";
        Yii::app()->session['nav_msg'] = "";
        Yii::app()->session['board_name'] = "";
        Yii::app()->session['board_msg'] = "";
        $expire = time() + 86400 * 2;
        setcookie ("username", $username, $expire); 

        return true;
    }
    return true;
}

/*
参数：
user_name:用户名
video_id: 视频的ID，对应media_detail.auto_id

使用方法：
当用户点击购买视频的时候调用此函数

返回值
函数返回bool类型，true即表示购买成功，直接跳转到播放页面即可。
为false即表示购买失败.此时具体的失败信息在Yii::app()->session['msg']里面
*/

function air_video_buy($user_name, $video_id) {
    $ret = array();
    $m_id = 0;
    $mv_id = 0;
    $beans = 0;
    $traffic = 0;
    $balance = 0;

    $check_ret = array();
    $check_ret = air_check_user_buy($user_name, $video_id);
    if ($check_ret["flag"] != 'need_buy') {
        return false;
    }

    $m_id = $check_ret["m_id"];
    $mv_id = $check_ret["mv_id"];
    $mv_name = $check_ret["mv_name"];
    $beans = $check_ret["beans"];
    $balance = $check_ret["balance"];
    $traffic = $check_ret["traffic"];
    $price = $check_ret["price"];

    $quota_sql_arr = array();
    if ($beans > 0) {
        /*处理电影豆*/
        $sql  = "select auto_id, remain from user_quota where user_name = '$user_name' ";
        $sql .= "and state = 'enable' and category = 'beans' and stop_date > now() ";
        $sql .= "and remain > 0 order by stop_date;";
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        $count = count($set_t);
        if($count <= 0){
            Yii::app()->session['msg'] =  "没有查找到bean quota.";
            return false;
        }
        
        foreach ($set_t as $tuple) {
            $quota_id = $tuple["auto_id"];
            $remain = $tuple["remain"];
            $state = "enable";
            $state_desc = "使用中";
            if ($beans > $remain) {
                $beans -= $remain;
                $state = "disable";
                $state_desc = "已用完";
                $t_sql  = "update from user_quota set remain = 0, state='$state', ";
                $t_sql .= "state_desc = '$state_desc' where auto_id = $quota_id";
                array_push($quota_sql_arr, $t_sql);

            } else {
                if ($beans == $remain) {
                    $state = "disable";
                    $state_desc = "已用完";

                }

                $t_sql  = "update from user_quota set remain = remain - $beans, state='$state', ";
                $t_sql .= "state_desc = '$state_desc' where auto_id = $quota_id"; 
                array_push($quota_sql_arr, $t_sql);
                break;
            }
        }
    }
    
    $conn  = Yii::app()->getDbByName("db_air");
    $transaction = $conn->beginTransaction();

    try {
        foreach ($quota_sql_arr as $k){
            Yii::app()->getDbByName("db_air")->createCommand($k)->execute();
        }

        if ($balance > 0) {
            $t_sql = "update user_info set balance -= $balance where user_name = '$user_name'";
            Yii::app()->getDbByName("db_air")->createCommand($t_sql)->execute();
        }

        $start_date = Date("Y-m-d");
        $star_date .= " 00:00:00";
        $stop_stamp  = air_get_stamp_after_month($start_date, 1);
        $stop_date = date("Y-m-d H:i:s", $stop_stamp - 1);

        if ($traffic > 0) {
            $t_sql  = "insert into user_quota (user_name, category, quota, remain, deal_id, state, ";
            $t_sql .= "state_desc, packet_desc, packet_category, start_date, stop_date, create_date) values ";
            $t_sql .= "('$user_name', 'traffic', $traffic, $traffic, 0, 'enable', '未启用', ";
            $t_sql .= "'看电影"."$mv_name"."赠送', 'null', '$start_date', '$stop_date',now()) "; 
            Yii::app()->getDbByName("db_air")->createCommand($t_sql)->execute();
        }

        $mac = Yii::app()->session['mac'];

        $start_date = date("Y-m-d H:i:s");
        $stop_stamp  = air_get_stamp_after_day($start_date, 3);
        $stop_date = date("Y-m-d H:i:s", $stop_stamp - 1);

        $t_sql  = "insert into media_deal_info ";
        $t_sql .= "(m_id, mv_id, user_name, mac, price,m_chs_desc,create_date,expire_date) values ";
        $t_sql .= "(m_id,mv_id,'$user_name','$mac',$price,'$mv_name','$start_date','$stop_date')";
        Yii::app()->getDbByName("db_air")->createCommand($t_sql)->execute();

        $transaction->commit();

    } catch (Exception $e) {
        $transaction->rollBack();
        Yii::app()->session['msg'] = "交易失败，请重试，如果再有问题请联系管理员".$e->getMessage();
        return false;
    }

    return true;
}


/*
功能：
此函数会检查用户是否需要购买此电影，并给出相关的详细信息

参数：
user_name:用户名
video_id: 视频的ID，对应media_detail.auto_id

使用方法：
当用户点击具体电影视频的时候调用此函数

返回值
返回值为一个数组。数组恒有flag值。flag有以下可能的值
flag  = error;
此时发生了错误,或余额不足，具体的错误信息在Yii::app()->session['msg']里面

flag = already_buy;
表示用户已经购买过此视频并且还在有效期内，或此视频不需要购买，可以直接观看.

flag = need_buy;
表示用户需要购买此视频，提供给页面的信息包括:

$ret["mv_id"]   : 视频的id (对应media_detail.auto_id)
$ret["mv_name"] : 视频的显示名称 (对应media_detail.m_chs_desc)
$ret["price"]   : 视频的价格（一定大于0）
$ret["space"]   : 视频的大小(单位MB，可以不展示)
$ret["buy_msg"] : 视频的支付说明(展示出来即可);

支付上不需要用户自己做选择，系统会根据电影豆剩余及到期情况和账户余额自动做出选择。选取原则
1、优先选择使用电影豆支付，其次选择账户余额，电影豆的选择中优先选择最早过期的电影豆
2、如果电影豆有余额但是余额不足，会把电影豆余额用完，并用余额支付剩余的部分
3、使用电影豆不会获得流量赠送，使用余额支付将会获得流量赠送

具体的选择情况已经写在$ret["bug_msg"]里面了，把这个信息展示给用户即可，(信息比较长，一行展示不完)。

*/
function air_check_user_buy($user_name, $video_id) {
    $ret = array();
    $price = 0;
    $space = 0;
    $mv_name = "";
    $m_id = 0;
    $mac = Yii::app()->session['mac'];

    $sql = "select m_id, m_space, m_price,m_chs_desc from media_detail where auto_id = $video_id";
    $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
    $count = count($set_t);

    if($count > 0){
        $price = $set_t[0]["m_price"];
        $mv_name = $set_t[0]["m_chs_desc"];
        $space = $set_t[0]["m_space"];
        $m_id = $set_t[0]["m_id"];

    } else {
        $ret["flag"] = "error";
        Yii::app()->session['msg'] = "没有查到该视频的信息，请选择其他视频观看。";
        return;
    }

    if ($price <= 0) {
        $ret["flag"] = "already_buy";
        return $ret;
    }

    $sql  = "select deal_id from media_deal_info where user_name = '$user_name' ";
    $sql .= "and mac = '$mac' and m_id = $video_id and expire_date > now()"; 
    $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
    $count = count($set_t);
    if($count > 0){
        $ret["flag"] = "already_buy";
        return $ret;
    }
 
    /*检查电影豆*/
    $sql  = "select sum(remain) as value from user_quota where user_name = '$user_name' ";
    $sql .= "and  category = 'beans' and state = 'enable' and stop_date > now()";
    $beans = air_get_value_by_sql($sql);
    $ret["m_id"] = $m_id;
    $ret["mv_id"] = $video_id;
    $ret["flag"] = "need_buy";
    $ret["mv_name"] = $mv_name;
    $ret["price"] = $price;
    $ret["space"] = $space;

    if ($beans >= $price) {
        $ret["beans"] = $beans;
        $ret["balance"] = 0;
        $ret["traffic"] = 0;
        $ret["buy_msg"] = "电影豆充足，购买此电影需要消耗$price电影豆.";
        return $ret;
    }

    $sql  =  "select balance as value from user_info where user_name = '$user_name'";
    $balance = air_get_value_by_sql($sql);

    if ($beans + $balance >= $price) {
        $ramain = $balance - $beans;
        $traffic = sprintf("%.1f", $remain * 5);
        $ret["beans"] = $beans;
        $ret["balance"] = $remain;
        $ret["traffic"] = $traffic;

        if ($beans > 0) {
            $ret["buy_msg"]  = "购买此电影需要消耗".$beans."电影豆和".$remain;
            $ret["buy_msg"] .= "元账户余额。同时你将获得".$traffic."MB上网流量.";

        } else {
            $ret["buy_msg"] = "购买此电影需要消耗".$remain."元账户余额.";
            $ret["buy_msg"] .= "同时你将获得".$traffic."MB上网流量.";
        }

        return $ret;

    } else {
        $ret["flag"] = "error";
        Yii::app()->session['msg'] = "oh...no...，账户余额不足，赶紧去充值吧.";
        return;
    }

}

function air_get_value_by_sql($sql)
{
    $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
    $count = count($set_t);
    if ($count < 1) {
        return 0;
    }

    return $set_t[0]["values"];
}

function air_get_category($id_kind, $id_area, $id_type)
{
    $ret = array();
    $kind = array();
    $area = array();
    $type = array();
    $record = array();
    $selected_auto_id = 0;

    $sql  = "select auto_id, class, m_kind, m_name, value, parent_class_id ";
    $sql .= "from media_category order by class, value";
    $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
    $count = count($set_t);
    if($count <= 0){
        Yii::app()->session['msg'] =  "电影分类查找错误，请重试，如果还有问题请联系管理员";
        $this->render('error_msg');
        exit;
    }

    foreach ($set_t as $tuple) {
        $auto_id = $tuple["auto_id"];
        $class = $tuple["class"];
        $m_kind = $tuple["m_kind"];
        $m_name = $tuple["m_name"];
        $value = $tuple["value"];
        $parent_class_id = $tuple["parent_class_id"];

        if ($class == 1) {
            $obj = array();
            if ($m_kind == "video_kind") {
                $obj["name"] = $m_name;
                $obj["id"] = $value;
                if ($value == $id_kind) {
                    $obj["selected"] = 1;
                    $selected_auto_id = $auto_id;
                } else {
                    $obj["selected"] = 0;
                }
                $kind[] = $obj;

            } else if ($m_kind == "area") {
                $obj["name"] = $m_name;
                $obj["id"] = $value;
                if ($value == $id_area) {
                    $obj["selected"] = 1;
                
                } else {
                    $obj["selected"] = 0;
                }
                $area[] = $obj;
            }

        } else if ($class == 2) {
            $obj = array();
            if ($parent_class_id == $selected_auto_id) {
                $obj["name"] = $m_name;
                $obj["id"] = $value;
                if ($value == $id_type) {
                    $obj["selected"] = 1;
                
                } else {
                    $obj["selected"] = 0;
                }
                $type[] = $obj;
            }
        } /*end if*/
    }/*end foreach*/

    $ret["kind"] = $kind;
    $ret["area"] = $area;
    $ret["type"] = $type;
    return $ret;
}

/*参数：
id_kind: 大类目的标志位
id_area: 区域标志位
id_type: 小类目的标志位
page:当前页数，默认第一页
num:每一页的记录数
key:搜索关键字(默认空)

返回结果
ret["kind"] item("id", "name", "selected")
ret["area"] item("id", "name", "selected")
ret["type"] item("id", "name", "selected")
ret["records"] item();
ret["cur_page"] = 
ret["total_page"] = 
ret["total_record"] =
*/
function air_get_media_list($id_kind,$id_area,$id_type,$page,$keys)
{
    $ret = array();
    $category = array();

    $rows = 40;
    if ($id_kind <= 0) {
        $id_kind = 0;
        $id_type = 0;
    }

    if ($id_area <= 0) {
        $id_area = 0;
    }

    if ($page <= 0) {
        $page = 1;
    }

    $category = air_get_category($id_kind,$id_area,$id_type);
    $ret["kind"] = $category["kind"];
    $ret["area"] = $category["area"];
    $ret["type"] = $category["type"];

    $ret["total_records"] = 0;
    $ret["cur_page"] = $page;
    $ret["total_page"] = 0;

    $cond = "";
    if ($id_kind > 0) {
        $cond .= " and m_kind_flag & $id_kind != 0 ";
    }

    if ($id_area > 0) {
        $cond .= " and m_area_flag & $id_area != 0 ";
    }

    if ($id_type > 0) {
        $cond .= " and m_type_flag & $id_type != 0 ";
    }

    $key_len = strlen($keys);
    if ($key_len > 1) {
        $cond .= " and (m_chs_name like '%$keys%' or m_director like '%$keys%' or m_main_actors like '%$keys%') ";
    }


    $sql = "select count(*) as records from media where enable_state = 'enable' $cond";
    $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
	
	$count = count($set_t);
    if ($count == 1) {
        $ret["total_records"]  = $set_t[0]["records"];

    } else {
        return $ret;
    }

    $ret["total_page"] = ceil($ret["total_records"] / $rows);

    if ($page >= $ret["total_page"]) {
        $page = $ret["total_page"];
    }

    $low = ($page -1 ) * $rows;
	//BY haodan	
	$criteria=new CDbCriteria();
    $sql  = "select auto_id, m_chs_name,m_main_actors,m_douban_num,m_total_pv, m_pic_path ";
    $sql .= "from media where enable_state = 'enable'  $cond ";
    $sql .= "order by m_create_date desc ";
    //$sql .= "limit $low, $rows";

    $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->query();
	//BY haodan
	$pages=new CPagination($set_t->rowCount);
	$pages->pageSize=16; 
	$pages->applyLimit($criteria); 
	$set_t=Yii::app()->getDbByName("db_air")->createCommand($sql." LIMIT :offset,:limit"); 
	$set_t->bindValue(':offset', $pages->currentPage*$pages->pageSize); 
	$set_t->bindValue(':limit', $pages->pageSize); 
	$set_t=$set_t->queryAll();
	//end of haodan
	$count = count($set_t);
    if ($count < 1) {
        $ret["total_records"] = 0;
        return $ret;
    }

    foreach ($set_t as $tuple) {
        $obj = array();
        $obj["id"] = $tuple["auto_id"];
        $obj["name"] = $tuple["m_chs_name"];
        $obj["actors"] = $tuple["m_main_actors"];
        $obj["douban_num"] = $tuple["m_douban_num"];
        $obj["pv"] = $tuple["m_total_pv"];
        $obj["poster_url"] = $tuple["m_pic_path"];
        $ret["recoreds"][] = $obj;
    }
	$ret['pages']=$pages;

    return $ret;

}
/*
   添加套餐交易。
   start_date 格式：2014-05-29 12:15:36
 */
function air_add_packet_deal($user_name, $packet_id)
{
    $start_date = Date("Y-m-d H:i:s");
    //用户信息
    $sql = "select balance from user_info where user_name = '$user_name'";
    $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
    $count = count($set_t);
    if ($count != 1) {
        Yii::app()->session['msg'] =  "用户信息有误，请重新登录后再试，如果还有问题请联系管理员";
        return false;
    }

    $balance = $set_t[0]["balance"];

    //套餐信息
    $sql  = "select p_desc, traffic, period_month, movie_tickets, category, price ";
    $sql .= "from packet_info where packet_id=$packet_id and enable_state = 'enable'";
    $data_set = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
    $count = count($data_set);
    if ($count != 1) {
        Yii::app()->session['msg'] =  "套餐信息有误，请重新登录后再试，如果还有问题请联系管理员";
        return false;
    }

    $packet_desc          = $data_set[0]["p_desc"];
    $packet_price         = $data_set[0]["price"];
    $packet_traffic       = $data_set[0]["traffic"];
    $packet_category      = $data_set[0]["category"];
    $packet_period_month  = $data_set[0]["period_month"];
    $beans = $data_set[0]["movie_tickets"];


    if ($balance < $packet_price) {
        Yii::app()->session['msg'] =  "余额不足, 交易失败。";
        return false;
    }

    $start_stamp = air_get_stamp_after_month($start_date, 0);
    $stop_stamp  = air_get_stamp_after_month($start_date, $packet_period_month);
    $stop_date = date("Y-m-d H:i:s", $stop_stamp - 1);

    Yii::log("stop_date=$stop_date stop_stamp=$stop_stamp\n","info","sql");
    if ($start_stamp == 0) {
        Yii::app()->session['msg'] =  "时间参数格式有误，参考格式2013-02-06 04:10:28.";
        return false;
    }

    /*插入deal_id,获得deal_id*/
    $sql  = "insert into packet_deal (user_name, packet_id, price, state,create_date) ";
    $sql .= "values ('$user_name', $packet_id, $packet_price, 'init', now()); ";
    
    Yii::app()->getDbByName("db_air")->createCommand($sql)->execute();
    
    $sql = "select LAST_INSERT_ID() as id;";
    $data_set = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
    $count = count($data_set);
    $deal_id = 0;
    if ($count == 1) {
        $deal_id = $data_set[0]["id"];
    }

    if ($deal_id <= 0) {
        Yii::app()->session['msg'] = "交易表插入数据出错，请重试，如果再有问题请联系管理员";
        return false;
    }

    /*所有数据收集完毕，进行更新*/

    $conn  = Yii::app()->getDbByName("db_air");
    $transaction = $conn->beginTransaction();

    $sql  = "insert into user_quota (user_name, category, quota, remain, deal_id, state, ";
    $sql .= "state_desc, packet_desc, packet_category, start_date, stop_date, create_date) values ";
    $sql .= "('$user_name', 'traffic', $packet_traffic, $packet_traffic, $deal_id, 'enable', '未启用', ";
    $sql .= "'$packet_desc', '$packet_category', '$start_date', '$stop_date',now()) ";
    
    if ($beans > 0) {
        $sql .= ",('$user_name', 'beans',  $beans, $beans, $deal_id, 'enable', '未启用', ";
        $sql .= "'$packet_desc', '$packet_category', '$start_date', '$stop_date',now()) ;";
    }

    try {
        Yii::log($sql,"info","sql");
        Yii::app()->getDbByName("db_air")->createCommand($sql)->execute();
        $sql = "update packet_deal set state = 'done' where auto_id = $deal_id;";
        Yii::log($sql,"info","sql");
        Yii::app()->getDbByName("db_air")->createCommand($sql)->execute();
        $sql  = "update user_info set balance = balance - $packet_price, ";
        $sql .= "total_cost = total_cost + $packet_price where user_name = '$user_name';";
        Yii::log($sql,"info","sql");
        Yii::app()->getDbByName("db_air")->createCommand($sql)->execute();
        $transaction->commit();

    } catch (Exception $e) {
        $transaction->rollBack();
        Yii::app()->session['msg'] = "交易失败，请重试，如果再有问题请联系管理员".$e->getMessage();
        return false;
    }

    return true;
}


function air_get_stamp_after_day($date, $period)
{
    if (preg_match_all('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/', $date, $matchs) == 0) {
        return 0;
    }

    $year = $matchs[1][0];
    $mon  = $matchs[2][0];
    $day  = $matchs[3][0];
    $hour = $matchs[4][0];
    $min  = $matchs[5][0];
    $sec  = $matchs[6][0];

    return mktime($hour,$min,$sec,$mon,$day + $period,$year);
}


function air_get_stamp_after_month($date, $period)
{
    if (preg_match_all('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/', $date, $matchs) == 0) {
        return 0;
    }

    $year = $matchs[1][0];
    $mon  = $matchs[2][0];
    $day  = $matchs[3][0];
    $hour = $matchs[4][0];
    $min  = $matchs[5][0];
    $sec  = $matchs[6][0];

    return mktime($hour,$min,$sec,$mon + $period,$day,$year);
}

function air_format_str($string, $length, $etc = '...')
{
    $result = '';
    $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
    $strlen = strlen($string);
    for ($i = 0; (($i < $strlen) && ($length > 0)); $i++)
    {
        if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0'))
        {
            if ($length < 1.0)
            {
                break;
            }
            $result .= substr($string, $i, $number);
            $length -= 1.0;
            $i += $number - 1;

        } else {
            $result .= substr($string, $i, 1);
            $length -= 0.5;
        }
    }

    $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');

    if ($i < $strlen)
    {
        $result .= $etc;
    }
    return $result;
}


function air_output_des($des)
{
    $ret = "";
    $arr = array();
    $arr = explode("\n",$des);

    foreach ($arr as $line){
        $ret .=  "  $line <br/>";
    }

    return $ret;
}


?>
