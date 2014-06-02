=pod
流量计费的相关定义：
时间点： 10:00时脚本统计的流量是来自09:55~10:00的流量，流量计费09:55的流量。 

功能：统计用户的实时流量 
此表是跟freeradius.radacct的关联表，每5/10分钟汇总一次流量。具体流程为
1、取radacct表的所有记录A，按照用户名、mac两个维度做流量统计
2、对每组（user_name、mac、traffic_A)
   A、查找user_traffic_realtime表是否有对应(user_name，mac的记录).
   B、如果有,记录对应的流量traffic_B，并使traffic_C=trafficA-traffic_B。
   C、如果没有，则tarffic_C=traffic_A-traffic_B
   D、计算当前时间点为闲时还是忙时，设置闲时因子 factor=1或1/3
   E、对该用户的radacct的原始记录做过滤，已经下线的记录对应的（id，流量和traffic_A1）
   F、根据traffic_C和factor的值更新user_mon_traffic表

   G、把所有已经下线的数据记录到traffic_history，并删除radacct对应的记录。
   H、traffic_A2=traffic_A-trafficA1。计为当前时刻用户在radacct的流量，计入user_traffic_realtime
3、把这一阶段的总流量计入 traffic_total

=cut

#!/usr/bin/perl -w
use strict;
use warnings;
use lib qw(/root/air/protected/air_mgr/);
use Time::Local;
use POSIX;
use AirMgr;
use DBI;
use Data::Dumper;

use constant ONE_DAY => 86400;
use constant IDLE_RATIO => 3; #闲时流量的折算比
use constant STAT_INTERVAL => 300;

my ($db_air, $db_radius, $db_air_tranc);
my ($v_day, $v_date, $sql, $sql_update, $sql_sub, $count );
my ($year, $mon, $day, $hour, $min);
my ($sth, $sth_insert);

my %old_rt_hash = ();
my %new_rt_hash = ();
my %origin_hash = ();
my %delete_hash = (); #记录已经完成的记录的t_id，后面会删除

$count = 0;
($year, $mon, $day, $hour, $min) = &air_get_normalized_time();
$v_day="$year-$mon-$day";
$v_date="$year-$mon-$day $hour:$min:00";

my ($prev_year, $prev_mon, $prev_day, $prev_hour, $prev_min) =
    &air_get_normalized_time(time() - STAT_INTERVAL);

my $v_prev_day = "$prev_year-$prev_mon-$prev_day";
my $v_prev_date = "$v_prev_day $prev_hour:$prev_min:00";

my $cur_date_type = &air_get_date_type($v_date);

$db_air    = &air_connect_db("air", "localhost", "air", "***King1985***", "3306");
$db_air_tranc = &air_connect_db_tranc("air", "localhost", "air", "***King1985***", "3306");
$db_radius = &air_connect_db("radius", "localhost", "air", "***King1985***", "3306");

#取radacct数据库
$sql  = "select radacctid id, username, acctstarttime start_time, acctstoptime stop_time, ";
$sql .= "acctsessiontime session_time, acctinputoctets input, acctoutputoctets output, ";
$sql .= "callingstationid mac,acctterminatecause terminate_cause, framedipaddress client_ip";
$sql .= " from radacct";

$sql_sub  = "insert into user_login_history (username, start_time, stop_time, session_time, input,";
$sql_sub .= "output, mac, terminate_cause, clientip) values ";
$sql_update = $sql_sub;

$sth = $db_radius->prepare($sql);


#1、取radacct记录---------------------

