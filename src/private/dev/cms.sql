-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 16, 2013 at 08:14 PM
-- Server version: 5.5.29
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user`, `client`) VALUES
(7, 2, '');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `sort`, `name`, `type`, `pointer`, `thumbnail`, `icon`, `added`, `updated`, `user`) VALUES
(19, 997, 'Casa', 'image', 'CasaBabilon Back.png', 'CasaBabilon Back.png', NULL, '2013-04-16 17:54:55', '2013-04-16 17:54:55', 2),
(20, 996, 'neki', 'image', 'artworks-000041116269-hievf6-original.jpg', 'artworks-000041116269-hievf6-original.jpg', NULL, '2013-04-16 18:02:16', '2013-04-16 18:02:16', 2),
(21, 995, 'belshof', 'image', 'belshof51.png', 'belshof51.png', NULL, '2013-04-16 18:02:23', '2013-04-16 18:02:23', 2),
(22, 994, 'schotland', 'image', '78320_4612979892860_1495591108_o.jpg', '78320_4612979892860_1495591108_o.jpg', NULL, '2013-04-16 18:02:33', '2013-04-16 18:02:33', 2),
(23, 993, 'jungle sometimes', 'image', '2013 New Years Jungle Snake Hd Wallpaper.jpg', '2013 New Years Jungle Snake Hd Wallpaper.jpg', NULL, '2013-04-16 18:02:45', '2013-04-16 18:02:45', 2),
(24, 992, 'SoundCloudz', 'embed', '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F38800594&amp;auto_play=true&amp;show_artwork=true&amp;color=000"></iframe>', 'artworks-000019457682-ybg1hx-t300x30077.jpg', NULL, '2013-04-16 18:06:56', '2013-04-16 18:06:56', 2),
(25, 991, 'YouTubes', 'embed', '<iframe width=''100%'' height=''471'' src="http://www.youtube.com/embed/_jPaYnaKVDk?rel=0&amp;autoplay=1&amp;wmode=transparent" frameborder="0" allowfullscreen></iframe>', '065.jpg', NULL, '2013-04-16 18:07:02', '2013-04-16 18:07:02', 2),
(26, 990, 'Vimeoz', 'embed', '<iframe src="http://player.vimeo.com/video/63804783?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1" width=''100%'' height=''471'' frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>', '434210943_640.jpg', NULL, '2013-04-16 18:07:21', '2013-04-16 18:07:21', 2);

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nodetype` int(10) unsigned NOT NULL COMMENT 'Posttype',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `format` enum('string','int') NOT NULL COMMENT 'Format',
  PRIMARY KEY (`id`),
  KEY `posttype` (`nodetype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nodetype` int(10) unsigned NOT NULL COMMENT 'Posttype',
  `name` varchar(255) NOT NULL COMMENT 'Title',
  `subtitle` varchar(255) DEFAULT NULL COMMENT 'Subtitle',
  `added` timestamp NULL DEFAULT NULL COMMENT 'Added',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated',
  `content` text NOT NULL COMMENT 'Article',
  `description` text COMMENT 'Description',
  `media` int(10) unsigned DEFAULT NULL COMMENT 'Cover',
  `user` int(10) unsigned NOT NULL COMMENT 'Owner',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `published` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Published',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `media` (`media`),
  KEY `posttype` (`nodetype`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `nodetype`, `name`, `subtitle`, `added`, `updated`, `content`, `description`, `media`, `user`, `sort`, `published`) VALUES
(15, 2, 'Testing', NULL, '2013-04-11 18:54:21', '2013-04-16 18:04:42', '<p>gfdgfd</p>\r\n', NULL, NULL, 3, 9998, 0),
(16, 2, 'Welkom bij Vakadi', NULL, '2013-04-11 19:21:29', '2013-04-16 18:04:58', '<h2>Wist u dat...</h2>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit libero massa, ac semper elit. Integer convallis enim sed erat ornare lobortis. Sed vitae augue mi, id pellentesque sapien. Cras non ipsum enim, vitae tincidunt risus. Quisque et tortor diam, vel rutrum quam. Quisque tortor dui, gravida vel tincidunt ut, euismod non erat. Nulla eget sem at nulla sagittis accumsan et eleifend dui.</p>\r\n', NULL, NULL, 2, 9994, 0),
(17, 2, 'Wat doen wij', NULL, '2013-04-11 19:26:27', '2013-04-16 18:03:29', '<p>etst</p>\r\n', NULL, NULL, 2, 9997, 1),
(18, 2, ' Wie zijn wij', NULL, '2013-04-11 19:26:37', '2013-04-16 18:03:29', '<p>fd</p>\r\n', NULL, NULL, 2, 9995, 1),
(19, 2, 'Nieuwsbrief', NULL, '2013-04-11 19:26:43', '2013-04-16 18:03:29', '<p>test</p>\r\n', NULL, NULL, 2, 9993, 1),
(20, 2, 'Downloads', NULL, '2013-04-11 19:26:49', '2013-04-16 18:03:29', '<p>fdfd</p>\r\n', NULL, NULL, 2, 9996, 1);

-- --------------------------------------------------------

--
-- Table structure for table `nodetype`
--

CREATE TABLE IF NOT EXISTS `nodetype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `nodetype`
--

INSERT INTO `nodetype` (`id`, `name`, `category`) VALUES
(1, 'page', NULL),
(2, 'blog-item', NULL),
(3, 'product', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `node_media`
--

CREATE TABLE IF NOT EXISTS `node_media` (
  `node` int(10) unsigned NOT NULL COMMENT 'Post',
  `media` int(10) unsigned NOT NULL COMMENT 'Media',
  KEY `post` (`node`),
  KEY `media` (`media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `node_meta`
--

CREATE TABLE IF NOT EXISTS `node_meta` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID',
  `node` int(10) unsigned NOT NULL COMMENT 'Post',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `value` text NOT NULL COMMENT 'Value',
  PRIMARY KEY (`id`),
  KEY `meta` (`meta`),
  KEY `post` (`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `node_tag`
--

CREATE TABLE IF NOT EXISTS `node_tag` (
  `node` int(10) unsigned NOT NULL COMMENT 'Post',
  `tag` int(10) unsigned NOT NULL COMMENT 'Distributor',
  KEY `tag` (`tag`),
  KEY `post` (`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `node_tag`
--

INSERT INTO `node_tag` (`node`, `tag`) VALUES
(18, 53),
(20, 53),
(20, 54),
(20, 55);

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
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `sort` varchar(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `name`, `sort`) VALUES
(53, 'Audio', '999'),
(54, 'New one', '999'),
(55, 'some tag', '999'),
(56, 'another one', '999'),
(57, 'dogs', '999'),
(58, 'cats', '999'),
(59, 'photography', '999');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `usergroup`, `email`, `fullname`, `address`, `zipcode`, `city`, `region`, `country`, `secret`) VALUES
(2, 'admin', '$2a$10$0a58870584e946cff61c1ewcD/a4dbTvUH5ysrhqJweggm0hvLHda', 2, 'info@halfweeks.com', 'Administrator', NULL, NULL, NULL, NULL, NULL, 'f2e7095a84f4d8acb6050cbf96fc12ef10ca87a3'),
(3, 'hongaar', '$2a$10$6e355e1df64dcb06de052uupiBtlcPKNnyUqKYbdakRKd04akcqqO', 1, 'hongaar@gmail.com', 'rood', NULL, NULL, NULL, NULL, NULL, '49fe8fea753aa1f6a41681fc0d0f4835bd5b51dd'),
(4, 'test', '$2a$10$49ce3c9ec377625fa9f38uoAleiNP86slsoyMa23f5isZXzj0Kmxi', 1, 'rood@rood.nl', 'rood', NULL, NULL, NULL, NULL, NULL, '7cc0e5699254fdf0213f9405804c7e1c84d5966c');

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
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `meta`
--
ALTER TABLE `meta`
  ADD CONSTRAINT `meta_ibfk_2` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `node_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `node_ibfk_4` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `node_ibfk_5` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`);

--
-- Constraints for table `node_media`
--
ALTER TABLE `node_media`
  ADD CONSTRAINT `node_media_ibfk_2` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_media_ibfk_3` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node_meta`
--
ALTER TABLE `node_meta`
  ADD CONSTRAINT `node_meta_ibfk_4` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_meta_ibfk_5` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node_tag`
--
ALTER TABLE `node_tag`
  ADD CONSTRAINT `node_tag_ibfk_2` FOREIGN KEY (`tag`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_tag_ibfk_3` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
