-- meta, add publish 
ALTER TABLE  `meta` CHANGE  `type`  `type` ENUM(  'Text',  'Numeric',  'List of options',  'Node link',  'Media',  'Date',  'Time',  'Timespan',  'Spatial',  'Toggle',  'User', 'Publish',  'Grouper' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  'Type';