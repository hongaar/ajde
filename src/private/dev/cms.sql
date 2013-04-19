-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2013 at 12:26 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

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
(31, 'usergroup', NULL, 3, '_core', '*', '*', 'allow');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user`, `client`) VALUES
(8, 2, ''),
(9, 18, '');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=137 ;

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
(88, 959, 'Joram van den Boezem', 'image', 'fc039fdc140c2efef1e85f5d65cba8cf40.jpeg', 'fc039fdc140c2efef1e85f5d65cba8cf40.jpeg', NULL, '2013-04-16 23:02:32', '2013-04-18 02:39:49', 2),
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
(106, 941, 'paul duro', 'image', 'paul-duro-0247.jpg', 'paul-duro-0247.jpg', NULL, '2013-04-16 23:02:40', '2013-04-17 21:37:59', 2),
(107, 940, 'Radiohead_Band25', 'image', 'Radiohead_Band25.jpg', 'Radiohead_Band25.jpg', NULL, '2013-04-16 23:02:40', '2013-04-16 23:02:40', 2),
(134, 913, 'Folder', 'image', 'Folder.jpg', 'Folder.jpg', NULL, '2013-04-16 23:05:08', '2013-04-16 23:05:08', 2),
(135, 912, 'New one', 'embed', '<iframe width=''100%'' height=''471'' src="http://www.youtube.com/embed/RBfemo8lIDk?rel=0&amp;autoplay=1&amp;wmode=transparent" frameborder="0" allowfullscreen></iframe>', 'maxresdefault.jpg', NULL, '2013-04-19 22:12:06', '2013-04-19 22:12:06', 2),
(136, 911, 'BASSLIGHT - 14-03-201393', 'file', 'BASSLIGHT - 14-03-201393.pdf', NULL, NULL, '2013-04-19 22:14:58', '2013-04-19 22:14:58', 2);

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `type` enum('Text','Numeric','List of options','Node link','Media','Date','Time','Spatial','Yes/No') NOT NULL COMMENT 'Type',
  `options` text COMMENT 'Options',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`id`, `name`, `type`, `options`) VALUES
(1, 'Date', 'Text', '<p>fdfd</p>\r\n'),
(2, 'TestingGG', 'List of options', '<p>Fds</p>\r\n'),
(3, 'fdsfds', 'Node link', NULL),
(4, 'Test', 'Text', NULL),
(5, 'Test page', 'Text', NULL),
(6, 'hoi', 'Text', NULL),
(7, 'Test project', 'Text', NULL),
(9, 'Productje', 'Text', NULL),
(10, 'Required field', 'List of options', '<p>Options!</p>\r\n'),
(11, 'name', 'Text', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nodetype` int(10) unsigned NOT NULL COMMENT 'Node type',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `nodetype`, `name`, `subtitle`, `added`, `updated`, `content`, `description`, `media`, `user`, `sort`, `published`) VALUES
(16, 2, 'Welkom bij Vakadi', NULL, '2013-04-11 19:21:29', '2013-04-17 21:37:07', '<h2>Wist u dat...</h2>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit libero massa, ac semper elit. Integer convallis enim sed erat ornare lobortis. Sed vitae augue mi, id pellentesque sapien. Cras non ipsum enim, vitae tincidunt risus. Quisque et tortor diam, vel rutrum quam. Quisque tortor dui, gravida vel tincidunt ut, euismod non erat. Nulla eget sem at nulla sagittis accumsan et eleifend dui.</p>\r\n', NULL, NULL, 2, 9997, 0),
(17, 2, 'Wat doen wij', NULL, '2013-04-11 19:26:27', '2013-04-17 21:37:07', '<p>etst</p>\r\n', NULL, NULL, 2, 9998, 1),
(18, 2, ' Wie zijn wij', NULL, '2013-04-11 19:26:37', '2013-04-17 21:37:07', '<p>fd</p>\r\n', NULL, NULL, 2, 9996, 1),
(19, 2, 'Nieuwsbrief', NULL, '2013-04-11 19:26:43', '2013-04-17 21:37:07', '<p>test</p>\r\n', NULL, NULL, 2, 9995, 1),
(20, 2, 'Downloads', NULL, '2013-04-11 19:26:49', '2013-04-17 21:37:07', '<p>fdfd</p>\r\n', NULL, NULL, 2, 9994, 1),
(21, 2, 'Een titel', NULL, '2013-04-17 23:13:57', '2013-04-17 23:13:57', '<p>Een artikel</p>\r\n', NULL, NULL, 2, 998, 1),
(24, 2, 'test', NULL, '2013-04-18 02:36:45', '2013-04-18 02:36:45', '<p>test</p>\r\n', NULL, 95, 2, 995, 1),
(25, 5, 'New page', NULL, '2013-04-18 11:55:11', '2013-04-18 11:55:11', '<p>Some content</p>\r\n', NULL, NULL, 2, 994, 1),
(26, 2, 'Some other test', NULL, '2013-04-19 21:58:21', '2013-04-19 21:58:21', '<p>test</p>\r\n', NULL, NULL, 2, 993, 1),
(27, 2, 'Some more', NULL, '2013-04-19 21:58:35', '2013-04-19 21:58:35', '<p>more</p>\r\n', NULL, NULL, 2, 992, 1);

-- --------------------------------------------------------

--
-- Table structure for table `nodetype`
--

