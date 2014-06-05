--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;


ALTER TABLE `media` CHANGE `type` `type` ENUM('unknown','image','file','embed') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unknown' COMMENT 'Type';

ALTER TABLE `media` CHANGE `mediatype` `mediatype` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Category';


-- --------------------------------------------------------

--
-- Table structure for table `email`
--

CREATE TABLE IF NOT EXISTS `email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `identifier` varchar(255) DEFAULT NULL COMMENT 'Identifier',
  `type` enum('transactional','manual') NOT NULL COMMENT 'Type',
  `module` varchar(255) DEFAULT NULL COMMENT 'Module',
  `template` int(10) unsigned DEFAULT NULL COMMENT 'Template',
  `from_name` varchar(255) NOT NULL COMMENT 'Name',
  `from_email` varchar(255) NOT NULL COMMENT 'E-mail address',
  PRIMARY KEY (`id`),
  KEY `template` (`template`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

CREATE TABLE IF NOT EXISTS `template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `lang` varchar(5) NOT NULL COMMENT 'Language',
  `lang_root` int(10) unsigned DEFAULT NULL COMMENT 'Translation of',
  `subject` varchar(255) NOT NULL COMMENT 'Subject',
  `content` text NOT NULL COMMENT 'Content',
  PRIMARY KEY (`id`),
  KEY `lang_root` (`lang_root`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email`
--
ALTER TABLE `email`
  ADD CONSTRAINT `email_ibfk_1` FOREIGN KEY (`template`) REFERENCES `template` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `template`
--
ALTER TABLE `template`
  ADD CONSTRAINT `template_ibfk_1` FOREIGN KEY (`lang_root`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE  `template` ADD  `master` INT UNSIGNED NULL DEFAULT NULL AFTER  `lang_root` ,
ADD INDEX (  `master` );

ALTER TABLE  `template` ADD FOREIGN KEY (  `master` ) REFERENCES  `template` (
`id`
) ON DELETE SET NULL ON UPDATE SET NULL ;

ALTER TABLE  `template` ADD  `style` ENUM(  'serif',  'sans-serif' ) NOT NULL DEFAULT  'sans-serif' COMMENT  'Style' AFTER  `master`;

ALTER TABLE  `template` CHANGE  `master`  `master` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Master';

ALTER TABLE  `template` CHANGE  `style`  `style` ENUM(  'serif',  'sans-serif' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  'Style';

ALTER TABLE  `meta` CHANGE  `target`  `target` ENUM(  'node',  'setting',  'form' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'node' COMMENT  'Targeting';

ALTER TABLE `meta` CHANGE `type` `type` ENUM('Text','Numeric','List of options','Node link','Media','Date','Time','Timespan','Spatial','Toggle','User','Publish','Grouper','Form') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Type';

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE IF NOT EXISTS `form` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `email` int(10) unsigned DEFAULT NULL COMMENT 'E-mail',
  `email_to` int(10) unsigned DEFAULT NULL COMMENT 'Recipient field',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `email_to` (`email_to`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `submission`
--

CREATE TABLE IF NOT EXISTS `submission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form` int(10) unsigned DEFAULT NULL COMMENT 'Form',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Added',
  `entry` text NOT NULL COMMENT 'Form entry',
  `entry_text` text NOT NULL COMMENT 'Entry text',
  `ip` varchar(255) DEFAULT NULL COMMENT 'IP address',
  `user` int(10) unsigned DEFAULT NULL COMMENT 'User',
  PRIMARY KEY (`id`),
  KEY `post` (`form`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `form`
--
ALTER TABLE `form`
  ADD CONSTRAINT `form_ibfk_1` FOREIGN KEY (`email`) REFERENCES `email` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `form_ibfk_2` FOREIGN KEY (`email_to`) REFERENCES `meta` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `submission`
--
ALTER TABLE `submission`
  ADD CONSTRAINT `submission_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `submission_ibfk_2` FOREIGN KEY (`form`) REFERENCES `form` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;


ALTER TABLE `transaction` ADD COLUMN `added`  timestamp NULL DEFAULT NULL COMMENT 'Added' AFTER `secret_archive`;
ALTER TABLE `user` ADD COLUMN `tester`  tinyint(1) NULL DEFAULT 0 COMMENT 'Tester' AFTER `debug`;


-- --------------------------------------------------------

--
-- Table structure for table `mailerlog`
--

CREATE TABLE IF NOT EXISTS `mailerlog` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;



-- --------------------------------------------------------

--
-- Table structure for table `revision`
--

CREATE TABLE IF NOT EXISTS `revision` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `model` varchar(255) NOT NULL COMMENT 'Model',
  `user` int(10) unsigned DEFAULT NULL COMMENT 'User',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time',
  `foreignkey` int(10) unsigned NOT NULL COMMENT 'Foreign key',
  `field` varchar(255) NOT NULL COMMENT 'Field',
  `old` text NOT NULL COMMENT 'Old value',
  `new` text NOT NULL COMMENT 'New value',
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=151 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `revision`
--
ALTER TABLE `revision`
  ADD CONSTRAINT `revision_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
