-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2013 at 11:51 AM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

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
(32, 'usergroup', NULL, 3, 'admin', '*', 'tag', 'allow');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=256 ;

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
(136, 911, 'BASSLIGHT - 14-03-201393', 'file', 'BASSLIGHT - 14-03-201393.pdf', NULL, NULL, '2013-04-19 22:14:58', '2013-04-19 22:14:58', 2),
(138, 909, 'die-antwoord', 'image', 'die-antwoord26.jpeg', 'die-antwoord26.jpeg', NULL, '2013-04-20 09:13:32', '2013-04-22 03:03:35', 2),
(139, 908, 'tickets1 (1)67', 'image', 'tickets1 (1)67.jpg', 'tickets1 (1)67.jpg', NULL, '2013-04-20 09:13:33', '2013-04-20 09:13:33', 2),
(142, 905, 'MiniVelo67', 'image', 'MiniVelo67.jpg', 'MiniVelo67.jpg', NULL, '2013-04-20 09:29:09', '2013-04-20 09:29:09', 2),
(143, 904, 'Bombino', 'embed', '<iframe width=''100%'' height=''471'' src="http://www.youtube.com/embed/IaqScs7vR-4?rel=0&amp;autoplay=1&amp;wmode=transparent" frameborder="0" allowfullscreen></iframe>', 'maxresdefault34.jpg', NULL, '2013-04-22 02:48:36', '2013-04-22 02:48:36', 2),
(144, 903, 'MiniVelo92', 'image', 'MiniVelo92.jpg', 'MiniVelo92.jpg', NULL, '2013-04-24 15:48:51', '2013-04-24 15:48:51', 2),
(145, 902, 'done52', 'image', 'done52.JPG', 'done52.JPG', NULL, '2013-04-24 15:49:56', '2013-04-24 15:49:56', 2),
(146, 901, '9-and-a-half-weeks26', 'image', '9-and-a-half-weeks26.jpg', '9-and-a-half-weeks26.jpg', NULL, '2013-04-24 15:59:35', '2013-04-24 15:59:35', 2),
(147, 900, 'paul-duro-0278', 'image', 'paul-duro-0278.jpg', 'paul-duro-0278.jpg', NULL, '2013-04-24 16:00:39', '2013-04-24 16:00:39', 2),
(148, 899, 'done45', 'image', 'done45.JPG', 'done45.JPG', NULL, '2013-04-24 16:14:52', '2013-04-24 16:14:52', 2),
(149, 898, 'domtorenutrechtvuurwerk270', 'image', 'domtorenutrechtvuurwerk270.jpg', 'domtorenutrechtvuurwerk270.jpg', NULL, '2013-04-24 16:15:09', '2013-04-24 16:15:09', 2),
(150, 897, 'Bart_Skils_0246', 'image', 'Bart_Skils_0246.jpg', 'Bart_Skils_0246.jpg', NULL, '2013-04-24 16:16:17', '2013-04-24 16:16:17', 2),
(151, 896, 'Bart_Skils_0260', 'image', 'Bart_Skils_0260.jpg', 'Bart_Skils_0260.jpg', NULL, '2013-04-24 16:16:55', '2013-04-24 16:16:55', 2),
(152, 895, 'Radiohead_Band37', 'image', 'Radiohead_Band37.jpg', 'Radiohead_Band37.jpg', NULL, '2013-04-24 16:17:38', '2013-04-24 16:17:38', 2),
(153, 894, 'tickets1 (1)85', 'image', 'tickets1 (1)85.jpg', 'tickets1 (1)85.jpg', NULL, '2013-04-24 16:18:17', '2013-04-24 16:18:17', 2),
(154, 893, 'diplo42', 'image', 'diplo42.jpg', 'diplo42.jpg', NULL, '2013-04-24 16:18:22', '2013-04-24 16:18:22', 2),
(155, 892, 'diplo13', 'image', 'diplo13.jpg', 'diplo13.jpg', NULL, '2013-04-24 16:18:28', '2013-04-24 16:18:28', 2),
(156, 891, 'radiohead-radiohead-22916726-2480-108732', 'image', 'radiohead-radiohead-22916726-2480-108732.jpg', 'radiohead-radiohead-22916726-2480-108732.jpg', NULL, '2013-04-24 16:18:29', '2013-04-24 16:18:29', 2),
(157, 890, 'IMG_220686', 'image', 'IMG_220686.JPG', 'IMG_220686.JPG', NULL, '2013-04-24 16:18:30', '2013-04-24 16:18:30', 2),
(158, 889, 'icon80', 'image', 'icon80.png', 'icon80.png', NULL, '2013-04-24 16:18:30', '2013-04-24 16:18:30', 2),
(159, 888, 'Radiohead_Band49', 'image', 'Radiohead_Band49.jpg', 'Radiohead_Band49.jpg', NULL, '2013-04-24 16:27:25', '2013-04-24 16:27:25', 2),
(160, 887, 'lomografie72', 'image', 'lomografie72.png', 'lomografie72.png', NULL, '2013-04-24 16:27:35', '2013-04-24 16:27:35', 2),
(161, 886, 'apps (1)36', 'image', 'apps (1)36.png', 'apps (1)36.png', NULL, '2013-04-24 16:27:44', '2013-04-24 16:27:44', 2),
(162, 885, 'LL07_plattegrond66', 'image', 'LL07_plattegrond66.jpg', 'LL07_plattegrond66.jpg', NULL, '2013-04-24 16:27:48', '2013-04-24 16:27:48', 2),
(163, 884, 'Bart_Skils_0219', 'image', 'Bart_Skils_0219.jpg', 'Bart_Skils_0219.jpg', NULL, '2013-04-24 16:27:48', '2013-04-24 16:27:48', 2),
(164, 883, 'free_your_mind66', 'image', 'free_your_mind66.jpg', 'free_your_mind66.jpg', NULL, '2013-04-24 16:33:58', '2013-04-24 16:33:58', 2),
(165, 882, 'halfweeks-dots60', 'image', 'halfweeks-dots60.png', 'halfweeks-dots60.png', NULL, '2013-04-24 16:33:58', '2013-04-24 16:33:58', 2),
(166, 881, 'css32', 'image', 'css32.png', 'css32.png', NULL, '2013-04-24 16:33:59', '2013-04-24 16:33:59', 2),
(167, 880, 'halfweeks-dots-inverted53', 'image', 'halfweeks-dots-inverted53.png', 'halfweeks-dots-inverted53.png', NULL, '2013-04-24 16:33:59', '2013-04-24 16:33:59', 2),
(168, 879, 'halfweeks46', 'image', 'halfweeks46.png', 'halfweeks46.png', NULL, '2013-04-24 16:34:00', '2013-04-24 16:34:00', 2),
(169, 878, 'halfweeks-inverted42', 'image', 'halfweeks-inverted42.png', 'halfweeks-inverted42.png', NULL, '2013-04-24 16:34:01', '2013-04-24 16:34:01', 2),
(170, 877, 'halfweeks-square11', 'image', 'halfweeks-square11.png', 'halfweeks-square11.png', NULL, '2013-04-24 16:34:01', '2013-04-24 16:34:01', 2),
(171, 876, 'validate58', 'image', 'validate58.png', 'validate58.png', NULL, '2013-04-24 16:34:02', '2013-04-24 16:34:02', 2),
(172, 875, '14556_418337524905116_1190215125_n12', 'image', '14556_418337524905116_1190215125_n12.jpg', '14556_418337524905116_1190215125_n12.jpg', NULL, '2013-04-24 16:34:02', '2013-04-24 16:34:02', 2),
(173, 874, 'ouderenbash25', 'image', 'ouderenbash25.png', 'ouderenbash25.png', NULL, '2013-04-24 16:34:03', '2013-04-24 16:34:03', 2),
(174, 873, 'IMG_221245', 'image', 'IMG_221245.JPG', 'IMG_221245.JPG', NULL, '2013-04-24 16:34:12', '2013-04-24 16:34:12', 2),
(175, 872, 'IMG_22101298', 'image', 'IMG_22101298.JPG', 'IMG_22101298.JPG', NULL, '2013-04-24 16:34:12', '2013-04-24 16:34:12', 2),
(176, 871, 'IMG_221440', 'image', 'IMG_221440.JPG', 'IMG_221440.JPG', NULL, '2013-04-24 16:34:13', '2013-04-24 16:34:13', 2),
(177, 870, '14556_418337524905116_1190215125_n22', 'image', '14556_418337524905116_1190215125_n22.jpg', '14556_418337524905116_1190215125_n22.jpg', NULL, '2013-04-24 16:35:06', '2013-04-24 16:35:06', 2),
(178, 869, 'lomografie49', 'image', 'lomografie49.png', 'lomografie49.png', NULL, '2013-04-24 16:36:14', '2013-04-24 16:36:14', 2),
(179, 868, 'tickets1 (1)94', 'image', 'tickets1 (1)94.jpg', 'tickets1 (1)94.jpg', NULL, '2013-04-24 16:36:15', '2013-04-24 16:36:15', 2),
(180, 867, 'diplo24', 'image', 'diplo24.jpg', 'diplo24.jpg', NULL, '2013-04-24 16:36:15', '2013-04-24 16:36:15', 2),
(181, 866, 'die-antwoord85', 'image', 'die-antwoord85.jpeg', 'die-antwoord85.jpeg', NULL, '2013-04-24 16:36:16', '2013-04-24 16:36:16', 2),
(182, 865, 'radiohead-radiohead-22916726-2480-108744', 'image', 'radiohead-radiohead-22916726-2480-108744.jpg', 'radiohead-radiohead-22916726-2480-108744.jpg', NULL, '2013-04-24 16:36:44', '2013-04-24 16:36:44', 2),
(183, 864, 'IMG_221498', 'image', 'IMG_221498.JPG', 'IMG_221498.JPG', NULL, '2013-04-24 16:36:45', '2013-04-24 16:36:45', 2),
(184, 863, 'IMG_220674', 'image', 'IMG_220674.JPG', 'IMG_220674.JPG', NULL, '2013-04-24 16:36:46', '2013-04-24 16:36:46', 2),
(185, 862, 'icon79', 'image', 'icon79.png', 'icon79.png', NULL, '2013-04-24 16:36:46', '2013-04-24 16:36:46', 2),
(186, 861, 'IMG_221041', 'image', 'IMG_221041.JPG', 'IMG_221041.JPG', NULL, '2013-04-24 16:36:47', '2013-04-24 16:36:47', 2),
(187, 860, 'tickets1 (1)15', 'image', 'tickets1 (1)15.jpg', 'tickets1 (1)15.jpg', NULL, '2013-04-24 16:38:03', '2013-04-24 16:38:03', 2),
(188, 859, 'radiohead-radiohead-22916726-2480-108718', 'image', 'radiohead-radiohead-22916726-2480-108718.jpg', 'radiohead-radiohead-22916726-2480-108718.jpg', NULL, '2013-04-24 16:38:04', '2013-04-24 16:38:04', 2),
(189, 858, 'die-antwoord77', 'image', 'die-antwoord77.jpeg', 'die-antwoord77.jpeg', NULL, '2013-04-24 16:38:04', '2013-04-24 16:38:04', 2),
(190, 857, 'diplo33', 'image', 'diplo33.jpg', 'diplo33.jpg', NULL, '2013-04-24 16:38:05', '2013-04-24 16:38:05', 2),
(191, 856, 'tickets1 (1)70', 'image', 'tickets1 (1)70.jpg', 'tickets1 (1)70.jpg', NULL, '2013-04-24 16:39:59', '2013-04-24 16:39:59', 2),
(192, 855, 'lomografie88', 'image', 'lomografie88.png', 'lomografie88.png', NULL, '2013-04-24 16:44:00', '2013-04-24 16:44:00', 2),
(193, 854, 'domtorenutrechtvuurwerk285', 'image', 'domtorenutrechtvuurwerk285.jpg', 'domtorenutrechtvuurwerk285.jpg', NULL, '2013-04-24 16:44:27', '2013-04-24 16:44:27', 2),
(194, 853, 'lomografie25', 'image', 'lomografie25.png', 'lomografie25.png', NULL, '2013-04-24 16:45:24', '2013-04-24 16:45:24', 2),
(195, 852, 'tickets1 (1)22', 'image', 'tickets1 (1)22.jpg', 'tickets1 (1)22.jpg', NULL, '2013-04-24 16:45:25', '2013-04-24 16:45:25', 2),
(196, 851, 'IMG_221256', 'image', 'IMG_221256.JPG', 'IMG_221256.JPG', NULL, '2013-04-24 16:45:25', '2013-04-24 16:45:25', 2),
(197, 850, 'die-antwoord70', 'image', 'die-antwoord70.jpeg', 'die-antwoord70.jpeg', NULL, '2013-04-24 16:45:26', '2013-04-24 16:45:26', 2),
(198, 849, 'diplo31', 'image', 'diplo31.jpg', 'diplo31.jpg', NULL, '2013-04-24 16:45:28', '2013-04-24 16:45:28', 2),
(199, 848, 'radiohead-radiohead-22916726-2480-108713', 'image', 'radiohead-radiohead-22916726-2480-108713.jpg', 'radiohead-radiohead-22916726-2480-108713.jpg', NULL, '2013-04-24 16:45:29', '2013-04-24 16:45:29', 2),
(200, 847, 'IMG_220670', 'image', 'IMG_220670.JPG', 'IMG_220670.JPG', NULL, '2013-04-24 16:45:30', '2013-04-24 16:45:30', 2),
(201, 846, 'icon11', 'image', 'icon11.png', 'icon11.png', NULL, '2013-04-24 16:45:30', '2013-04-24 16:45:30', 2),
(202, 845, 'IMG_221070', 'image', 'IMG_221070.JPG', 'IMG_221070.JPG', NULL, '2013-04-24 16:45:31', '2013-04-24 16:45:31', 2),
(203, 844, 'IMG_221491', 'image', 'IMG_221491.JPG', 'IMG_221491.JPG', NULL, '2013-04-24 16:45:31', '2013-04-24 16:45:31', 2),
(204, 843, 'validate52', 'image', 'validate52.png', 'validate52.png', NULL, '2013-04-24 16:45:50', '2013-04-24 16:45:50', 2),
(205, 842, 'fc039fdc140c2efef1e85f5d65cba8cf93', 'image', 'fc039fdc140c2efef1e85f5d65cba8cf93.jpeg', 'fc039fdc140c2efef1e85f5d65cba8cf93.jpeg', NULL, '2013-04-24 16:45:50', '2013-04-24 16:45:50', 2),
(206, 841, 'fc039fdc140c2efef1e85f5d65cba8cf36', 'image', 'fc039fdc140c2efef1e85f5d65cba8cf36.jpeg', 'fc039fdc140c2efef1e85f5d65cba8cf36.jpeg', NULL, '2013-04-24 16:47:12', '2013-04-24 16:47:12', 2),
(207, 840, 'validate96', 'image', 'validate96.png', 'validate96.png', NULL, '2013-04-24 16:47:13', '2013-04-24 16:47:13', 2),
(208, 839, 'a463957db6e7471ff875c4beb5b64f7a72', 'image', 'a463957db6e7471ff875c4beb5b64f7a72.png', 'a463957db6e7471ff875c4beb5b64f7a72.png', NULL, '2013-04-24 16:50:38', '2013-04-24 16:50:38', 2),
(209, 838, 'halfweeks-square48', 'image', 'halfweeks-square48.png', 'halfweeks-square48.png', NULL, '2013-04-24 16:50:39', '2013-04-24 16:50:39', 2),
(210, 837, 'a463957db6e7471ff875c4beb5b64f7a84', 'image', 'a463957db6e7471ff875c4beb5b64f7a84.png', 'a463957db6e7471ff875c4beb5b64f7a84.png', NULL, '2013-04-24 16:52:52', '2013-04-24 16:52:52', 2),
(211, 836, 'halfweeks-square27', 'image', 'halfweeks-square27.png', 'halfweeks-square27.png', NULL, '2013-04-24 16:52:53', '2013-04-24 16:52:53', 2),
(212, 835, 'a463957db6e7471ff875c4beb5b64f7a95', 'image', 'a463957db6e7471ff875c4beb5b64f7a95.png', 'a463957db6e7471ff875c4beb5b64f7a95.png', NULL, '2013-04-24 16:53:01', '2013-04-24 16:53:01', 2),
(213, 834, 'halfweeks-square86', 'image', 'halfweeks-square86.png', 'halfweeks-square86.png', NULL, '2013-04-24 16:53:01', '2013-04-24 16:53:01', 2),
(214, 833, 'tickets1 (1)42', 'image', 'tickets1 (1)42.jpg', 'tickets1 (1)42.jpg', NULL, '2013-04-24 16:53:11', '2013-04-24 16:53:11', 2),
(215, 832, 'domtorenutrechtvuurwerk253', 'image', 'domtorenutrechtvuurwerk253.jpg', 'domtorenutrechtvuurwerk253.jpg', NULL, '2013-04-24 16:53:12', '2013-04-24 16:53:12', 2),
(216, 831, 'done29', 'image', 'done29.JPG', 'done29.JPG', NULL, '2013-04-24 16:53:13', '2013-04-24 16:53:13', 2),
(217, 830, 'BASSLIGHT - 14-03-201394', 'file', 'BASSLIGHT - 14-03-201394.pdf', 'BASSLIGHT - 14-03-201394.pdf', NULL, '2013-04-24 16:53:13', '2013-04-24 16:53:13', 2),
(218, 829, 'lomografie97', 'image', 'lomografie97.png', 'lomografie97.png', NULL, '2013-04-24 16:53:14', '2013-04-24 16:53:14', 2),
(219, 828, 'die-antwoord45', 'image', 'die-antwoord45.jpeg', 'die-antwoord45.jpeg', NULL, '2013-04-24 16:53:24', '2013-04-24 16:53:24', 2),
(220, 827, 'diplo55', 'image', 'diplo55.jpg', 'diplo55.jpg', NULL, '2013-04-24 16:53:25', '2013-04-24 16:53:25', 2),
(221, 826, 'IMG_22068680', 'image', 'IMG_22068680.JPG', 'IMG_22068680.JPG', NULL, '2013-04-24 16:53:25', '2013-04-24 16:53:25', 2),
(222, 825, 'radiohead-radiohead-22916726-2480-108714', 'image', 'radiohead-radiohead-22916726-2480-108714.jpg', 'radiohead-radiohead-22916726-2480-108714.jpg', NULL, '2013-04-24 16:53:25', '2013-04-24 16:53:25', 2),
(223, 824, 'LL07_plattegrond63', 'image', 'LL07_plattegrond63.jpg', 'LL07_plattegrond63.jpg', NULL, '2013-04-24 16:54:10', '2013-04-24 16:54:10', 2),
(224, 823, 'Bart_Skils_0258', 'image', 'Bart_Skils_0258.jpg', 'Bart_Skils_0258.jpg', NULL, '2013-04-24 16:54:12', '2013-04-24 16:54:12', 2),
(225, 822, 'Bart_Skils_0245', 'image', 'Bart_Skils_0245.jpg', 'Bart_Skils_0245.jpg', NULL, '2013-04-25 09:53:20', '2013-04-25 09:53:20', 2),
(226, 821, 'done69', 'image', 'done69.JPG', 'done69.JPG', NULL, '2013-04-25 12:27:59', '2013-04-25 12:27:59', 2),
(227, 820, 'BASSLIGHT - 14-03-201316', 'file', 'BASSLIGHT - 14-03-201316.pdf', 'BASSLIGHT - 14-03-201316.pdf', NULL, '2013-04-25 12:28:00', '2013-04-25 12:28:00', 2),
(228, 819, 'lomografie45', 'image', 'lomografie45.png', 'lomografie45.png', NULL, '2013-04-25 12:28:01', '2013-04-25 12:28:01', 2),
(229, 818, 'tickets1 (1)82', 'image', 'tickets1 (1)82.jpg', 'tickets1 (1)82.jpg', NULL, '2013-04-25 12:28:02', '2013-04-25 12:28:02', 2),
(230, 817, 'die-antwoord80', 'image', 'die-antwoord80.jpeg', 'die-antwoord80.jpeg', NULL, '2013-04-25 12:28:03', '2013-04-25 12:28:03', 2),
(231, 816, 'diplo5145', 'image', 'diplo5145.jpg', 'diplo5145.jpg', NULL, '2013-04-25 12:28:03', '2013-04-25 12:28:03', 2),
(232, 815, 'radiohead-radiohead-22916726-2480-108789', 'image', 'radiohead-radiohead-22916726-2480-108789.jpg', 'radiohead-radiohead-22916726-2480-108789.jpg', NULL, '2013-04-25 12:28:03', '2013-04-25 12:28:03', 2),
(233, 814, 'IMG_220690', 'image', 'IMG_220690.JPG', 'IMG_220690.JPG', NULL, '2013-04-25 12:28:04', '2013-04-25 12:28:04', 2),
(234, 813, 'icon30', 'image', 'icon30.png', 'icon30.png', NULL, '2013-04-25 12:28:04', '2013-04-25 12:28:04', 2),
(235, 812, 'IMG_221087', 'image', 'IMG_221087.JPG', 'IMG_221087.JPG', NULL, '2013-04-25 12:28:05', '2013-04-25 12:28:05', 2),
(236, 811, '14556_418337524905116_1190215125_n57', 'image', '14556_418337524905116_1190215125_n57.jpg', '14556_418337524905116_1190215125_n57.jpg', NULL, '2013-04-26 08:39:34', '2013-04-26 08:39:34', 2),
(237, 810, '9938', 'image', '9938.JPG', '9938.JPG', NULL, '2013-04-26 08:41:01', '2013-04-26 08:41:01', 2),
(238, 809, 'AGF 247', 'image', 'AGF 247.JPG', 'AGF 247.JPG', NULL, '2013-04-26 08:41:09', '2013-04-26 08:41:09', 2),
(239, 808, 'a463957db6e7471ff875c4beb5b64f7a41', 'image', 'a463957db6e7471ff875c4beb5b64f7a41.png', 'a463957db6e7471ff875c4beb5b64f7a41.png', NULL, '2013-04-26 08:41:15', '2013-04-26 08:41:15', 2),
(240, 807, 'a463957db6e7471ff875c4beb5b64f7a79', 'image', 'a463957db6e7471ff875c4beb5b64f7a79.png', 'a463957db6e7471ff875c4beb5b64f7a79.png', NULL, '2013-04-26 11:11:54', '2013-04-26 11:11:54', 2),
(241, 806, 'IMG_221075', 'image', 'IMG_221075.JPG', 'IMG_221075.JPG', NULL, '2013-04-26 11:12:35', '2013-04-26 11:12:35', 2),
(242, 805, 'tickets1 (1)12', 'image', 'lomografie54.png', 'lomografie54.png', NULL, '2013-04-26 14:41:47', '2013-04-26 14:43:41', 2),
(243, 804, 'Begroting', 'file', 'Begroting.xlsx', 'Begroting.xlsx', NULL, '2013-04-28 10:22:36', '2013-04-28 10:22:36', 2),
(244, 803, 'linkedin_icon', 'image', 'linkedin_icon.png', 'linkedin_icon.png', NULL, '2013-04-28 13:01:45', '2013-04-28 13:01:45', 2),
(245, 802, 'error2', 'image', 'error2.jpg', 'error2.jpg', NULL, '2013-04-28 13:02:20', '2013-04-28 13:02:20', 2),
(246, 801, 'chart-5598812be3903cee117b4d595d9eaa1e', 'image', 'chart-5598812be3903cee117b4d595d9eaa1e.png', 'chart-5598812be3903cee117b4d595d9eaa1e.png', NULL, '2013-04-28 13:02:20', '2013-04-28 13:02:20', 2),
(247, 800, 'cover (1)', 'image', 'cover (1).png', 'cover (1).png', NULL, '2013-04-28 13:02:21', '2013-04-28 13:02:21', 2),
(248, 799, 'head01', 'image', 'head01.png', 'head01.png', NULL, '2013-04-28 13:02:21', '2013-04-28 13:02:21', 2),
(249, 798, 'iconmonstr-sound-wave-5-icon', 'image', 'iconmonstr-sound-wave-5-icon.png', 'iconmonstr-sound-wave-5-icon.png', NULL, '2013-04-28 13:02:22', '2013-04-28 13:02:22', 2),
(250, 797, '264616_483155591736288_1407612040_n', 'image', '264616_483155591736288_1407612040_n.jpg', '264616_483155591736288_1407612040_n.jpg', NULL, '2013-04-28 13:02:23', '2013-04-28 13:02:23', 2),
(251, 796, '426398_518978804810887_1063671984_n', 'image', '426398_518978804810887_1063671984_n.jpg', '426398_518978804810887_1063671984_n.jpg', NULL, '2013-04-28 13:02:23', '2013-04-28 13:02:23', 2),
(252, 795, '527562_357666390947938_873189518_n', 'image', '527562_357666390947938_873189518_n.jpg', '527562_357666390947938_873189518_n.jpg', NULL, '2013-04-28 13:02:25', '2013-04-28 13:02:25', 2),
(253, 794, '551475_122820057906934_2036167094_n', 'image', '551475_122820057906934_2036167094_n.jpg', '551475_122820057906934_2036167094_n.jpg', NULL, '2013-04-28 13:02:25', '2013-04-28 13:02:25', 2),
(254, 793, '882345_115923171929956_236178002_o', 'image', '882345_115923171929956_236178002_o.jpg', '882345_115923171929956_236178002_o.jpg', NULL, '2013-04-28 13:02:26', '2013-04-28 13:02:26', 2),
(255, 792, 'IMG_20130424_222738', 'image', 'IMG_20130424_222738.jpg', 'IMG_20130424_222738.jpg', NULL, '2013-04-28 18:10:49', '2013-04-28 18:10:49', 2);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`id`, `name`, `type`, `options`) VALUES
(1, 'Title', 'Text', '<p>fdfd</p>\r\n'),
(2, 'TestingGG', 'List of options', '<p>Fds</p>\r\n'),
(3, 'fdsfds', 'Node link', NULL),
(4, 'Test', 'Text', NULL),
(5, 'Test page', 'Text', NULL),
(6, 'hoi', 'Text', NULL),
(7, 'Test project', 'Text', NULL),
(9, 'Productje', 'Text', NULL),
(10, 'Required field', 'List of options', '<p>Options!</p>\r\n'),
(11, 'name', 'Text', NULL),
(12, 'Last item', 'Text', NULL);

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
  `parent` int(10) unsigned DEFAULT NULL COMMENT 'Parent node',
  `user` int(10) unsigned NOT NULL COMMENT 'Owner',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `published` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Published',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `media` (`media`),
  KEY `posttype` (`nodetype`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `nodetype`, `name`, `subtitle`, `added`, `updated`, `content`, `description`, `media`, `parent`, `user`, `sort`, `published`) VALUES
