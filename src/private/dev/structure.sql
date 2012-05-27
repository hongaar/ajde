-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 26, 2012 at 11:37 AM
-- Server version: 5.5.22
-- PHP Version: 5.3.10-1ubuntu3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=117 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=324 ;

-- --------------------------------------------------------

--
-- Table structure for table `samples`
--

CREATE TABLE IF NOT EXISTS `samples` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `usergroup`
--

CREATE TABLE IF NOT EXISTS `usergroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `vat`
--

CREATE TABLE IF NOT EXISTS `vat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `percentage` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

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
-- Constraints for table `samples`
--
ALTER TABLE `samples`
  ADD CONSTRAINT `samples_ibfk_3` FOREIGN KEY (`vat`) REFERENCES `vat` (`id`),
  ADD CONSTRAINT `samples_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

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

--
-- Dumping data for table `usergroup`
--

INSERT INTO `usergroup` (`id`, `name`) VALUES
(1, 'users'),
(2, 'admins');

--
-- Dumping data for table `acl`
--

INSERT INTO `acl` (`id`, `type`, `user`, `usergroup`, `module`, `action`, `extra`, `permission`) VALUES
(17, 'usergroup', NULL, 2, '_core', '*', '*', 'allow'),
(22, 'usergroup', NULL, NULL, 'shop', 'checkout', '*', 'allow'),
(23, 'usergroup', NULL, NULL, 'shop', '*', 'transaction', 'allow'),
(24, 'usergroup', NULL, NULL, 'samples', 'edit', '*', 'allow');
