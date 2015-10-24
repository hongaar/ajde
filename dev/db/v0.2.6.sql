ALTER TABLE  `nodetype` ADD `required_title` tinyint(4) DEFAULT '1' COMMENT 'Title';
ALTER TABLE  `nodetype` ADD `required_subtitle` tinyint(4) DEFAULT '0' COMMENT 'Subtitle';
ALTER TABLE  `nodetype` ADD `required_content` tinyint(4) DEFAULT '1' COMMENT 'Content';
ALTER TABLE  `nodetype` ADD `required_summary` tinyint(4) DEFAULT '0' COMMENT 'Summary';
ALTER TABLE  `nodetype` ADD `required_media` tinyint(4) DEFAULT '0' COMMENT 'Featured image';
ALTER TABLE  `nodetype` ADD `required_tag` tinyint(4) DEFAULT '0' COMMENT 'Tags';
ALTER TABLE  `nodetype` ADD `required_additional_media` tinyint(4) DEFAULT '0' COMMENT 'Additional media';
ALTER TABLE  `nodetype` ADD `required_children` tinyint(4) DEFAULT '0' COMMENT 'Tree structure';
ALTER TABLE  `nodetype` ADD `required_related_nodes` tinyint(4) DEFAULT '0' COMMENT 'Related nodes';