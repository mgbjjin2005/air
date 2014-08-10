=pod
统计各个节点通过ftp回传回来的带宽数据，做统计入库.

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

my ($db_air, $db_air_tranc);
my ($v_day, $v_date, $sql, $sql_update, $sql_sub);
my ($year, $mon, $day, $hour, $min);
my ($sth, $sth_insert);

my %packet_hash = ();
my %quota_total_hash = ();
my %quota_hash = ();
my %bw_hash = ();
my %login_user_hash = ();

($year, $mon, $day, $hour, $min) = &air_get_normalized_time();
$v_day="$year-$mon-$day";
$v_date="$year-$mon-$day $hour:$min:00";

my $root = "/home/king";

$db_air    = &air_connect_db("air", "localhost", "air", "***King1985***", "3306");
$db_air_tranc = &air_connect_db_tranc("air", "localhost", "air", "***King1985***", "3306");

my ($g_node,$g_idle_bw_ratio,$g_max_over_traffic,$g_traffic_free_price) = &air_get_global_info($db_air);

#----------------提前一个小时开始检查是否有固定套餐需要处理-------------------
&process_packet_auto();

sleep(10);

&collect_file();
&calc_bw();

#----------------检查过期的资源----------------
$sql  = "update user_quota set state = 'disable', state_desc = '资源已过期' where ";
$sql .= "stop_date <= now()";

$sth = $db_air->prepare($sql);
$sth->execute();

exit 0;

#------------------------------------function----------------------------------
sub calc_bw()
{
    foreach my $user_name (keys %bw_hash) {
        &update_user_bw($user_name);
    }
}

sub update_user_bw()
{
    my ($user_name,) = @_;
    $sql  = "select auto_id,remain, unix_timestamp(stop_date) as stop_stp from user_quota where ";
    $sql .= "user_name='$user_name' and category = 'traffic' and  state='enable' and start_date < '$v_date'";
    print("$sql\n");
    $sth = $db_air->prepare($sql);
    %quota_hash = ();
    %quota_total_hash = ();

    my $traffic_free = 0;
    my $traffic_free_cost = 0;

    if ($sth->execute()) {
        while (my $ref = $sth->fetchrow_hashref()) {
            my ($auto_id,$stop_stp) = ($ref->{'auto_id'}, $ref->{'stop_stp'});
            my ($remain) = ($ref->{'remain'});
            print("~~~~~stop_stp=$stop_stp\n");
            $quota_hash{$auto_id}{"remain"} = $remain;
            $quota_hash{$auto_id}{"stop_stp"} = $stop_stp;
            $quota_hash{$auto_id}{"state"} = "enable";
            $quota_hash{$auto_id}{"state_desc"} = "null";
            $quota_hash{$auto_id}{"change"} = "no";
            $quota_total_hash{"remain"} += $remain;
            print("---------------------quota $user_name, remain=$remain\n");
        }
    }

    my ($t_idle, $t_busy, $t_bill, $t_remain) = (0,0,0,0);
    $t_bill = $bw_hash{$user_name}{"bw"};
    my $busy_flag = $bw_hash{$user_name}{"busy_flag"};

    my $offset = $t_bill;
    foreach my $id (sort {$quota_hash{$a}{"stop_stp"} <=> $quota_hash{$b}{"stop_stp"}} keys %quota_hash) {
        if ($offset > 0) {
            my $remain = $quota_hash{$id}{"remain"};
            if ($offset > $remain) {
                #此流量包流量不够了，
                $offset -= $remain;
                $quota_hash{$id}{"state"} = 'disable';
                $quota_hash{$id}{"state_desc"} = '流量已用完';
                $quota_hash{$id}{"change"} = 'yes';
                $quota_hash{$id}{"remain"} = 0;
                next;

            } else {
                print("----user_quota: username=$user_name, offset=$offset remain=$remain\n");
                $quota_hash{$id}{"state_desc"} = '当前正使用';
                $quota_hash{$id}{"change"} = 'yes';
                $quota_hash{$id}{"remain"} = $remain - $offset;
                $offset = 0;
                last;
            }

        } else {
            last;
        }
    }


    #如果流量全部用完，此时如果流量超出2MB，则扣自然流量，如果小于2MB，则不计。
    if ($offset >= 2 ) {
        $traffic_free = $offset;
        $traffic_free_cost =  sprintf("%.2f", $traffic_free *  $g_traffic_free_price);  

    } else {
        #如果流量用超小于2MB，则这部分流量不计费，直接减掉.
        $t_bill = $t_bill - $offset;
    }

    #更新$t_busy 和 $t_idle
    if ($busy_flag eq 1) {
        $t_busy = $t_bill;

    } else {
        $t_idle = $t_bill * $g_idle_bw_ratio;
    }

    my $user_remain = 0;
    if (exists($quota_total_hash{"remain"})) {
        $user_remain = $quota_total_hash{"remain"};
    }

    $user_remain = $user_remain - $t_bill;

    print("quota user_name=$user_name,user_remain=$user_remain bill=$t_bill\n");
    
    if ($user_remain <= 0) {
        #流量已经用超了，下线.
        foreach my $node (keys %{$login_user_hash{$user_name}}) {
            &air_add_user_action($db_air,$user_name, $node, "reject","traffic over.", 10);
        }

        &air_change_user_net_state($db_air,$user_name,"limited");

        print("用户$user_name"."流量耗尽，下线并访问受限.\n");
        $user_remain = 0;

    } elsif ($user_remain <= 20) {
        #剩余流量不足20MB，降速.
        foreach my $node (keys %{$login_user_hash{$user_name}}) {
            &air_add_user_action($db_air,$user_name, $node, "reject","traffic low.",30);
        }

        &air_change_user_net_state($db_air,$user_name,"low");

        print("用户$user_name"."流量低位，下线并降速.\n");
    }

    $sql_update = "";
    foreach my $id (keys %quota_hash) {
        if ($quota_hash{$id}{'change'} eq "yes") {
            my $state = $quota_hash{$id}{"state"};
            my $state_desc = $quota_hash{$id}{"state_desc"};
            my $remain = $quota_hash{$id}{"remain"};
            $sql_update  = "update user_quota set state = '$state', state_desc = '$state_desc',";
            $sql_update .= "remain = $remain where auto_id = $id;";
            $sth = $db_air->prepare($sql_update);
            print("$sql_update\n");
            $sth->execute();
        }
    }

    $sql =  "select traffic_idle from ";
    $sql .= "user_mon where user_name = '$user_name' and date_mon = '$year$mon'";
    $sth = $db_air->prepare($sql);
    print("user_mon_sql:$sql\n");
    if ($sth->execute()) {
        if (my $ref = $sth->fetchrow_hashref()) {
            $sql_update = "update user_mon set traffic_idle = traffic_idle + $t_idle,";
            if ($traffic_free > 0) {
                $sql_update .= "traffic_free = traffic_free + $traffic_free, ";
                $sql_update .= "traffic_free_cost = traffic_free_cost + $traffic_free_cost, ";
            }

            $sql_update .= "traffic_busy = traffic_busy + $t_busy, traffic_bill = traffic_bill + $t_bill,";
            $sql_update .= "traffic_remain = $user_remain where ";
            $sql_update .= "user_name = '$user_name' and date_mon = '$year$mon';";

        } else {
            $sql_update  = "insert into user_mon (user_name, traffic_idle, traffic_busy, ";
            $sql_update .= " traffic_free, traffic_free_cost,";
            $sql_update .= "traffic_bill, traffic_remain, date_mon) values ";
            $sql_update .= "('$user_name', $t_idle, $t_busy, $traffic_free, $traffic_free_cost, ";
            $sql_update .= " $t_bill, $user_remain, '$year$mon');";
        }
    }

    print("$sql_update\n");
    $sth = $db_air->prepare($sql_update);
    $sth->execute();

    if ($traffic_free_cost > 0) {
        $sql_update = "update user_info set balance = balance - $traffic_free_cost where user_name='$user_name'";
        print("$sql_update\n");
        $sth = $db_air->prepare($sql_update);
        $sth->execute();
    }

}



