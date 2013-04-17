-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2013 at 03:02 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=171 ;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `sort`, `name`, `type`, `pointer`, `thumbnail`, `icon`, `added`, `updated`, `user`) VALUES
(30, 9998, 'Promo', 'image', 'promo.png', 'promo.png', NULL, '2013-04-16 19:20:45', '2013-04-16 19:20:45', 2),
(31, 9997, 'SS1', 'image', 'screenshot-1.png', 'screenshot-1.png', NULL, '2013-04-16 19:20:54', '2013-04-16 19:20:54', 2),
(32, 9996, 'Header', 'image', 'header.png', 'header.png', NULL, '2013-04-16 19:21:02', '2013-04-16 19:21:02', 2),
(33, 9995, 'Icon', 'image', 'icon-high.png', 'icon-high.png', NULL, '2013-04-16 19:21:15', '2013-04-16 19:21:15', 2),
(34, 9994, 'SS3', 'image', 'screenshot-3.png', 'screenshot-3.png', NULL, '2013-04-16 19:21:25', '2013-04-16 19:21:25', 2),
(46, 998, '14556_418337524905116_1190215125_n', 'image', '14556_418337524905116_1190215125_n.jpg', '14556_418337524905116_1190215125_n.jpg', NULL, '2013-04-16 22:14:56', '2013-04-16 22:14:56', 2),
(47, 997, 'free_your_mind82', 'image', 'free_your_mind82.jpg', 'free_your_mind82.jpg', NULL, '2013-04-16 22:14:56', '2013-04-16 22:14:56', 2),
(48, 996, '9-and-a-half-weeks45', 'image', '9-and-a-half-weeks45.jpg', '9-and-a-half-weeks45.jpg', NULL, '2013-04-16 22:14:56', '2013-04-16 22:14:56', 2),
(49, 995, 'AGF 220', 'image', 'AGF 220.JPG', 'AGF 220.JPG', NULL, '2013-04-16 22:14:57', '2013-04-16 22:14:57', 2),
(50, 994, 'die-antwoord', 'image', 'die-antwoord.jpeg', 'die-antwoord.jpeg', NULL, '2013-04-16 22:14:57', '2013-04-16 22:14:57', 2),
(51, 993, 'diplo51', 'image', 'diplo51.jpg', 'diplo51.jpg', NULL, '2013-04-16 22:14:57', '2013-04-16 22:14:57', 2),
(52, 992, 'domtorenutrechtvuurwerk', 'image', 'domtorenutrechtvuurwerk.jpg', 'domtorenutrechtvuurwerk.jpg', NULL, '2013-04-16 22:14:58', '2013-04-16 22:14:58', 2),
(53, 991, 'fc039fdc140c2efef1e85f5d65cba8cf31', 'image', 'fc039fdc140c2efef1e85f5d65cba8cf31.jpeg', 'fc039fdc140c2efef1e85f5d65cba8cf31.jpeg', NULL, '2013-04-16 22:14:58', '2013-04-16 22:14:58', 2),
(59, 988, 'Schermafbeelding 2013-03-27 om 21.55.40', 'image', 'Schermafbeelding 2013-03-27 om 21.55.40.png', 'Schermafbeelding 2013-03-27 om 21.55.40.png', NULL, '2013-04-16 22:57:46', '2013-04-16 22:57:46', 2),
(60, 987, 'paul-duro-02', 'image', 'paul-duro-02.jpg', 'paul-duro-02.jpg', NULL, '2013-04-16 22:57:52', '2013-04-16 22:57:52', 2),
(61, 986, 'ouderenbash73', 'image', 'ouderenbash73.png', 'ouderenbash73.png', NULL, '2013-04-16 22:58:23', '2013-04-16 22:58:23', 2),
(62, 985, 'paul-duro-0268', 'image', 'paul-duro-0268.jpg', 'paul-duro-0268.jpg', NULL, '2013-04-16 22:58:26', '2013-04-16 22:58:26', 2),
(63, 984, 'Radiohead_Band', 'image', 'Radiohead_Band.jpg', 'Radiohead_Band.jpg', NULL, '2013-04-16 22:58:32', '2013-04-16 22:58:32', 2),
(64, 983, 'validate92', 'image', 'validate92.png', 'validate92.png', NULL, '2013-04-16 22:59:27', '2013-04-16 22:59:27', 2),
(65, 984, 'Beauty', 'embed', '<iframe src="http://player.vimeo.com/video/64077961?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1" width=''100%'' height=''471'' frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>', '434583494_640.jpg', NULL, '2013-04-16 23:01:53', '2013-04-17 00:37:24', 2),
(66, 981, 'radiohead-radiohead-22916726-2480-108727', 'image', 'radiohead-radiohead-22916726-2480-108727.jpg', 'radiohead-radiohead-22916726-2480-108727.jpg', NULL, '2013-04-16 23:02:21', '2013-04-16 23:02:21', 2),
(67, 980, 'Schermafbeelding 2013-03-27 om 21.55.4078', 'image', 'Schermafbeelding 2013-03-27 om 21.55.4078.png', 'Schermafbeelding 2013-03-27 om 21.55.4078.png', NULL, '2013-04-16 23:02:21', '2013-04-16 23:02:21', 2),
(68, 979, 'tickets1 (1)79', 'image', 'tickets1 (1)79.jpg', 'tickets1 (1)79.jpg', NULL, '2013-04-16 23:02:22', '2013-04-16 23:02:22', 2),
(69, 978, 'validate76', 'image', 'validate76.png', 'validate76.png', NULL, '2013-04-16 23:02:22', '2013-04-16 23:02:22', 2),
(70, 977, 'Zuivel', 'image', 'Zuivel.jpg', 'Zuivel.jpg', NULL, '2013-04-16 23:02:22', '2013-04-16 23:02:22', 2),
(73, 974, '9980', 'image', '9980.JPG', '9980.JPG', NULL, '2013-04-16 23:02:25', '2013-04-16 23:02:25', 2),
(74, 973, 'a463957db6e7471ff875c4beb5b64f7a', 'image', 'a463957db6e7471ff875c4beb5b64f7a.png', 'a463957db6e7471ff875c4beb5b64f7a.png', NULL, '2013-04-16 23:02:26', '2013-04-16 23:02:26', 2),
(75, 972, 'AGF 289', 'image', 'AGF 289.JPG', 'AGF 289.JPG', NULL, '2013-04-16 23:02:26', '2013-04-16 23:02:26', 2),
(77, 970, 'Annemarie van Alphen38', 'image', 'Annemarie van Alphen38.jpg', 'Annemarie van Alphen38.jpg', NULL, '2013-04-16 23:02:27', '2013-04-16 23:02:27', 2),
(78, 969, 'apps (1)', 'image', 'apps (1).png', 'apps (1).png', NULL, '2013-04-16 23:02:28', '2013-04-16 23:02:28', 2),
(80, 967, 'Bart_Skils_02', 'image', 'Bart_Skils_02.jpg', 'Bart_Skils_02.jpg', NULL, '2013-04-16 23:02:29', '2013-04-16 23:02:29', 2),
(81, 966, 'css', 'image', 'css.png', 'css.png', NULL, '2013-04-16 23:02:30', '2013-04-16 23:02:30', 2),
(82, 965, 'die-antwoord64', 'image', 'die-antwoord64.jpeg', 'die-antwoord64.jpeg', NULL, '2013-04-16 23:02:30', '2013-04-16 23:02:30', 2),
(83, 964, 'diplo99', 'image', 'diplo99.jpg', 'diplo99.jpg', NULL, '2013-04-16 23:02:31', '2013-04-16 23:02:31', 2),
(84, 963, 'domtorenutrechtvuurwerk2', 'image', 'domtorenutrechtvuurwerk2.jpg', 'domtorenutrechtvuurwerk2.jpg', NULL, '2013-04-16 23:02:31', '2013-04-16 23:02:31', 2),
(85, 962, 'domtorenutrechtvuurwerk47', 'image', 'domtorenutrechtvuurwerk47.jpg', 'domtorenutrechtvuurwerk47.jpg', NULL, '2013-04-16 23:02:31', '2013-04-16 23:02:31', 2),
(86, 961, 'done', 'image', 'done.JPG', 'done.JPG', NULL, '2013-04-16 23:02:32', '2013-04-16 23:02:32', 2),
(88, 959, 'fc039fdc140c2efef1e85f5d65cba8cf40', 'image', 'fc039fdc140c2efef1e85f5d65cba8cf40.jpeg', 'fc039fdc140c2efef1e85f5d65cba8cf40.jpeg', NULL, '2013-04-16 23:02:32', '2013-04-16 23:02:32', 2),
(89, 958, 'free_your_mind23', 'image', 'free_your_mind23.jpg', 'free_your_mind23.jpg', NULL, '2013-04-16 23:02:33', '2013-04-16 23:02:33', 2),
(90, 957, 'google-map-LKFH-201226e4de98', 'image', 'google-map-LKFH-201226e4de98.jpg', 'google-map-LKFH-201226e4de98.jpg', NULL, '2013-04-16 23:02:33', '2013-04-16 23:02:33', 2),
(95, 952, 'halfweeks-square', 'image', 'halfweeks-square.png', 'halfweeks-square.png', NULL, '2013-04-16 23:02:35', '2013-04-16 23:02:35', 2),
(96, 951, 'icon', 'image', 'icon.png', 'icon.png', NULL, '2013-04-16 23:02:35', '2013-04-16 23:02:35', 2),
(97, 950, 'IMG_220639', 'image', 'IMG_220639.JPG', 'IMG_220639.JPG', NULL, '2013-04-16 23:02:36', '2013-04-16 23:02:36', 2),
(98, 949, 'MiniVelo24', 'image', 'MiniVelo24.jpg', 'MiniVelo24.jpg', NULL, '2013-04-16 23:02:36', '2013-04-16 23:02:36', 2),
(99, 948, 'IMG_221012', 'image', 'IMG_221012.JPG', 'IMG_221012.JPG', NULL, '2013-04-16 23:02:36', '2013-04-16 23:02:36', 2),
(100, 947, 'IMG_221246', 'image', 'IMG_221246.JPG', 'IMG_221246.JPG', NULL, '2013-04-16 23:02:37', '2013-04-16 23:02:37', 2),
(101, 946, 'IMG_221475', 'image', 'IMG_221475.JPG', 'IMG_221475.JPG', NULL, '2013-04-16 23:02:37', '2013-04-16 23:02:37', 2),
(103, 944, 'LL07_plattegrond', 'image', 'LL07_plattegrond.jpg', 'LL07_plattegrond.jpg', NULL, '2013-04-16 23:02:38', '2013-04-16 23:02:38', 2),
(104, 943, 'N-264x30047', 'image', 'N-264x30047.png', 'N-264x30047.png', NULL, '2013-04-16 23:02:39', '2013-04-16 23:02:39', 2),
(106, 941, 'paul-duro-0247', 'image', 'paul-duro-0247.jpg', 'paul-duro-0247.jpg', NULL, '2013-04-16 23:02:40', '2013-04-16 23:02:40', 2),
(107, 940, 'Radiohead_Band25', 'image', 'Radiohead_Band25.jpg', 'Radiohead_Band25.jpg', NULL, '2013-04-16 23:02:40', '2013-04-16 23:02:40', 2),
(134, 913, 'Folder', 'image', 'Folder.jpg', 'Folder.jpg', NULL, '2013-04-16 23:05:08', '2013-04-16 23:05:08', 2);

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nodetype` int(10) unsigned NOT NULL COMMENT 'Node type',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `type` enum('Text','Numeric','List of options','Node link','Media','Date','Time','Spatial','Yes/No') NOT NULL COMMENT 'Type',
  `options` text COMMENT 'Options',
  PRIMARY KEY (`id`),
  KEY `posttype` (`nodetype`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`id`, `nodetype`, `name`, `type`, `options`) VALUES
(1, 5, 'Date', 'Text', '<p>fdfd</p>\r\n');

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
(16, 2, 'Welkom bij Vakadi', NULL, '2013-04-11 19:21:29', '2013-04-17 00:14:01', '<h2>Wist u dat...</h2>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit libero massa, ac semper elit. Integer convallis enim sed erat ornare lobortis. Sed vitae augue mi, id pellentesque sapien. Cras non ipsum enim, vitae tincidunt risus. Quisque et tortor diam, vel rutrum quam. Quisque tortor dui, gravida vel tincidunt ut, euismod non erat. Nulla eget sem at nulla sagittis accumsan et eleifend dui.</p>\r\n', NULL, NULL, 2, 9996, 0),
(17, 2, 'Wat doen wij', NULL, '2013-04-11 19:26:27', '2013-04-16 18:03:29', '<p>etst</p>\r\n', NULL, NULL, 2, 9997, 1),
(18, 2, ' Wie zijn wij', NULL, '2013-04-11 19:26:37', '2013-04-17 00:14:01', '<p>fd</p>\r\n', NULL, NULL, 2, 9994, 1),
(19, 2, 'Nieuwsbrief', NULL, '2013-04-11 19:26:43', '2013-04-16 18:03:29', '<p>test</p>\r\n', NULL, NULL, 2, 9993, 1),
(20, 2, 'Downloads', NULL, '2013-04-11 19:26:49', '2013-04-17 00:14:01', '<p>fdfd</p>\r\n', NULL, NULL, 2, 9995, 1);

-- --------------------------------------------------------

--
-- Table structure for table `nodetype`
--

CREATE TABLE IF NOT EXISTS `nodetype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `category` varchar(255) DEFAULT NULL COMMENT 'Category',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `nodetype`
--

INSERT INTO `nodetype` (`id`, `name`, `category`) VALUES
(2, 'Blog item', NULL),
(3, 'Product', NULL),
(5, 'Page', NULL);

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

--
-- Dumping data for table `node_media`
--

INSERT INTO `node_media` (`node`, `media`) VALUES
(20, 84);

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
