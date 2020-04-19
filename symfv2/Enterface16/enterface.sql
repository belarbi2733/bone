-- MySQL dump 10.13  Distrib 5.7.29, for Linux (x86_64)
--
-- Host: localhost    Database: enterfacedb
-- ------------------------------------------------------
-- Server version	5.7.29-0ubuntu0.18.04.1

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
-- Table structure for table `fos_user`
--

DROP TABLE IF EXISTS `fos_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fos_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `adress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `credit` int(11) NOT NULL,
  `avatar_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nb_post` int(11) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fos_user`
--

LOCK TABLES `fos_user` WRITE;
/*!40000 ALTER TABLE `fos_user` DISABLE KEYS */;
INSERT INTO `fos_user` VALUES (1,'umons7000','umons7000','umons@gmail.com','umons@gmail.com',1,'gay4hf3wbkock8ww0oo8s44og88woso','$2y$13$gay4hf3wbkock8ww0oo8suMB1xOszGzh.qgshish7s8E202keP4WS','2019-05-09 10:16:12',0,0,NULL,NULL,NULL,'a:0:{}',0,NULL,'bone','umons','ir','umons','umons',974,NULL,NULL,NULL);
/*!40000 ALTER TABLE `fos_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prix_service`
--

DROP TABLE IF EXISTS `prix_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prix_service` (
  `Prix` int(11) NOT NULL,
  `Id_service` int(11) NOT NULL,
  `Id_type_service` int(11) NOT NULL,
  PRIMARY KEY (`Id_service`,`Id_type_service`),
  UNIQUE KEY `idx_service` (`Id_service`,`Id_type_service`),
  KEY `fk_type_service` (`Id_type_service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prix_service`
--

LOCK TABLES `prix_service` WRITE;
/*!40000 ALTER TABLE `prix_service` DISABLE KEYS */;
INSERT INTO `prix_service` VALUES (2,1,1),(4,1,2),(6,1,3),(2,2,1),(4,2,2),(6,2,3),(0,3,1),(0,3,2),(2,3,3);
/*!40000 ALTER TABLE `prix_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service` (
  `Label` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Id_service` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Id_service`),
  UNIQUE KEY `idx_Service` (`Id_service`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service`
--

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
INSERT INTO `service` VALUES ('CAD\r\nFOR\r\nOSTEOPOROSIS',1),('CAD\r\nFOR\r\nSCOLIOSIS',2),('MEDICAL\r\nALGORITHMS\r\nTOOLBOX',3);
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_service`
--

DROP TABLE IF EXISTS `type_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type_service` (
  `Label` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Id_Type_service` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Id_Type_service`),
  UNIQUE KEY `idx_type_service` (`Id_Type_service`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_service`
--

