-- DEFAULT EMAILS

INSERT INTO `template` VALUES (5, 'order', 'en_GB', NULL, 3, null, 'Your order', '<p>Thank you for your order. Review and track your order online by clicking the following button:</p>\r\n\r\n<p><a class="btn btn-default" href="%viewlink%">View my order</a></p>', null);

INSERT INTO `email` VALUES (3, 'Your order', 'your_order', 'transactional', 'shop', 5, null, null);