if ($sth->execute()) {
    while (my $ref = $sth->fetchrow_hashref()) {
        my ($username, $start_time, $stop_time) = ($ref->{"username"}, $ref->{"start_time"}, $ref->{"stop_time"});
        my ($session_time, $input, $output) = ($ref->{"session_time"}, $ref->{"input"}, $ref->{"output"});
        my ($mac, $terminate_cause, $ip) = ($ref->{"mac"}, $ref->{"terminate_cause"}, $ref->{"client_ip"});
        my $traffic = $input+$output;
        $origin_hash{$username}{"total"} += $traffic;

        if (defined($stop_time) and length($stop_time) > 5) {
            my $t_id = $ref->{"id"};
            $count ++;
            $terminate_cause = "" if (not defined($terminate_cause));
            $sql_update .= "('$username','$start_time','$stop_time',$session_time,";
            $sql_update .= "$input,$output,'$mac','$terminate_cause','$ip'),";

            $delete_hash{$t_id} = "1";

        } else {
            $origin_hash{$username}{"not_finish"} += $traffic;
        }

        if ($count > 0 and $count % 100 == 0) {
            chop($sql_update);

            $sth_insert = $db_air->prepare($sql_update);
            $sth_insert -> execute() or &air_write_log("ERROR ".$sth_insert->errstr);

            $sql_update = $sql_sub;
            $count = 0;
        };
    }

    if ($count > 0) {
        chop($sql_update);
        $sth_insert = $db_air->prepare($sql_update);
        $sth_insert -> execute() or &air_write_log("ERROR ".$sth_insert->errstr);
    }

}

#2、取traffic_realtime表

$sql  = "select user_name, traffic, update_date from traffic_realtime ";
$sql .= "where update_date < '$v_date'";

$sth = $db_air->prepare($sql);
if ($sth->execute()) {
    while (my $ref = $sth->fetchrow_hashref()) {
        my ($username, $traffic, $update_date) = ($ref->{"user_name"}, $ref->{"traffic"}, $ref->{"update_date"});
        $old_rt_hash{$username}{"traffic"} = $traffic;
        $old_rt_hash{$username}{"date_type"} = &air_get_date_type($update_date); #"idle/busy"
    }
}

#3、把已经完成的session记录从radacct表删除--------
$count = 0;
$sql_update = "";
foreach my $id (keys %delete_hash) {
    $sql_update = "delete from radacct where radacctid = $id;";
    print("$sql_update\n");
    $sth = $db_radius->prepare($sql_update);
    $sth -> execute() or &air_write_log("ERROR ".$sth_insert->errstr);
}
#4、记录还在线的用户信息-----------------
foreach my $username (keys %origin_hash) {
    if (exists($origin_hash{$username}{"not_finish"})) {
        $new_rt_hash{$username}{"traffic"} = $origin_hash{$username}{"not_finish"};
        $new_rt_hash{$username}{"date"} = "$v_date";
    }
}

#5、更新traffic_realtime-----------------

$count = 0;
$sql_sub = "replace into traffic_realtime values ";
$sql_update = $sql_sub;
foreach my $username (keys %new_rt_hash) {
    my ($traffic, $date) = ($new_rt_hash{$username}{"traffic"}, $new_rt_hash{$username}{"date"});
    $sql_update .= "('$username', $traffic, '$date'),";

    if (++$count % 100 == 0) {
        chop($sql_sub);
        print("$sql_update\n");
        $sth_insert = $db_air->prepare($sql_update);
        $sth_insert -> execute() or &air_write_log("ERROR ".$sth_insert->errstr);

        $sql_update = $sql_sub;
        $count = 0;
    }
}

if ($count > 0) {
    chop($sql_update);
    print("$sql_update\n");
    $sth_insert = $db_air->prepare($sql_update);
    $sth_insert -> execute() or &air_write_log("ERROR ".$sth_insert->errstr);
}


#6、清空traffic_realtime------------------

$sql = "delete from traffic_realtime where update_date < '$v_date'";
$sth = $db_air->prepare($sql);
$sth -> execute() or &air_write_log("ERROR ".$sth->errstr);


#7、更新用户带宽数据表-------------------

$sql  = "select auto_id,user_name,remain, unix_timestamp(stop_date) as stop_stp from user_quota where ";
$sql .= "category = 'traffic' and  state='enable' and start_date < '$v_date'";
print("$sql\n");
$sth = $db_air->prepare($sql);
my %quota_total_hash = ();
my %quota_hash = ();

