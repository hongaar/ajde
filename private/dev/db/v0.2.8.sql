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
