-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2013 at 11:04 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `belay`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `acl`
--

INSERT INTO `acl` (`id`, `type`, `user`, `usergroup`, `module`, `action`, `extra`, `permission`) VALUES
(22, 'usergroup', NULL, NULL, 'shop', 'checkout', '*', 'allow'),
(23, 'usergroup', NULL, NULL, 'shop', '*', 'transaction', 'allow'),
(26, 'usergroup', NULL, 2, '*', '*', '*', 'allow'),
(27, 'usergroup', NULL, 3, 'admin', '*', 'cms', 'allow'),
(28, 'usergroup', NULL, 3, 'admin', '*', 'node', 'allow'),
(29, 'usergroup', NULL, 3, 'admin', '*', 'media', 'allow'),
(30, 'usergroup', NULL, 3, 'admin', 'view', '', 'allow'),
(31, 'usergroup', NULL, 3, '_core', '*', '*', 'allow'),
(32, 'usergroup', NULL, 3, 'admin', '*', 'tag', 'allow'),
(33, 'usergroup', NULL, 3, 'admin', '*', 'menu', 'allow');

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

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user`, `client`) VALUES
(11, 20, '');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=289 ;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `sort`, `name`, `type`, `pointer`, `thumbnail`, `icon`, `added`, `updated`, `user`) VALUES
(282, 988, 'home', 'image', 'home.png', 'home.png', NULL, '2013-05-08 23:03:09', '2013-05-08 23:03:09', 20),
(283, 987, 'finance', 'image', 'finance.png', 'finance.png', NULL, '2013-05-08 23:03:09', '2013-05-08 23:03:09', 20),
(284, 986, 'deploygatesdk-r2', 'file', 'deploygatesdk-r2.zip', 'deploygatesdk-r2.zip', NULL, '2013-05-09 02:19:49', '2013-05-09 02:19:49', 20),
(285, 985, 'onchange_1.7', 'file', 'onchange_1.7.zip', 'onchange_1.7.zip', NULL, '2013-05-09 02:27:54', '2013-05-09 02:27:54', 20),
(286, 984, 'php-library-master', 'file', 'php-library-master.zip', 'php-library-master.zip', NULL, '2013-05-09 02:27:58', '2013-05-09 02:27:58', 20),
(287, 983, 'cubiq-iscroll-d1e642c', 'file', 'cubiq-iscroll-d1e642c.zip', 'cubiq-iscroll-d1e642c.zip', NULL, '2013-05-09 02:36:13', '2013-05-09 02:36:13', 20),
(288, 982, 'tickets1 (1)', 'image', 'tickets1 (1).jpg', 'tickets1 (1).jpg', NULL, '2013-05-09 12:41:49', '2013-05-09 12:41:49', 20);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `type` enum('Node link','Submenu') NOT NULL DEFAULT 'Node link' COMMENT 'Type',
  `parent` int(10) unsigned DEFAULT NULL COMMENT 'Parent menu',
  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Recursion level',
  `node` int(10) unsigned DEFAULT NULL COMMENT 'Node link',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `node` (`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`id`, `name`, `target`, `type`, `options`) VALUES
