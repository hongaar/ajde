-- add grouper to meta types
ALTER TABLE  `meta` CHANGE  `type`  `type` ENUM(  'Text',  'Numeric',  'List of options',  'Node link',  'Media',  'Date',  'Time',  'Timespan',  'Spatial',  'Toggle',  'User', 'Grouper' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  'Type';

-- add icon to nodetype
ALTER TABLE  `nodetype` ADD  `icon` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT  'Icon' AFTER  `category`;