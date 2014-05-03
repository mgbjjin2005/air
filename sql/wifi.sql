-- data dump from console billstat output

CREATE database IF NOT EXISTS panda default charset=utf8;
GRANT CREATE,SELECT,INSERT,UPDATE,DELETE ON panda.* TO wifi@localhost IDENTIFIED BY '***King1985***';
flush privileges;

use panda;

-- -----------------------------------------------------
-- Table `panda`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `panda`.`media` (
   `m_id` bigint(20) NOT NULL AUTO_INCREMENT,
   `m_chs_name` varchar(64) NOT NULL,/*中文名（007之皇家赌场，爱情公寓3）*/
   `m_original_name` varchar(128) NOT NULL, /*母语名*/
   `m_alias` varchar(64) NOT NULL,/*内部名称，(只能由字母、数字、下划线组成）*/
   `m_series` varchar(256) DEFAULT NULL,/*系列(007，爱情公寓)*/
   `m_episode` int NOT NULL DEFAULT 1,/*第几集(电影默认1)*/
   `m_video_type` varchar(32) NOT DEFAULT 'moive',/*电影、电视剧、纪录片(moive,history...)*/
   `m_show_date` date NOT NULL DEFAULT '0000-00-00',/*上映时间*/
   `m_area_show` varchar(64) DEFAULT NULL,/*网页上显示的影片地域信息*/
   `m_area_flag` varchar(128) DEFAULT NULL,/*用于检索的区域信息*/
   `m_director` varchar(128) DEFAULT NULL,/*导演*/
   `m_type` varchar(128) DEFAULT NULL,/*影片类型*/
   `m_main_actors` varchar(256) DEFAULT NULL,/*主要演员*/
   `m_time_length` int DEFAULT 0,/*影片时长(分钟)*/
   `m_des` varchar(2560) DEFAULT NULL,/*剧情简介*/

   `m_total_play` int DEFAULT 0,/*累积播放次数*/
   `m_month_play` int DEFAULT 0,/*本月播放次数*/
   `m_day_play` int DEFAULT 0,/*昨天播放次数*/
   `m_total_pv` int DEFAULT 0,/*影片访问pv*/
   `m_wifi_total` DECIMAL(14,3) DEFAULT '0.0',/*影片总收入*/
   `m_resolution` varchar(256) DEFAULT NULL,/*提供的分辨率(480P,211MB;720P,985MB)*/
   `m_price` DECIMAL(14,3) DEFAULT '0.0',/*影片价格(wifi币)*/
   `m_url` varchar(1024) DEFAULT NULL,/*中心的下载地址(不包括m_alias，m_alias统一构造)*/
   `m_sata_path` varchar(1024) DEFAULT NULL,/*电影目录在sata中的绝对路径*/
   `m_ssd_path` varchar(1024) DEFAULT NULL,/*电影目录在ssd中的路径*/
   `m_create_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,/*记录创建时间*/
   `m_modify_date` TIMESTAMP NULL DEFAULT NULL,/*记录修改时间*/

   PRIMARY KEY (`m_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

