#!/usr/bin/perl -w
use strict;
use DBI;
use constant ONE_DAY => 86400;

my $media_root_dir = "/home/media_center";
my $media_dir = "";
my ($alias, $resolution, $host) = ("","","");

while (my $arg = shift()) {
        if ($arg =~ /--alias=([^\s]+)$/) {
                $alias = $1;
                next;
        } elsif ($arg =~ /--resolution=([^\s]+)$/) {
                $resolution = $1;
                next;
        }
}

if ($alias eq "" or $resolution eq "") {
    print("args null\n");
    exit 0;
}

if (not ($resolution eq "1080p" or $resolution eq "720p" or $resolution eq "480p")) {
    print("resolution values: 1080p,720p,480p\n");
    exit 0;
}

$media_dir = "$media_root_dir/$alias";
`mkdir -p $media_dir` if (not -e $media_dir);
chdir("$media_dir");

my $common_path = "$alias.$resolution";
my $media_path = "$common_path.mp4";
my $media_ts_path = "$common_path.ts";
my $media_m3u8_path = "$common_path.m3u8";

my $url = "http://10.0.0.4/moive/$alias.$resolution.mp4";
`rm $media_path` if (-e $media_path);
`rm $media_ts_path` if (-e $media_ts_path);
`rm $media_m3u8_path` if (-e $media_m3u8_path);

`wget -O $media_path $url`;

`ffmpeg -y -i $media_path -vcodec copy -acodec copy -vbsf h264_mp4toannexb $media_ts_path`;
`mkdir $resolution` if (not -e $resolution);
`ffmpeg -i $media_ts_path -c copy -map 0 -f segment -segment_list $media_m3u8_path -segment_time 60 $resolution/$alias-%04d.ts`;
`rm -f $media_path` if (-e $media_path);
`rm -f $media_ts_path` if (-e $media_ts_path);
exit 0;

#=================================================================


sub get_normalized_time
{
    my $time = shift || time();
    my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($time);
    $mday = ($mday < 10) ? "0$mday" : $mday;
    $mon  = ($mon < 9) ? "0".($mon+1) : $mon + 1;
    $year += 1900; #从1900年算起的年数
    $min = int($min / 5) * 5;
    $hour = ($hour < 10) ? "0$hour" : $hour;
    $min = ($min < 10) ? "0$min" : $min;
    return ($year, $mon, $mday, $hour, $min);
}
