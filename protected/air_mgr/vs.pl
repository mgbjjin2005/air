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

my ($db_air);
my ($v_day, $sth,$v_date, $sql);
my ($year, $mon, $day, $hour, $min);

($year, $mon, $day, $hour, $min) = &air_get_normalized_time();
$v_day="$year-$mon-$day";
$v_date="$year-$mon-$day $hour:$min:00";

my $media_root = "/data/media";
my $media_root_ln = "/home/media";

my $root_url = "http://10.0.0.10/web_download";
my $index_file = "$root_url/index.$year$mon.txt";

my $msg = `curl -s $index_file`;

my @index_content = split /\r/, $msg;

my %class_hash = ();
my %class2_hash = ();

$db_air = &air_connect_db("air", "localhost", "air", "***King1985***", "3306");
$sql  = "select auto_id, class, m_kind, m_name, value,parent_class_id from media_category";
$sth = $db_air->prepare($sql);
if ($sth->execute()) {
    while (my $ref = $sth->fetchrow_hashref()) {
        my ($id, $class, $kind) = ($ref->{"auto_id"}, $ref->{"class"}, $ref->{"m_kind"});
        my ($name, $value, $parent_id) = ($ref->{"m_name"}, $ref->{"value"}, $ref->{"parent_class_id"});
        if ($class == 1) {
            $class_hash{$id}{"name"} = $name;
            $class_hash{$id}{"value"} = $value;
        } else {
            $class2_hash{$parent_id}{$id}{"name"} = $name;
            $class2_hash{$parent_id}{$id}{"value"} = $value;
        }
    }
}

sub get_class1_id()
{
    my $name = shift;
    my $flag = 0;
    foreach my $id (keys %class_hash) {
        #print "class name=".$class_hash{$id}{"name"}." name=$name id=".$class_hash{$id}{"value"}."\n";
        if ($name =~ /$class_hash{$id}{"name"}/) {
            $flag += $class_hash{$id}{"value"};
            #print("match\n");
        }   
    }

    return $flag;
}

sub get_class2_id()
{
    my ($class1_id, $name) = @_;
    my $flag = 0;
    foreach my $id (keys %{$class2_hash{$class1_id}}) {
        my $value = $class2_hash{$class1_id}{$id}{"value"};
        my $s_name = $class2_hash{$class1_id}{$id}{"name"};
        #print("class2 name=$s_name, id=$id, value=$value, name=$name\n");
        if ($name =~ /$s_name/) {
            $flag += $value;
           # print("match\n");
        }
    }

    return $flag;
}

=pod
BEGIN SUMMARY

中文名  (cha_name)  : X战警:第一战 
                      母语名  (original_name) : X-Men: First Class
内部名称(alias)         : x.men.first.class
导演    (director)      : 马修·沃恩
主要演员(actors)        :  詹姆斯·麦卡沃伊 / 迈克尔·法斯宾德 / 詹纽瑞·琼斯
影片时长(time_length)   : 132
上映时间(show_date)     : 2011-06-03
视频类目(kind)          : 电影
类型    (type)      : 剧情/动作/科幻
区域    (area)      : 欧美
票房    (revenue)   : 677,000,000
imdb分数(imdb_num)  :
豆瓣分数(douban_num)    : 8.0
剧情简介(desc)      :
    年轻的X教授查尔斯（詹姆斯·麦卡沃伊 James McAvoy 饰）和万磁王埃里克（迈克尔·法斯宾德 Michael Fassbender 饰）是一对志向相投的好朋友。他们最早发现了自己的超能力，并与其他几个变种人一起在CIA工作。引发埃里克超能力的纳粹战争贩子肖（凯文·贝肯 Kevin Bacon 饰）一直试图挑起核战争，而想方设法挑起美苏两国的矛盾。埃里克和查尔斯一直与肖手下的变种人战斗，努力阻止肖的阴谋。肖怂恿苏联引发古巴导弹危机，查尔斯和埃里克获知消息后，在海湾与肖手下的变种人拉开了对决的一战。最终，肖被查尔斯和埃里克合力消灭。可是，在这个过程中，埃里克逐渐被肖的政治观点影响，与查尔斯产生裂痕。通过海湾的这一战，埃里克和查尔斯最终决裂，一方变成支持人类与变种人和平共存的X教授；另一方坚持通过消灭人类换来变种人兴起的观点，自称万磁王.........

    END


    BEGIN DETAIL

    路径   (path)    : x.men.first.class/x.men.first.class.720p.mp4
    价格   (price)   : 2.0
    描述   (chs_desc): X战警:第一战 720P

    END
