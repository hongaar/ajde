-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2013 at 02:46 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `ajde_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `nodetype`
--

CREATE TABLE IF NOT EXISTS `nodetype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `category` varchar(255) DEFAULT NULL COMMENT 'Category',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `title` tinyint(4) DEFAULT '1' COMMENT 'Title',
  `subtitle` tinyint(4) DEFAULT '0' COMMENT 'Subtitle',
  `content` tinyint(4) DEFAULT '1' COMMENT 'Content',
  `summary` tinyint(4) DEFAULT '0' COMMENT 'Summary',
  `media` tinyint(4) DEFAULT '1' COMMENT 'Featured image',
  `tag` tinyint(4) DEFAULT '0' COMMENT 'Tags',
  `additional_media` tinyint(4) DEFAULT '0' COMMENT 'Additional media',
  `children` tinyint(4) DEFAULT '0' COMMENT 'Tree structure',
  `published` tinyint(4) DEFAULT '0' COMMENT 'Toggle published',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `nodetype`
--

INSERT INTO `nodetype` (`id`, `name`, `category`, `sort`, `title`, `subtitle`, `content`, `summary`, `media`, `tag`, `additional_media`, `children`, `published`) VALUES
(17, 'Blog post', 'pages', 1001, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(18, 'Page', 'pages', 999, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'New node type', 'some category', 1002, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, NULL),
(20, 'New type of style thingy', 'some category', 1000, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, NULL);
