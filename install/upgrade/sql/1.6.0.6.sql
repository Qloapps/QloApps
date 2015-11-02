SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_invoice_tax` ADD INDEX (`id_tax`);

INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'product', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'product');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'category', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'category');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'cms', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'cms');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'products-comparison', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'products-comparison');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'module-bankwire-payment', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'module-bankwire-payment');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'module-bankwire-validation', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'module-bankwire-validation');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'module-cheque-validation', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'module-cheque-validation');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'module-cheque-payment', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'module-cheque-payment');

INSERT IGNORE INTO `PREFIX_theme_meta` ( `id_theme` , `id_meta` , `left_column` , `right_column` )
  SELECT `PREFIX_theme`.`id_theme` , `PREFIX_meta`.`id_meta` , `default_left_column` , `default_right_column`
  FROM `PREFIX_theme` , `PREFIX_meta`;

ALTER TABLE `PREFIX_tab` ADD `hide_host_mode` tinyint(1) NOT NULL DEFAULT '0' AFTER  `active`;

UPDATE `PREFIX_employee` SET `bo_theme` = 'default';

DELETE FROM `PREFIX_image_type` WHERE `name` = 'cart_default';

INSERT INTO `PREFIX_image_type` (`id_image_type`,`name`,`width`,`height`,`products`,`categories`,`manufacturers`,`suppliers`,`scenes`,`stores`)
VALUES (NULL, 'cart_default', '80', '80', '1', '0', '0', '0', '0', '0');

ALTER TABLE `PREFIX_cart_rule_combination` ADD INDEX `id_cart_rule_1` (`id_cart_rule_1`);
ALTER TABLE `PREFIX_cart_rule_combination` ADD INDEX `id_cart_rule_2` (`id_cart_rule_2`);

/* PHP:p1606module_exceptions(); */;