=cut


my ($state,$des_flag) = ("",0);
 
my ($m_chs_name,$m_original_name, $m_alias, $m_director) = ("","","","");
my ($m_actor, $m_time_length, $m_show_date, $m_kind) = ("",0,"",0);
my ($m_type, $m_area, $m_revenue, $m_imdb_num) = (0, 0, 0,0);
my ($m_douban_num, $m_desc, $m_path, $m_price,$m_chs_desc) = (0,"","",0,""); 
my ($m_episode) = (0); 
my ($id_kind, $id_area, $id_type);
sub do_state()
{
    if ($state eq "summary") {
        print("desc:$m_desc");
        if (length($m_chs_name) < 1 or
            length($m_original_name) < 1 or
            length($m_alias) < 1 or
            length($m_director) < 1 or
            length($m_actor) < 1 or
            $m_time_length < 1 or
            length($m_show_date) < 1)
         {
             print("...1\n");
            return 0;
         }

        if (($id_kind = &get_class1_id($m_kind)) < 1) {
             print("...2 id_kind=$id_kind\n");
            return 0;
        }
        
        if (($id_area = &get_class1_id($m_area)) < 1) {
            print("...3 id_area=$id_area\n");
            return 0;
        }
        
        if (($id_type = &get_class2_id($id_kind, $m_type)) < 1) {
            print("...4\n");
            return 0;
        }

        print("$id_kind, $id_area, $id_type\n");
        if (not $m_show_date=~ /\d+-\d+-\d+/) {
            print("show date = $m_show_date\n");
            return 0;
        }

        &update_media();
        return 1;

    } elsif ($state eq "detail") {
        if (length($m_path) < 1 or length($m_chs_desc) < 1) {
            return 0;
        }
        
        if (length($m_price) < 1) {
            $m_price = 0.0;
        }

        if (length($m_price) < 1) {
            $m_episode = 0;
        }
        &update_media_detail();
        return 1;
    }

    return 0;
}

sub down_load_poster()
{
    my $url = "$root_url/$year$mon/$m_alias/$m_alias.jpg";
    my $media_dir = "$media_root/$year$mon/$m_alias";
    my $jpg_path = "$media_dir/$m_alias.jpg";

    my $media_dir_ssd = "$media_root_ln/$year$mon/poster";
    my $jpg_path_ssd = "$media_dir_ssd/$m_alias.jpg";

    if (-e $jpg_path_ssd and -e $jpg_path ) {
        return;
    }

    `mkdir -p $media_dir` if (not -e $media_dir);
    `mkdir -p $media_dir_ssd` if (not -e $media_dir_ssd);

    `wget "$url" -q -O $jpg_path`;
    `cp -f $jpg_path $jpg_path_ssd`;

}

sub down_load_detail()
{
    my ($alias,$file_name) = ("","");;
    if(not $m_path =~ /\/?([^\/]+)\/([^\/]+)\.mp4/) {
        print("文件名错误 $m_path\n");
        return;
    }

    ($alias, $file_name) = ($2, $2);
    $sql = "select auto_id from media where m_alias = '$1'";
    $sth = $db_air->prepare($sql);
    if(not $sth->execute()) {
        print("sql execute error. ".$sth->errstr."\n");
    }
    my $m_id = 0;
    if (my $ref = $sth->fetchrow_hashref()) {
        $m_id = $ref->{"auto_id"};
    
    } else {
        print("没有查询到任何数据\n");
    }
    
    my $url = "$root_url/$year$mon/$m_path";
    my $media_dir = "$media_root/$year$mon/$alias";
    my $video_path   = "$media_dir/$alias.mp4";
    my $video_path_ts   = "$media_dir/$alias.ts";
    my $video_path_m3u8 = "$media_dir/$alias.m3u8";
    my $url_m3u8 = "media/$year$mon/$alias/$alias.m3u8";

    my $video_dir_ssd = "$media_root_ln/$year$mon/$alias";

    if (-e $video_path) {
        return;
    }

    `mkdir -p $media_dir` if (not -e $media_dir);
    `ln -s $media_dir $video_dir_ssd` if (not -e $video_dir_ssd);

    `wget "$url" -q -O $video_path`;
    my $space = -s $video_path;
    $space = sprintf("%.2f", $space / 1024 /1024);
    if ($space < 2) {
        `rm -rf $media_dir`;
        print("下载的文件太小，不保存\n");
        return;
    }

    chdir($media_dir);
    `ffmpeg -y -i $video_path -vcodec copy -acodec copy -vbsf h264_mp4toannexb $video_path_ts`;
    `ffmpeg -i $video_path_ts -c copy -map 0 -f segment -segment_list $video_path_m3u8 -segment_time 60 $alias-%04d.ts`;
    `rm -f $video_path` if (-e $video_path);
    `rm -f $video_path_ts` if (-e $video_path_ts);

    if (not -e $video_path_m3u8) {
        print("没有发现m3u8文件 $video_path_m3u8\n");
        return;
    }

    $sql  = "replace into media_detail (m_id,m_alias,m_space,m_price,m_chs_desc,";
    $sql .= "m_episode,m_video_path,m_real_path) values (";
    $sql .= "$m_id,'$alias',$space,$m_price,'$m_chs_desc',$m_episode,";
    $sql .= "'$url_m3u8','$media_dir')";

    $sth = $db_air->prepare($sql);
    if(not $sth -> execute()) {
        print("sql execute error. ".$sth->errstr."\n");
        #&air_write_log("ERROR ".$sth->errstr);
    }

    $sql = "update media set enable_state = 'enable' where auto_id = $m_id";
    $sth = $db_air->prepare($sql);
    if(not $sth -> execute()) {
        print("sql execute error. ".$sth->errstr."\n");
    }

    return;
}


