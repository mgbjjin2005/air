-- MySQL dump 10.13  Distrib 5.5.17, for Linux (x86_64)
--
-- Host: localhost    Database: air
-- ------------------------------------------------------
-- Server version	5.5.17

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `global_info`
--

DROP TABLE IF EXISTS `global_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_info` (
  `station_name` varchar(64) NOT NULL,
  `moive_expire` int(11) DEFAULT '3',
  `m_charge_off` int(11) DEFAULT '100',
  `t_charge_off` int(11) DEFAULT '100',
  `i_b_ratio` decimal(14,2) DEFAULT '0.00',
  KEY `station_name` (`station_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_info`
--

LOCK TABLES `global_info` WRITE;
/*!40000 ALTER TABLE `global_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `auto_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `m_chs_name` varchar(64) NOT NULL,
  `m_original_name` varchar(128) NOT NULL,
  `m_alias` varchar(64) NOT NULL,
  `m_series` varchar(256) DEFAULT NULL,
  `m_episode` int(11) NOT NULL DEFAULT '1',
  `m_video_type` varchar(32) NOT NULL DEFAULT 'moive',
  `m_show_date` date NOT NULL DEFAULT '0000-00-00',
  `m_area_show` varchar(64) DEFAULT NULL,
  `m_area_flag` varchar(128) DEFAULT NULL,
  `m_director` varchar(128) DEFAULT NULL,
  `m_type` varchar(128) DEFAULT NULL,
  `m_main_actors` varchar(256) DEFAULT NULL,
  `m_time_length` int(11) DEFAULT '0',
  `m_des` varchar(2560) DEFAULT NULL,
  `m_total_play` int(11) DEFAULT '0',
  `m_month_play` int(11) DEFAULT '0',
  `m_day_play` int(11) DEFAULT '0',
  `m_total_pv` int(11) DEFAULT '0',
  `m_wifi_total` decimal(14,3) DEFAULT '0.000',
  `m_resolution` varchar(256) DEFAULT NULL,
  `m_price` decimal(14,2) DEFAULT '0.00',
  `m_url` varchar(1024) DEFAULT NULL,
  `m_sata_path` varchar(1024) DEFAULT NULL,
  `m_ssd_path` varchar(1024) DEFAULT NULL,
  `m_create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `m_modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_cart`
--

DROP TABLE IF EXISTS `media_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_cart` (
  `user_name` varchar(64) NOT NULL,
  `m_id` bigint(20) NOT NULL,
  `m_desc` varchar(128) NOT NULL,
  `price` decimal(14,2) DEFAULT '0.00',
  `expire` int(11) DEFAULT '0',
  `expire_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_cart`
--

LOCK TABLES `media_cart` WRITE;
/*!40000 ALTER TABLE `media_cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_deal_info`
--

DROP TABLE IF EXISTS `media_deal_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_deal_info` (
  `deal_id` varchar(64) NOT NULL,
  `m_id` bigint(20) NOT NULL,
  `user_name` varchar(64) NOT NULL,
  `price` decimal(14,2) DEFAULT '0.00',
  `total_cost` decimal(14,2) DEFAULT '0.00',
  `view_times` int(11) DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `user_name` (`user_name`),
  KEY `m_id` (`m_id`,`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_deal_info`
--

LOCK TABLES `media_deal_info` WRITE;
/*!40000 ALTER TABLE `media_deal_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_deal_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packet_auto`
--

DROP TABLE IF EXISTS `packet_auto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packet_auto` (
  `auto_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `packet_id` bigint(20) NOT NULL,
  `enable_state` varchar(16) NOT NULL DEFAULT 'enable',
  `check_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`auto_id`),
  KEY `enable_state` (`enable_state`),
  KEY `check_date` (`check_date`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packet_auto`
--

LOCK TABLES `packet_auto` WRITE;
/*!40000 ALTER TABLE `packet_auto` DISABLE KEYS */;
INSERT INTO `packet_auto` VALUES (5,'iphone',1,'enable','2014-04-30 16:00:00','2014-05-28 08:20:45'),(6,'iphone',8,'enable','2014-04-30 16:00:00','2014-05-28 08:20:45'),(7,'acer',1,'enable','2014-04-30 16:00:00','2014-05-28 08:20:45'),(8,'acer',8,'enable','2014-04-30 16:00:00','2014-05-28 08:20:45');
/*!40000 ALTER TABLE `packet_auto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packet_deal`
--

DROP TABLE IF EXISTS `packet_deal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packet_deal` (
  `auto_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `deal_id` varchar(32) NOT NULL,
  `user_name` varchar(64) NOT NULL,
  `packet_id` bigint(20) NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stop_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `price` decimal(14,2) DEFAULT '0.00',
  `state` varchar(16) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`auto_id`),
  KEY `deal_id` (`deal_id`),
  KEY `state` (`state`),
  KEY `user_name` (`user_name`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packet_deal`
--

LOCK TABLES `packet_deal` WRITE;
/*!40000 ALTER TABLE `packet_deal` DISABLE KEYS */;
/*!40000 ALTER TABLE `packet_deal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packet_info`
--

DROP TABLE IF EXISTS `packet_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packet_info` (
  `packet_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `p_desc` varchar(128) NOT NULL,
  `traffic` decimal(14,2) DEFAULT '0.00',
  `period_month` int(11) DEFAULT '30',
  `movie_tickets` decimal(14,2) DEFAULT '0.00',
  `category` varchar(32) NOT NULL,
  `enable_state` varchar(16) DEFAULT 'enable',
  `price` decimal(14,2) DEFAULT '0.00',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`packet_id`),
  KEY `category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packet_info`
--

LOCK TABLES `packet_info` WRITE;
/*!40000 ALTER TABLE `packet_info` DISABLE KEYS */;
INSERT INTO `packet_info` VALUES (1,'0元30MB',30.00,1,0.00,'packet','enable',0.00,'2014-05-28 08:13:19'),(2,'5元100MB',100.00,1,2.00,'packet','enable',5.00,'2014-05-28 08:13:19'),(3,'10元200MB',200.00,1,5.00,'packet','enable',10.00,'2014-05-28 08:13:19'),(4,'20元450MB',450.00,1,10.00,'packet','enable',20.00,'2014-05-28 08:13:19'),(5,'25元600MB',600.00,1,15.00,'packet','enable',25.00,'2014-05-28 08:13:19'),(6,'30元800MB',800.00,1,20.00,'packet','enable',30.00,'2014-05-28 08:13:19'),(7,'40元1500MB',1500.00,1,30.00,'packet','enable',40.00,'2014-05-28 08:13:19'),(8,'50元2000MB',2000.00,1,40.00,'packet','enable',50.00,'2014-05-28 08:13:19'),(9,'5元100MB',100.00,6,2.00,'addition','enable',5.00,'2014-05-28 08:13:19'),(10,'10元200MB',200.00,6,4.00,'addition','enable',10.00,'2014-05-28 08:13:19'),(11,'20元400MB',400.00,6,8.00,'addition','enable',20.00,'2014-05-28 08:13:19'),(12,'30元600MB',600.00,6,12.00,'addition','enable',30.00,'2014-05-28 08:13:19'),(13,'40元800MB',800.00,6,16.00,'addition','enable',40.00,'2014-05-28 08:13:19');
/*!40000 ALTER TABLE `packet_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_realtime`
--

DROP TABLE IF EXISTS `traffic_realtime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_realtime` (
  `user_name` varchar(64) NOT NULL,
  `traffic` decimal(14,2) DEFAULT '0.00',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_name`,`update_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_realtime`
--

LOCK TABLES `traffic_realtime` WRITE;
/*!40000 ALTER TABLE `traffic_realtime` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_realtime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_total`
--

DROP TABLE IF EXISTS `traffic_total`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_total` (
  `day` date NOT NULL DEFAULT '0000-00-00',
  `hour` int(11) NOT NULL,
  `min` int(11) NOT NULL,
  `traffic` decimal(14,2) DEFAULT '0.00',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_total`
--

LOCK TABLES `traffic_total` WRITE;
/*!40000 ALTER TABLE `traffic_total` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_total` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_binding`
--

DROP TABLE IF EXISTS `user_binding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_binding` (
  `auto_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `mac` varchar(64) NOT NULL,
  `bind_state` varchar(16) NOT NULL,
  `valid_key` varchar(256) DEFAULT NULL,
  `valid_state` varchar(16) DEFAULT 'done',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`auto_id`),
  KEY `user_name` (`user_name`,`mac`,`bind_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_binding`
--

LOCK TABLES `user_binding` WRITE;
/*!40000 ALTER TABLE `user_binding` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_binding` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info` (
  `user_name` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `password_md5` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `balance` decimal(14,2) DEFAULT '0.00',
  `total_cost` decimal(14,2) DEFAULT '0.00',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_name`),
  KEY `user_name` (`user_name`,`password`),
  KEY `user_name_2` (`user_name`,`password_md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_info`
--

LOCK TABLES `user_info` WRITE;
/*!40000 ALTER TABLE `user_info` DISABLE KEYS */;
INSERT INTO `user_info` VALUES ('acer','King1985','c6595641855b62e9d7948a7c9e07141e','jinhuafeng@yeah.net',100.00,0.00,'2014-05-28 07:41:27'),('iphone','King1985','c6595641855b62e9d7948a7c9e07141e','jinhuafeng@yeah.net',100.00,0.00,'2014-05-28 07:41:27');
/*!40000 ALTER TABLE `user_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_login_history`
--

DROP TABLE IF EXISTS `user_login_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_login_history` (
  `username` varchar(64) NOT NULL DEFAULT '',
  `start_time` datetime NOT NULL,
  `stop_time` datetime NOT NULL,
  `session_time` int(12) DEFAULT NULL,
  `input` bigint(20) DEFAULT NULL,
  `output` bigint(20) DEFAULT NULL,
  `mac` varchar(50) NOT NULL DEFAULT '',
  `terminate_cause` varchar(32) DEFAULT '',
  `clientip` varchar(15) NOT NULL DEFAULT '',
  KEY `username` (`username`),
  KEY `start_time` (`start_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_login_history`
--

LOCK TABLES `user_login_history` WRITE;
/*!40000 ALTER TABLE `user_login_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_login_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_mon`
--

DROP TABLE IF EXISTS `user_mon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_mon` (
  `user_name` varchar(64) NOT NULL,
  `traffic_idle` decimal(14,2) DEFAULT '0.00',
  `traffic_busy` decimal(14,2) DEFAULT '0.00',
  `traffic_internal` decimal(14,2) DEFAULT '0.00',
  `traffic_bill` decimal(14,2) DEFAULT '0.00',
  `traffic_remain` decimal(14,2) DEFAULT '0.00',
  `movie_tickets` decimal(14,2) DEFAULT '0.00',
  `date_mon` varchar(64) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_name`,`date_mon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_mon`
--

LOCK TABLES `user_mon` WRITE;
/*!40000 ALTER TABLE `user_mon` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_mon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_quota`
--

DROP TABLE IF EXISTS `user_quota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_quota` (
  `auto_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `category` varchar(32) NOT NULL,
  `quota` decimal(14,2) DEFAULT '0.00',
  `remain` decimal(14,2) DEFAULT '0.00',
  `deal_id` bigint(20) DEFAULT NULL,
  `state` varchar(16) NOT NULL,
  `state_desc` varchar(16) NOT NULL,
  `packet_desc` varchar(128) NOT NULL,
  `packet_category` varchar(16) NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stop_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`auto_id`),
  KEY `start_date` (`start_date`),
  KEY `category` (`category`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_quota`
--

LOCK TABLES `user_quota` WRITE;
/*!40000 ALTER TABLE `user_quota` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_quota` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-28 17:06:14
