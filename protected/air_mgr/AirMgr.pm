#Package
#
#Functions for load cdn log
#
package AirMgr;
require Exporter;
@ISA = qw(Exporter);
@EXPORT = qw(air_update_action air_add_user_action air_change_user_net_state air_get_global_info air_connect_db_tranc air_msg_user air_get_date_type air_connect_db air_get_normalized_time air_write_log);
use strict;
use warnings;
use DBI;

use constant LOG=>"/temp/air_mgr.log";

sub air_connect_db(){
    my($name,$host,$user,$passwd,$port) = @_;
    my $DB_DBH = DBI->connect("DBI:mysql:database=$name;host=$host:$port","$user", "$passwd");
    if (!$DB_DBH){
        write_log("ERROR ".$DBI::errstr);
        exit 0;
    }
    $DB_DBH->do("SET NAMES 'utf8'");
    return $DB_DBH;
}

sub air_connect_db_tranc(){
    my($name,$host,$user,$passwd,$port) = @_;
    my $DB_DBH = DBI->connect("DBI:mysql:database=$name;host=$host:$port","$user",
                "$passwd", {'RaiseError' => 1, 'AutoCommit' => 0});
    if (!$DB_DBH){
        write_log("ERROR ".$DBI::errstr);
        exit 0;
    }
    $DB_DBH->do("SET NAMES 'utf8'");
    return $DB_DBH;
}

sub air_add_user_action()
{
    my ($db,$user_name,$node,$action,$msg,$interval)= @_;
    if ($interval <= 0) {
        $interval = 5;
    }

    my ($year, $mon, $day, $hour, $min) = &air_get_normalized_time(time() - ($interval * 60));
    my $date = "$year-$mon-$day $hour:$min:00";
    my $sql = "select user_name from user_action where user_name ='$user_name' ";
    $sql .= "and node='$node' and action='$action' and state = 'success' and  ";
    $sql .= "create_date >= '$date' order by create_date desc limit 1";
   
    print("Air_mgr:$sql\n");
    my $sth = $db->prepare($sql);
    if ($sth->execute()) {
        if (my $ref = $sth->fetchrow_hashref()) {
            return;
        }
    }
    
    $sql  = "insert into user_action ";
    $sql .= "(user_name,node,action,msg,create_date) values ";
    $sql .= "('$user_name','$node','$action','$msg', now())";
    $sth = $db->prepare($sql);
    print("Air_mgr:$sql\n");
    $sth->execute();
}

sub air_update_action()
{
    my ($db, $auto_id, $state, $state_msg)= @_;
    my $sql  = "update user_action  set state='$state', state_msg='$state_msg' ";
    $sql .= "where auto_id = $auto_id";
    my $sth = $db->prepare($sql);
    print("Air_mgr air_update_action:$sql\n");
    $sth->execute();
}

sub air_change_user_net_state()
{
    my ($db,$user_name,$net_state) = @_;
    my $sql  = "update user_info set net_state='$net_state' where user_name = '$user_name'";
    my $sth = $db->prepare($sql);
    print("Air_mgr:$sql\n");
    $sth->execute();
}

sub air_get_global_info()
{
    my ($db) = @_;
    my ($node,$idle_bw_ratio,$max_over_traffic,$traffic_free_price) = ("",1,1,0.05);
    my $sql  = "select g_key,g_value from global_info";
    my $sth = $db->prepare($sql);

    if ($sth->execute()) {
        while (my $ref = $sth->fetchrow_hashref()) {
            my ($key,$value) = ($ref->{'g_key'}, $ref->{'g_value'});
            if ($key eq "node") {
                $node = $value;

            } elsif ($key eq "idle_bw_ratio") {
                $idle_bw_ratio = $value;

            } elsif ($key eq "max_over_traffic") {
                $max_over_traffic = $value;

            } elsif ($key eq "traffic_free_price") {
                $traffic_free_price = $value;
            }
        }
    }

    return ($node,$idle_bw_ratio,$max_over_traffic,$traffic_free_price);
}


sub air_get_normalized_time
{
    my $time = shift || time();
    my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($time);
    $mday = ($mday < 10) ? "0$mday" : $mday; #这个月的第几天[1,31]
    $mon  = ($mon < 9) ? "0".($mon+1) : $mon + 1; #月数[0,11],要将$mon加1之后，才能符合实际情况。
    $year += 1900; #从1900年算起的年数
    $min = int($min / 5) * 5;
    $hour = ($hour < 10) ? "0$hour" : $hour;
    $min = ($min < 10) ? "0$min" : $min;
    return ($year, $mon, $mday, $hour, $min);
}

#闲时忙时判断
# 7点到23点为忙时，其它时间为闲时
sub air_get_date_type
{
    #2014-05-17 20:26:43
    my $time = shift;

    if ($time =~ /\s+(\d+):(\d+):\d+/) {
        my ($hour, $min) = ($1, $2);
        if ($hour > 7 && $hour < 23) {
            return "busy";

        } elsif ($hour == 7 && $min > 0) {
            return "busy";

        } elsif ($hour == 23 && $min == 0) {
            return "busy";
        }
    }
    return "idle";
}

sub air_write_log()
{

}

sub air_msg_user()
{
}

1;