sub update_media()
{
    $m_desc = $db_air->quote($m_desc);
    my $poster_url = "media/$year$mon/$m_alias/$m_alias.jpg";
    $sql = "insert into media (m_chs_name,m_original_name,m_alias,m_director,";
    $sql .= "m_main_actors,m_time_length,m_show_date,m_kind_flag,m_area_flag,";
    $sql .= "m_type_flag,m_kind_desc,m_area_desc,m_type_desc,m_revenue,m_imdb_num,";
    $sql .= "m_douban_num,m_pic_path,m_des) values (";
    $sql .= "'$m_chs_name','$m_original_name','$m_alias','$m_director',";
    $sql .= "'$m_actor', $m_time_length,'$m_show_date', $id_kind, $id_area,";
    $sql .= "$id_type,'$m_kind','$m_area','$m_type',$m_revenue,";
    $sql .= "$m_imdb_num, $m_douban_num,'$poster_url',$m_desc)";

    $sth = $db_air->prepare($sql);
    if(not $sth -> execute()) {
        print("sql execute error. ".$sth->errstr."\n");
        #&air_write_log("ERROR ".$sth->errstr);
    }
    &down_load_poster();
    print("do update_media\n\n");
}

sub update_media_detail()
{
    &down_load_detail();
    print("do update_media_detail\n\n");    
}