LOCK TABLES `type_service` WRITE;
/*!40000 ALTER TABLE `type_service` DISABLE KEYS */;
INSERT INTO `type_service` VALUES ('Basic',1),('Advanced',2),('Personalized',3);
/*!40000 ALTER TABLE `type_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_results`
--

DROP TABLE IF EXISTS `user_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `applicationname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `urldata` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `urlkey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ipp` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imageResultType` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `urlkeyslave` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `urldirectory` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C7AEFA1EA76ED395` (`user_id`),
  CONSTRAINT `FK_C7AEFA1EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_results`
--

LOCK TABLES `user_results` WRITE;
/*!40000 ALTER TABLE `user_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_transactions`
--

DROP TABLE IF EXISTS `user_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `transactionID` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6A64664EA76ED395` (`user_id`),
  CONSTRAINT `FK_6A64664EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_transactions`
--

LOCK TABLES `user_transactions` WRITE;
/*!40000 ALTER TABLE `user_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workingforum_forum`
--

DROP TABLE IF EXISTS `workingforum_forum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workingforum_forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workingforum_forum`
--

LOCK TABLES `workingforum_forum` WRITE;
/*!40000 ALTER TABLE `workingforum_forum` DISABLE KEYS */;
/*!40000 ALTER TABLE `workingforum_forum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workingforum_post`
--

DROP TABLE IF EXISTS `workingforum_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workingforum_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL,
  `cdate` datetime NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `moderateReason` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_1C563EF6E2904019` (`thread_id`),
  KEY `IDX_1C563EF6A76ED395` (`user_id`),
  CONSTRAINT `FK_1C563EF6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
  CONSTRAINT `FK_1C563EF6E2904019` FOREIGN KEY (`thread_id`) REFERENCES `workingforum_thread` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workingforum_post`
--

LOCK TABLES `workingforum_post` WRITE;
/*!40000 ALTER TABLE `workingforum_post` DISABLE KEYS */;
/*!40000 ALTER TABLE `workingforum_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workingforum_post_report`
--

DROP TABLE IF EXISTS `workingforum_post_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workingforum_post_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cdate` datetime NOT NULL,
  `processed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A95E2B754B89032C` (`post_id`),
  KEY `IDX_A95E2B75A76ED395` (`user_id`),
  CONSTRAINT `FK_A95E2B754B89032C` FOREIGN KEY (`post_id`) REFERENCES `workingforum_post` (`id`),
  CONSTRAINT `FK_A95E2B75A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workingforum_post_report`
--

LOCK TABLES `workingforum_post_report` WRITE;
/*!40000 ALTER TABLE `workingforum_post_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `workingforum_post_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workingforum_setting`
--

DROP TABLE IF EXISTS `workingforum_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workingforum_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workingforum_setting`
--

LOCK TABLES `workingforum_setting` WRITE;
/*!40000 ALTER TABLE `workingforum_setting` DISABLE KEYS */;
/*!40000 ALTER TABLE `workingforum_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workingforum_subforum`
--

DROP TABLE IF EXISTS `workingforum_subforum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workingforum_subforum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nb_thread` int(11) DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nb_post` int(11) DEFAULT NULL,
  `last_reply_date` datetime DEFAULT NULL,
  `allowed_roles` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)',
  `lastReplyUser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9EACE2E229CCBAD0` (`forum_id`),
  KEY `IDX_9EACE2E21F7EE8A0` (`lastReplyUser`),
  CONSTRAINT `FK_9EACE2E21F7EE8A0` FOREIGN KEY (`lastReplyUser`) REFERENCES `fos_user` (`id`),
  CONSTRAINT `FK_9EACE2E229CCBAD0` FOREIGN KEY (`forum_id`) REFERENCES `workingforum_forum` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workingforum_subforum`
--

LOCK TABLES `workingforum_subforum` WRITE;
/*!40000 ALTER TABLE `workingforum_subforum` DISABLE KEYS */;
/*!40000 ALTER TABLE `workingforum_subforum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workingforum_thread`
--

DROP TABLE IF EXISTS `workingforum_thread`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workingforum_thread` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subforum_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `cdate` datetime NOT NULL,
  `nbReplies` int(11) NOT NULL,
  `lastReplyDate` datetime NOT NULL,
  `resolved` tinyint(1) DEFAULT NULL,
  `locked` tinyint(1) DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sublabel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pin` tinyint(1) DEFAULT NULL,
  `lastReplyUser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_788E9ABA225C0759` (`subforum_id`),
  KEY `IDX_788E9ABAF675F31B` (`author_id`),
  KEY `IDX_788E9ABA1F7EE8A0` (`lastReplyUser`),
  CONSTRAINT `FK_788E9ABA1F7EE8A0` FOREIGN KEY (`lastReplyUser`) REFERENCES `fos_user` (`id`),
  CONSTRAINT `FK_788E9ABA225C0759` FOREIGN KEY (`subforum_id`) REFERENCES `workingforum_subforum` (`id`),
  CONSTRAINT `FK_788E9ABAF675F31B` FOREIGN KEY (`author_id`) REFERENCES `fos_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workingforum_thread`
--

LOCK TABLES `workingforum_thread` WRITE;
/*!40000 ALTER TABLE `workingforum_thread` DISABLE KEYS */;
/*!40000 ALTER TABLE `workingforum_thread` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-04-19 10:14:15