if ($sth->execute()) {
    while (my $ref = $sth->fetchrow_hashref()) {
        my ($auto_id,$stop_stp) = ($ref->{'auto_id'}, $ref->{'stop_stp'});
        my ($user_name,$remain) = ($ref->{'user_name'}, $ref->{'remain'});
        print("~~~~~stop_stp=$stop_stp\n");
        $quota_hash{$user_name}{$auto_id}{"remain"} = $remain;
        $quota_hash{$user_name}{$auto_id}{"stop_stp"} = $stop_stp;
        $quota_hash{$user_name}{$auto_id}{"state"} = "enable";
        $quota_hash{$user_name}{$auto_id}{"state_desc"} = "null";
        $quota_hash{$user_name}{$auto_id}{"change"} = "no";
        $quota_total_hash{$user_name}{"remain"} += $remain;
        print("---------------------quota $user_name, remain=$remain\n");
    }
}

foreach my $username (keys %origin_hash) {
    my $traffic = 0;
    my $old_date_type = "busy";
    my $busy_flag = 1;
    my $total = 0;
    my ($t_idle, $t_busy, $t_bill, $t_remain) = ();
    $total = $origin_hash{$username}{"total"};

    #计算使用流量
    if (exists($old_rt_hash{$username})) {
        $traffic = $total - $old_rt_hash{$username}{"traffic"};
        $old_date_type = $old_rt_hash{$username}{"date_type"};

    } else {
        $traffic = $total;
    }

    $traffic = sprintf("%.3f", $traffic/1024/1024);
    #1、只要有一个时间点为闲时，即按照闲时流量算
    if ($old_date_type eq "idle" or $cur_date_type eq "idle") {
        #此时记闲时流量
        $busy_flag = 0;
    }

    ($t_busy, $t_bill, $t_idle) = (0,0,0);
    if ($busy_flag eq 1) {
        $t_busy = $traffic;
        $t_bill = $traffic;

    } else {
        $t_idle = $traffic;
        $t_bill = sprintf("%.3f", $traffic/IDLE_RATIO);
    }

    my $offset = $t_bill;
    $quota_total_hash{$username}{"remain"} -= $t_bill;
    foreach my $id (sort {$quota_hash{$username}{$a}{"stop_stp"} <=> $quota_hash{$username}{$b}{"stop_stp"}} keys %{$quota_hash{$username}}) {
        if ($offset > 0) {
            my $remain = $quota_hash{$username}{$id}{"remain"};
            if ($offset > $remain) {
                #此流量包流量不够了，
                $offset -= $remain;
                $quota_hash{$username}{$id}{"state"} = 'disable';
                $quota_hash{$username}{$id}{"state_desc"} = '流量已用完';
                $quota_hash{$username}{$id}{"change"} = 'yes';
                $quota_hash{$username}{$id}{"reamin"} = 0;
                next;

            } else {
                print("----user_quota: username=$username, offset=$offset remain=$remain\n");
                $quota_hash{$username}{$id}{"state_desc"} = '当前正使用';
                $quota_hash{$username}{$id}{"change"} = 'yes';
                $quota_hash{$username}{$id}{"remain"} = $remain - $offset;
                $offset = 0;
                last;
            }

        } else {
            last;
        }
    }

    my $user_remain = $quota_total_hash{$username}{"remain"};
    print("---quota user_remain=$user_remain\n");
    if ($user_remain <= 0) {
        #此用户流量全部使用完，踢掉此用户。
        my $msg = "亲,你的账户$username"."流量已经全部用完，系统已经阻止你登陆, ";
        $msg .= "请及时去www.air-wifi.cn购买新的加油包后再来登陆。";
        $msg .= "给亲带来的不便，我们深表歉意。";
        &air_msg_user($username, "流量用尽", $msg, "emerge");
        $user_remain = 0;
    }

    if ($user_remain <= 50) {
        #流量过少，需要给用户消息了.
        my $msg = "亲，你的账户$username"."流量已经低于50MB了，为了不影响你的正常使用, ";
        $msg .= "建议你及时办理加油包。如果你的账户已经无法登陆Air-WIFI，说明流量已经全部用完，";
        $msg .= "系统已经拒绝你的登陆,请及时去www.air-wifi.cn购买新的加油包后再来登陆。";
        $msg .= "给亲带来的不便，我们深表歉意。";
        &air_msg_user($username, "流量低于$user_remain"."MB告警", $msg, "emerge");
    }

    $sql_update = "";
    foreach my $id (keys %{$quota_hash{$username}}) {
        if ($quota_hash{$username}{$id}{'change'} eq "yes") {
            my $state = $quota_hash{$username}{$id}{"state"};
            my $state_desc = $quota_hash{$username}{$id}{"state_desc"};
            my $remain = $quota_hash{$username}{$id}{"remain"};
            $sql_update .= "update user_quota set state = '$state', state_desc = '$state_desc',";
            $sql_update .= "remain = $remain where auto_id = $id;";
            $sth = $db_air->prepare($sql_update);
            print("$sql_update\n");
            $sth->execute();
        }
    }

    $sql =  "select traffic_idle from ";
    $sql .= "user_mon where user_name = '$username' and date_mon = '$year$mon'";
    $sth = $db_air->prepare($sql);
    print("user_mon_sql:$sql\n");
    if ($sth->execute()) {
        if (my $ref = $sth->fetchrow_hashref()) {
            $sql_update = "update user_mon set traffic_idle = traffic_idle + $t_idle,";
            $sql_update .= "traffic_busy = traffic_busy + $t_busy, traffic_bill = traffic_bill + $t_bill,";
            $sql_update .= "traffic_remain = $user_remain where ";
            $sql_update .= "user_name = '$username' and date_mon = '$year$mon';";

        } else {
            $sql_update = "insert into user_mon (user_name, traffic_idle, traffic_busy, ";
            $sql_update .= "traffic_bill, traffic_remain, date_mon) values ";
            $sql_update .= "('$username', $t_idle, $t_busy, $t_bill, $user_remain, '$year$mon');";
        }
    }
    print("$sql_update\n");
    $sth = $db_air->prepare($sql_update);
    $sth->execute();
}