(63, 11, 'Beste mensen', NULL, '2013-04-25 15:25:50', '2013-04-28 17:52:02', '<p>Hoe gaat het er mee?</p>\r\n', NULL, 254, NULL, 2, 999, 1),
(65, 11, 'fds', NULL, '2013-04-28 13:23:10', '2013-04-28 13:23:10', '<p>fds</p>\r\n', NULL, 96, NULL, 2, 999, 1),
(70, 11, 'fds', NULL, '2013-04-28 13:43:47', '2013-04-28 13:50:51', '<p>fds</p>\r\n', NULL, 99, NULL, 2, 999, 1);

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
(5, 'Page', 'products', 1010),
(7, 'Project', 'pages', 1011),
(11, 'Movie', NULL, 999),
(16, 'Video', NULL, 1000);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `nodetype_meta`
--

INSERT INTO `nodetype_meta` (`id`, `nodetype`, `meta`, `sort`) VALUES
(23, 11, 2, 1005),
(24, 11, 5, 1003),
(25, 11, 1, 1004),
(26, 11, 12, 1001),
(27, 16, 6, 1006),
(28, 16, 9, 1005),
(29, 11, 10, 1002);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=142 ;

--
-- Dumping data for table `node_media`
--

INSERT INTO `node_media` (`id`, `node`, `media`, `sort`) VALUES
(141, 63, 254, 15);

