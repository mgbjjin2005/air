-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: air
-- ------------------------------------------------------
-- Server version	5.1.73

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
-- Table structure for table `binding_info`
--

DROP TABLE IF EXISTS `binding_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `binding_info` (
  `b_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `mac` varchar(64) NOT NULL,
  `bind_state` varchar(16) NOT NULL,
  `valid_key` varchar(256) DEFAULT NULL,
  `valid_state` varchar(16) DEFAULT 'done',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`b_id`),
  KEY `user_name` (`user_name`,`mac`,`bind_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `binding_info`
--

LOCK TABLES `binding_info` WRITE;
/*!40000 ALTER TABLE `binding_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `binding_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
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
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_conf`
--

DROP TABLE IF EXISTS `global_conf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_conf` (
  `station_name` varchar(64) NOT NULL,
  `moive_expire` int(11) DEFAULT '3',
  `m_charge_off` int(11) DEFAULT '100',
  `t_charge_off` int(11) DEFAULT '100',
  `i_b_ratio` decimal(14,2) DEFAULT '0.00',
  KEY `station_name` (`station_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_conf`
--

LOCK TABLES `global_conf` WRITE;
/*!40000 ALTER TABLE `global_conf` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_conf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `m_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `m_modify_date` datetime DEFAULT NULL,
  PRIMARY KEY (`m_id`)
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
-- Table structure for table `moive_deal_info`
--

DROP TABLE IF EXISTS `moive_deal_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `moive_deal_info` (
  `deal_id` varchar(64) NOT NULL,
  `m_id` bigint(20) NOT NULL,
  `user_name` varchar(64) NOT NULL,
  `mac` varchar(64) NOT NULL,
  `price` decimal(14,2) DEFAULT '0.00',
  `total_cost` decimal(14,2) DEFAULT '0.00',
  `view_times` int(11) DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `user_name` (`user_name`,`mac`),
  KEY `m_id` (`m_id`,`user_name`,`mac`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `moive_deal_info`
--

LOCK TABLES `moive_deal_info` WRITE;
/*!40000 ALTER TABLE `moive_deal_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `moive_deal_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_deal`
--

DROP TABLE IF EXISTS `traffic_deal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_deal` (
  `t_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `mac` varchar(64) NOT NULL,
  `traffic` decimal(14,2) DEFAULT '0.00',
  `price` decimal(14,2) DEFAULT '0.00',
  `traffic_type` int(11) NOT NULL,
  `desc` varchar(256) NOT NULL,
  `date_mon` varchar(64) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`t_id`),
  KEY `user_name` (`user_name`,`mac`,`date_mon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_deal`
--

LOCK TABLES `traffic_deal` WRITE;
/*!40000 ALTER TABLE `traffic_deal` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_deal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_mon`
--

DROP TABLE IF EXISTS `traffic_mon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_mon` (
  `t_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `traffic_packet` decimal(14,2) DEFAULT '0.00',
  `traffic_addition` decimal(14,2) DEFAULT '0.00',
  `traffic_recharge` decimal(14,2) DEFAULT '0.00',
  `traffic_last` decimal(14,2) DEFAULT '0.00',
  `traffic_idle` decimal(14,2) DEFAULT '0.00',
  `traffic_busy` decimal(14,2) DEFAULT '0.00',
  `traffic_internal` decimal(14,2) DEFAULT '0.00',
  `traffic_bill` decimal(14,2) DEFAULT '0.00',
  `traffic_remain` decimal(14,2) DEFAULT '0.00',
  `date_mon` varchar(64) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`t_id`),
  KEY `user_name` (`user_name`,`date_mon`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_mon`
--

LOCK TABLES `traffic_mon` WRITE;
/*!40000 ALTER TABLE `traffic_mon` DISABLE KEYS */;
INSERT INTO `traffic_mon` VALUES (1,'haodan','500.00','0.00','0.00','150.00','300.00','160.00','1500.00','260.00','390.00','201405','2014-05-18 06:41:10'),(2,'iphone','500.00','0.00','0.00','150.00','300.00','160.00','1500.00','260.00','390.00','201405','2014-05-18 06:41:18'),(3,'ipad','500.00','0.00','0.00','150.00','300.00','160.00','1500.00','260.00','390.00','201405','2014-05-18 06:41:24'),(4,'acer','500.00','0.00','0.00','150.00','300.00','160.00','1500.00','260.00','390.00','201405','2014-05-18 06:41:30'),(5,'air','500.00','0.00','0.00','150.00','300.00','160.00','1500.00','260.00','390.00','201405','2014-05-18 06:41:37'),(6,'mi','500.00','0.00','0.00','150.00','300.00','160.00','1500.00','260.00','390.00','201405','2014-05-18 06:41:43');
/*!40000 ALTER TABLE `traffic_mon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_packet`
--

DROP TABLE IF EXISTS `traffic_packet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_packet` (
  `t_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `t_desc` varchar(256) NOT NULL,
  `traffic` decimal(14,2) DEFAULT '0.00',
  `expires` int(11) DEFAULT '30',
  `price` decimal(14,2) DEFAULT '0.00',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_packet`
--

LOCK TABLES `traffic_packet` WRITE;
/*!40000 ALTER TABLE `traffic_packet` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_packet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_realtime`
--

DROP TABLE IF EXISTS `traffic_realtime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_realtime` (
  `user_name` varchar(64) NOT NULL,
  `mac` varchar(64) NOT NULL,
  `traffic` decimal(14,2) DEFAULT '0.00',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `user_name` (`user_name`,`mac`)
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
  KEY `day` (`day`)
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
  `login_times` int(11) DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
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
INSERT INTO `user_info` VALUES ('acer','King1985','c6595641855b62e9d7948a7c9e07141e','jinhuafeng@yeah.net','100.00','0.00',1,'2014-05-18 04:07:22','2014-05-18 04:07:22'),('air','King1985','c6595641855b62e9d7948a7c9e07141e','jinhuafeng@yeah.net','100.00','0.00',1,'2014-05-18 04:07:40','2014-05-18 04:07:40'),('haodan','King1985','c6595641855b62e9d7948a7c9e07141e','jinhuafeng@yeah.net','100.00','0.00',1,'2014-05-18 04:07:29','2014-05-18 04:07:29'),('ipad','King1985','c6595641855b62e9d7948a7c9e07141e','jinhuafeng@yeah.net','100.00','0.00',1,'2014-05-18 04:08:02','2014-05-18 04:08:02'),('iphone','King1985','c6595641855b62e9d7948a7c9e07141e','jinhuafeng@yeah.net','100.00','0.00',1,'2014-05-18 04:06:54','2014-05-18 04:06:54'),('mi','King1985','c6595641855b62e9d7948a7c9e07141e','jinhuafeng@yeah.net','100.00','0.00',1,'2014-05-18 04:07:51','2014-05-18 04:07:51');
/*!40000 ALTER TABLE `user_info` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-18 17:22:53