sub collect_file()
{
    my $bw_dir = "$root/bw";
    my $content = `ls $bw_dir`;

    my @file_array = ();
    @file_array = grep /\.bw/, split /\n/, $content;

    for my $file_name (@file_array) {
        print("file_name=$file_name\n");
        &collect_bw($file_name);
    }

}


sub collect_bw()
{ 
    my ($file_name) = @_;
    my $bw_dir = "$root/bw";
    my $bw_dir_done = "$root/done";
    if (not $file_name =~ /^(\d\d\d\d)(\d\d)(\d\d)\d+\.([^\s]+)\.bw/) {
        #201407211150.cn1.bw
        #my ($b_year,$b_mon,$b_day) = ($1,$2,$3);
        print("format error\n");
        return;
    }

    my ($b_year,$b_mon,$b_day,$node) = ($1,$2,$3,$4);
    
    my $bw_file_done_dir = "$bw_dir_done/$b_year/$b_mon/$b_day";
    `mkdir -p $bw_file_done_dir` if (not -e $bw_file_done_dir);

    my $bw_file_done = "$bw_file_done_dir/$file_name";
    my $bw_file = "$bw_dir/$file_name";
    if (not open(FD,$bw_file)){
        print("open file $bw_file failed, mv $bw_file to $bw_file_done directly\n");
        #`mv $bw_file $bw_file_done`;
    }

    my $line = "";
    print("open file $bw_file success\n");
    while ($line = <FD>) {
        print("$line");
        #bw      king    0.45   1
        if ($line =~ /bw\s+([^\s]+)\s+([\d\.]+)\s(\d+)/) {
            $bw_hash{$1}{"bw"} += $2;
            $bw_hash{$1}{"busy_flag"} = $3;
            $login_user_hash{$1}{$node} = 1;
        }
    }

    `mv $bw_file $bw_file_done`;
    close(FD);
}

sub air_transaction()
{
    my ($db, $auto_id, $packet_id, $user_name, $check_date)  = @_;
    my $start_date = $check_date;
    if ($check_date =~ /^(\d+)-(\d+)/) {
        $start_date = "$1-$2-01 00:00:00";
    }

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

    $stop_date = &air_get_date_by_month_offset($start_date, $period);
    $sql = "insert into packet_deal (user_name, packet_id, start_date, stop_date,";
    $sql .= "price, state, create_date) values ";
    $sql .= "('$user_name', $packet_id, '$start_date', '$stop_date', $price, 'init', ";
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
    $sql .= "'未使用', '$desc', 'packet', '$start_date', '$stop_date', now())";

    if ($beans > 0) {
        $sql .= ",('$user_name', 'beans', $beans, $beans, $deal_id,'enable', '未使用', ";
        $sql .= "'$desc', 'packet', '$start_date', '$stop_date', now())";
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

sub process_packet_auto
{
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
}