CREATE TABLE IF NOT EXISTS `nodetype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `category` varchar(255) DEFAULT NULL COMMENT 'Category',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `nodetype`
--

INSERT INTO `nodetype` (`id`, `name`, `category`, `sort`) VALUES
(2, 'Blog item', NULL, 1002),
(3, 'Okeee', 'products', 1004),
(5, 'Page', 'products', 1000),
(7, 'Project', 'pages', 1003),
(11, 'Movie', NULL, 999),
(16, 'Video', NULL, 1001);

-- --------------------------------------------------------

--
-- Table structure for table `nodetype_meta`
--

CREATE TABLE IF NOT EXISTS `nodetype_meta` (
  `nodetype` int(10) unsigned NOT NULL COMMENT 'Node type',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  KEY `meta` (`meta`),
  KEY `post` (`nodetype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nodetype_meta`
--

INSERT INTO `nodetype_meta` (`nodetype`, `meta`, `sort`) VALUES
(5, 3, 999),
(5, 4, 999),
(5, 7, 999),
(5, 1, 999),
(3, 1, 999),
(3, 3, 999),
(3, 4, 999),
(3, 7, 999),
(11, 3, 999),
(11, 11, 999);

-- --------------------------------------------------------

--
-- Table structure for table `node_media`
--

CREATE TABLE IF NOT EXISTS `node_media` (
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `media` int(10) unsigned NOT NULL COMMENT 'Media',
  KEY `post` (`node`),
  KEY `media` (`media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `node_media`
--

INSERT INTO `node_media` (`node`, `media`) VALUES
(20, 84),
(27, 135),
(27, 52),
(27, 80),
(25, 83),
(25, 78),
(25, 64),
(25, 136);

-- --------------------------------------------------------

--
-- Table structure for table `node_meta`
--

CREATE TABLE IF NOT EXISTS `node_meta` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID',
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
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
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `tag` int(10) unsigned NOT NULL COMMENT 'Tag',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `usergroup`, `email`, `fullname`, `address`, `zipcode`, `city`, `region`, `country`, `secret`) VALUES
(2, 'admin', '$2a$10$3d21e63bf274b5731ec89uJYO86OPKqS8G5u2zGMElqS.Y57R07E2', 2, 'AUAAv/9FyK6KQEsD9xa0Oc9feicmdvR39vMV45wv+doZyA8lNwJnPFteK7/BW0No9u0+qXWb0uP/g4ATJNIMe65AaADH', 'ASAA3/8kDSvE3ggutzBXe8+i4zKQeINJLFaG5PbVzq3X6lPArA==', 'AWAAn/+5Qp6XDMKG8PBOSuEND7D2dzyyQUq+FVIXWWuZbjIukrYyhCwLWZRyeUSBWRRQvBdCRaH51qCLXUhggtFHyvHzW2ViR4aSHTBDGPLcjZ1o7L8P7OuXPhUnWjcPgLJvSCE=', 'AWAAn/+5Qp6XDMKG8PBOSuEND7D2dzyyQUq+FVIXWWuZbjIukrYyhCwLWZRyeUSBWRRQvBdCRaH51qCLXUhggtFHyvHzW2ViR4aSHTBDGPLcjZ1o7L8P7OuXPhUnWjcPgLJvSCE=', 'AWAAn/+5Qp6XDMKG8PBOSuEND7D2dzyyQUq+FVIXWWuZbjIukrYyhCwLWZRyeUSBWRRQvBdCRaH51qCLXUhggtFHyvHzW2ViR4aSHTBDGPLcjZ1o7L8P7OuXPhUnWjcPgLJvSCE=', 'AWAAn/+5Qp6XDMKG8PBOSuEND7D2dzyyQUq+FVIXWWuZbjIukrYyhCwLWZRyeUSBWRRQvBdCRaH51qCLXUhggtFHyvHzW2ViR4aSHTBDGPLcjZ1o7L8P7OuXPhUnWjcPgLJvSCE=', 'AWAAn/+5Qp6XDMKG8PBOSuEND7D2dzyyQUq+FVIXWWuZbjIukrYyhCwLWZRyeUSBWRRQvBdCRaH51qCLXUhggtFHyvHzW2ViR4aSHTBDGPLcjZ1o7L8P7OuXPhUnWjcPgLJvSCE=', 'f2e7095a84f4d8acb6050cbf96fc12ef10ca87a3'),
(18, 'user', '$2a$10$eb504d515e53dd9f31ae2OJ05Ky37q0ziGAHlwNilL8ePggfUDaJ2', 1, 'ASAA3/96cQsz+OGM2a7/kxDiqVawv5LeU3sTRihI0+5XTfw0sA==', 'u7CY+5adwDSLWtmWH//DuRLOyU45ytQtEfrOvCOpoT8yCwA=', NULL, NULL, NULL, NULL, NULL, 'c0d19277b81b5ffde5803cc978c4595414b32289'),
(19, 'editor', '$2a$10$43614f48abe685cb6ad5fu0y2xpHhEuVipCZ3YzcUVzSF0RhTbfai', 3, 'W/oj3qz2b/LCPypbdKQ8Yh2ZJ5tN6M7VPB2nzZZidoDjBAA=', 'AUAAv/+2EFbO3Zkj2CIIqrJ4LkGdddX6KWIdjGvInkogjZ0sHuUSJruyuZPsydGxlA0KtKy8Fl61iieIqsyn+LnyiPX8', NULL, NULL, NULL, NULL, NULL, 'a94f78908b743fde9b1bb5bab69752d33bba71c3');

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
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `node_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `node_ibfk_4` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `node_ibfk_5` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`);

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
