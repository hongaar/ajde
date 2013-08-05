-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 05, 2013 at 12:35 PM
-- Server version: 5.5.31
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `vakadi`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `type`, `parent`, `level`, `node`, `sort`) VALUES
(8, 'Hoofdmenu', 'Submenu', NULL, 0, 103, 1),
(9, 'Activiteiten', 'Submenu', NULL, 0, 103, 6),
(10, 'Footer', 'Submenu', NULL, 0, NULL, 13),
(11, 'Wat doen wij', 'Node link', 8, 1, 95, 2),
(12, 'Wie zijn wij', 'Node link', 8, 1, 98, 3),
(13, 'Nieuwsbrief', 'Node link', 8, 1, 99, 4),
(14, 'Downloads', 'Node link', 8, 1, 100, 5),
(15, 'Bedrijfsadvies', 'Node link', 9, 1, 101, 7),
(16, 'Fiscaal', 'Node link', 9, 1, 102, 8),
(17, 'Administratie', 'Node link', 9, 1, 103, 9),
(18, 'Salaris', 'Node link', 9, 1, 104, 10),
(19, 'Assurantiën', 'Node link', 9, 1, 105, 11),
(20, 'Arbeidsrecht', 'Node link', 9, 1, 106, 12);

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `target` enum('node','setting') NOT NULL DEFAULT 'node' COMMENT 'Targeting',
  `type` enum('Text','Numeric','List of options','Node link','Media','Date','Time','Spatial','Toggle') NOT NULL COMMENT 'Type',
  `options` text COMMENT 'Options',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`id`, `name`, `target`, `type`, `options`) VALUES