#----------------检查过期的资源----------------
$sql  = "update user_quota set state = 'disable', state_desc = '资源已过期' where ";
$sql .= "stop_date <= now()";

$sth = $db_air->prepare($sql);
$sth->execute();

#----------------检查固定套餐-------------------
my %packet_hash = ();
$sql  = "select packet_id, p_desc, traffic, period_month, movie_tickets, price ";
$sql .= "from packet_info where category='packet' and enable_state = 'enable'";
$sth = $db_air->prepare($sql);
if ($sth->execute()) {
    while (my $ref = $sth->fetchrow_hashref()) {
        my ($id, $desc, $traffic) = ($ref->{'packet_id'}, $ref->{'p_desc'}, $ref->{'traffic'});
        my ($period, $beans, $price) = ($ref->{'period_month'}, $ref->{'movie_tickets'}, $ref->{'price'});
        $packet_hash{$id}{"desc"} = "$desc";
        $packet_hash{$id}{"traffic"} = "$traffic";
        $packet_hash{$id}{"period_month"} = $period;
        $packet_hash{$id}{"beans"} = $beans;
        $packet_hash{$id}{"price"} = $price;
    }
} else {
    &air_write_log("sql execute failed\n");
    exit 0;
}

#提前一个小时开始检查
my ($xyear, $xmon, $xday, $xhour, $xmin) = &air_get_normalized_time(time() + 3600);
$sql  = "select auto_id, packet_id, user_name, check_date from packet_auto ";
$sql .= "where check_date < '$xyear-$xmon-$xday $xhour:$xmin:00' and enable_state = 'enable'";
$sth = $db_air->prepare($sql);
if ($sth->execute()) {
    while (my $ref = $sth->fetchrow_hashref()) {
        my ($auto_id, $packet_id) = ($ref->{'auto_id'}, $ref->{'packet_id'});
        my ($user_name, $check_date) = ($ref->{'user_name'}, $ref->{'check_date'});
        print("====check_date=$check_date\n");
        &air_transaction($db_air, $auto_id, $packet_id, $user_name, $check_date);
    }
}

print("------------------------------------------------------------------------\n");
exit 0;

