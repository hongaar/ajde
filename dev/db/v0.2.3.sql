-- lang to node
ALTER TABLE  `node` ADD  `lang` VARCHAR( 5 ) NOT NULL COMMENT  'Language' AFTER  `title` ,
ADD  `lang_root` INT UNSIGNED NULL DEFAULT NULL COMMENT  'Translation of' AFTER  `lang` ,
ADD INDEX (  `lang_root` );

ALTER TABLE  `node` ADD FOREIGN KEY (  `lang_root` ) REFERENCES  `node` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

-- lang to menu
ALTER TABLE  `menu` ADD  `lang` VARCHAR( 5 ) NOT NULL COMMENT  'Language' AFTER  `name` ,
ADD  `lang_root` INT UNSIGNED NULL DEFAULT NULL COMMENT  'Translation of' AFTER  `lang` ,
ADD INDEX (  `lang_root` );

ALTER TABLE  `menu` ADD FOREIGN KEY (  `lang_root` ) REFERENCES  `menu` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

-- restrict parent deletion
ALTER TABLE  `node` DROP FOREIGN KEY  `node_ibfk_7` ,
ADD FOREIGN KEY (  `parent` ) REFERENCES  `node` (
`id`
) ON DELETE RESTRICT ON UPDATE RESTRICT ;

ALTER TABLE  `node` DROP FOREIGN KEY  `node_ibfk_13` ,
ADD FOREIGN KEY (  `root` ) REFERENCES  `node` (
`id`
) ON DELETE RESTRICT ON UPDATE RESTRICT ;