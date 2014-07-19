-- data dump from console billstat output

CREATE database IF NOT EXISTS media default charset=utf8;
GRANT CREATE,SELECT,INSERT,UPDATE,DELETE ON media.* TO media@localhost IDENTIFIED BY '***King1985***';
flush privileges;

use media;

/*电影类目信息*/
CREATE TABLE IF NOT EXISTS `media`.`media_category` (
   `auto_id`         bigint(20)     NOT NULL AUTO_INCREMENT,
   `class`           int            DEFAULT 1,                      /*属于几级类目*/
   `m_kind`          varchar(32),                                   /*对于一级目录，要填上类目(视频分类/区域信息)*/
   `m_name`          varchar(32),                                   /*类目名*/
   `value`           int            DEFAULT 1,                      /*标志位*/
   `parent_class_id` bigint(20)     DEFAULT 0,                      /*如果不是顶级类目，此处是上一层级类目的auto_id*/
   `m_create_date`   DATETIME,                                      /*记录创建时间*/

   PRIMARY KEY (`auto_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*一条记录对应一部电视剧的一集或一部电影的一个视频*/
CREATE TABLE IF NOT EXISTS `media`.`media_detail` (
   `auto_id`         bigint(20)     NOT NULL AUTO_INCREMENT,
   `m_alias`         varchar(64)    NOT NULL,                       /*内部名称，(只能由字母、数字、下划线组成）*/
   `mv_alias`        varchar(64)    NOT NULL,                       /*内部名称，(只能由字母、数字、下划线组成）*/
   `m_space`         DECIMAL(14,2)  DEFAULT '0.0',                  /*视频总大小(MB)*/
   `m_price`         DECIMAL(14,2)  DEFAULT '0.0',                  /*视频价格*/
   `m_chs_desc`      varchar(64),                                   /*视频文字描述.(功夫熊猫 720p, 功夫熊猫 1080p)*/
   `m_episode`       int            DEFAULT 0,                      /*对应第几集*/

   `m_video_path`    varchar(512),                                  /*播放路径(media/201405/video/md5(m_id.auto_id).m3u8)*/
   `m_real_path`     varchar(512),                                  /*视频的永久目录(/home/disk1/201405/md5(m_id.auto_id)*/
   `m_path_is_ln`    int            DEFAULT 0,                      /*播放路径是不是软连接,1为是，0为不是*/
   `m_pv`            int            DEFAULT 0,                      /*视频被浏览的次数*/
   `m_buy_pv`        int            DEFAULT 0,                      /*视频被购买的次数*/
    
   `m_create_date`   DATETIME,                                      /*记录创建时间*/
   `m_modify_date`   DATETIME,                                      /*记录修改时间*/

   PRIMARY KEY (`auto_id`),
   UNIQUE KEY (`m_alias`), 
   INDEX (`m_id`),
   INDEX (`m_create_date`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `media`.`media` (
   `auto_id`         bigint(20)     NOT NULL AUTO_INCREMENT,
   `m_chs_name`      varchar(64)    NOT NULL,                       /*中文名（007之皇家赌场，爱情公寓3）*/
   `m_original_name` varchar(128)   NOT NULL,                       /*母语名*/
   `m_alias`         varchar(64)    NOT NULL,                       /*内部名称，(只能由字母、数字、下划线组成）*/
   `m_director`      varchar(128),                                  /*导演*/
   `m_main_actors`   varchar(256),                                  /*主要演员*/
   `m_time_length`   int            DEFAULT 0,                      /*影片时长(分钟)*/
   `m_show_date`     date           DEFAULT '0000-00-00',           /*上映时间*/
   `m_kind_flag`     int            DEFAULT 0,                      /*视频大类（电视剧/电影...）标志位*/
   `m_type_flag`     int            DEFAULT 0,                      /*细分小类（爱情/恐怖...） 标志位*/
   `m_area_flag`     int            DEFAULT 0,                      /*区域信息标志位*/
   `m_other_flag`    int            DEFAULT 0,                      /*IMDB标志位等*/
   `m_kind_desc`     varchar(64)    DEFAULT NULL,                   /*视频大类描述 '电视剧','电影','综艺'*/
   `m_type_desc`     varchar(64)    DEFAULT NULL,                   /*视频细分小类描述 '爱情/灾难' '宫廷/古装'*/
   `m_area_desc`     varchar(64)    DEFAULT NULL,                   /*影片区域信息描述 '大陆/港台' */
   `enable_state`    varchar(16)    DEFAULT 'disable',              /*enable/disable*/
   `m_revenue`       DECIMAL(14,2)  DEFAULT '0.0',                  /*电影票房(单位:千万美元)*/
   `m_imdb_num`      DECIMAL(14,2)  DEFAULT '0.0',                  /*IMDB分数*/
   `m_douban_num`    DECIMAL(14,2)  DEFAULT '0.0',                  /*豆瓣分数*/
   `m_total_pv`      int            DEFAULT 0,                      /*影片访问pv*/
   `m_pic_path`      varchar(512)   DEFAULT NULL,                   /*视频的展示图片的路径(media/201403/pic/md5(id).jpg)*/
   `m_des`           varchar(4096)  DEFAULT NULL,                   /*剧情简介*/
   `m_create_date`   DATETIME,                                      /*记录创建时间*/
   `m_modify_date`   DATETIME,                                      /*记录修改时间*/

   PRIMARY KEY (`auto_id`),
   UNIQUE KEY (`m_alias`), 
   INDEX (`m_kind_flag`,`enable_state`),
   INDEX (`m_create_date`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

