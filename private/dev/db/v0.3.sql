
-- FORM META TABLE

CREATE TABLE `form_meta` (
  `id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `form`  int(10) UNSIGNED NOT NULL COMMENT 'Form' ,
  `meta`  int(10) UNSIGNED NOT NULL COMMENT 'Meta' ,
  `sort`  int(11) NOT NULL DEFAULT 999 COMMENT 'Ordering' ,
  PRIMARY KEY (`id`),
  CONSTRAINT `form_meta_ibfk_1` FOREIGN KEY (`form`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_meta_ibfk_2` FOREIGN KEY (`meta`) REFERENCES `meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE INDEX `nodetype` (`form`, `meta`) USING BTREE ,
  INDEX `meta` (`meta`) USING BTREE ,
  INDEX `post` (`form`) USING BTREE
)
  ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
  ROW_FORMAT=Compact
;

-- ADD SOME FIELDS TO MEDIA

ALTER TABLE `media` ADD COLUMN `caption`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `thumbnail`;
ALTER TABLE `media` ADD COLUMN `href`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `caption`;


-- DEFAULT EMAILS

INSERT INTO `template` VALUES (3, 'master', 'en_GB', NULL, NULL, 'sans-serif', 'master', '<div class=\"wrapper\">\r\n<table align=\"center\" border=\"0\" cellpadding=\"10\" cellspacing=\"10\" style=\"width:600px\">\r\n	<tbody>\r\n		<tr>\r\n			<td style=\"text-align:center\"><img alt=\"\" src=\"public/media/ajde-medium.png\" style=\"height:52px; width:100px\" /></td>\r\n		</tr>\r\n		<tr>\r\n			<td>%body%</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p style=\"text-align:center\"><span class=\"gray\">A one-time message from <a href=\"#\">%sitename%</a></span></p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>\r\n');
INSERT INTO `template` VALUES (1, 'reset link', 'en_GB', NULL, 3, 'sans-serif', 'Password reset for %sitename%', '<p>You have requested a password reset. Click on the link below to choose a new password.</p>\r\n\r\n<p style=\"text-align: center;\"><a class=\"btn btn-default\" href=\"%resetlink%\"><strong>Reset password</strong></a></p>\r\n\r\n<p>Best regards,<br />\r\nThe %sitename% team</p>\r\n');
INSERT INTO `template` VALUES (4, 'Form submission', 'en_GB', NULL, 3, 'sans-serif', 'We have received your submission', '<p>Thank you.&nbsp;We have received your form submission in good shape&nbsp;and we will get back to you as soon as we can.&nbsp;</p>\r\n\r\n<p>%entry%</p>\r\n');


INSERT INTO `email` VALUES (1, 'User reset link', 'user_reset_link', 'transactional', 'user', 1, 'Site name', 'info@example.com');
INSERT INTO `email` VALUES (2, 'Form submission', 'form_submission', 'transactional', 'form', 4, 'Site name', 'info@example.com');


-- SETTINGS

INSERT INTO `setting` VALUES (3, 'Social settings', 999);

INSERT INTO `meta` VALUES (14, 'Google Analytics', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"Tracking ID\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES (15, 'Facebook URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES (16, 'Twitter URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES (17, 'Instagram URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES (18, 'LinkedIn URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES (19, 'Google URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES (20, 'Pinterest URL', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `meta` VALUES (35, 'Date format', 'setting', 'Text', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"See http:\\/\\/php.net\\/manual\\/en\\/function.date.php\",\"default\":\"j F Y\",\"popup\":\"0\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');

INSERT INTO `setting_meta` VALUES (74, 3, 15, 999, 'https://www.facebook.com/example');
INSERT INTO `setting_meta` VALUES (75, 3, 16, 999, 'https://twitter.com/example');
INSERT INTO `setting_meta` VALUES (76, 3, 17, 999, 'https://instagram.com/example');
INSERT INTO `setting_meta` VALUES (77, 3, 18, 999, 'https://www.linkedin.com/in/example');
INSERT INTO `setting_meta` VALUES (78, 3, 19, 999, 'https://plus.google.com/+example');
INSERT INTO `setting_meta` VALUES (79, 3, 20, 999, 'https://www.pinterest.com/example');
INSERT INTO `setting_meta` VALUES (81, 1, 35, 999, 'j F Y');
INSERT INTO `setting_meta` VALUES (82, 2, 14, 999, 'UA-XXXXXXXX-X');

ALTER TABLE `form` ADD COLUMN `submit_text`  varchar(255) NULL DEFAULT 'Submit' AFTER `email_to`;

INSERT INTO `meta` VALUES (50, 'Homepage', 'setting', 'Node link', '{\"required\":\"0\",\"readonly\":\"0\",\"help\":\"\",\"default\":\"\",\"popup\":\"1\",\"list\":\"\",\"usemediatype\":\"\",\"usenodetype\":\"\",\"length\":\"255\",\"default_toggle\":\"0\",\"twitter_consumerkey\":\"\",\"twitter_consumersecret\":\"\",\"twitter_token\":\"\",\"twitter_tokensecret\":\"\",\"media\":\"\",\"height\":\"10\",\"wysiwyg\":\"0\"}');
INSERT INTO `setting_meta` VALUES (83, 1, 50, 999, null);

ALTER TABLE `template` ADD COLUMN `markup`  text NULL COMMENT 'Markup' AFTER `content`;

ALTER TABLE `email` MODIFY COLUMN `from_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Name' AFTER `template`,
MODIFY COLUMN `from_email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'E-mail address' AFTER `from_name`;