sub air_transaction()
{
    my ($db, $auto_id, $packet_id, $user_name, $check_date)  = @_;
    print("======check_date=$check_date\n");
    my ($sth, $sql, $stop_date, $ref) = ();
    if (not exists ($packet_hash{$packet_id})) {
        print("packetid=$packet_id is not exists\n");
        $sql = "update packet_auto set enable_state = 'disable' where auto_id = $auto_id";
        $sth = $db->prepare($sql);
        $sth->execute();
        return ;
    }

    my ($desc, $price, $period, $beans,$traffic) = ($packet_hash{$packet_id}{"desc"},
        $packet_hash{$packet_id}{"price"}, $packet_hash{$packet_id}{"period_month"},
        $packet_hash{$packet_id}{"beans"}, $packet_hash{$packet_id}{"traffic"});

    $sql = "select balance from user_info where user_name = '$user_name'";
    $sth = $db->prepare($sql);
    if (not $sth->execute()) {
        return;
    }

    my $balance = 0;
    if ($ref = $sth->fetchrow_hashref()) {
        $balance = $ref->{"balance"};
    }

    if ($balance < $price) {
        print("账户余额不足，disable此套餐.");
        $sql = "update packet_auto set enable_state = 'disable' where auto_id = $auto_id";
        $sth = $db->prepare($sql);
        $sth->execute();
        return;
    }

    $stop_date = &air_get_date_by_month_offset($check_date, $period);
    $sql = "insert into packet_deal (user_name, packet_id, start_date, stop_date,";
    $sql .= "price, state, create_date) values ";
    $sql .= "('$user_name', $packet_id, '$check_date', '$stop_date', $price, 'init', ";
    $sql .= "now())";
    print("$sql\n");
    $sth = $db->prepare($sql);
    if (not $sth->execute()) {
        return;
    }

    $sql = "select last_insert_id() as id";
    $sth = $db->prepare($sql);
    if (not $sth->execute()) {
        return;
    }

    my $deal_id = 0;
    if ($ref = $sth->fetchrow_hashref()) {
        $deal_id = $ref->{"id"};
    }

    if ($deal_id <= 0) {
        print("deal_id error. deal_id=$deal_id\n");
        return;
    }

    $sql  = "insert into user_quota (user_name, category, quota, remain, deal_id, ";
    $sql .= "state, state_desc, packet_desc, packet_category, start_date, ";
    $sql .= "stop_date, create_date ) values ";
    $sql .= "('$user_name', 'traffic', $traffic, $traffic, $deal_id,'enable', ";
    $sql .= "'未使用', '$desc', 'packet', '$check_date', '$stop_date', now())";

    if ($beans > 0) {
        $sql .= ",('$user_name', 'beans', $beans, $beans, $deal_id,'enable', '未使用', ";
        $sql .= "'$desc', 'packet', '$check_date', '$stop_date', now())";
    }

    
    eval {
        $db_air_tranc->do($sql);

        $sql = "update packet_deal set state = 'done' where auto_id = $deal_id";
        $db_air_tranc->do($sql);

        $sql = "update packet_auto set check_date = '$stop_date' where auto_id = $auto_id";
        $db_air_tranc->do($sql);

        $sql  = "update user_info set balance = balance - $price, total_cost = total_cost + $price ";
        $sql .= "where user_name = '$user_name'";
        $db_air_tranc->do($sql);

        $db_air_tranc->commit();
    };

    if ($@) { 
        print("Transaction aborted: $@"); 
        $db_air_tranc->rollback();
    } 
}

sub air_get_date_by_month_offset()
{
    my ($date, $month)  = @_;
    my ($year, $mon, $day, $hour, $min, $sec) = ();
    if (not $date =~ /(\d+)-(\d+)-(\d+)\s+(\d+):(\d+):(\d+)/) {
        return "";
    }
    
    ($year, $mon, $day, $hour, $min, $sec) = ($1, $2, $3, $4, $5, $6);
    $year = $year + floor(($mon + $month) / 12);
    $mon = ($mon + $month) % 12;
    $mon = "0$mon" if ($mon < 10);
    
    return "$year-$mon-$day $hour:$min:$sec";
}