for my $key (@index_content) {

    $key =~ s/\n//g;
    if (length($key) == 0 or $key =~ /^\s+#/ or $key =~ /^\s+$/) {
        next;
    }

    if ($state eq "" ) {
        if ($key =~ /BEGIN\s+SUMMARY/) {
            $state = "summary";
            print("$state start\n");
            next;

        } elsif ($key =~ /BEGIN\s+DETAIL/) {
            $state = "detail";
            print("$state start\n");
            next;
        }
    }

    if ($key =~ /\s*END/) {
        print("$state finish $key\n");
        if(not &do_state($state)) {
            print("有错误发生\n");
        }
        $state = "";
        $des_flag = 0;
        next;
    }

    if ($des_flag) {
        if ($key =~ /^\s+(.*)/) {
            $m_desc .= "$1\n";
            next;
        }
    }

    if ($key =~ /\(chs_name\)\s*:\s*(.*)$/) {
        $m_chs_name = $1;
        while ($m_chs_name =~ /\s+$/) {
            chop($m_chs_name);
        }
        print("m_chs_name=$m_chs_name\n");
        next;
    }

    if ($key =~ /\(original_name\)\s*:\s*(.*)$/) {
        $m_original_name = $1;
        while ($m_original_name =~ /\s+$/) {
            chop($m_original_name);
        }
        print("m_original_name=$m_original_name\n");
        next;
    }

    if ($key =~ /\(alias\)\s*:\s*(.*)$/) {
        $m_alias = $1;
        while ($m_alias =~ /\s+$/) {
            chop($m_alias);
        }
        print("m_alias=$m_alias\n");
        next;
    }

    if ($key =~ /\(director\)\s*:\s*(.*)$/) {
        $m_director = $1;
        while ($m_director =~ /\s+$/) {
            chop($m_director);
        }
        print("m_director=$m_director\n");
        next;
    }

    if ($key =~ /\(actors\)\s*:\s*(.*)$/) {
        $m_actor = $1;
        while ($m_actor =~ /\s+$/) {
            chop($m_actor);
        }
        print("m_actor=$m_actor\n");
        next;
    }

    if ($key =~ /\(time_length\)\s*:\s*(\d+)$/) {
        $m_time_length = $1;
        print("m_time_length=$m_time_length\n");
        next;
    }

    if ($key =~ /\(show_date\)\s*:\s*(.*)$/) {
        $m_show_date = $1;
        while ($m_show_date =~ /\s+$/) {
            chop($m_show_date);
        }
        print("m_show_date=$m_show_date\n");
        next;
    }

    if ($key =~ /\(kind\)\s*:\s*(.*)$/) {
        $m_kind = $1;
        while ($m_kind =~ /\s+$/) {
            chop($m_kind);
        }
        print("m_kind=$m_kind\n");
        next;
    }

    if ($key =~ /\(type\)\s*:\s*(.*)$/) {
        $m_type = $1;
        while ($m_type =~ /\s+$/) {
            chop($m_type);
        }
        print("m_type=$m_type\n");
        next;
    }

    if ($key =~ /\(area\)\s*:\s*(.*)$/) {
        $m_area = $1;
        while ($m_area =~ /\s+$/) {
            chop($m_area);
        }
        print("m_area=$m_area\n");
        next;
    }

    if ($key =~ /\(revenue\)\s*:\s*(.*)$/) {
        $m_revenue = $1;
        while ($m_revenue =~ /\s+$/) {
            chop($m_revenue);
        }
        if (not defined($m_revenue)) {
            $m_revenue = 0;
        }

        $m_revenue =~ s/,//g;
        print("m_revenue=$m_revenue\n");
        next;
    }

    if ($key =~ /\(imdb_num\)\s*:\s*(.*)$/) {
        $m_imdb_num = $1;
        while ($m_imdb_num =~ /\s+$/) {
            chop($m_imdb_num);
        }
        if (not defined($m_imdb_num) or length($m_imdb_num) < 1) {
            $m_imdb_num = 0.0;
        }

        $m_imdb_num *= 1.0;
        if ($m_imdb_num <= 0) {
            $m_imdb_num = 0;
        }

        print("m_imdb_num=$m_imdb_num\n");
        next;
    }

    if ($key =~ /\(douban_num\)\s*:\s*(.*)$/) {
        $m_douban_num = $1;
        while ($m_douban_num =~ /\s+$/) {
            chop($m_douban_num);
        }
        if (not defined($m_douban_num) or length($m_douban_num) < 1) {
            $m_douban_num = 0.0;
        }

        $m_douban_num *= 1.0;
        if ($m_douban_num <= 0) {
            $m_douban_num = 0;
        }

        print("m_douban_num=$m_douban_num\n");
        next;
    }

    if ($key =~ /\(desc\)\s*:\s*(.*)$/) {
        $m_desc = "";
        if (defined($1) and length($1) > 1) {
            $m_desc = "$1\n";
        }
        $des_flag = 1;
        next;
    }

    if ($key =~ /\(path\)\s*:\s*(.*)$/) {
        $m_path = $1;
        while ($m_path =~ /\s+$/) {
            chop($m_path);
        }
        print("m_path=$m_path\n");
        next;
    }

    if ($key =~ /\(episode\)\s*:\s*(.*)$/) {
        $m_episode = $1;
        while ($m_episode =~ /\s+$/) {
            chop($m_episode);
        }
        print("m_episode=$m_episode\n");
        next;
    }

    if ($key =~ /\(price\)\s*:\s*(.*)$/) {
        $m_price = $1;
        while ($m_price =~ /\s+$/) {
            chop($m_price);
        }
        print("m_price=$m_price\n");
        next;
    }

    if ($key =~ /\(chs_desc\)\s*:\s*(.*)$/) {
        $m_chs_desc = $1;
        while ($m_chs_desc =~ /\s+$/) {
            chop($m_chs_desc);
        }
        print("m_chs_desc=$m_chs_desc\n");
        next;
    }

}

# 
