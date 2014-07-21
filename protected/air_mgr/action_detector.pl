=pod
#检查是否有动作要做。（用户下线，用户信息，其它信息等）
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

my ($db_air, $db_radius);
my ($v_day, $v_date, $sql,$sth);
my ($year, $mon, $day, $hour, $min);

($year, $mon, $day, $hour, $min) = &air_get_normalized_time();
$v_day="$year-$mon-$day";
$v_date="$year-$mon-$day $hour:$min:00";

$db_air    = &air_connect_db("air", "localhost", "air", "***King1985***", "3306");
$db_radius = &air_connect_db("radius", "localhost", "air", "***King1985***", "3306");

my ($g_node,$g_ratio,$g_over,$g_price) = &air_get_global_info($db_air);

$sql  = "select auto_id, user_name, action, msg from user_action where ";
$sql .= "node = '$g_node' and state = 'init'";
$sth = $db_air->prepare($sql);
if ($sth->execute()) {
    while (my $ref = $sth->fetchrow_hashref()) {
        my ($auto_id, $user_name) = ($ref->{"auto_id"}, $ref->{"user_name"});
        my ($action, $msg) = ($ref->{"action"}, $ref->{"msg"});
        if ($action eq "reject") {
            #踢用户
            &reject_user($user_name,$msg);
            &air_update_action($db_air,$auto_id,"success","done");
        }
    }
}

exit 0;

#----------------------------------------------function---------------------------------------------------

sub reject_user()
{
    my($user_name) = @_;
    $sql  = "select acctsessionid session_id,";
    $sql .= "nasipaddress ros_ip, framedipaddress client_ip from ";
    $sql .= "radacct where username = '$user_name' and acctstoptime is null";
    $sth = $db_radius->prepare($sql);
    if ($sth->execute()) {
        while (my $ref = $sth->fetchrow_hashref()) {
            my ($session_id, $ros_ip) = ($ref->{"session_id"}, $ref->{"ros_ip"});
            my ($client_ip) = ($ref->{"client_ip"});
            &do_reject($user_name, $client_ip, $ros_ip, $session_id);
        }
    }
}

sub do_reject()
{
    my ($user_name,$client_ip,$ros_ip,$session_id) = @_;
    print("ros_ip =$ros_ip, session_id=$session_id, user_name=$user_name, client_ip=$client_ip\n");
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