(1, 'Twitter username', 'setting', 'Text', '{"required":"0","length":"50","wysiwyg":""}'),
(12, 'Keywords', 'node', 'Text', '{"required":"1","help":"Extra keywords op afbeelding, 1 per regel","list":"","popup":"","length":"0","media":"","wysiwyg":"0"}'),
(13, 'Call to action', 'node', 'Node link', '{"required":"","help":"Link naar node met knop","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(14, 'Quote', 'node', 'Text', '{"required":"","help":"","list":"","popup":"","length":"0","media":"","wysiwyg":"0"}'),
(15, 'Twitter widget ID', 'setting', 'Text', '{"required":"","help":"Visit https:\\/\\/twitter.com\\/settings\\/widgets to create a widget","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(16, 'LinkedIn username', 'setting', 'Text', '{"required":"","help":"","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(17, 'Categorie', 'node', 'List of options', '{"required":"1","help":"","list":"Formulier\\r\\nRekenmodule","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}'),
(18, 'File', 'node', 'Media', '{"required":"1","help":"","list":"","usenodetype":"","popup":"","length":"255","media":"","wysiwyg":""}');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=110 ;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `nodetype`, `title`, `slug`, `subtitle`, `added`, `updated`, `content`, `summary`, `media`, `parent`, `level`, `user`, `sort`, `published`) VALUES
(94, 18, 'Welkom bij Vakadi', 'welkom-bij-vakadi', 'Vakadi neemt u werk uit handen!', '2013-05-08 21:40:46', '2013-05-09 00:40:59', '<h3>Intro</h3>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit libero massa, ac semper elit. Integer convallis enim sed erat ornare lobortis. Sed vitae augue mi, id pellentesque sapien. Cras non ipsum enim, vitae tincidunt risus. Quisque et tortor diam, vel rutrum quam. Quisque tortor dui, gravida vel tincidunt ut, euismod non erat. Nulla eget sem at nulla sagittis accumsan et eleifend dui.</p>\r\n', NULL, 283, NULL, 0, 20, 1, 1),
(95, 17, 'Wat doen wij', 'sample-page', NULL, '2013-05-08 22:52:21', '2013-05-09 00:57:32', '<h3>Vertel wat meer over wat Vakadi doet met bedrijfsadvies</h3>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit libero massa, ac semper elit. Integer convallis enim sed erat ornare lobortis. Sed vitae augue mi, id pellentesque sapien. Cras non ipsum enim, vitae tincidunt risus. Quisque et tortor diam, vel rutrum quam. Quisque tortor dui, gravida vel tincidunt ut, euismod non erat. Nulla eget sem at nulla sagittis accumsan et eleifend dui.</p>\r\n\r\n<p>In vestibulum, lorem non mattis accumsan, metus orci tristique augue, sit amet convallis elit dolor nec dolor. Fusce tristique felis et tortor adipiscing sed faucibus eros hendrerit. Donec non diam non enim condimentum imperdiet. Nam hendrerit magna sit amet augue iaculis at rhoncus felis posuere. Suspendisse at tortor dui, aliquet dapibus dui. Integer at odio eros. Integer dapibus massa lacus. Nam at lectus eu lacus pharetra euismod. Nunc ut lorem purus, nec volutpat nulla. Suspendisse nisi justo, gravida nec convallis molestie, tincidunt id tortor. Praesent egestas arcu eget tortor cursus eget fermentum nisi lobortis. Cras iaculis, dui eu interdum iaculis, mauris massa feugiat lectus, nec facilisis ipsum leo ut enim. Morbi eleifend ullamcorper urna, nec dignissim libero hendrerit nec. Donec porta neque non metus tempus cursus. Ut porta, justo sit amet suscipit facilisis, neque massa sollicitudin neque, eu bibendum lorem quam feugiat nulla. Fusce egestas erat a est gravida et tristique est tempus.</p>\r\n\r\n<h3>Direct een offerte aanvragen</h3>\r\n\r\n<p>Wij helpen uw graag bij het adviseren van uw bedrijf. Maak gebruik van de onderstaande knop om meteen een offerte aan te vragen. Het kan niet makkelijker. Waar wacht je nog op?</p>\r\n', NULL, 282, NULL, 0, 20, 2, 1),
(96, 19, 'Horizontaal toezicht', 'horizontaal-toezicht', NULL, '2013-05-09 00:42:34', '2013-05-09 00:43:06', '<p>In onze laatste nieuwsbrief hebben we al aangekondigd dat wij willen onderzoeken of we aan Horizontaal Toezicht kunnen gaan deelnemen ten behoeve van onze cli&euml;nten.</p>\r\n', 'In onze laatste nieuwsbrief hebben we al aangekondigd dat wij willen onderzoeken of we aan Horizontaal Toezicht kunnen gaan deelnemen ten behoeve van onze cliënten.', NULL, NULL, 0, 20, 3, 1),
(97, 19, 'BTW omhoog naar 21%', 'btw-omhoog-naar-21', NULL, '2013-05-09 00:43:27', '2013-08-05 10:07:01', '<p>Vanaf 1 oktober wordt het hoge tarief voor de omzetbelasting opgehoogd met 2%-punt tot 21%.&nbsp;Vanaf 1 oktober wordt het hoge tarief voor de omzetbelasting opgehoogd met 2%-punt tot 21%.Vanaf 1 oktober wordt het hoge tarief voor de omzetbelasting opgehoogd met 2%-punt tot 21%.Vanaf 1 oktober wordt het hoge tarief voor de omzetbelasting opgehoogd met 2%-punt tot 21%.Vanaf 1 oktober wordt het hoge tarief voor de omzetbelasting opgehoogd met 2%-punt tot 21%.Vanaf 1 oktober wordt het hoge tarief voor de omzetbelasting opgehoogd met 2%-punt tot 21%.</p>\r\n', 'Vanaf 1 oktober wordt het hoge tarief voor de omzetbelasting opgehoogd met 2%-punt tot 21%.', NULL, NULL, 0, 20, 4, 1),
(98, 17, 'Wie zijn wij', 'wie-zijn-wij', NULL, '2013-05-09 00:58:22', '2013-05-09 00:58:23', NULL, NULL, NULL, NULL, 0, 20, 5, 1),
(99, 17, 'Nieuwsbrief', 'nieuwsbrief', NULL, '2013-05-09 00:58:33', '2013-05-09 00:58:33', NULL, NULL, NULL, NULL, 0, 20, 6, 1),
(100, 22, 'Downloads', 'downloads', NULL, '2013-05-09 00:58:43', '2013-05-09 12:41:55', '<p>Dit is de downloads page</p>\r\n', NULL, 288, NULL, 0, 20, 7, 1),
(101, 20, 'Bedrijfsadvies', 'bedrijfsadvies', NULL, '2013-05-09 00:58:55', '2013-05-09 12:35:28', NULL, NULL, NULL, NULL, 0, 20, 11, 1),
(102, 20, 'Fiscaal', 'fiscaal', NULL, '2013-05-09 00:59:03', '2013-05-09 12:35:28', NULL, NULL, NULL, NULL, 0, 20, 12, 1),
(103, 20, 'Administratie', 'administratie', NULL, '2013-05-09 00:59:12', '2013-05-09 12:35:28', NULL, NULL, NULL, NULL, 0, 20, 13, 1),
(104, 20, 'Salaris', 'salaris', NULL, '2013-05-09 01:00:45', '2013-05-09 12:35:28', NULL, NULL, NULL, NULL, 0, 20, 14, 1),
(105, 20, 'Assurantiën', 'assurantin', NULL, '2013-05-09 01:01:07', '2013-05-09 12:35:28', NULL, NULL, NULL, NULL, 0, 20, 15, 1),
(106, 20, 'Arbeidsrecht', 'arbeidsrecht', NULL, '2013-05-09 01:02:32', '2013-05-09 12:35:28', NULL, NULL, NULL, NULL, 0, 20, 16, 1),
(107, 21, 'Test download', 'test-download', NULL, '2013-05-09 02:14:08', '2013-05-09 12:31:30', NULL, 'Dit is een afbeelding', NULL, 100, 1, 20, 8, 1),
(108, 21, 'Deploygate', 'deploygate', NULL, '2013-05-09 02:20:53', '2013-05-09 12:35:28', NULL, NULL, NULL, 100, 1, 20, 9, 1),
(109, 21, 'Rekenmodule Cubiq', 'rekenmodule-cubiq', NULL, '2013-05-09 02:36:44', '2013-05-09 12:35:28', NULL, 'Dit is nog een zip bestand om te downloaden', NULL, 100, 1, 20, 10, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `nodetype`
--

INSERT INTO `nodetype` (`id`, `name`, `category`, `sort`, `title`, `subtitle`, `content`, `summary`, `media`, `tag`, `additional_media`, `children`, `published`, `related_nodes`) VALUES
(17, 'Tekst', 'pages', 1000, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, 1, 1),
(18, 'Homepage', 'pages', 999, 1, 1, 1, NULL, 1, NULL, NULL, NULL, 1, 1),
(19, 'Nieuws', 'item', 1003, 1, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, 1),
(20, 'Activiteit', 'pages', 1001, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, 1, 1),
(21, 'Bestand', 'item', 1004, 1, NULL, NULL, 1, NULL, NULL, NULL, 1, 1, NULL),
(22, 'Downloads', 'pages', 1002, 1, NULL, 1, NULL, 1, NULL, NULL, 1, NULL, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `nodetype_meta`
--

INSERT INTO `nodetype_meta` (`id`, `nodetype`, `meta`, `sort`) VALUES
(1, 18, 12, 1),
(2, 18, 13, 2),
(3, 17, 14, 3),
(4, 17, 13, 4),
(5, 20, 13, 6),
(6, 20, 14, 5),
(7, 21, 17, 7),
(8, 21, 18, 8);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=367 ;

--
-- Dumping data for table `node_meta`
--

INSERT INTO `node_meta` (`id`, `node`, `meta`, `value`) VALUES
(338, 95, 13, '95'),
(339, 95, 14, 'Vakadi speelt professioneel in op uw werkwijze. Samen met u wordt bepaald van welke diensten u wel of niet gebruik maakt.'),
(357, 109, 17, 'Rekenmodule'),
(358, 109, 18, '287'),
(359, 108, 17, 'Rekenmodule'),
(360, 108, 18, '284'),
(363, 107, 17, 'Formulier'),
(364, 107, 18, '282'),
(365, 94, 12, 'Test met CMS\r\nDienstverlening voor het midden- en kleinbedrijf\r\nSalarisadministratie\r\nJuridisch advies bij geschillen\r\nCompleet pakket voor MKB'),
(366, 94, 13, '95');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `node_related`
--

INSERT INTO `node_related` (`id`, `node`, `related`, `sort`) VALUES
(5, 95, 95, 2),
(6, 95, 94, 3),
(14, 101, 106, 4),
(15, 101, 96, 5),
(16, 101, 98, 6),
(17, 96, 106, 7),
(18, 96, 102, 8),
(19, 100, 106, 9),
(20, 100, 99, 10),
(21, 100, 107, 11),
(22, 94, 101, 12),
(23, 94, 95, 13),
(26, 97, 105, 14),
(27, 97, 108, 15);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `setting_meta`
--

INSERT INTO `setting_meta` (`id`, `setting`, `meta`, `sort`, `value`) VALUES
(39, 2, 16, 999, 'joramvandenboezem'),
(40, 2, 1, 999, 'hongaar'),
(41, 2, 15, 999, '319509549642022912');

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
(20, 'admin', '$2a$10$17bceef4aa8a6ecd87138eE..IXvFqGE7PXF/G6JOSx2LePqYb7pu', 2, 'W1nGW/VSmUUx1L/28ZP/JYctopgd3strPJrcenfV5H0SpwE=', 'q8kP3mJXx1DbK2h9/IXSlAyWuwtebFg4Jeanad7FuheWGQA=', NULL, NULL, NULL, NULL, NULL, '6402649ab41f623fa9d635f4cf261bf3d6004a7a'),
(21, 'vakadi', '$2a$10$913dd67cc2a9cc1c1046aeK7mKOlzOpjXXHjB0QGg9ILCXVZ8de9m', 3, NULL, '+9ziaFwj3W/66/Xu88KpyZ7R/CF9pdUbZ97bH7en9abNdgA=', NULL, NULL, NULL, NULL, NULL, '5fc730b6d4ace22bbf2dd5472ad1256121968353');

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
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_ibfk_2` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `node_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `node_ibfk_4` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `node_ibfk_5` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`),
  ADD CONSTRAINT `node_ibfk_7` FOREIGN KEY (`parent`) REFERENCES `node` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `nodetype_meta`
--
ALTER TABLE `nodetype_meta`
  ADD CONSTRAINT `nodetype_meta_ibfk_1` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nodetype_meta_ibfk_2` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `node_related`
--
ALTER TABLE `node_related`
  ADD CONSTRAINT `node_related_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_related_ibfk_2` FOREIGN KEY (`related`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `setting_meta`
--
ALTER TABLE `setting_meta`
  ADD CONSTRAINT `setting_meta_ibfk_2` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `setting_meta_ibfk_3` FOREIGN KEY (`setting`) REFERENCES `setting` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
