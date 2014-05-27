#Package
#
#Functions for load cdn log
#
package AirMgr;
require Exporter;
@ISA = qw(Exporter);
@EXPORT = qw(air_get_date_type air_connect_db air_get_normalized_time air_write_log);
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

    if ($time =~ /\s+(\d+):\d+:\d+/) {
        if ($1 >= 7 && $1 < 23) {
            return "busy";
        }
    }

    return "idle";
}

sub air_write_log()
{

}

1;
