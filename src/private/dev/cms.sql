-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 25, 2012 at 07:53 PM
-- Server version: 5.1.61
-- PHP Version: 5.3.5-1ubuntu7.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ajde`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl`
--

CREATE TABLE IF NOT EXISTS `acl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('user','usergroup','public') NOT NULL,
  `user` int(10) unsigned DEFAULT NULL,
  `usergroup` int(10) unsigned DEFAULT NULL,
  `module` varchar(255) NOT NULL DEFAULT '*',
  `action` varchar(255) NOT NULL DEFAULT '*',
  `extra` varchar(255) NOT NULL DEFAULT '*',
  `permission` enum('allow','own','deny') NOT NULL DEFAULT 'own',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `usergroup` (`usergroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `acl`
--

INSERT INTO `acl` (`id`, `type`, `user`, `usergroup`, `module`, `action`, `extra`, `permission`) VALUES
(17, 'usergroup', NULL, 2, '_core', '*', '*', 'allow'),
(22, 'usergroup', NULL, NULL, 'shop', 'checkout', '*', 'allow'),
(23, 'usergroup', NULL, NULL, 'shop', '*', 'transaction', 'allow'),
(24, 'usergroup', NULL, NULL, 'sample', 'edit', '*', 'allow'),
(25, 'usergroup', NULL, 2, 'admin', '*', '*', 'allow'),
(26, 'usergroup', NULL, 2, '*', '*', '*', 'allow');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned DEFAULT NULL,
  `client` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user`, `client`) VALUES
(2, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE IF NOT EXISTS `cart_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cart` int(10) unsigned NOT NULL,
  `entity` varchar(255) NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `unitprice` decimal(8,2) NOT NULL,
  `qty` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cart` (`cart`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cart_item`
--


-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `name` varchar(255) NOT NULL COMMENT 'Title',
  `type` enum('image','embed') NOT NULL COMMENT 'Type',
  `filename` varchar(255) DEFAULT NULL COMMENT 'Image',
  `embed` text COMMENT 'Embed code / URL',
  `portfolio` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `portfolio` (`portfolio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `media`
--


-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `posttype` int(10) unsigned NOT NULL COMMENT 'Posttype',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `format` enum('string','int') NOT NULL COMMENT 'Format',
  PRIMARY KEY (`id`),
  KEY `posttype` (`posttype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `meta`
--


-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `posttype` int(10) unsigned NOT NULL COMMENT 'Posttype',
  `name` varchar(255) NOT NULL COMMENT 'Title',
  `subtitle` varchar(255) DEFAULT NULL COMMENT 'Subtitle',
  `added` timestamp NULL DEFAULT NULL COMMENT 'Added on',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Article updated on',
  `content` text NOT NULL COMMENT 'Article',
  `description` text COMMENT 'Description',
  `media` int(10) unsigned DEFAULT NULL COMMENT 'Featured media',
  `user` int(10) unsigned NOT NULL COMMENT 'Owner',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `published` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Published',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `media` (`media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `post`
--


-- --------------------------------------------------------

--
-- Table structure for table `posttype`
--

CREATE TABLE IF NOT EXISTS `posttype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `posttype`
--


-- --------------------------------------------------------

--
-- Table structure for table `post_media`
--

CREATE TABLE IF NOT EXISTS `post_media` (
  `post` int(10) unsigned NOT NULL COMMENT 'Post',
  `media` int(10) unsigned NOT NULL COMMENT 'Media',
  KEY `post` (`post`),
  KEY `media` (`media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `post_media`
--


-- --------------------------------------------------------

--
-- Table structure for table `post_meta`
--

CREATE TABLE IF NOT EXISTS `post_meta` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID',
  `posttype` int(10) unsigned NOT NULL COMMENT 'Posttype',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `value` text NOT NULL COMMENT 'Value',
  PRIMARY KEY (`id`),
  KEY `posttype` (`posttype`),
  KEY `meta` (`meta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `post_meta`
--


-- --------------------------------------------------------

--
-- Table structure for table `post_tag`
--

CREATE TABLE IF NOT EXISTS `post_tag` (
  `post` int(10) unsigned NOT NULL COMMENT 'Post',
  `tag` int(10) unsigned NOT NULL COMMENT 'Distributor',
  KEY `tag` (`tag`),
  KEY `post` (`post`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `post_tag`
--


-- --------------------------------------------------------

--
-- Table structure for table `sample`
--

CREATE TABLE IF NOT EXISTS `sample` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) NOT NULL COMMENT 'Title',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Article updated on',
  `type` enum('video','image','text') NOT NULL DEFAULT 'text' COMMENT 'Type of content',
  `content` text NOT NULL COMMENT 'Article',
  `image` varchar(255) DEFAULT NULL COMMENT 'Image',
  `unitprice` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Unit price',
  `vat` int(10) unsigned NOT NULL COMMENT 'VAT %',
  `optional_field` varchar(255) DEFAULT NULL COMMENT 'Optional field',
  `user` int(10) unsigned NOT NULL COMMENT 'Owner',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `published` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Published',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `vat` (`vat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sample`
--


-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `sort` varchar(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `name`, `sort`) VALUES
(53, 'Audio', '999');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE IF NOT EXISTS `transaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned DEFAULT NULL,
  `ip` varchar(255) NOT NULL,
  `payment_provider` varchar(255) DEFAULT NULL,
  `payment_status` enum('pending','requested','refused','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_details` text,
  `payment_amount` decimal(8,2) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `shipment_address` varchar(255) DEFAULT NULL,
  `shipment_zipcode` varchar(255) DEFAULT NULL,
  `shipment_city` varchar(255) DEFAULT NULL,
  `shipment_region` varchar(255) DEFAULT NULL,
  `shipment_country` varchar(255) DEFAULT NULL,
  `shipment_status` enum('new','shipped','delivered') NOT NULL DEFAULT 'new',
  `shipment_itemsqty` int(11) NOT NULL,
  `shipment_itemsvatamount` decimal(8,2) NOT NULL,
  `shipment_itemstotal` decimal(8,2) NOT NULL,
  `shipment_description` text,
  `shipment_method` varchar(255) DEFAULT NULL,
  `shipment_cost` decimal(8,2) NOT NULL,
  `shipment_trackingcode` text,
  `comment` text,
  `secret` varchar(255) NOT NULL,
  `secret_archive` text,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `secret` (`secret`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `transaction`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `usergroup` int(10) unsigned NOT NULL DEFAULT '1',
  `email` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `secret` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `usergroup` (`usergroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `usergroup`, `email`, `fullname`, `address`, `zipcode`, `city`, `region`, `country`, `secret`) VALUES
(1, 'joram', '$2a$10$81581f5349256bf96edc0uS5ceOfytTAmDb/UrgoKmxa5nu2rvZLa', 2, 'hongaar@gmail.com', 'Joram van den Boezem', NULL, NULL, NULL, NULL, NULL, '74706f5ea4d45d1f18eb0a20dd318bf51c0c1972');

-- --------------------------------------------------------

--
-- Table structure for table `usergroup`
--

CREATE TABLE IF NOT EXISTS `usergroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `usergroup`
--

INSERT INTO `usergroup` (`id`, `name`) VALUES
(1, 'users'),
(2, 'admins'),
(3, 'editors');

-- --------------------------------------------------------

--
-- Table structure for table `vat`
--

CREATE TABLE IF NOT EXISTS `vat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `percentage` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `vat`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `acl`
--
ALTER TABLE `acl`
  ADD CONSTRAINT `acl_ibfk_5` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acl_ibfk_6` FOREIGN KEY (`usergroup`) REFERENCES `usergroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`cart`) REFERENCES `cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`portfolio`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`media`) REFERENCES `meta` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `post_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `post_media`
--
ALTER TABLE `post_media`
  ADD CONSTRAINT `post_media_ibfk_1` FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_media_ibfk_2` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post_meta`
--
ALTER TABLE `post_meta`
  ADD CONSTRAINT `post_meta_ibfk_3` FOREIGN KEY (`posttype`) REFERENCES `posttype` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_meta_ibfk_4` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post_tag`
--
ALTER TABLE `post_tag`
  ADD CONSTRAINT `post_tag_ibfk_2` FOREIGN KEY (`tag`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_tag_ibfk_3` FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sample`
--
ALTER TABLE `sample`
  ADD CONSTRAINT `sample_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `sample_ibfk_3` FOREIGN KEY (`vat`) REFERENCES `vat` (`id`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`usergroup`) REFERENCES `usergroup` (`id`);
