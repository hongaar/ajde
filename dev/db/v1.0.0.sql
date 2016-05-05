SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for acl
-- ----------------------------
DROP TABLE IF EXISTS `acl`;
CREATE TABLE `acl` (
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
  KEY `usergroup` (`usergroup`),
  CONSTRAINT `acl_ibfk_7` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `acl_ibfk_8` FOREIGN KEY (`usergroup`) REFERENCES `usergroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acl
-- ----------------------------
INSERT INTO `acl` VALUES ('90', 'page', 'usergroup', null, '2', '*', '*', '*', 'allow');
INSERT INTO `acl` VALUES ('111', 'model', 'public', null, null, 'node', 'read', '*', 'allow');
INSERT INTO `acl` VALUES ('112', 'model', 'usergroup', null, '2', 'node', '*', '*', 'allow');

-- ----------------------------
-- Table structure for ajde
-- ----------------------------
DROP TABLE IF EXISTS `ajde`;
CREATE TABLE `ajde` (
  `k` text,
  `v` text,
  UNIQUE KEY `k` (`k`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ajde
-- ----------------------------
INSERT INTO `ajde` VALUES ('version', 'v0.2');

-- ----------------------------
-- Table structure for cart
-- ----------------------------
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned DEFAULT NULL COMMENT 'User',
  `client` varchar(255) NOT NULL COMMENT 'Client ID',
  `added` timestamp NULL DEFAULT NULL COMMENT 'Added',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cart
-- ----------------------------

-- ----------------------------
-- Table structure for cart_item
-- ----------------------------
DROP TABLE IF EXISTS `cart_item`;
CREATE TABLE `cart_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cart` int(10) unsigned NOT NULL COMMENT 'Cart',
  `entity` varchar(255) NOT NULL COMMENT 'Entity',
  `entity_id` int(10) unsigned NOT NULL COMMENT 'Entity ID',
  `unitprice` decimal(8,2) NOT NULL COMMENT 'Unit price',
  `qty` tinyint(3) unsigned NOT NULL COMMENT 'Quantity',
  PRIMARY KEY (`id`),
  KEY `cart` (`cart`),
  CONSTRAINT `cart_item_ibfk_3` FOREIGN KEY (`cart`) REFERENCES `cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cart_item
-- ----------------------------

-- ----------------------------
-- Table structure for email
-- ----------------------------
DROP TABLE IF EXISTS `email`;
CREATE TABLE `email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `identifier` varchar(255) DEFAULT NULL COMMENT 'Identifier',
  `type` enum('transactional','manual') NOT NULL COMMENT 'Type',
  `module` varchar(255) DEFAULT NULL COMMENT 'Module',
  `template` int(10) unsigned DEFAULT NULL COMMENT 'Template',
  `from_name` varchar(255) DEFAULT NULL COMMENT 'Name',
  `from_email` varchar(255) DEFAULT NULL COMMENT 'E-mail address',
  PRIMARY KEY (`id`),
  KEY `template` (`template`),
  CONSTRAINT `email_ibfk_1` FOREIGN KEY (`template`) REFERENCES `template` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of email
-- ----------------------------
INSERT INTO `email` VALUES ('1', 'User reset link', 'user_reset_link', 'transactional', 'user', '1', 'Site name', 'info@example.com');
INSERT INTO `email` VALUES ('2', 'Form submission', 'form_submission', 'transactional', 'form', '4', 'Site name', 'info@example.com');
INSERT INTO `email` VALUES ('3', 'Your order', 'your_order', 'transactional', 'shop', '5', null, null);

-- ----------------------------
-- Table structure for form
-- ----------------------------
DROP TABLE IF EXISTS `form`;
CREATE TABLE `form` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `email` int(10) unsigned DEFAULT NULL COMMENT 'E-mail',
  `email_to` int(10) unsigned DEFAULT NULL COMMENT 'Recipient field',
  `submit_text` varchar(255) DEFAULT 'Submit',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `email_to` (`email_to`),
  CONSTRAINT `form_ibfk_1` FOREIGN KEY (`email`) REFERENCES `email` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `form_ibfk_2` FOREIGN KEY (`email_to`) REFERENCES `meta` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of form
-- ----------------------------

-- ----------------------------
-- Table structure for form_meta
-- ----------------------------
DROP TABLE IF EXISTS `form_meta`;
CREATE TABLE `form_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form` int(10) unsigned NOT NULL COMMENT 'Form',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nodetype` (`form`,`meta`) USING BTREE,
  KEY `meta` (`meta`) USING BTREE,
  KEY `post` (`form`) USING BTREE,
  CONSTRAINT `form_meta_ibfk_1` FOREIGN KEY (`form`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_meta_ibfk_2` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of form_meta
-- ----------------------------

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time',
  `channel` enum('Exception','Error','Routing','Security','Info','Application') NOT NULL COMMENT 'Channel',
  `description` text COMMENT 'Error description',
  `level` enum('1:Emergency','2:Alert','3:Critical','4:Error','5:Warning','6:Notice','7:Informational','8:Debug') NOT NULL COMMENT 'Severity',
  `request` text COMMENT 'Request URI',
  `message` text NOT NULL COMMENT 'Message',
  `code` text COMMENT 'Location in code',
  `user_agent` text COMMENT 'User agent',
  `referer` text COMMENT 'Referer',
  `ip` text COMMENT 'IP',
  `trace` text COMMENT 'Trace',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log
-- ----------------------------

-- ----------------------------
-- Table structure for mailerlog
-- ----------------------------
DROP TABLE IF EXISTS `mailerlog`;
CREATE TABLE `mailerlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sent_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Sent on',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Status',
  `from_name` varchar(255) DEFAULT NULL COMMENT 'Sender name',
  `from_email` varchar(255) NOT NULL COMMENT 'Sender',
  `to_name` varchar(255) DEFAULT NULL COMMENT 'Recipient name',
  `to_email` varchar(255) NOT NULL COMMENT 'Recipient',
  `subject` varchar(255) NOT NULL COMMENT 'Subject',
  `body` text NOT NULL COMMENT 'Message',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mailerlog
-- ----------------------------

-- ----------------------------
-- Table structure for media
-- ----------------------------
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mediatype` int(10) unsigned DEFAULT NULL COMMENT 'Category',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `name` varchar(255) NOT NULL COMMENT 'Title',
  `type` enum('unknown','image','file','embed') NOT NULL DEFAULT 'unknown' COMMENT 'Type',
  `pointer` text NOT NULL COMMENT 'Pointer',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'Thumbnail',
  `caption` text,
  `href` text,
  `icon` varchar(255) DEFAULT NULL COMMENT 'Icon',
  `added` timestamp NULL DEFAULT NULL COMMENT 'Added',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated',
  `user` int(10) unsigned NOT NULL COMMENT 'Owner',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `mediatype` (`mediatype`),
  CONSTRAINT `media_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  CONSTRAINT `media_ibfk_3` FOREIGN KEY (`mediatype`) REFERENCES `mediatype` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=327 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of media
-- ----------------------------
INSERT INTO `media` VALUES ('292', null, '996', 'ajde', 'image', 'ajde.jpg', 'ajde.jpg', null, null, null, '2013-08-06 15:22:10', '2013-08-06 15:22:10', '20');

-- ----------------------------
-- Table structure for mediatype
-- ----------------------------
DROP TABLE IF EXISTS `mediatype`;
CREATE TABLE `mediatype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `category` varchar(255) DEFAULT NULL COMMENT 'Category',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mediatype
-- ----------------------------

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `lang` varchar(5) NOT NULL COMMENT 'Language',
  `lang_root` int(10) unsigned DEFAULT NULL COMMENT 'Translation of',
  `type` enum('Node link','Submenu','URL') NOT NULL DEFAULT 'Node link' COMMENT 'Type',
  `url` text COMMENT 'URL',
  `parent` int(10) unsigned DEFAULT NULL COMMENT 'Parent menu',
  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Recursion level',
  `node` int(10) unsigned DEFAULT NULL COMMENT 'Node link',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `node` (`node`),
  KEY `lang_root` (`lang_root`),
  CONSTRAINT `menu_ibfk_3` FOREIGN KEY (`parent`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_ibfk_4` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_ibfk_5` FOREIGN KEY (`lang_root`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', 'mainmenu', '', null, 'Submenu', null, null, '0', null, '1');
INSERT INTO `menu` VALUES ('2', 'Home', '', null, 'Node link', null, '1', '1', '397', '2');

-- ----------------------------
-- Table structure for meta
-- ----------------------------
DROP TABLE IF EXISTS `meta`;
CREATE TABLE `meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `target` enum('node','setting','form') NOT NULL DEFAULT 'node' COMMENT 'Targeting',
  `type` varchar(255) NOT NULL COMMENT 'Type',
  `options` text COMMENT 'Options',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of meta
-- ----------------------------
INSERT INTO `meta` VALUES ('14', 'Google Analytics', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"Tracking ID\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES ('15', 'Facebook URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES ('16', 'Twitter URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES ('17', 'Instagram URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES ('18', 'LinkedIn URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES ('19', 'Google URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES ('20', 'Pinterest URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES ('35', 'Date format', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"See http:\\/\\/php.net\\/manual\\/en\\/function.date.php\",\"default\":\"j F Y\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES ('50', 'Homepage', 'setting', 'Node link', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"1\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');

-- ----------------------------
-- Table structure for node
-- ----------------------------
DROP TABLE IF EXISTS `node`;
CREATE TABLE `node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nodetype` int(10) unsigned NOT NULL COMMENT 'Node type',
  `title` varchar(255) NOT NULL COMMENT 'Title',
  `lang` varchar(5) NOT NULL COMMENT 'Language',
  `lang_root` int(10) unsigned DEFAULT NULL COMMENT 'Translation of',
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
  `published_start` timestamp NULL DEFAULT NULL COMMENT 'Publish start',
  `published_end` timestamp NULL DEFAULT NULL COMMENT 'Publish end',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `media` (`media`),
  KEY `posttype` (`nodetype`),
  KEY `parent` (`parent`),
  KEY `root` (`root`),
  KEY `lang_root` (`lang_root`),
  CONSTRAINT `node_ibfk_12` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`),
  CONSTRAINT `node_ibfk_14` FOREIGN KEY (`lang_root`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `node_ibfk_15` FOREIGN KEY (`parent`) REFERENCES `node` (`id`),
  CONSTRAINT `node_ibfk_16` FOREIGN KEY (`root`) REFERENCES `node` (`id`),
  CONSTRAINT `node_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  CONSTRAINT `node_ibfk_9` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=398 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of node
-- ----------------------------
INSERT INTO `node` VALUES ('397', '32', 'Welcome to the Ajde Framework', '', null, 'home', null, '2013-08-05 19:25:05', '2013-08-06 15:22:17', '<p>Yet another PHP 5.0 MVC framework with out-of-the-box HTML / CSS / JS / caching and HTTP optimizations. Your project will be fast and cutting edges right from the start!</p>\r\n', null, '292', null, null, '0', '20', '1', '1', null, null);

-- ----------------------------
-- Table structure for node_media
-- ----------------------------
DROP TABLE IF EXISTS `node_media`;
CREATE TABLE `node_media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `media` int(10) unsigned NOT NULL COMMENT 'Media',
  `sort` int(10) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`,`media`),
  KEY `post` (`node`),
  KEY `media` (`media`),
  CONSTRAINT `node_media_ibfk_4` FOREIGN KEY (`media`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `node_media_ibfk_5` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of node_media
-- ----------------------------

-- ----------------------------
-- Table structure for node_meta
-- ----------------------------
DROP TABLE IF EXISTS `node_meta`;
CREATE TABLE `node_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `value` text NOT NULL COMMENT 'Value',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`,`meta`),
  KEY `meta` (`meta`),
  KEY `post` (`node`),
  CONSTRAINT `node_meta_ibfk_6` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `node_meta_ibfk_7` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of node_meta
-- ----------------------------

-- ----------------------------
-- Table structure for node_meta_multiple
-- ----------------------------
DROP TABLE IF EXISTS `node_meta_multiple`;
CREATE TABLE `node_meta_multiple` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `foreign` int(10) unsigned NOT NULL COMMENT 'Foreign',
  `sort` int(10) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`meta`,`foreign`,`node`) USING BTREE,
  KEY `meta` (`meta`) USING BTREE,
  KEY `foreign` (`foreign`) USING BTREE,
  KEY `node` (`node`),
  CONSTRAINT `node_meta_multiple_ibfk_1` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `node_meta_multiple_ibfk_2` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of node_meta_multiple
-- ----------------------------

-- ----------------------------
-- Table structure for node_related
-- ----------------------------
DROP TABLE IF EXISTS `node_related`;
CREATE TABLE `node_related` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `related` int(10) unsigned NOT NULL COMMENT 'Related node',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`,`related`),
  KEY `meta` (`related`),
  KEY `post` (`node`),
  CONSTRAINT `node_related_ibfk_3` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `node_related_ibfk_4` FOREIGN KEY (`related`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of node_related
-- ----------------------------

-- ----------------------------
-- Table structure for node_tag
-- ----------------------------
DROP TABLE IF EXISTS `node_tag`;
CREATE TABLE `node_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node` int(10) unsigned NOT NULL COMMENT 'Node',
  `tag` int(10) unsigned NOT NULL COMMENT 'Tag',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`,`tag`),
  KEY `tag` (`tag`),
  KEY `post` (`node`),
  CONSTRAINT `node_tag_ibfk_4` FOREIGN KEY (`tag`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `node_tag_ibfk_5` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of node_tag
-- ----------------------------

-- ----------------------------
-- Table structure for nodetype
-- ----------------------------
DROP TABLE IF EXISTS `nodetype`;
CREATE TABLE `nodetype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `category` varchar(255) DEFAULT NULL COMMENT 'Category',
  `icon` varchar(255) DEFAULT NULL COMMENT 'Icon',
  `child_type` int(10) unsigned DEFAULT NULL COMMENT 'Default child type',
  `parent_type` int(10) unsigned DEFAULT NULL COMMENT 'Default parent type',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `title` tinyint(4) DEFAULT '1' COMMENT 'Title',
  `subtitle` tinyint(4) DEFAULT '0' COMMENT 'Subtitle',
  `content` tinyint(4) DEFAULT '1' COMMENT 'Content',
  `summary` tinyint(4) DEFAULT '0' COMMENT 'Summary',
  `media` tinyint(4) DEFAULT '1' COMMENT 'Featured image',
  `tag` tinyint(4) DEFAULT '0' COMMENT 'Tags',
  `additional_media` tinyint(4) DEFAULT '0' COMMENT 'Additional media',
  `children` tinyint(4) DEFAULT '0' COMMENT 'Tree structure',
  `published` tinyint(4) DEFAULT '1' COMMENT 'Toggle published',
  `related_nodes` tinyint(4) DEFAULT '0' COMMENT 'Related nodes',
  `required_title` tinyint(4) DEFAULT '1' COMMENT 'Title',
  `required_subtitle` tinyint(4) DEFAULT '0' COMMENT 'Subtitle',
  `required_content` tinyint(4) DEFAULT '1' COMMENT 'Content',
  `required_summary` tinyint(4) DEFAULT '0' COMMENT 'Summary',
  `required_media` tinyint(4) DEFAULT '0' COMMENT 'Featured image',
  `required_tag` tinyint(4) DEFAULT '0' COMMENT 'Tags',
  `required_additional_media` tinyint(4) DEFAULT '0' COMMENT 'Additional media',
  `required_children` tinyint(4) DEFAULT '0' COMMENT 'Tree structure',
  `required_related_nodes` tinyint(4) DEFAULT '0' COMMENT 'Related nodes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `child_type` (`child_type`),
  KEY `parent_type` (`parent_type`),
  CONSTRAINT `nodetype_ibfk_1` FOREIGN KEY (`child_type`) REFERENCES `nodetype` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `nodetype_ibfk_2` FOREIGN KEY (`parent_type`) REFERENCES `nodetype` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of nodetype
-- ----------------------------
INSERT INTO `nodetype` VALUES ('32', 'Homepage', null, null, null, null, '999', '1', null, '1', null, '1', null, null, '1', '1', null, '1', '0', '1', '0', '0', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for nodetype_meta
-- ----------------------------
DROP TABLE IF EXISTS `nodetype_meta`;
CREATE TABLE `nodetype_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nodetype` int(10) unsigned NOT NULL COMMENT 'Node type',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nodetype` (`nodetype`,`meta`),
  KEY `meta` (`meta`),
  KEY `post` (`nodetype`),
  CONSTRAINT `nodetype_meta_ibfk_3` FOREIGN KEY (`nodetype`) REFERENCES `nodetype` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nodetype_meta_ibfk_4` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of nodetype_meta
-- ----------------------------

-- ----------------------------
-- Table structure for product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) NOT NULL COMMENT 'Title',
  `slug` varchar(255) DEFAULT NULL,
  `content` text COMMENT 'Article',
  `image` varchar(255) DEFAULT NULL COMMENT 'Image',
  `unitprice` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Unit price',
  `vat` int(10) unsigned NOT NULL COMMENT 'VAT %',
  `stock` int(11) DEFAULT '0' COMMENT 'Stock',
  `user` int(10) unsigned NOT NULL COMMENT 'Owner',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `published` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Published',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Article updated on',
  PRIMARY KEY (`id`),
  KEY `user` (`user`) USING BTREE,
  KEY `vat` (`vat`) USING BTREE,
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  CONSTRAINT `product_ibfk_2` FOREIGN KEY (`vat`) REFERENCES `vat` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of product
-- ----------------------------

-- ----------------------------
-- Table structure for revision
-- ----------------------------
DROP TABLE IF EXISTS `revision`;
CREATE TABLE `revision` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `model` varchar(255) NOT NULL COMMENT 'Model',
  `user` int(10) unsigned DEFAULT NULL COMMENT 'User',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time',
  `foreignkey` int(10) unsigned NOT NULL COMMENT 'Foreign key',
  `field` varchar(255) NOT NULL COMMENT 'Field',
  `old` text NOT NULL COMMENT 'Old value',
  `new` text NOT NULL COMMENT 'New value',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  CONSTRAINT `revision_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of revision
-- ----------------------------

-- ----------------------------
-- Table structure for setting
-- ----------------------------
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Page',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of setting
-- ----------------------------
INSERT INTO `setting` VALUES ('1', 'Main settings', '999');
INSERT INTO `setting` VALUES ('2', 'Analytics', '999');
INSERT INTO `setting` VALUES ('3', 'Social settings', '999');

-- ----------------------------
-- Table structure for setting_meta
-- ----------------------------
DROP TABLE IF EXISTS `setting_meta`;
CREATE TABLE `setting_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting` int(10) unsigned NOT NULL COMMENT 'Page',
  `meta` int(10) unsigned NOT NULL COMMENT 'Meta',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nodetype` (`setting`,`meta`),
  KEY `meta` (`meta`),
  KEY `post` (`setting`),
  CONSTRAINT `setting_meta_ibfk_4` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `setting_meta_ibfk_5` FOREIGN KEY (`setting`) REFERENCES `setting` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of setting_meta
-- ----------------------------
INSERT INTO `setting_meta` VALUES ('74', '3', '15', '999', 'https://www.facebook.com/example');
INSERT INTO `setting_meta` VALUES ('75', '3', '16', '999', 'https://twitter.com/example');
INSERT INTO `setting_meta` VALUES ('76', '3', '17', '999', 'https://instagram.com/example');
INSERT INTO `setting_meta` VALUES ('77', '3', '18', '999', 'https://www.linkedin.com/in/example');
INSERT INTO `setting_meta` VALUES ('78', '3', '19', '999', 'https://plus.google.com/+example');
INSERT INTO `setting_meta` VALUES ('79', '3', '20', '999', 'https://www.pinterest.com/example');
INSERT INTO `setting_meta` VALUES ('81', '1', '35', '999', 'j F Y');
INSERT INTO `setting_meta` VALUES ('82', '2', '14', '999', 'UA-XXXXXXXX-X');
INSERT INTO `setting_meta` VALUES ('83', '1', '50', '999', null);

-- ----------------------------
-- Table structure for sso
-- ----------------------------
DROP TABLE IF EXISTS `sso`;
CREATE TABLE `sso` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL COMMENT 'User',
  `provider` varchar(255) NOT NULL COMMENT 'Provider',
  `username` varchar(255) DEFAULT NULL COMMENT 'Username at provider',
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Avatar at provider',
  `uid` text NOT NULL COMMENT 'User ID',
  `data` text NOT NULL COMMENT 'Data',
  PRIMARY KEY (`id`),
  KEY `user` (`user`) USING BTREE,
  CONSTRAINT `sso_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of sso
-- ----------------------------

-- ----------------------------
-- Table structure for submission
-- ----------------------------
DROP TABLE IF EXISTS `submission`;
CREATE TABLE `submission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form` int(10) unsigned DEFAULT NULL COMMENT 'Form',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Added',
  `entry` text NOT NULL COMMENT 'Form entry',
  `entry_text` text NOT NULL COMMENT 'Entry text',
  `ip` varchar(255) DEFAULT NULL COMMENT 'IP address',
  `user` int(10) unsigned DEFAULT NULL COMMENT 'User',
  PRIMARY KEY (`id`),
  KEY `post` (`form`),
  KEY `user` (`user`),
  CONSTRAINT `submission_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `submission_ibfk_2` FOREIGN KEY (`form`) REFERENCES `form` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of submission
-- ----------------------------

-- ----------------------------
-- Table structure for tag
-- ----------------------------
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `sort` varchar(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tag
-- ----------------------------

-- ----------------------------
-- Table structure for template
-- ----------------------------
DROP TABLE IF EXISTS `template`;
CREATE TABLE `template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `lang` varchar(5) NOT NULL COMMENT 'Language',
  `lang_root` int(10) unsigned DEFAULT NULL COMMENT 'Translation of',
  `master` int(10) unsigned DEFAULT NULL COMMENT 'Master',
  `style` enum('serif','sans-serif') DEFAULT NULL COMMENT 'Style',
  `subject` varchar(255) NOT NULL COMMENT 'Subject',
  `content` text NOT NULL COMMENT 'Content',
  `markup` text COMMENT 'Markup',
  PRIMARY KEY (`id`),
  KEY `lang_root` (`lang_root`),
  KEY `master` (`master`),
  CONSTRAINT `template_ibfk_1` FOREIGN KEY (`lang_root`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `template_ibfk_2` FOREIGN KEY (`master`) REFERENCES `template` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of template
-- ----------------------------
INSERT INTO `template` VALUES ('1', 'reset link', 'en_GB', null, '3', 'sans-serif', 'Password reset for %sitename%', '<p>You have requested a password reset. Click on the link below to choose a new password.</p>\r\n\r\n<p style=\"text-align: center;\"><a class=\"btn btn-default\" href=\"%resetlink%\"><strong>Reset password</strong></a></p>\r\n\r\n<p>Best regards,<br />\r\nThe %sitename% team</p>\r\n', null);
INSERT INTO `template` VALUES ('3', 'master', 'en_GB', null, null, 'sans-serif', 'master', '<div class=\"wrapper\">\r\n<table align=\"center\" border=\"0\" cellpadding=\"10\" cellspacing=\"10\" style=\"width:600px\">\r\n	<tbody>\r\n		<tr>\r\n			<td style=\"text-align:center\"><img alt=\"\" src=\"assets/media/ajde-medium.png\" style=\"height:52px; width:100px\" /></td>\r\n		</tr>\r\n		<tr>\r\n			<td>%body%</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p style=\"text-align:center\"><span class=\"gray\">A one-time message from <a href=\"#\">%sitename%</a></span></p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>\r\n', null);
INSERT INTO `template` VALUES ('4', 'Form submission', 'en_GB', null, '3', 'sans-serif', 'We have received your submission', '<p>Thank you.&nbsp;We have received your form submission in good shape&nbsp;and we will get back to you as soon as we can.&nbsp;</p>\r\n\r\n<p>%entry%</p>\r\n', null);
INSERT INTO `template` VALUES ('5', 'order', 'en_GB', null, '3', null, 'Your order', '<p>Thank you for your order. Review and track your order online by clicking the following button:</p>\r\n\r\n<p><a class=\"btn btn-default\" href=\"%viewlink%\">View my order</a></p>', null);

-- ----------------------------
-- Table structure for transaction
-- ----------------------------
DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned DEFAULT NULL COMMENT 'User',
  `ip` varchar(255) NOT NULL COMMENT 'IP address',
  `payment_provider` varchar(255) DEFAULT NULL COMMENT 'Provider',
  `payment_status` enum('pending','requested','refused','completed','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Status',
  `payment_details` text COMMENT 'Details',
  `payment_providerid` text COMMENT 'Provider ID',
  `payment_amount` decimal(8,2) NOT NULL COMMENT 'Amount',
  `name` varchar(255) DEFAULT NULL COMMENT 'Name',
  `email` varchar(255) DEFAULT NULL COMMENT 'E-mail',
  `shipment_address` varchar(255) DEFAULT NULL COMMENT 'Address',
  `shipment_zipcode` varchar(255) DEFAULT NULL COMMENT 'Zipcode',
  `shipment_city` varchar(255) DEFAULT NULL COMMENT 'City',
  `shipment_region` varchar(255) DEFAULT NULL COMMENT 'Region',
  `shipment_country` varchar(255) DEFAULT NULL COMMENT 'Country',
  `shipment_status` enum('new','shipped','delivered') NOT NULL DEFAULT 'new' COMMENT 'Status',
  `shipment_itemsqty` int(11) NOT NULL COMMENT 'Quantity',
  `shipment_itemsvatamount` decimal(8,2) NOT NULL COMMENT 'VAT',
  `shipment_itemstotal` decimal(8,2) NOT NULL COMMENT 'Total',
  `shipment_description` text COMMENT 'Description',
  `shipment_method` varchar(255) DEFAULT NULL COMMENT 'Method',
  `shipment_cost` decimal(8,2) NOT NULL COMMENT 'Cost',
  `shipment_trackingcode` text COMMENT 'Tracking code',
  `comment` text COMMENT 'Comment',
  `extra` text COMMENT 'Extra information',
  `secret` varchar(255) NOT NULL COMMENT 'Secret',
  `secret_archive` text COMMENT 'Secret archive',
  `added` timestamp NULL DEFAULT NULL COMMENT 'Added',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Modified',
  PRIMARY KEY (`id`),
  UNIQUE KEY `secret` (`secret`),
  KEY `user` (`user`),
  CONSTRAINT `transaction_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of transaction
-- ----------------------------

-- ----------------------------
-- Table structure for transaction_item
-- ----------------------------
DROP TABLE IF EXISTS `transaction_item`;
CREATE TABLE `transaction_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction` int(10) unsigned NOT NULL COMMENT 'Transaction',
  `entity` varchar(255) NOT NULL COMMENT 'Entity',
  `entity_id` int(10) unsigned NOT NULL COMMENT 'Entity ID',
  `unitprice` decimal(8,2) NOT NULL COMMENT 'Unit price',
  `qty` tinyint(3) unsigned NOT NULL COMMENT 'Quantity',
  PRIMARY KEY (`id`),
  KEY `transaction` (`transaction`) USING BTREE,
  CONSTRAINT `transaction_item_ibfk_1` FOREIGN KEY (`transaction`) REFERENCES `transaction` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of transaction_item
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
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
  `avatar` text COMMENT 'Avatar',
  `secret` varchar(255) NOT NULL COMMENT 'Secret hash',
  `reset_hash` varchar(255) DEFAULT NULL COMMENT 'Reset hash',
  `debug` tinyint(4) DEFAULT '0' COMMENT 'Turn debugging on',
  `tester` tinyint(1) DEFAULT '0' COMMENT 'Tester',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `usergroup` (`usergroup`),
  CONSTRAINT `user_ibfk_3` FOREIGN KEY (`usergroup`) REFERENCES `usergroup` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('20', 'admin', '$2a$10$611167be6e8607a7fab6euCxjM/v92mTP/pwSu5j5Jc4/V4ZHHZqm', '2', 'AUAAv/9Ro3C5pozvIgoxcIpsSkzoFEDpeIvAE7r86vL+h0dBfMUDmgiDWl7Z8deUCbvbxLbK2K2QtD1mMGUSoo5P0aHh', null, null, null, null, null, null, null, '6402649ab41f623fa9d635f4cf261bf3d6004a7a', null, '1', '0');

-- ----------------------------
-- Table structure for user_node
-- ----------------------------
DROP TABLE IF EXISTS `user_node`;
CREATE TABLE `user_node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `node` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `node` (`node`),
  CONSTRAINT `user_node_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_node_ibfk_2` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_node
-- ----------------------------

-- ----------------------------
-- Table structure for usergroup
-- ----------------------------
DROP TABLE IF EXISTS `usergroup`;
CREATE TABLE `usergroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of usergroup
-- ----------------------------
INSERT INTO `usergroup` VALUES ('1', 'users', '1');
INSERT INTO `usergroup` VALUES ('2', 'admins', '4');
INSERT INTO `usergroup` VALUES ('3', 'clients', '2');
INSERT INTO `usergroup` VALUES ('4', 'employees', '3');

-- ----------------------------
-- Table structure for vat
-- ----------------------------
DROP TABLE IF EXISTS `vat`;
CREATE TABLE `vat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `percentage` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vat
-- ----------------------------
INSERT INTO `vat` VALUES ('1', 'High', '21');
INSERT INTO `vat` VALUES ('2', 'Low', '6');
INSERT INTO `vat` VALUES ('3', 'Exempt', '0');
