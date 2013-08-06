-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 06, 2013 at 04:45 PM
-- Server version: 5.5.31
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `ajde_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl`
--

CREATE TABLE IF NOT EXISTS `acl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` enum('page','model') NOT NULL DEFAULT 'page',
  `type` enum('user','usergroup','public') NOT NULL,
  `user` int(10) unsigned DEFAULT NULL,
  `usergroup` int(10) unsigned DEFAULT NULL,
  `module` varchar(255) NOT NULL DEFAULT '*',
  `action` varchar(255) NOT NULL DEFAULT '*',
  `extra` varchar(255) NOT NULL DEFAULT '*',
  `permission` enum('allow','parent','own','deny') NOT NULL DEFAULT 'own',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `usergroup` (`usergroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=113 ;

--
-- Dumping data for table `acl`
--

INSERT INTO `acl` (`id`, `entity`, `type`, `user`, `usergroup`, `module`, `action`, `extra`, `permission`) VALUES
(90, 'page', 'usergroup', NULL, 2, '*', '*', '*', 'allow'),
(111, 'model', 'public', NULL, NULL, 'node', 'read', '*', 'allow'),
(112, 'model', 'usergroup', NULL, 2, 'node', '*', '*', 'allow');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `name` varchar(255) NOT NULL COMMENT 'Title',
  `type` enum('unknown','image','file','embed') NOT NULL DEFAULT 'unknown' COMMENT 'Type',
  `pointer` text NOT NULL COMMENT 'Pointer',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'Thumbnail',
  `icon` varchar(255) DEFAULT NULL COMMENT 'Icon',
  `added` timestamp NULL DEFAULT NULL COMMENT 'Added',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated',
  `user` int(10) unsigned NOT NULL COMMENT 'Owner',
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=293 ;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `sort`, `name`, `type`, `pointer`, `thumbnail`, `icon`, `added`, `updated`, `user`) VALUES
(292, 996, 'ajde', 'image', 'ajde.jpg', 'ajde.jpg', NULL, '2013-08-06 13:22:10', '2013-08-06 13:22:10', 20);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `type` enum('Node link','Submenu','URL') NOT NULL DEFAULT 'Node link' COMMENT 'Type',
  `url` text COMMENT 'URL',
  `parent` int(10) unsigned DEFAULT NULL COMMENT 'Parent menu',
  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Recursion level',
  `node` int(10) unsigned DEFAULT NULL COMMENT 'Node link',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `node` (`node`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `type`, `url`, `parent`, `level`, `node`, `sort`) VALUES
(1, 'mainmenu', 'Submenu', NULL, NULL, 0, NULL, 1),
(2, 'Home', 'Node link', NULL, 1, 1, 397, 2);

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `target` enum('node','setting') NOT NULL DEFAULT 'node' COMMENT 'Targeting',
  `type` enum('Text','Numeric','List of options','Node link','Media','Date','Time','Timespan','Spatial','Toggle','User') NOT NULL COMMENT 'Type',
  `options` text COMMENT 'Options',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nodetype` int(10) unsigned NOT NULL COMMENT 'Node type',
  `title` varchar(255) NOT NULL COMMENT 'Title',
  `slug` varchar(255) DEFAULT NULL COMMENT 'Slug',
  `subtitle` varchar(255) DEFAULT NULL COMMENT 'Subtitle',
  `added` timestamp NULL DEFAULT NULL COMMENT 'Added',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated',
  `content` text COMMENT 'Content',
  `summary` text COMMENT 'Summary',
  `media` int(10) unsigned DEFAULT NULL COMMENT 'Cover',
  `parent` int(10) unsigned DEFAULT NULL COMMENT 'Parent node',
  `root` int(10) unsigned DEFAULT NULL COMMENT 'Root node',
  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Recursion level',
  `user` int(10) unsigned NOT NULL COMMENT 'Owner',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `published` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Published',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `media` (`media`),
  KEY `posttype` (`nodetype`),
  KEY `parent` (`parent`),
  KEY `root` (`root`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=398 ;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `nodetype`, `title`, `slug`, `subtitle`, `added`, `updated`, `content`, `summary`, `media`, `parent`, `root`, `level`, `user`, `sort`, `published`) VALUES
(397, 32, 'Welcome to the Ajde Framework', 'home', NULL, '2013-08-05 17:25:05', '2013-08-06 13:22:17', '<p>Yet another PHP 5.0 MVC framework with out-of-the-box HTML / CSS / JS / caching and HTTP optimizations. Your project will be fast and cutting edges right from the start!</p>\r\n', NULL, 292, NULL, NULL, 0, 20, 1, 1);

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
  `related_nodes` tinyint(4) DEFAULT '0' COMMENT 'Related nodes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `nodetype`
--

INSERT INTO `nodetype` (`id`, `name`, `category`, `sort`, `title`, `subtitle`, `content`, `summary`, `media`, `tag`, `additional_media`, `children`, `published`, `related_nodes`) VALUES
(32, 'Homepage', NULL, 999, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `nodetype_meta`
--

CREATE TABLE IF NOT EXISTS `nodetype_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nodetype` int(10) unsigned NOT NULL COMMENT 'Node type',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nodetype` (`nodetype`,`meta`),
  KEY `meta` (`meta`),
  KEY `post` (`nodetype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `node_media`
--

CREATE TABLE IF NOT EXISTS `node_media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `media` int(10) unsigned NOT NULL COMMENT 'Media',
  `sort` int(10) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`,`media`),
  KEY `post` (`node`),
  KEY `media` (`media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `node_meta`
--

CREATE TABLE IF NOT EXISTS `node_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `value` text NOT NULL COMMENT 'Value',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`,`meta`),
  KEY `meta` (`meta`),
  KEY `post` (`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `node_related`
--

CREATE TABLE IF NOT EXISTS `node_related` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `related` int(10) unsigned NOT NULL COMMENT 'Related node',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`,`related`),
  KEY `meta` (`related`),
  KEY `post` (`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `node_tag`
--

CREATE TABLE IF NOT EXISTS `node_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `tag` int(10) unsigned NOT NULL COMMENT 'Tag',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`,`tag`),
  KEY `tag` (`tag`),
  KEY `post` (`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Page',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `name`, `sort`) VALUES
(1, 'Main settings', 999);

-- --------------------------------------------------------

--
-- Table structure for table `setting_meta`
--

CREATE TABLE IF NOT EXISTS `setting_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting` int(10) unsigned NOT NULL COMMENT 'Page',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nodetype` (`setting`,`meta`),
  KEY `meta` (`meta`),
  KEY `post` (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `sort` varchar(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL COMMENT 'Username',
  `password` text NOT NULL COMMENT 'Password',
  `usergroup` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'User group',
  `email` varchar(255) DEFAULT NULL COMMENT 'E-mail',
  `fullname` varchar(255) DEFAULT NULL COMMENT 'Full name',
  `address` varchar(255) DEFAULT NULL COMMENT 'Address',
  `zipcode` varchar(255) DEFAULT NULL COMMENT 'Zipcode',
  `city` varchar(255) DEFAULT NULL COMMENT 'City',
  `region` varchar(255) DEFAULT NULL COMMENT 'Region',
  `country` varchar(255) DEFAULT NULL COMMENT 'Country',
  `secret` varchar(255) NOT NULL COMMENT 'Secret hash',
  `debug` tinyint(4) DEFAULT '0' COMMENT 'Turn debugging on',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `usergroup` (`usergroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Table structure for table `usergroup`
--

CREATE TABLE IF NOT EXISTS `usergroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `usergroup`
--

INSERT INTO `usergroup` (`id`, `name`, `sort`) VALUES
(1, 'users', 1),
(2, 'admins', 4),
(3, 'clients', 2),
(4, 'employees', 3);

-- --------------------------------------------------------

--
-- Table structure for table `user_node`
--

CREATE TABLE IF NOT EXISTS `user_node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `node` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `node` (`node`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

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
-- Constraints for dumped tables
--

--
-- Constraints for table `acl`
--
ALTER TABLE `acl`
  ADD CONSTRAINT `acl_ibfk_5` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acl_ibfk_6` FOREIGN KEY (`usergroup`) REFERENCES `usergroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acl_ibfk_7` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acl_ibfk_8` FOREIGN KEY (`usergroup`) REFERENCES `usergroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`cart`) REFERENCES `cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_item_ibfk_3` FOREIGN KEY (`cart`) REFERENCES `cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `media_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_ibfk_2` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_ibfk_3` FOREIGN KEY (`parent`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_ibfk_4` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `node_ibfk_10` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`),
  ADD CONSTRAINT `node_ibfk_11` FOREIGN KEY (`parent`) REFERENCES `node` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `node_ibfk_12` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`),
  ADD CONSTRAINT `node_ibfk_13` FOREIGN KEY (`root`) REFERENCES `node` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `node_ibfk_14` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `node_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `node_ibfk_4` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `node_ibfk_7` FOREIGN KEY (`parent`) REFERENCES `node` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `node_ibfk_9` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `nodetype_meta`
--
ALTER TABLE `nodetype_meta`
  ADD CONSTRAINT `nodetype_meta_ibfk_1` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nodetype_meta_ibfk_2` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nodetype_meta_ibfk_3` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nodetype_meta_ibfk_4` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node_media`
--
ALTER TABLE `node_media`
  ADD CONSTRAINT `node_media_ibfk_2` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_media_ibfk_3` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_media_ibfk_4` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_media_ibfk_5` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node_meta`
--
ALTER TABLE `node_meta`
  ADD CONSTRAINT `node_meta_ibfk_4` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_meta_ibfk_5` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_meta_ibfk_6` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_meta_ibfk_7` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node_related`
--
ALTER TABLE `node_related`
  ADD CONSTRAINT `node_related_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_related_ibfk_2` FOREIGN KEY (`related`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_related_ibfk_3` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_related_ibfk_4` FOREIGN KEY (`related`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node_tag`
--
ALTER TABLE `node_tag`
  ADD CONSTRAINT `node_tag_ibfk_2` FOREIGN KEY (`tag`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_tag_ibfk_3` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_tag_ibfk_4` FOREIGN KEY (`tag`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_tag_ibfk_5` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sample`
--
ALTER TABLE `sample`
  ADD CONSTRAINT `sample_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `sample_ibfk_3` FOREIGN KEY (`vat`) REFERENCES `vat` (`id`),
  ADD CONSTRAINT `sample_ibfk_4` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `sample_ibfk_5` FOREIGN KEY (`vat`) REFERENCES `vat` (`id`);

--
-- Constraints for table `setting_meta`
--
ALTER TABLE `setting_meta`
  ADD CONSTRAINT `setting_meta_ibfk_2` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `setting_meta_ibfk_3` FOREIGN KEY (`setting`) REFERENCES `setting` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `setting_meta_ibfk_4` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `setting_meta_ibfk_5` FOREIGN KEY (`setting`) REFERENCES `setting` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `transaction_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`usergroup`) REFERENCES `usergroup` (`id`),
  ADD CONSTRAINT `user_ibfk_3` FOREIGN KEY (`usergroup`) REFERENCES `usergroup` (`id`);

--
-- Constraints for table `user_node`
--
ALTER TABLE `user_node`
  ADD CONSTRAINT `user_node_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_node_ibfk_2` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
