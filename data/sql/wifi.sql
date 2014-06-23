-- data dump from console billstat output

CREATE database IF NOT EXISTS air default charset=utf8;
GRANT CREATE,SELECT,INSERT,UPDATE,DELETE ON air.* TO air@localhost IDENTIFIED BY '***King1985***';
flush privileges;

use air;

/*电影类目信息*/
CREATE TABLE IF NOT EXISTS `air`.`media_category` (
   `auto_id`         bigint(20)     NOT NULL AUTO_INCREMENT,
   `class`           int            DEFAULT 1,                      /*属于几级类目*/
   `m_kind`          varchar(32),                                   /*对于一级目录，要填上类目(视频分类/区域信息)*/
   `m_name`          varchar(32),                                   /*类目名*/
   `value`           int            DEFAULT 1,                      /*标志位*/
   `parent_class_id` bigint(20)     DEFAULT 0,                      /*如果不是顶级类目，此处是上一层级类目的auto_id*/
   `m_create_date`   TIMESTAMP,                                     /*记录创建时间*/

   PRIMARY KEY (`auto_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*一条记录对应一部电视剧的一集或一部电影的一个视频*/
CREATE TABLE IF NOT EXISTS `air`.`media_detail` (
   `auto_id`         bigint(20)     NOT NULL AUTO_INCREMENT,
   `m_id`            bigint(20),                                    /*属于哪个media*/
   `m_alias`         varchar(64)    NOT NULL,                       /*内部名称，(只能由字母、数字、下划线组成）*/
   `m_space`         DECIMAL(14,2)  DEFAULT '0.0',                  /*视频总大小(MB)*/
   `m_price`         DECIMAL(14,2)  DEFAULT '0.0',                  /*视频价格*/
   `m_chs_desc`      varchar(64),                                   /*视频文字描述.(功夫熊猫 720p, 功夫熊猫 1080p)*/
   `m_episode`       int            DEFAULT 0,                      /*对应第几集*/

   `m_video_path`    varchar(512),                                  /*播放路径(media/201405/video/md5(m_id.auto_id).m3u8)*/
   `m_real_path`     varchar(512),                                  /*视频的永久目录(/home/disk1/201405/md5(m_id.auto_id)*/
   `m_path_is_ln`    int            DEFAULT 0,                      /*播放路径是不是软连接,1为是，0为不是*/
   `m_pv`            int            DEFAULT 0,                      /*视频被浏览的次数*/
   `m_buy_pv`        int            DEFAULT 0,                      /*视频被购买的次数*/
    
   `m_create_date`   TIMESTAMP,                                     /*记录创建时间*/
   `m_modify_date`   TIMESTAMP,                                     /*记录修改时间*/

   PRIMARY KEY (`auto_id`),
   UNIQUE KEY (`m_alias`), 
   INDEX (`m_id`),
   INDEX (`m_create_date`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `air`.`media` (
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
   `m_create_date`   TIMESTAMP,                                     /*记录创建时间*/
   `m_modify_date`   TIMESTAMP,                                     /*记录修改时间*/

   PRIMARY KEY (`auto_id`),
   UNIQUE KEY (`m_alias`), 
   INDEX (`m_kind_flag`,`enable_state`),
   INDEX (`m_create_date`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*用户信息表*/
CREATE TABLE IF NOT EXISTS `air`.`user_info` (
   `user_name`    varchar(64)     NOT NULL,        /*用户名*/
   `password`     varchar(64)     NOT NULL,        /*明文密码*/
   `password_md5` varchar(64)     NOT NULL,        /*密文密码*/
   `email`        varchar(64)     NOT NULL,        /*邮箱*/
   `balance`      DECIMAL(14,2)   DEFAULT '0.0',   /*账户余额*/
   `total_cost`   DECIMAL(14,2)   DEFAULT '0.0',   /*累积消费额*/
   `create_date`  TIMESTAMP,                       /*用户创建时间*/

   PRIMARY KEY (`user_name`),
   INDEX(`user_name`,`password`),
   INDEX(`user_name`,`password_md5`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*绑定有几种情况
一、首次使用
    1、账号处于解绑状态
    2、登录后系统要求绑定账号，
    3、按照操作进行绑定

二、更换手机绑定（前提是已经绑定手机A）
    1、在手机A上登录账号
    2、在 www.wifi.com 首页的"我的信息"下面查看自己的绑定状态，并按照提示进行解绑操作
       解绑的过程需要通过注册时使用的邮箱做验证，只有验证通过了才能顺利解绑
    3、解除绑定后，会自动退出账号
    4、使用新的手机B重新登录该账号，登录后系统会提示要求绑定，按照操作进行绑定后即可完成账号绑定

账号设备绑定表*/
CREATE TABLE IF NOT EXISTS `air`.`user_binding` (
   `auto_id`      bigint(20)      NOT NULL AUTO_INCREMENT,
   `user_name`    varchar(64)     NOT NULL,                  /*用户名*/
   `mac`          varchar(64)     NOT NULL,                  /*设备mac*/
   `bind_state`   varchar(16)     NOT NULL,                  /*绑定状态 (YES/NO)*/
   `valid_key`    varchar(256)    DEFAULT NULL,              /*设备迁移过程时要通过邮件验证的验证key*/
   `valid_state`  varchar(16)     DEFAULT 'done',            /*当前验证状态("email_done":邮件已发出；"done":已经验证通过)*/
   `create_date`  TIMESTAMP,                                 /*创建时间*/

    PRIMARY KEY (`auto_id`),
    INDEX(`user_name`, `mac`, `bind_state`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*电影购买记录(目前只能支持单集购买，不支持打包购买)*/
CREATE TABLE IF NOT EXISTS `air`.`media_deal_info` (
   `deal_id`      varchar(64)     NOT NULL,         /*订单编号*/
   `m_id`         bigint(20)      NOT NULL,         /*视频ID*/
   `mv_id`        bigint(20)      NOT NULL,         /*视频所属的电影或电视剧的media_id*/
   `user_name`    varchar(64)     NOT NULL,         /*用户名*/
   `mac`          varchar(64)     NOT NULL,         /*设备mac*/
   `price`        DECIMAL(14,2)   DEFAULT '0.0',    /*购买价格*/
   `m_chs_desc`   varchar(64),                      /*视频文字描述.(功夫熊猫 720p, 功夫熊猫 1080p)*/
   `create_date`  TIMESTAMP,                        /*用户创建时间*/
   `expire_date`  TIMESTAMP,                        /*资源有效期截止时间*/

   INDEX(`user_name`,`mac`),
   INDEX(`user_name`,`mac`,`m_id`),
   INDEX(`user_name`,`mac`,`mv_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*套餐信息*/
CREATE TABLE IF NOT EXISTS `air`.`packet_info` (
   `packet_id`     bigint(20)      NOT NULL   AUTO_INCREMENT,   /*套餐ID*/
   `p_desc`        varchar(128)    NOT NULL,                    /*套餐描述*/
   `traffic`       DECIMAL(14,2)   DEFAULT   '0.0',             /*套餐流量*/
   `period_month`  int             DEFAULT    30,               /*套餐有效期(单位.月)*/
   `movie_tickets` DECIMAL(14,2)   DEFAULT    0,                /*电影券*/
   `category`      varchar(32)     NOT NULL,                    /*addition(加油包)/packet(固定套餐)*/
   `enable_state`  varchar(16)     DEFAULT   'enable',          /*enable/disable*/
   `price`         DECIMAL(14,2)   DEFAULT   '0.0',             /*价格*/
   `create_date`   TIMESTAMP,                                   /*套餐创建时间*/

   PRIMARY KEY (`packet_id`),
   INDEX(`category`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*固定套餐的自动结算信息*/
CREATE TABLE IF NOT EXISTS `air`.`packet_auto` (
   `auto_id`       bigint(20)      NOT NULL   AUTO_INCREMENT,
   `user_name`     varchar(64)     NOT NULL,                    /*用户名*/
   `packet_id`     bigint(20)      NOT NULL,
   `enable_state`  varchar(16)     NOT NULL   DEFAULT 'enable', /*状态 enable/disable*/
   `check_date`    TIMESTAMP       NOT NULL,                    /*检查点*/
   `valid_date`    TIMESTAMP       NOT NULL,                    /*套餐第一次生效的时间*/
   `create_date`   TIMESTAMP,                                   /*创建时间*/

   PRIMARY KEY (`auto_id`),
   INDEX(`enable_state`),
   INDEX(`check_date`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*订单（固定套餐、流量包）*/
/*
auto_key: 非必填，对于自动扣费的套餐，需要填充此项,格式 "201405_".packet_auto.auto_id，
用于一致性检查
state: 主要用于流量包的一致性检查，对于固定套餐的，可以忽略此项
*/
CREATE TABLE IF NOT EXISTS `air`.`packet_deal` (
   `auto_id`       bigint(20)      NOT NULL   AUTO_INCREMENT,
   `user_name`     varchar(64)     NOT NULL,                    /*用户名*/
   `packet_id`     bigint(20)      NOT NULL,                    /*套餐ID*/
   `price`         DECIMAL(14,2)   DEFAULT  '0.0',              /*当时购买价格*/
   `start_date`    TIMESTAMP       NOT NULL,                    /*开始时间*/
   `stop_date`     TIMESTAMP       NOT NULL,                    /*资源过期时间*/
   `state`         varchar(16)     NOT NULL,                    /*init(正在做)/done(已完成)*/
   `create_date`   TIMESTAMP,                                   /*创建时间*/

   PRIMARY KEY (`auto_id`),
   INDEX(`state`),
   INDEX(`user_name`,`state`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



/*用户的资源（流量、电影券）*/
CREATE TABLE IF NOT EXISTS `air`.`user_quota` (
   `auto_id`         bigint(20)      NOT NULL   AUTO_INCREMENT,
   `user_name`       varchar(64)     NOT NULL,                  /*用户名*/
   `category`        varchar(32)     NOT NULL,                  /*traffic/ticket*/
   `quota`           DECIMAL(14,2)   DEFAULT  '0.0',            /*总量，多少MB，或多少电影券*/
   `remain`          DECIMAL(14,2)   DEFAULT  '0.0',            /*余量*/
   `deal_id`         bigint(20)      ,                          /*交易号*/
   `state`           varchar(16)     NOT NULL,                  /*enable/disable*/
   `state_desc`      varchar(16)     NOT NULL,                  /*new(还没使用)/using(正在被使用)/finish/expire*/
   `packet_desc`     varchar(128)    NOT NULL,                  /*套餐描述:冗余数据，对象packet_info.p_desc*/
   `packet_category` varchar(16)     NOT NULL,                  /*套餐类型:冗余数据，对应packet_info.category*/
   `start_date`      TIMESTAMP       NOT NULL,                  /*开始时间*/
   `stop_date`       TIMESTAMP       NOT NULL,                  /*资源过期时间*/
   `create_date`     TIMESTAMP,                                 /*创建时间*/

   PRIMARY KEY (`auto_id`),
   INDEX(`start_date`),
   INDEX(`stop_date`),
   INDEX(`category`,`state`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*用户月信息汇总表*/
CREATE TABLE IF NOT EXISTS `air`.`user_mon` (
   `user_name`         varchar(64)     NOT NULL,                  /*用户名*/
   `traffic_idle`      DECIMAL(14,2)   DEFAULT  '0.0',            /*已使用的空闲流量*/
   `traffic_busy`      DECIMAL(14,2)   DEFAULT  '0.0',            /*已使用的忙时流量*/
   `traffic_internal`  DECIMAL(14,2)   DEFAULT  '0.0',            /*已使用的内网流量*/
   `traffic_bill`      DECIMAL(14,2)   DEFAULT  '0.0',            /*计费流量*/
   `traffic_remain`    DECIMAL(14,2)   DEFAULT  '0.0',            /*剩余流量*/
   `movie_tickets`     DECIMAL(14,2)   DEFAULT   0,               /*剩余电影券*/
   `date_mon`          varchar(64)     NOT NULL,                  /*月份(201405)*/
   `create_date`       TIMESTAMP,                                 /*创建时间*/

   PRIMARY KEY(`user_name`,`date_mon`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*用户实时流量 
此表是跟freeradius.radacct的关联表，每5/10分钟汇总一次流量。具体流程为
1、取radacct表的所有记录A，按照用户名做流量统计
2、对每组（user_name、traffic_A)
   A、查找user_traffic_realtime表是否有对应(user_name的记录).
   B、如果有,记录对应的流量traffic_B，并使traffic_C=trafficA-traffic_B。
   C、如果没有，则tarffic_C=traffic_A-traffic_B
   D、计算当前时间点为闲时还是忙时，设置闲时因子 factor=1或1/3
   E、对该用户的radacct的原始记录做过滤，已经下线的记录对应的（id，流量和traffic_A1）
   F、根据traffic_C和factor的值更新user_mon_traffic表

   G、把所有已经下线的数据记录到traffic_history，并删除radacct对应的记录。
   H、traffic_A2=traffic_A-trafficA1。计为当前时刻用户在radacct的流量，计入user_traffic_realtime
3、把这一阶段的总流量计入 traffic_total

*/
CREATE TABLE IF NOT EXISTS `air`.`traffic_realtime` (
   `user_name`    varchar(64)     NOT NULL,       /*用户名*/
   `traffic`      DECIMAL(14,2)   DEFAULT  '0.0', /*流量*/
   `update_date`  TIMESTAMP,                      /*创建时间*/

    PRIMARY KEY (`user_name`,`update_date`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*记录每天5分钟粒度的流量，非必须，统计用*/
CREATE TABLE IF NOT EXISTS `air`.`traffic_total` (
   `day`          date            NOT NULL DEFAULT '0000-00-00',  /*日期*/
   `hour`         int             NOT NULL,                       /*小时*/
   `min`          int             NOT NULL,                       /*分(5-10分钟粒度)*/
   `traffic`      DECIMAL(14,2)   DEFAULT  '0.0',                 /*出口流量+入口流量*/
   `create_date`  TIMESTAMP,                                      /*创建时间*/

   PRIMARY KEY(`day`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*电影购物车*/
CREATE TABLE IF NOT EXISTS `air`.`media_cart` (
   `user_name`    varchar(64)     NOT NULL,      /*用户名*/
   `m_id`         bigint(20)      NOT NULL,      /*视频ID*/
   `m_desc`       varchar(128)    NOT NULL,      /*视频信息描述（蜘蛛侠系列之超凡蜘蛛侠、爱情公寓四第18集）*/
   `price`        DECIMAL(14,2)   DEFAULT '0.0', /*购买价格*/
   `expire`       int             DEFAULT  0,    /*资源购买后的有效期，单位为天，对应global_conf.moive_expire*/
   `expire_date`  TIMESTAMP,                     /*该信息在购物车的有效期*/

   INDEX(`user_name`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*全局配置项*/
CREATE TABLE IF NOT EXISTS `air`.`global_info` (
   `g_key`        varchar(64)     NOT NULL,        /*全局变量名称*/
   `g_value`      varchar(128),                    /*值*/

   PRIMARY KEY (`g_key`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*用户信息表*/
CREATE TABLE IF NOT EXISTS `air`.`user_info` (
   `user_name`    varchar(64)     NOT NULL,        /*用户名*/
   `password`     varchar(64)     NOT NULL,        /*明文密码*/
   `password_md5` varchar(64)     NOT NULL,        /*密文密码*/
   `email`        varchar(64)     NOT NULL,        /*邮箱*/
   `balance`      DECIMAL(14,2)   DEFAULT '0.0',   /*账户余额*/
   `total_cost`   DECIMAL(14,2)   DEFAULT '0.0',   /*累积消费额*/
   `create_date`  TIMESTAMP,                       /*用户创建时间*/

   PRIMARY KEY (`user_name`),
   INDEX(`user_name`,`password`),
   INDEX(`user_name`,`password_md5`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*用户使用详情*/
CREATE TABLE IF NOT EXISTS `air`.`user_login_history` (
  username          varchar(64) NOT NULL default '',
  start_time        datetime    NOT NULL,
  stop_time         datetime    NOT NULL,
  session_time      int(12)     default NULL,
  input             bigint(20)  default NULL,
  output            bigint(20)  default NULL,
  mac               varchar(50) NOT NULL default '',
  terminate_cause   varchar(32) default '',
  clientip          varchar(15) NOT NULL default '',

  INDEX username (`username`),
  INDEX start_time (`start_time`)
) ;

