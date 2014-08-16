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

my ($db_air,$v_day, $sth,$v_date, $sql);
my ($year, $mon, $day, $hour, $min);

($year, $mon, $day, $hour, $min) = &air_get_normalized_time();

while (my $arg = shift()) {
    if ($arg =~ /--year=(\d+)$/) {
        $year = $1;
        next;
    } elsif ($arg =~ /--mon=(\d*)$/) {
        $mon = $1;
        next;
    } elsif ($arg =~ /--day=(\d*)$/) {
        $day = $1;
        next;
    }
}


$v_day="$year-$mon-$day";
$v_date="$year-$mon-$day $hour:$min:00";

my %dir_hash = ();
$db_air = &air_connect_db("air", "localhost", "air", "***King1985***", "3306");
my ($g_node,$g_ratio,$g_a,$g_b) = &air_get_global_info($db_air);

&fill_dir_hash();

my $media_root_ln = "/home/media";

my $root_url = "http://10.0.0.10/mp4";
my $index_file = "$root_url/index.$year$mon.txt";

my $msg = `curl -s $index_file`;

my @index_content = split /\r/, $msg;

my %class_hash = ();
my %class2_hash = ();

$db_air = &air_connect_db("air", "localhost", "air", "***King1985***", "3306");
&get_class();

my ($state,$des_flag) = ("",0);
my ($m_chs_name,$m_original_name, $m_alias, $m_director) = ("","","","");
my ($m_actor, $m_time_length, $m_show_date, $m_kind) = ("",0,"",0);
my ($m_type, $m_area, $m_revenue, $m_imdb_num) = (0, 0, 0,0);
my ($m_douban_num, $m_desc, $m_path, $m_price,$m_chs_desc) = (0,"","",0,""); 
my ($m_episode, $m_version, $m_update_info) = (0,1); 
my ($id_kind, $id_area, $id_type);

&do_cycle();

&update_dir(0);
exit 0;


#------------------------------------------------
sub fill_dir_hash(){
    my $sql = "select auto_id, node, dir, total, remain, ssd, state from disk_quota where node = '$g_node'";

    $sth = $db_air->prepare($sql);
    if ($sth->execute()) {
        while (my $ref = $sth->fetchrow_hashref()) {
            my ($id,$dir,$total) = ($ref->{"auto_id"}, $ref->{"dir"}, $ref->{"total"});
            my ($remain,$ssd,$state) = ($ref->{"remain"}, $ref->{"ssd"}, $ref->{"state"});
         
            print("$dir, total=$total remain=$remain\n");
            $dir_hash{$id}{"dir"} = $dir;
            $dir_hash{$id}{"total"} = $total;
            $dir_hash{$id}{"remain"} = $remain;
            $dir_hash{$id}{"ssd"} = $ssd;
            $dir_hash{$id}{"state"} = $state;
        }
    }
}


sub choose_dir()
{
    my $id = 0;
    my $cur_remain = 0;
    foreach my $key (keys %dir_hash) {
        my ($dir,$total) = ($dir_hash{$key}{"dir"}, $dir_hash{$key}{"total"});
        my ($remain) = ($dir_hash{$key}{"remain"});

        if ($remain < 15) {
            next;
        }

        if ($dir_hash{$key}{"ssd"} eq "yes") {
            next;
        }

        if ($dir_hash{$key}{"state"} eq "disable") {
            next;
        }

        my $used = `du -sm $dir | awk '{print \$1}'`;
        chomp($used);
        $used = int($used/1024);

        $remain = $dir_hash{$key}{"total"} - $used;
        #print("$dir used=$used remain=$remain\n");

        if ($cur_remain < $remain) {
            $id = $key;
            $cur_remain = $remain;
            next;
        }

    }

    #print("id $id is selected\n");
    return $id;
}


