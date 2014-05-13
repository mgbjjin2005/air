-- data dump from console billstat output

CREATE database IF NOT EXISTS air default charset=utf8;
GRANT CREATE,SELECT,INSERT,UPDATE,DELETE ON air.* TO wifi@localhost IDENTIFIED BY '***King1985***';
flush privileges;

use air;



-- -----------------------------------------------------
-- Table `air`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `air`.`media` (
   `m_id`            bigint(20)     NOT NULL AUTO_INCREMENT,
   `m_chs_name`      varchar(64)    NOT NULL,                       /*中文名（007之皇家赌场，爱情公寓3）*/
   `m_original_name` varchar(128)   NOT NULL,                       /*母语名*/
   `m_alias`         varchar(64)    NOT NULL,                       /*内部名称，(只能由字母、数字、下划线组成）*/
   `m_series`        varchar(256)   DEFAULT NULL,                   /*系列(007，爱情公寓)*/
   `m_episode`       int            NOT NULL DEFAULT 1,             /*第几集(电影默认1)*/
   `m_video_type`    varchar(32)    NOT DEFAULT 'moive',            /*电影、电视剧、纪录片(moive,history...)*/
   `m_show_date`     date           NOT NULL DEFAULT '0000-00-00',  /*上映时间*/
   `m_area_show`     varchar(64)    DEFAULT NULL,                   /*网页上显示的影片地域信息*/
   `m_area_flag`     varchar(128)   DEFAULT NULL,                   /*用于检索的区域信息*/
   `m_director`      varchar(128)   DEFAULT NULL,                   /*导演*/
   `m_type`          varchar(128)   DEFAULT NULL,                   /*影片类型*/
   `m_main_actors`   varchar(256)   DEFAULT NULL,                   /*主要演员*/
   `m_time_length`   int            DEFAULT 0,                      /*影片时长(分钟)*/
   `m_des`           varchar(2560)  DEFAULT NULL,                   /*剧情简介*/

   `m_total_play`    int            DEFAULT 0,                      /*累积播放次数*/
   `m_month_play`    int            DEFAULT 0,                      /*本月播放次数*/
   `m_day_play`      int            DEFAULT 0,                      /*昨天播放次数*/
   `m_total_pv`      int            DEFAULT 0,                      /*影片访问pv*/
   `m_wifi_total`    DECIMAL(14,3)  DEFAULT '0.0',                  /*影片总收入*/
   `m_resolution`    varchar(256)   DEFAULT NULL,                   /*提供的分辨率(480P,211MB;720P,985MB)*/
   `m_price`         DECIMAL(14,2)  DEFAULT '0.0',                  /*影片价格(wifi币)*/
   `m_url`           varchar(1024)  DEFAULT NULL,                   /*中心的下载地址(不包括m_alias，m_alias统一构造)*/
   `m_sata_path`     varchar(1024)  DEFAULT NULL,                   /*电影目录在sata中的绝对路径*/
   `m_ssd_path`      varchar(1024)  DEFAULT NULL,                   /*电影目录在ssd中的路径*/
   `m_create_date`   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,      /*记录创建时间*/
   `m_modify_date`   TIMESTAMP NULL DEFAULT NULL,                   /*记录修改时间*/

   PRIMARY KEY (`m_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*用户信息表*/
CREATE TABLE IF NOT EXISTS `air`.`user_info` (
   `user_name`    varchar(64)     NOT NULL,                       /*用户名*/
   `password`     varchar(64)     NOT NULL,                       /*明文密码*/
   `password_md5` varchar(64)     NOT NULL,                       /*密文密码*/
   `email`        varchar(64)     NOT NULL,                       /*邮箱*/
   `balance`      DECIMAL(14,2)   DEFAULT '0.0',                  /*账户余额*/
   `total_cost`   DECIMAL(14,2)   DEFAULT '0.0',                  /*累积消费额*/
   `bind_state`   varchar(16)     NOT NULL,                       /*账号绑定状态，只要绑定一个设备，状态就应该是绑定的(YES/NO)*/

   `login_times`  int             DEFAULT  0,                     /*累积登录次数*/
   `create_date`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP, /*用户创建时间*/
   `last_date`    TIMESTAMP       NULL DEFAULT NULL,              /*上次登录时间*/

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

/*账号设备绑定表*/
CREATE TABLE IF NOT EXISTS `air`.`binding_info` (
   `b_id`         bigint(20)      NOT NULL AUTO_INCREMENT,
   `user_name`    varchar(64)     NOT NULL,                       /*用户名*/
   `mac`          varchar(64)     NOT NULL,                       /*设备mac*/
   `bind_state`   varchar(16)     NOT NULL,                       /*绑定状态 (YES/NO)*/
   `valid_key`    varchar(256)    NULL,                           /*设备迁移过程时要通过邮件验证的验证key*/
   `valid_state`  varchar(16)     DEFAULT 'done'                  /*当前验证状态("email_done":邮件已发出；"done":已经验证通过)*/
   `create_date`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP, /*创建时间*/

    PRIMARY KEY (`b_id`),
    INDEX(`user_name`, `mac`, `bind_state`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*电影购买记录(目前只能支持单集购买，不知道打包购买)*/
CREATE TABLE IF NOT EXISTS `air`.`moive_deal_info` (
   `deal_id`      varchar(64)     NOT NULL,                       /*订单编号*/
   `m_id`         bigint(20)      NOT NULL,                       /*视频ID*/
   `user_name`    varchar(64)     NOT NULL,                       /*用户名*/
   `mac`          varchar(64)     NOT NULL,                       /*购买该电影的设备*/
   `price`        DECIMAL(14,2)   DEFAULT '0.0',                  /*购买价格*/
   `total_cost`   DECIMAL(14,2)   DEFAULT '0.0',                  /*累积消费额*/
   `bind_state`   varchar(16)     NOT NULL,                       /*账号绑定状态，只要绑定一个设备，状态就应该是绑定的(YES/NO)*/

   `login_times`  int             DEFAULT  0,                     /*累积登录次数*/
   `create_date`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP, /*用户创建时间*/
   `expire_date`  TIMESTAMP       NULL DEFAULT NULL,              /*资源有效期截止时间*/

   INDEX(`user_name`,`mac`),
   INDEX(`m_id`,`user_name`,`mac`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*流量包*/
CREATE TABLE IF NOT EXISTS `air`.`traffic_packet` (
   `t_id`         bigint(20)      NOT NULL AUTO_INCREMENT,        /*套餐ID*/
   `t_desc`       varchar(256)    NOT NULL,                       /*套餐描述*/
   `traffic`      DECIMAL(14,2)   DEFAULT  '0.0',                 /*套餐流量*/
   `expires`      int             DEFAULT  30,                    /*套餐有效期，默认30天, 流量可以累积到下个月*/
   `price`        DECIMAL(14,2)   DEFAULT '0.0',                  /*价格*/
   `create_date`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP  /*套餐创建时间*/

   PRIMARY KEY (`t_id`),
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*用户月流量信息汇总表*/
CREATE TABLE IF NOT EXISTS `air`.`raffic_mon` (
   `t_id`              bigint(20)      NOT NULL AUTO_INCREMENT,
   `user_name`         varchar(64)     NOT NULL,                       /*用户名*/
   `mac`               varchar(64)     NOT NULL,                       /*mac地址*/
   `traffic_last`      DECIMAL(14,2)   DEFAULT  '0.0',                 /*上个月剩余流量*/
   `traffic_idle`      DECIMAL(14,2)   DEFAULT  '0.0',                 /*已使用的空闲流量*/
   `traffic_busy`      DECIMAL(14,2)   DEFAULT  '0.0',                 /*已使用的忙时流量*/
   `traffic_internal`  DECIMAL(14,2)   DEFAULT  '0.0',                 /*已使用的内网流量*/
   `traffic_bill`      DECIMAL(14,2)   DEFAULT  '0.0',                 /*计费流量*/
   `traffic_remain`    DECIMAL(14,2)   DEFAULT  '0.0',                 /*剩余流量*/
   `date_mon`          varchar(64)     NOT NULL,                       /*月份(201405)*/
   `create_date`       TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP  /*套餐创建时间*/

   PRIMARY KEY (`t_id`),
   INDEX(`user_name`,`mac`,`date_mon`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*用户实时流量 
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
*/
CREATE TABLE IF NOT EXISTS `air`.`traffic_realtime` (
   `user_name`         varchar(64)     NOT NULL,                       /*用户名*/
   `mac`               varchar(64)     NOT NULL,                       /*mac地址*/
   `traffic`           DECIMAL(14,2)   DEFAULT  '0.0',                 /*流量*/
   `update_date`       varchar(64)     NOT NULL,                       /*更新时间*/

   INDEX(`user_name`,`mac`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*用户获得的流量明细*/
CREATE TABLE IF NOT EXISTS `air`.`traffic_deal` (
   `t_id`              bigint(20)      NOT NULL AUTO_INCREMENT,
   `user_name`         varchar(64)     NOT NULL,                       /*用户名*/
   `mac`               varchar(64)     NOT NULL,                       /*mac地址*/
   `traffic`           DECIMAL(14,2)   DEFAULT  '0.0',                 /*流量*/
   `price`             DECIMAL(14,2)   DEFAULT  '0.0',                 /*价格*/
   `traffic_type`      int             NOT NULL,                       /*流量的类型(固定套餐/加油包/赠送的流量/上期结余)*/
   `desc`              varchar(256)    NOT NULL,                       /*描述*/
   `date_mon`          varchar(64)     NOT NULL,                       /*月份(201405)*/
   `create_date`       TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP  /*创建时间*/

   PRIMARY KEY (`t_id`),
   INDEX(`user_name`,`mac`,`date_mon`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*电影购物车*/
CREATE TABLE IF NOT EXISTS `air`.`cart` (
   `user_name`    varchar(64)     NOT NULL,                       /*用户名*/
   `m_id`         bigint(20)      NOT NULL,                       /*视频ID*/
   `m_desc`       varchar(128)    NOT NULL,                       /*视频信息描述（蜘蛛侠系列之超凡蜘蛛侠、爱情公寓四第18集）*/
   `price`        DECIMAL(14,2)   DEFAULT '0.0',                  /*购买价格*/
   `expire`       int             DEFAULT  0,                     /*资源购买后的有效期，单位为天，对应global_conf.moive_expire*/
   `expire_date`  TIMESTAMP       NULL DEFAULT NULL,              /*该信息在购物车的有效期*/

   INDEX(`user_name`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*全局配置项*/
CREATE TABLE IF NOT EXISTS `air`.`global_conf` (
   `station_name` varchar(64)     NOT NULL,                       /*节点名称*/
   `moive_expire` int             DEFAULT   3,                    /*购买后视频的有效期*/
   `m_charge_off` int             DEFAULT   100,                  /*电影是否打折*/
   `t_charge_off` int             DEFAULT   100,                  /*流量是否打折*/
   `i_b_ratio`    DECIMAL(14,2)   DEFAULT   '0.0',                /*闲时流量的计费比*/
   INDEX(`station_name`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

