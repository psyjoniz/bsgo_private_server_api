-- MariaDB dump 10.19  Distrib 10.5.23-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: bsgo_api
-- ------------------------------------------------------
-- Server version	10.5.23-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `connection_cooldown`
--

DROP TABLE IF EXISTS `connection_cooldown`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connection_cooldown` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT 3500,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fingerprint` (`ip_address`,`user_id`),
  KEY `created` (`created`),
  KEY `ip_address` (`ip_address`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connection_cooldown_log`
--

DROP TABLE IF EXISTS `connection_cooldown_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connection_cooldown_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event` varchar(50) NOT NULL,
  `duration_seconds` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`),
  KEY `user_id` (`user_id`),
  KEY `event` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connection_tracker`
--

DROP TABLE IF EXISTS `connection_tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connection_tracker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fingerprint` (`ip_address`,`user_id`),
  KEY `created` (`created`),
  KEY `last_seen` (`last_seen`),
  KEY `ip_address` (`ip_address`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invitations`
--

DROP TABLE IF EXISTS `invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_user_id` int(11) NOT NULL,
  `used` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `used_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `used` (`used`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rate_limit_offenses`
--

DROP TABLE IF EXISTS `rate_limit_offenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rate_limit_offenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event` varchar(50) NOT NULL,
  `count` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `offense` (`ip_address`,`user_id`,`event`),
  KEY `created` (`created`),
  KEY `ip_address` (`ip_address`),
  KEY `event` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rate_limit_tracker`
--

DROP TABLE IF EXISTS `rate_limit_tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rate_limit_tracker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`),
  KEY `ip_address` (`ip_address`),
  KEY `event` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `servers`
--

DROP TABLE IF EXISTS `servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `port` int(11) NOT NULL,
  `type` enum('prod','dev') NOT NULL DEFAULT 'prod',
  PRIMARY KEY (`id`),
  UNIQUE KEY `server` (`name`,`address`,`port`),
  KEY `name` (`name`),
  KEY `address` (`address`),
  KEY `port` (`port`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_server`
--

DROP TABLE IF EXISTS `user_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `server_id` (`server_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) DEFAULT NULL,
  `key` varchar(32) NOT NULL DEFAULT '',
  `authenticated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` enum('admin','player','developer','moderator') NOT NULL DEFAULT 'player',
  `ip` varchar(36) DEFAULT NULL,
  `bsgo_user_id` int(11) DEFAULT NULL,
  `bsgo_session_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `password` (`password`),
  KEY `authentication` (`email`,`password`),
  KEY `key` (`key`),
  KEY `authenticated` (`authenticated`),
  KEY `last_seen` (`last_seen`),
  KEY `type` (`type`),
  KEY `ip` (`ip`),
  KEY `bsgo_user_id` (`bsgo_user_id`),
  KEY `bsgo_session_id` (`bsgo_session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-17 19:26:04
