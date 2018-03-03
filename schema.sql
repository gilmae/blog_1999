-- MySQL dump 10.13  Distrib 5.7.20, for osx10.13 (x86_64)
--
-- Host: localhost    Database: gilmae
-- ------------------------------------------------------
-- Server version	5.7.20

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
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Category` (
  `CategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `Category` char(50) DEFAULT NULL,
  `CategoryDesc` text,
  `nodeType` char(1) DEFAULT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Links`
--

DROP TABLE IF EXISTS `Links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Links` (
  `LinkID` int(11) NOT NULL AUTO_INCREMENT,
  `LinkName` char(50) DEFAULT NULL,
  `Link` char(100) DEFAULT NULL,
  `LinkRSS` char(100) DEFAULT NULL,
  `LinkDescription` char(100) DEFAULT NULL,
  `currentRSS` text,
  `DateRead` char(14) DEFAULT NULL,
  PRIMARY KEY (`LinkID`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `NodeCategories`
--

DROP TABLE IF EXISTS `NodeCategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NodeCategories` (
  `NodeCategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `nodeID` int(11) DEFAULT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  PRIMARY KEY (`NodeCategoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=345 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `NodeEdits`
--

DROP TABLE IF EXISTS `NodeEdits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NodeEdits` (
  `EditID` int(11) NOT NULL AUTO_INCREMENT,
  `NodeID` int(11) DEFAULT NULL,
  `NodeTitle` char(100) DEFAULT NULL,
  `NodeBody` text,
  `NodePrecise` text,
  `EditDate` char(14) DEFAULT NULL,
  `EditedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`EditID`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Pingback`
--

DROP TABLE IF EXISTS `Pingback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Pingback` (
  `PingbackID` int(11) NOT NULL AUTO_INCREMENT,
  `NodeID` int(11) DEFAULT NULL,
  `Source` char(255) DEFAULT NULL,
  `Title` char(255) DEFAULT NULL,
  PRIMARY KEY (`PingbackID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SmartLinks`
--

DROP TABLE IF EXISTS `SmartLinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SmartLinks` (
  `SmartLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `SmartLinkKey` char(100) DEFAULT NULL,
  `SmartLink` char(255) DEFAULT NULL,
  PRIMARY KEY (`SmartLinkID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Threading`
--

DROP TABLE IF EXISTS `Threading`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Threading` (
  `ThreadingID` int(11) NOT NULL AUTO_INCREMENT,
  `ThreadID` int(11) DEFAULT NULL,
  `BlockID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ThreadingID`)
) ENGINE=MyISAM AUTO_INCREMENT=8934 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Token`
--

DROP TABLE IF EXISTS `Token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Token` (
  `TokenId` int(11) NOT NULL AUTO_INCREMENT,
  `Token` char(255) DEFAULT NULL,
  `Data` text,
  PRIMARY KEY (`TokenId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aqwiki_revision`
--

DROP TABLE IF EXISTS `aqwiki_revision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aqwiki_revision` (
  `revision` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page` int(10) NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  `comment` tinytext,
  `creator` tinytext NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`revision`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aqwiki_users`
--

DROP TABLE IF EXISTS `aqwiki_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aqwiki_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(64) NOT NULL DEFAULT '',
  `real_name` tinytext,
  `email` tinytext,
  `birthday` date DEFAULT NULL,
  `password` tinytext,
  `location` int(11) DEFAULT NULL,
  `last_access` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_level` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aqwiki_wikipage`
--

DROP TABLE IF EXISTS `aqwiki_wikipage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aqwiki_wikipage` (
  `page` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wiki` tinytext NOT NULL,
  `name` tinytext NOT NULL,
  `spinlock` bigint(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `origin` tinytext,
  `yalelock` tinytext,
  PRIMARY KEY (`page`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `channels`
--

DROP TABLE IF EXISTS `channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channels` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `title` char(255) NOT NULL DEFAULT '',
  `url` char(255) NOT NULL DEFAULT '',
  `siteurl` char(255) DEFAULT NULL,
  `parent` tinyint(4) DEFAULT '0',
  `descr` char(255) DEFAULT NULL,
  `dateadded` datetime DEFAULT NULL,
  `icon` char(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `mode` int(16) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=188 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `key_` char(127) NOT NULL DEFAULT '',
  `value_` text NOT NULL,
  `default_` text NOT NULL,
  `type_` enum('string','num','boolean','array','enum') NOT NULL DEFAULT 'string',
  `desc_` text,
  `export_` char(127) DEFAULT NULL,
  PRIMARY KEY (`key_`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `folders` (
  `id` tinyint(11) NOT NULL AUTO_INCREMENT,
  `name` char(127) NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item` (
  `id` bigint(16) NOT NULL AUTO_INCREMENT,
  `cid` bigint(11) NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` char(255) DEFAULT NULL,
  `url` char(255) DEFAULT NULL,
  `description` text,
  `unread` tinyint(4) DEFAULT '1',
  `pubdate` datetime DEFAULT NULL,
  `author` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=5463 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `metatag`
--

DROP TABLE IF EXISTS `metatag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metatag` (
  `fid` bigint(16) NOT NULL DEFAULT '0',
  `tid` bigint(16) NOT NULL DEFAULT '0',
  `ttype` enum('item','folder','channel') NOT NULL DEFAULT 'item',
  KEY `fid` (`fid`),
  KEY `tid` (`tid`),
  KEY `ttype` (`ttype`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nodeTypes`
--

DROP TABLE IF EXISTS `nodeTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodeTypes` (
  `typeName` char(50) NOT NULL DEFAULT '',
  `typeCode` char(1) DEFAULT NULL,
  `PingbackEnabled` int(11) DEFAULT '0',
  `BlogsPingName` char(50) DEFAULT '',
  `BlogsPingURI` char(255) DEFAULT '',
  PRIMARY KEY (`typeName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nodes`
--

DROP TABLE IF EXISTS `nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodes` (
  `nodeID` int(11) NOT NULL AUTO_INCREMENT,
  `nodeTitle` char(100) DEFAULT NULL,
  `nodeBody` text,
  `nodePrecise` text,
  `datetime` char(14) DEFAULT NULL,
  `nodeType` char(1) DEFAULT NULL,
  `parentNode` int(11) DEFAULT NULL,
  `nextSibling` int(11) DEFAULT '-1',
  `prevSibling` int(11) DEFAULT '-1',
  `childNodes` int(11) DEFAULT NULL,
  `threadID` int(11) DEFAULT '-1',
  `FirstChild` int(11) DEFAULT '-1',
  `LastChild` int(11) DEFAULT '-1',
  `postedBy` int(11) DEFAULT NULL,
  `public` int(11) DEFAULT '1',
  `Edited` char(1) DEFAULT '',
  `Pings` int(11) DEFAULT '0',
  PRIMARY KEY (`nodeID`)
) ENGINE=MyISAM AUTO_INCREMENT=15269 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rating`
--

DROP TABLE IF EXISTS `rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rating` (
  `iid` bigint(16) NOT NULL DEFAULT '0',
  `rating` tinyint(4) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` bigint(16) NOT NULL AUTO_INCREMENT,
  `tag` char(63) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `counter` int(11) NOT NULL AUTO_INCREMENT,
  `userID` char(20) DEFAULT NULL,
  `password` char(20) DEFAULT NULL,
  `userName` char(30) DEFAULT NULL,
  `email` char(100) DEFAULT NULL,
  `url` char(100) DEFAULT NULL,
  `admin` tinyint(4) DEFAULT NULL,
  `trusted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`counter`)
) ENGINE=MyISAM AUTO_INCREMENT=3630 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-03-03 16:11:15
