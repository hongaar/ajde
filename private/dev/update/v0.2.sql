-- allow users to reset their passwords
ALTER TABLE  `user` ADD  `reset_hash` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT  'Reset hash' AFTER  `secret`

-- publish start/end
ALTER TABLE  `node` ADD  `published_start` TIMESTAMP NULL DEFAULT NULL COMMENT  'Publish start' AFTER  `published` ,
ADD  `published_end` TIMESTAMP NULL DEFAULT NULL COMMENT  'Publish end' AFTER  `published_start`

-- publish toggle default on
ALTER TABLE  `nodetype` CHANGE  `published`  `published` TINYINT( 4 ) NULL DEFAULT  '1' COMMENT  'Toggle published'

-- media types
CREATE TABLE IF NOT EXISTS `mediatype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `category` varchar(255) DEFAULT NULL COMMENT 'Category',
  `sort` int(11) NOT NULL DEFAULT '999' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
ALTER TABLE  `media` DROP `nodetype`;
ALTER TABLE  `media` ADD  `mediatype` INT UNSIGNED NULL DEFAULT NULL COMMENT  'Media type' AFTER  `id`, ADD INDEX (  `mediatype` );
ALTER TABLE  `media` ADD FOREIGN KEY (  `mediatype` ) REFERENCES  `mediatype` (`id`) ON DELETE SET NULL ON UPDATE SET NULL ;

-- ajde table + version string
CREATE TABLE IF NOT EXISTS `ajde` (
  `k` text,
  `v` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `ajde` (`k`, `v`) VALUES
('version', 'v0.2');