sub update_dir()
{
    my ($id) = @_;
    foreach my $key (keys %dir_hash) {
        if ($id > 0 and ($id ne $key)) {
            next;
        }

        my $dir = $dir_hash{$key}{"dir"};
        my $used = `du -sm $dir | awk '{print \$1}'`;
        chomp($used);
        $used = int($used/1024);

        $dir_hash{$key}{"remain"} = $dir_hash{$key}{"total"} - $used;
        my $remain = $dir_hash{$key}{"remain"};
        $sql = "update disk_quota set remain = $remain where auto_id = $key";
        
        $sth = $db_air->prepare($sql);
        if(not $sth -> execute()) {
            print("sql execute error. ".$sth->errstr."\n");
        }
    }
}


sub get_class_id()
{
    my ($hash, $name, $kind) = @_;
    my ($flag, $other_value) = (0, 0);
    foreach my $id (keys %{$$hash{$kind}}) {
        my $pattern = $$hash{$kind}{$id}{"pattern"};
        if ($pattern =~ /其它/) {
            $other_value = $$hash{$kind}{$id}{"value"};
        }

        my @items = split(/\//, $name);
        for my $item (@items) {
            $item =~ s/\s//g;
            $item = ",$item,";
            if ($pattern =~ /$item/) {
                $flag |= $$hash{$kind}{$id}{"value"};
            }
        }

    }

    if ($flag == 0) {
        $flag = $other_value;
    }

    return $flag;
}


sub do_state()
{
    if ($state eq "summary") {
        if (length($m_chs_name) < 1 or
                length($m_original_name) < 1 or
                length($m_alias) < 1 or
                length($m_director) < 1 or
                length($m_actor) < 1 or
                $m_time_length < 1 or
                length($m_show_date) < 1)
        {
            print("$m_chs_name,$m_original_name,$m_alias,$m_director,$m_actor,$m_time_length,$m_show_date\n");
            print("...1\n");
            return 0;
        }


        if (($id_kind = &get_class_id(\%class_hash, $m_kind, "video_kind")) < 1) {
            $id_kind = 0;
        }

        if (($id_area = &get_class_id(\%class_hash, $m_area, "area")) < 1) {
            $id_area = 0;
        }

        if (($id_type = &get_class_id(\%class2_hash, $m_type, $id_kind)) < 1) {
            $id_type = 0;
        }

        if (not $m_show_date=~ /\d+-\d+-\d+/) {
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
            $m_episode = 1;
        }
        &update_media_detail();
        return 1;
    }

    return 0;
}

sub down_load_poster()
{
    my $url = "$root_url/$year$mon/$m_alias/$m_alias.jpg";
    my $media_dir_ssd = "$media_root_ln/$year$mon/poster";
    my $jpg_path_ssd = "$media_dir_ssd/$m_alias.jpg";

    if (-e $jpg_path_ssd) {
        `rm -f $jpg_path_ssd`;
    }

    `mkdir -p $media_dir_ssd` if (not -e $media_dir_ssd);

    `wget "$url" -q -O $jpg_path_ssd`;
}

sub down_load_detail()
{
    my ($m_alias, $alias,$file_name) = ("","","");;
    if(not $m_path =~ /\/?([^\/]+)\/([^\/]+)\.mp4/) {
        print("文件名错误 $m_path\n");
        return;
    }

    ($m_alias, $alias, $file_name) = ($1, $2, $2);
    $sql = "select auto_id from media where m_alias = '$m_alias'";
    $sth = $db_air->prepare($sql);
    if(not $sth->execute()) {
        print("没有查询到对应的电影信息，忽略本次下载$m_path sql execute error. ".$sth->errstr."\n");
        return;
    }
    my $m_id = 0;
    if (my $ref = $sth->fetchrow_hashref()) {
        $m_id = $ref->{"auto_id"};
    
    } else {
        print("没有查询到任何数据\n");
    }
   
    my $id = &choose_dir();

    if ($id == 0) {
        print("磁盘空间不足\n");
    }

    my $dir = $dir_hash{$id}{"dir"};

    my $url = "$root_url/$year$mon/$m_path";
    my $media_parent_dir = "$dir/$year$mon/$m_alias";
    my $media_dir = "$dir/$year$mon/$m_alias/$alias";
    my $video_path   = "$media_dir/$alias.mp4";
    my $video_path_ts   = "$media_dir/$alias.ts";
    my $video_path_m3u8 = "$media_dir/$alias.m3u8";
    my $url_m3u8 = "media/$year$mon/$m_alias/$alias/$alias.m3u8";
    my $media_parent = "$media_root_ln/$year$mon/$m_alias";
    my $video_dir_ssd = "$media_root_ln/$year$mon/$m_alias/$alias";


    my ($auto_id, $m_real_path, $version) = (0, "", 1);
    $sql = "select auto_id, m_real_path, m_version from media_detail where m_alias = '$alias'";
    $sth = $db_air->prepare($sql);
    if($sth->execute()) {
        my $ref = $sth->fetchrow_hashref();
        if ($ref) {
            ($auto_id, $m_real_path, $version) = ($ref->{"auto_id"}, $ref->{"m_real_path"},$ref->{"m_version"});

            if ($version >= $m_version) {
                print("$m_alias 已经添加过了，忽略...\n");
                return;
            
            } else {
                print("$m_alias 发现新的版本$m_version,替换...\n");
                #原来的直接删除
                `rm -rf $m_real_path`;
            }
        }
    }

    `mkdir -p $media_dir` if (not -e $media_dir);
    `mkdir -p $media_parent` if (not -e $media_parent);
    `rm -f $video_dir_ssd`;
    `ln -s $media_dir $video_dir_ssd` if (not -e $video_dir_ssd);

    print("download $url...\n");
    `wget "$url" -q -O $video_path`;
    my $space = -s $video_path;
    $space = sprintf("%.2f", $space / 1024 /1024);
    if ($space < 2) {
        `rm -rf $media_dir`;
        print("$video_path 文件貌似有点问题，不保存\n");
        return;
    }

    chdir($media_dir);
    print("change to $video_path_ts...\n");
    `ffmpeg -y -i $video_path -vcodec copy -acodec copy -vbsf h264_mp4toannexb $video_path_ts`;
    print("change to $video_path_m3u8...\n");
    `ffmpeg -i $video_path_ts -c copy -map 0 -f segment -segment_list $video_path_m3u8 -segment_time 60 $alias-%04d.ts`;
    `rm -f $video_path` if (-e $video_path);
    `rm -f $video_path_ts` if (-e $video_path_ts);

    if (not -e $video_path_m3u8) {
        print("没有发现m3u8文件 $video_path_m3u8\n");
        return;
    }

    print("-----m_chs_desc=$m_chs_desc\n");
    if ($auto_id > 0 ) {
        $sql  = "update media_detail set m_id = $m_id, m_alias = '$alias', ";
        $sql .= "m_space = $space, m_price = $m_price, m_chs_desc = '$m_chs_desc', ";
        $sql .= "m_episode = $m_episode, m_video_path = '$url_m3u8', m_real_path = '$media_dir', ";
        $sql .= "m_version=$m_version where auto_id = $auto_id";
         

    } else {
        $sql  = "insert into media_detail (m_id,m_alias,m_space,m_price,m_chs_desc,";
        $sql .= "m_episode,m_video_path,m_real_path, m_version, m_create_date) values (";
        $sql .= "$m_id,'$alias',$space,$m_price,'$m_chs_desc',$m_episode,";
        $sql .= "'$url_m3u8','$media_dir',$m_version, now())";
    }

    print("sql=$sql\n");
    $sth = $db_air->prepare($sql);
    if(not $sth -> execute()) {
        print("sql execute error. ".$sth->errstr."\n");
        #&air_write_log("ERROR ".$sth->errstr);
    }

    $sql = "update media set enable_state = 'enable', m_update_info = '$m_update_info' where auto_id = $m_id";
    $sth = $db_air->prepare($sql);
    if(not $sth -> execute()) {
        print("sql execute error. ".$sth->errstr."\n");
    }

    &update_dir($id);
    return;
}


sub update_media()
{
    $m_original_name = $db_air->quote($m_original_name);
    $m_chs_name = $db_air->quote($m_chs_name);
    $m_desc = $db_air->quote($m_desc);
    
    #先检查有没有，如果有的话检查版本号，如果版本号一致则不更新，其它情况都要做更新
    my ($version,$id) = (1,0);
    $sql = "select auto_id, m_version from media where m_alias = '$m_alias'";
    $sth = $db_air->prepare($sql);
    if($sth->execute()) {
        my $version = 1;
        my $ref = $sth->fetchrow_hashref();
        if ($ref) {
            ($id, $version) = ($ref->{"auto_id"},$ref->{"m_version"});
            if ($version >= $m_version) {
                print("$m_chs_name 已经添加过了，忽略...\n");
                return;
            
            } else {
                print("$m_chs_name 发现新的版本 $m_version,更新...\n");
            }
        }
    }

    my $poster_url = "media/$year$mon/poster/$m_alias.jpg";
    if ($id > 0) {
        $sql  = "update media set m_chs_name = $m_chs_name, m_original_name =$m_original_name, ";
        $sql .= "m_director = '$m_director', m_main_actors='$m_actor', m_time_length = $m_time_length, ";
        $sql .= "m_show_date= '$m_show_date', m_kind_flag = $id_kind, m_area_flag=$id_area, ";
        $sql .= "m_type_flag= $id_type, m_kind_desc = '$m_kind', m_area_desc='$m_area',";
        $sql .= "m_type_desc= '$m_type', m_revenue = $m_revenue, m_imdb_num = $m_imdb_num,";
        $sql .= "m_douban_num = $m_douban_num, m_pic_path = '$poster_url', m_des= $m_desc,";
        $sql .= "m_version = $m_version where auto_id = $id";

    } else {
        $sql = "insert into media (m_chs_name,m_original_name,m_alias,m_director,";
        $sql .= "m_main_actors,m_time_length,m_show_date,m_kind_flag,m_area_flag,";
        $sql .= "m_type_flag,m_kind_desc,m_area_desc,m_type_desc,m_revenue,m_imdb_num,";
        $sql .= "m_douban_num,m_pic_path,m_des, m_version, m_create_date) values (";
        $sql .= "$m_chs_name,$m_original_name,'$m_alias','$m_director',";
        $sql .= "'$m_actor', $m_time_length,'$m_show_date', $id_kind, $id_area,";
        $sql .= "$id_type,'$m_kind','$m_area','$m_type',$m_revenue,";
        $sql .= "$m_imdb_num, $m_douban_num,'$poster_url',$m_desc,$m_version,now())";
    }

    $sth = $db_air->prepare($sql);
    if(not $sth -> execute()) {
        print("$m_chs_name 更新或插入新数据出现错误，请查看. ".$sth->errstr."\n");
        #&air_write_log("ERROR ".$sth->errstr);
    }
    &down_load_poster();
    print("do update_media\n\n");
}

sub update_media_detail()
{
    &down_load_detail();
}


sub get_class()
{
    $sql  = "select auto_id, class, m_kind, m_name, value,parent_class_id from media_category";
    $sth = $db_air->prepare($sql);
    if ($sth->execute()) {
        while (my $ref = $sth->fetchrow_hashref()) {
            my ($id, $class, $m_kind) = ($ref->{"auto_id"}, $ref->{"class"}, $ref->{"m_kind"});
            my ($name, $value) = ($ref->{"m_name"}, $ref->{"value"});
            my ($desc, $parent_id) = ($ref->{"m_desc"},$ref->{"parent_class_id"});

            if ($class == 1) {
                $class_hash{$m_kind}{$id}{"name"} = $name;
                $class_hash{$m_kind}{$id}{"value"} = $value;

                if ( defined($desc) and length($desc) > length($name)) {
                    $class_hash{$m_kind}{$id}{"pattern"} = $desc;

                } else {
                    $class_hash{$m_kind}{$id}{"pattern"} = ",$name,";
                }
                print("pattern:".$class_hash{$m_kind}{$id}{"pattern"}."\n");

            } else {
                $class2_hash{$parent_id}{$id}{"name"} = $name;
                $class2_hash{$parent_id}{$id}{"value"} = $value;
                if (defined($desc) and length($desc) > length($name)) {
                    $class2_hash{$parent_id}{$id}{"pattern"} = $desc;

                } else {
                    $class2_hash{$parent_id}{$id}{"pattern"} = ",$name,";

                }
            }
        }
    }
}



sub do_cycle()
{
    for my $key (@index_content) {

        $key =~ s/\n//g;
        if (length($key) == 0 or $key =~ /^\s+#/ or $key =~ /^\s+$/) {
            next;
        }

        if ($state eq "" ) {
            if ($key =~ /BEGIN\s+SUMMARY/) {
                $state = "summary";
                #print("$state start\n");
                next;

            } elsif ($key =~ /BEGIN\s+DETAIL/) {
                $state = "detail";
                #print("$state start\n");
                next;
            }
        }

        if ($key =~ /\s*END/) {
            #print("$state finish $key\n");
            if(not &do_state($state)) {
                print("有错误发生\n");
            }
            $state = "";
            $des_flag = 0;
            $m_version = 1;
            $m_update_info = "";
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
            next;
        }

        if ($key =~ /\(original_name\)\s*:\s*(.*)$/) {
            $m_original_name = $1;
            while ($m_original_name =~ /\s+$/) {
                chop($m_original_name);
            }
            next;
        }

        if ($key =~ /\(alias\)\s*:\s*(.*)$/) {
            $m_alias = $1;
            while ($m_alias =~ /\s+$/) {
                chop($m_alias);
            }
            next;
        }

        if ($key =~ /\(director\)\s*:\s*(.*)$/) {
            $m_director = $1;
            while ($m_director =~ /\s+$/) {
                chop($m_director);
            }
            next;
        }

        if ($key =~ /\(actors\)\s*:\s*(.*)$/) {
            $m_actor = $1;
            while ($m_actor =~ /\s+$/) {
                chop($m_actor);
            }
            next;
        }

        if ($key =~ /\(time_length\)\s*:\s*(\d+)/) {
            $m_time_length = $1;
            next;
        }

        if ($key =~ /\(show_date\)\s*:\s*(.*)$/) {
            $m_show_date = $1;
            while ($m_show_date =~ /\s+$/) {
                chop($m_show_date);
            }
            next;
        }

        if ($key =~ /\(kind\)\s*:\s*(.*)$/) {
            $m_kind = $1;
            while ($m_kind =~ /\s+$/) {
                chop($m_kind);
            }
            next;
        }

        if ($key =~ /\(type\)\s*:\s*(.*)$/) {
            $m_type = $1;
            while ($m_type =~ /\s+$/) {
                chop($m_type);
            }
            next;
        }

        if ($key =~ /\(area\)\s*:\s*(.*)$/) {
            $m_area = $1;
            while ($m_area =~ /\s+$/) {
                chop($m_area);
            }
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
            next;
        }

        if ($key =~ /\(episode\)\s*:\s*(.*)$/) {
            $m_episode = $1;
            while ($m_episode =~ /\s+$/) {
                chop($m_episode);
            }
            next;
        }

        if ($key =~ /\(price\)\s*:\s*(.*)$/) {
            $m_price = $1;
            while ($m_price =~ /\s+$/) {
                chop($m_price);
            }
            next;
        }

        if ($key =~ /\(version\)\s*:\s*(.*)$/) {
            $m_version = $1;
            while ($m_version =~ /\s+$/) {
                chop($m_version);
            }
            next;
        }

        if ($key =~ /\(chs_desc\)\s*:\s*(.*)$/) {
            $m_chs_desc = $1;
            while ($m_chs_desc =~ /\s+$/) {
                chop($m_chs_desc);
            }
            next;
        }

        if ($key =~ /\(update\)\s*:\s*(.*)$/) {
            $m_update_info = $1;
            while ($m_update_info =~ /\s+$/) {
                chop($m_update_info);
            }
            next;
        }

    }

}