(20, 'Streak status', 'node', 'List of options', '{"required":"1","help":"","default":"","list":"In consideration\\r\\nApproved\\r\\nBilled","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(21, 'Time allocated', 'node', 'Timespan', '{"required":"0","help":"","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(22, 'Time spent', 'node', 'Timespan', '{"required":"","help":"","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(23, 'Issue status', 'node', 'List of options', '{"required":"1","help":"","default":"New","list":"New\\r\\nActive\\r\\nWaiting for approval\\r\\nClosed","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(25, 'Manager', 'node', 'User', '{"required":"1","help":"","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(26, 'Tender number', 'node', 'Text', '{"required":"","help":"","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(27, 'Invoice number', 'node', 'Text', '{"required":"","help":"","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(28, 'Issue due', 'node', 'Date', '{"required":"1","help":"","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(29, 'Billing type', 'node', 'List of options', '{"required":"1","help":"","default":"","list":"Fixed price\\r\\nPer hour billing","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(30, 'Discount', 'node', 'Numeric', '{"required":"","help":"In percentage","default":"0","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}');

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
  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Recursion level',
  `user` int(10) unsigned NOT NULL COMMENT 'Owner',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `published` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Published',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `media` (`media`),
  KEY `posttype` (`nodetype`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=129 ;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `nodetype`, `title`, `slug`, `subtitle`, `added`, `updated`, `content`, `summary`, `media`, `parent`, `level`, `user`, `sort`, `published`) VALUES
(110, 24, 'Vakadi website', 'vakadi', NULL, '2013-05-10 14:22:20', '2013-05-12 00:24:51', NULL, NULL, NULL, 114, 1, 20, 2, 1),
(111, 26, 'New website', 'new-website', NULL, '2013-05-10 14:23:23', '2013-05-11 23:33:11', NULL, NULL, NULL, 110, 2, 20, 3, 1),
(112, 25, 'Open issue', 'design', NULL, '2013-05-10 14:24:19', '2013-05-10 17:18:20', NULL, NULL, NULL, 111, 3, 20, 4, 1),
(113, 25, 'Closed issue', 'design1', NULL, '2013-05-10 14:26:10', '2013-05-10 17:18:05', NULL, NULL, NULL, 111, 3, 20, 5, 1),
(114, 23, 'Vakadi', 'vakadi1', NULL, '2013-05-10 14:29:14', '2013-05-10 16:09:43', NULL, NULL, NULL, NULL, 0, 20, 1, 1),
(115, 24, 'New project', 'new-project', NULL, '2013-05-11 23:25:12', '2013-05-13 22:34:27', NULL, NULL, NULL, 114, 1, 20, 10, 1),
(116, 23, 'Impakt', 'impakt', NULL, '2013-05-11 23:46:45', '2013-05-13 23:02:39', NULL, NULL, NULL, NULL, 0, 20, 16, 1),
(117, 23, 'Raymond Taudin Chabot', 'raymond-taudin-chabot', NULL, '2013-05-11 23:47:09', '2013-05-14 01:29:57', NULL, NULL, NULL, NULL, 0, 20, 18, 1),
(118, 25, 'New project issue', 'new-project-issue', NULL, '2013-05-12 00:59:15', '2013-05-13 23:25:28', NULL, NULL, NULL, 115, 2, 20, 11, 1),
(119, 24, 'Leaf project', 'leaf-project', NULL, '2013-05-12 01:05:13', '2013-05-13 23:24:53', NULL, NULL, NULL, 114, 1, 20, 14, 1),
(120, 26, 'Leaf streak', 'leaf-streak', NULL, '2013-05-12 01:05:50', '2013-05-13 23:25:28', NULL, NULL, NULL, 115, 2, 20, 12, 1),
(121, 25, 'Accepted issue', 'accepted-issue', NULL, '2013-05-12 16:29:56', '2013-05-13 23:25:28', NULL, NULL, NULL, 111, 3, 20, 6, 1),
(122, 25, 'Won''t fix issue', 'wont-fix-issue', NULL, '2013-05-12 16:31:04', '2013-05-13 23:25:28', NULL, NULL, NULL, 110, 2, 20, 9, 1),
(123, 24, 'New vakadi subproject', 'new-vakadi-subproject', NULL, '2013-05-13 19:19:34', '2013-05-13 23:24:53', '<p>test</p>\r\n', NULL, NULL, 114, 1, 20, 15, 1),
(125, 27, 'New project', 'new-project1', NULL, '2013-05-13 20:16:58', '2013-05-13 23:25:28', NULL, NULL, NULL, 111, 3, 20, 7, 1),
(126, 25, 'Work on client logon', 'work-on-client-logon', NULL, '2013-05-13 22:34:27', '2013-05-13 23:25:28', NULL, NULL, NULL, 111, 3, 20, 8, 1),
(127, 26, 'Billed streak', 'billed-streak', NULL, '2013-05-13 23:02:39', '2013-05-13 23:25:28', NULL, NULL, NULL, 114, 1, 20, 13, 1),
(128, 24, 'Impakt Online', 'impakt-online', NULL, '2013-05-14 01:29:57', '2013-05-14 01:29:57', NULL, NULL, NULL, 116, 1, 20, 17, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `nodetype`
--

INSERT INTO `nodetype` (`id`, `name`, `category`, `sort`, `title`, `subtitle`, `content`, `summary`, `media`, `tag`, `additional_media`, `children`, `published`, `related_nodes`) VALUES
(23, 'Client', 'crm', 999, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL),
(24, 'Project', 'projects', 1002, 1, NULL, 1, NULL, 1, NULL, NULL, 1, NULL, NULL),
(25, 'Issue', 'projects', 1004, 1, NULL, 1, NULL, NULL, NULL, 1, 1, NULL, NULL),
(26, 'Streak', 'projects', 1003, 1, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL),
(27, 'Note', 'projects', 1005, 1, NULL, 1, NULL, NULL, NULL, 1, 1, NULL, NULL),
(28, 'Work', 'timers', 1000, 1, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL),
(29, 'Consultation', 'timers', 1001, 1, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `nodetype_meta`
--

INSERT INTO `nodetype_meta` (`id`, `nodetype`, `meta`, `sort`) VALUES
(10, 26, 20, 3),
(11, 26, 21, 4),
(12, 26, 22, 5),
(13, 25, 21, 7),
(14, 25, 22, 6),
(15, 25, 23, 1),
(16, 23, 25, 7),
(17, 26, 26, 6),
(18, 26, 27, 9),
(19, 25, 28, 2),
(20, 26, 29, 7),
(21, 26, 30, 8),
(22, 28, 22, 10),
(23, 29, 22, 11);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=747 ;

--
-- Dumping data for table `node_meta`
--

INSERT INTO `node_meta` (`id`, `node`, `meta`, `value`) VALUES
(409, 116, 25, '20'),
(410, 117, 25, '20'),
(430, 121, 23, 'Active'),
(431, 121, 28, '2013-05-17'),
(432, 118, 23, 'New'),
(433, 118, 28, '2013-05-14'),
(497, 122, 23, 'Closed'),
(499, 122, 28, '2013-05-05'),
(524, 111, 20, 'Approved'),
(527, 111, 29, 'Fixed price'),
(589, 112, 23, 'New'),
(591, 112, 28, '2013-05-15'),
(593, 126, 23, 'New'),
(594, 126, 28, '2013-05-30'),
(595, 120, 20, 'In consideration'),
(598, 120, 29, 'Fixed price'),
(599, 127, 23, 'New'),
(600, 127, 20, 'Billed'),
(601, 127, 29, 'Per hour billing'),
(603, 113, 23, 'Closed'),
(605, 113, 28, '2013-05-13'),
(716, 114, 21, '30d'),
(717, 114, 25, '21'),
(718, 110, 21, '30d'),
(719, 110, 25, '21'),
(720, 111, 21, '30d'),
(721, 111, 25, '21'),
(722, 112, 21, '30d'),
(723, 112, 25, '21'),
(724, 113, 21, '30d'),
(725, 113, 25, '21'),
(726, 121, 21, '30d'),
(727, 121, 25, '21'),
(728, 125, 21, '30d'),
(729, 125, 25, '21'),
(730, 126, 21, '30d'),
(731, 126, 25, '21'),
(732, 122, 21, '30d'),
(733, 122, 25, '21'),
(734, 115, 21, '30d'),
(735, 115, 25, '21'),
(736, 118, 21, '30d'),
(737, 118, 25, '21'),
(738, 120, 21, '30d'),
(739, 120, 25, '21'),
(740, 127, 21, '30d'),
(741, 127, 25, '21'),
(742, 119, 21, '30d'),
(743, 119, 25, '21'),
(744, 123, 21, '30d'),
(745, 123, 25, '21'),
(746, 128, 23, 'New');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `name`, `sort`) VALUES
(1, 'Main settings', 999),
(2, 'Social settings', 1000);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `name`, `sort`) VALUES
(74, 'joi', '999'),
(75, 'new tag', '999');

-- --------------------------------------------------------

--
-- Table structure for table `timer`
--

CREATE TABLE IF NOT EXISTS `timer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `elapsed` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('active','paused') NOT NULL DEFAULT 'active',
  `node` int(10) unsigned NOT NULL,
  `user` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node` (`node`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `timer`
--

INSERT INTO `timer` (`id`, `updated`, `elapsed`, `status`, `node`, `user`) VALUES
(18, '2013-05-14 01:33:56', 153, 'active', 112, 20);

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
  `fullname` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `usergroup`, `email`, `fullname`, `address`, `zipcode`, `city`, `region`, `country`, `secret`) VALUES
(20, 'admin', '', 2, '', '', NULL, NULL, NULL, NULL, NULL, '');

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
(3, 'clients');

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
  ADD CONSTRAINT `node_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `node_ibfk_4` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `node_ibfk_5` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`),
  ADD CONSTRAINT `node_ibfk_7` FOREIGN KEY (`parent`) REFERENCES `node` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `node_ibfk_8` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
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
-- Constraints for table `timer`
--
ALTER TABLE `timer`
  ADD CONSTRAINT `timer_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `timer_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
