<?php

function airAutoLogin()
{
    air_get_media_list(0,0,0,1);
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
    //Yii::app()->session['msg'] = "正常";
    return true;
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
function air_get_media_list($id_kind,$id_area,$id_type,$page)
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
    $sql  = "select auto_id, m_chs_name,m_main_actors,m_douban_num,m_total_pv, m_pic_path ";
    $sql .= "from media where enable_state = 'enable'  $cond ";
    $sql .= "order by m_create_date desc ";
    $sql .= "limit $low, $rows";

    $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
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

?>
