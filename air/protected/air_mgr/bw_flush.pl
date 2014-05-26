=pod
用户实时流量 
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
use Time::Local;

use AirMgr;
use DBI;
use Data::Dumper;

use constant ONE_DAY => 86400;
use constant IDLE_RATIO => 3; #闲时流量的折算比
use constant STAT_INTERVAL => 300;

my ($db_air, $db_radius);
my ($v_date, $sql, $sql_update, $sql_sub, $count );
my ($year, $mon, $day, $hour, $min);
my ($sth, $sth_insert);

my %old_rt_hash = ();
my %new_rt_hash = ();
my %origin_hash = ();
my %delete_hash = (); #记录已经完成的记录的t_id，后面会删除

$count = 0;
($year, $mon, $day, $hour, $min) = &air_get_normalized_time();
$v_date="$year-$mon-$day";

my $cur_date_type = &air_get_date_type("$year-$mon-$day $hour:$min:00");

my $stp = timelocal(0, $min, $hour, $day, $mon-1 , $year-1900);
my ($prev_year, $prev_mon, $prev_day, $prev_hour, $prev_min) =
    &air_get_normalized_time($stp - STAT_INTERVAL);


$db_air    = &air_connect_db("air", "localhost", "air", "***King1985***", "3306");
$db_radius = &air_connect_db("radius", "localhost", "air", "***King1985***", "3306");

#取radacct数据库
$sql  = "select radacctid id, username, acctstarttime start_time, acctstoptime stop_time, ";
$sql .= "acctsessiontime session_time, acctinputoctets input, acctoutputoctets output, ";
$sql .= "callingstationid mac,acctterminatecause terminate_cause, framedipaddress client_ip";
$sql .= " from radacct";

$sql_sub  = "insert into login_history (username, start_time, stop_time, session_time, input,";
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

            $terminate_cause = "" if (not defined($terminate_cause));
            $sql_update .= "('$username','$start_time','$stop_time',$session_time,";
            $sql_update .= "$input,$output,'$mac','$terminate_cause','$ip'),";

            $delete_hash{$t_id} = "1";

        } else {
            $origin_hash{$username}{"not_finish"} += $traffic;
        }

        if (++$count % 100 == 0) {
            $count = 0;
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

    $sth->finish();
    $sth_insert->finish();
}

#2、取traffic_realtime表

$sql  = "select user_name, traffic, update_date from traffic_realtime ";
$sql .= "where update_date = '$prev_year-$prev_mon-$prev_day $prev_hour:$prev_min:00'";

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
    $sql_update .= "delete from radacct where radacctid = $id;";
    if (++$count % 100 == 0) {
        print("$sql_update\n");
        #$sth = $db_radius->prepare($sql_update);
        #$sth -> execute() or &air_write_log("ERROR ".$sth_insert->errstr);

        $count = 0;
        $sql_update = "";
    }
}

if ($count > 0) {
    print("$sql_update\n");
    #$sth = $db_air->prepare($sql_update);
    #$sth -> execute() or &air_write_log("ERROR ".$sth_insert->errstr);
}


#4、记录还在线的用户信息-----------------

foreach my $username (keys %origin_hash) {
    if (exists($origin_hash{$username}{"not_finish"})) {
        $new_rt_hash{$username}{"traffic"} = $origin_hash{$username}{"not_finish"};
        $new_rt_hash{$username}{"date"} = "$v_date $hour:$min:00";
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

$sql = "delete from traffic_realtime where update_date < '$v_date $hour:$min:00'";
$sth = $db_air->prepare($sql);
$sth -> execute() or &air_write_log("ERROR ".$sth->errstr);


#7、更新用户带宽数据表-------------------

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
    print("cur_date_type = $cur_date_type\n");
    if ($old_date_type eq "idle" or $cur_date_type eq "idle") {
        #此时记闲时流量
        $busy_flag = 0;
    }

    $sql =  "select traffic_idle, traffic_busy, traffic_bill, traffic_remain from ";
    $sql .= "traffic_mon where user_name = '$username' and date_mon = '$year$mon'";
    $sth = $db_air->prepare($sql);
    if ($sth->execute()) {
        while (my $ref = $sth->fetchrow_hashref()) {
            ($t_idle, $t_busy) = ($ref->{'traffic_idle'}, $ref->{'traffic_busy'});
            ($t_bill, $t_remain) = ($ref->{'traffic_bill'}, $ref->{'traffic_remain'});
            if ($busy_flag eq 1) {
                $t_busy += $traffic;
                $t_bill += $traffic;
                $t_remain -= $traffic;

            } else {
                $t_idle += $traffic;
                $t_bill += $traffic/IDLE_RATIO;
                $t_remain -= $traffic/IDLE_RATIO;
            }

            $sql  = "update traffic_mon set traffic_idle = $t_idle, traffic_busy = $t_busy,";
            $sql .= "traffic_bill = $t_bill, traffic_remain = $t_remain where user_name = '$username'";
            $sql .= " and date_mon = '$year$mon'";
            print("$sql\n");

            $sth_insert = $db_air->prepare($sql);
            if (not $sth_insert->execute()) {
                print("sth_insert failed ".$sth_insert->errstr."\n");
                #&air_write_log("ERROR 更新traffic_mon失败".$sth_insert->errstr);
                next;
            }
        } 

    } else {
        #如果没有发现本月的用户记录，则说明上月结算还没有做，属于极少发生的异常情况，本次的流量不计入.
    }
}

&air_write_log();

print("date=$v_date $hour:$min\n");

exit 0;
#-----------------------------------------------