-- --------------------------------------------------------

--
-- Table structure for table `node_meta`
--

CREATE TABLE IF NOT EXISTS `node_meta` (
  `id` int(10) unsigned NOT NULL,
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `value` text NOT NULL COMMENT 'Value',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`,`meta`),
  KEY `meta` (`meta`),
  KEY `post` (`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

--
-- Dumping data for table `node_tag`
--

INSERT INTO `node_tag` (`id`, `node`, `tag`) VALUES
(77, 63, 66),
(73, 63, 67),
(74, 63, 68),
(76, 63, 69),
(75, 63, 70);

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=74 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `name`, `sort`) VALUES
(66, 'Video', '999'),
(67, 'Audio', '999'),
(68, 'Image', '999'),
(69, 'Text', '999'),
(70, 'Subtiel', '999'),
(71, 'Test tag', '999'),
(72, 'new tag', '999'),
(73, 'Added from popup', '999');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `usergroup`, `email`, `fullname`, `address`, `zipcode`, `city`, `region`, `country`, `secret`) VALUES
(2, 'admin', '$2a$10$3d21e63bf274b5731ec89uJYO86OPKqS8G5u2zGMElqS.Y57R07E2', 2, 'W1nGW/VSmUUx1L/28ZP/JYctopgd3strPJrcenfV5H0SpwE=', 'AUAAv/+AV9OSL26LrrF4lXXK74vxoPYuPCaY3AMRrI/uZMScyLUbJw7iT0YZ7cVHlw2ebFwCJPoIh0btkT3XoWTzimj7', 'AaAAX/8HDVQpHDgPOJp2iX7Wu0jVd26PnHDB/IjZWSvfaO+6zh38IfFmBNND2orvjCjSYYxeJ90PQHjNEqu/l6BSIHzOOoofeUgi5APC8zkC1dn17B3tZzdA/KQSTHFgDxqkNjqv59VYs0uLwpJTrDJd2eaAuOliNu7HhiVZCA7a4aupakgc1oZ+Gy63/3pxqR7Tx+ijj3j3PQ3Jg65PEG3MVkKl', 'AaAAX/8HDVQpHDgPOJp2iX7Wu0jVd26PnHDB/IjZWSvfaO+6zh38IfFmBNND2orvjCjSYYxeJ90PQHjNEqu/l6BSIHzOOoofeUgi5APC8zkC1dn17B3tZzdA/KQSTHFgDxqkNjqv59VYs0uLwpJTrDJd2eaAuOliNu7HhiVZCA7a4aupakgc1oZ+Gy63/3pxqR7Tx+ijj3j3PQ3Jg65PEG3MVkKl', 'AaAAX/8HDVQpHDgPOJp2iX7Wu0jVd26PnHDB/IjZWSvfaO+6zh38IfFmBNND2orvjCjSYYxeJ90PQHjNEqu/l6BSIHzOOoofeUgi5APC8zkC1dn17B3tZzdA/KQSTHFgDxqkNjqv59VYs0uLwpJTrDJd2eaAuOliNu7HhiVZCA7a4aupakgc1oZ+Gy63/3pxqR7Tx+ijj3j3PQ3Jg65PEG3MVkKl', 'AaAAX/8HDVQpHDgPOJp2iX7Wu0jVd26PnHDB/IjZWSvfaO+6zh38IfFmBNND2orvjCjSYYxeJ90PQHjNEqu/l6BSIHzOOoofeUgi5APC8zkC1dn17B3tZzdA/KQSTHFgDxqkNjqv59VYs0uLwpJTrDJd2eaAuOliNu7HhiVZCA7a4aupakgc1oZ+Gy63/3pxqR7Tx+ijj3j3PQ3Jg65PEG3MVkKl', 'AaAAX/8HDVQpHDgPOJp2iX7Wu0jVd26PnHDB/IjZWSvfaO+6zh38IfFmBNND2orvjCjSYYxeJ90PQHjNEqu/l6BSIHzOOoofeUgi5APC8zkC1dn17B3tZzdA/KQSTHFgDxqkNjqv59VYs0uLwpJTrDJd2eaAuOliNu7HhiVZCA7a4aupakgc1oZ+Gy63/3pxqR7Tx+ijj3j3PQ3Jg65PEG3MVkKl', 'f2e7095a84f4d8acb6050cbf96fc12ef10ca87a3'),
(18, 'user', '$2a$10$dcdb5189e024add93b22duBIQMYCIN36d1nrA4dghn3eXLoAcWhF.', 1, NULL, 'e5ukcvS5/xy22MdVTN/eLrTTvsTkKMMu+et53Fd3lUtcJwA=', NULL, NULL, NULL, NULL, NULL, 'c0d19277b81b5ffde5803cc978c4595414b32289');

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
