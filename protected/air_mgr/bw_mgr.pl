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
use constant STAT_INTERVAL => 300;

my ($db_air, $db_radius);
my ($v_day, $v_date, $sql, $sql_update, $sql_sub, $count );
my ($year, $mon, $day, $hour, $min);
my ($sth, $sth_insert);

my %old_rt_hash = ();
my %new_rt_hash = ();
my %origin_hash = ();
my %delete_hash = (); #记录已经完成的记录的t_id，后面会删除
my %bw_hash = ();

$count = 0;
($year, $mon, $day, $hour, $min) = &air_get_normalized_time();
$v_day="$year-$mon-$day";
$v_date="$year-$mon-$day $hour:$min:00";

my ($prev_year, $prev_mon, $prev_day, $prev_hour, $prev_min) =
    &air_get_normalized_time(time() - STAT_INTERVAL);

my $cur_date_type = &air_get_date_type($v_date);

$db_air    = &air_connect_db("air", "localhost", "air", "***King1985***", "3306");
$db_radius = &air_connect_db("radius", "localhost", "air", "***King1985***", "3306");

my ($g_node,$g_idle_bw_ratio,$g_max_over_traffic,$g_traffic_free_price) = &air_get_global_info($db_air);

#-------------------实时计费------------------------
#1、汇总radacct的数据，填充origin_hash,$delete_hash.
#并把可以删除的radacct记录插入user_login_history。

&process_radacct_items();

#2、取traffic_realtime表,填充old_rt_hash
&get_realtime_items();

#3、统计实时带宽(更新radacct表和realtime表)
&update_rt_bw();

#4、更新用户带宽数据表-------------------

&get_user_traffic();

#5、ftp提交带宽数据
&submit_bw();

exit 0;

#----------------------------------------------function---------------------------------------------------

sub submit_bw()
{
    my $path = "/home/air_data";
    my $file_name = "$prev_year$prev_mon$prev_day$prev_hour$prev_min".".$g_node.bw";
    my $bw = 0;
    my $file_path = "$path/$file_name";
    if (not open(FIN, ">$file_path")){
        print("打开文件错误\n");
        return;
    }

    foreach my $user_name (keys %bw_hash) {
        my $bw = $bw_hash{$user_name}{"bw"};
        my $busy_flag = $bw_hash{$user_name}{"busy_flag"};
        print FIN "bw\t$user_name\t$bw\t$busy_flag\n";
    }

    close(FIN);

    #ftp
    `sh /root/mgr/transfer.sh $file_name >>/tmp/bw_transfer.log`;
    `rm -f $file_name` if (-e $file_name);
}


sub reject_user()
{
    my ($user_name,$client_ip,$ros_ip,$session_id) = @_;
    print("ros_ip =$ros_ip, session_id=$session_id, user_name=$user_name, client_ip=$client_ip\n");
    #更新用户组到受限用户组.
    $sql = "update radusergroup set groupname = 'limited' where username='$user_name'";
    $sth = $db_radius->prepare($sql);
    print("$sql\n");
    $sth->execute();

    #下线用户.
    my $path = "/tmp/reject_user.dat";
    `rm -f $path` if (-e $path);
    `echo "Acct-Session-Id=$session_id" >> $path`;
    `echo "Framed-IP-Address=$client_ip" >> $path`;
    `echo "User-Name=$user_name" >> $path`;
    `echo "NAS-IP-Address=$ros_ip" >> $path`;

    if (-e $path) {
        `cat $path | /usr/bin/radclient -x $ros_ip:3799 disconnect ''King1985*_*''`;
    }
}


sub get_user_traffic()
{
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

        $traffic = sprintf("%.2f", $traffic/1024/1024);
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
            $t_bill = sprintf("%.2f", $traffic/$g_idle_bw_ratio);
        }

        if ($t_bill <= 0.01) {
            next;
        }

        $bw_hash{$username}{"bw"} += $t_bill;
        $bw_hash{$username}{"busy_flag"} = $busy_flag;
    }
}

sub process_radacct_items()
{
    #取radacct数据库
    $sql  = "select radacctid id, username, acctstarttime start_time, acctstoptime stop_time,";
    $sql .= "acctsessiontime session_time, acctinputoctets input, acctoutputoctets output, ";
    $sql .= "callingstationid mac,acctterminatecause terminate_cause, framedipaddress client_ip,";
    $sql .= "acctsessionid session_id, nasipaddress ros_ip";
    $sql .= " from radacct";

    $sql_sub  = "insert into user_login_history (username, start_time, stop_time, session_time, input,";
    $sql_sub .= "output, mac, terminate_cause, clientip) values ";
    $sql_update = $sql_sub;

    $sth = $db_radius->prepare($sql);

    if ($sth->execute()) {
        while (my $ref = $sth->fetchrow_hashref()) {
            my ($username, $start_time, $stop_time) = ($ref->{"username"}, $ref->{"start_time"}, $ref->{"stop_time"});
            my ($session_time, $input, $output) = ($ref->{"session_time"}, $ref->{"input"}, $ref->{"output"});
            my ($mac, $terminate_cause, $ip) = ($ref->{"mac"}, $ref->{"terminate_cause"}, $ref->{"client_ip"});
            my $traffic = $input+$output;
            $origin_hash{$username}{"total"} += $traffic;
            $origin_hash{$username}{"session_id"} = $ref->{"session_id"};
            $origin_hash{$username}{"client_ip"} = $ref->{"client_ip"};
            $origin_hash{$username}{"ros_ip"} = $ref->{"ros_ip"};

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

}

sub get_realtime_items
{
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
}


sub update_rt_bw
{
    #A、把已经完成的session记录从radacct表删除
    $count = 0;
    $sql_update = "";
    foreach my $id (keys %delete_hash) {
        $sql_update = "delete from radacct where radacctid = $id;";
        print("$sql_update\n");
        $sth = $db_radius->prepare($sql_update);
        $sth -> execute() or &air_write_log("ERROR ".$sth_insert->errstr);
    }

    #B、记录还在线的用户信息
    foreach my $username (keys %origin_hash) {
        if (exists($origin_hash{$username}{"not_finish"})) {
            $new_rt_hash{$username}{"traffic"} = $origin_hash{$username}{"not_finish"};
            $new_rt_hash{$username}{"date"} = "$v_date";
        }
    }

    #C、更新traffic_realtime
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
        $sth_insert = $db_air->prepare($sql_update);
        $sth_insert -> execute() or &air_write_log("ERROR ".$sth_insert->errstr);
    }


    #D、清空traffic_realtime------------------

    $sql = "delete from traffic_realtime where update_date < '$v_date'";
    $sth = $db_air->prepare($sql);
    $sth -> execute() or &air_write_log("ERROR ".$sth->errstr);
}

