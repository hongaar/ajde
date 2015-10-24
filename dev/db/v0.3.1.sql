ALTER TABLE `sample` RENAME `product`;

INSERT INTO `vat` (`description`, `percentage`) VALUES ('High', '21');
INSERT INTO `vat` (`description`, `percentage`) VALUES ('Low', '6');
INSERT INTO `vat` (`description`, `percentage`) VALUES ('Exempt', '0');

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

ALTER TABLE `product` ADD COLUMN `stock` int(11) NULL DEFAULT 0 COMMENT 'Stock' AFTER `vat`;

ALTER TABLE `meta` MODIFY COLUMN `type`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Type' AFTER `target`;

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

