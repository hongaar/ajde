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


ALTER TABLE `media` CHANGE `type` `type` ENUM('unknown','image','file','embed') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unknown' COMMENT 'Type'

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
