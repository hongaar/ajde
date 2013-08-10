-- allow users to reset their passwords
ALTER TABLE  `user` ADD  `reset_hash` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT  'Reset hash' AFTER  `secret`

-- publish start/end
ALTER TABLE  `node` ADD  `published_start` TIMESTAMP NULL DEFAULT NULL COMMENT  'Publish start' AFTER  `published` ,
ADD  `published_end` TIMESTAMP NULL DEFAULT NULL COMMENT  'Publish end' AFTER  `published_start`

-- publish toggle default on
ALTER TABLE  `nodetype` CHANGE  `published`  `published` TINYINT( 4 ) NULL DEFAULT  '1' COMMENT  'Toggle published'