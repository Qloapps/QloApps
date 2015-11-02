SET NAMES 'utf8';

INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'product', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'product');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'index', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'index');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'category', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'category');
INSERT INTO `PREFIX_meta` (`page`, `configurable`) SELECT * FROM (SELECT 'cms', '0') AS tmp WHERE NOT EXISTS (SELECT `page` FROM `PREFIX_meta` WHERE `page` = 'cms');

ALTER TABLE `PREFIX_employee` ADD `optin` tinyint(1) unsigned NOT NULL default '1' AFTER `active`;

ALTER IGNORE TABLE `PREFIX_meta` ADD UNIQUE (`page`);
ALTER TABLE `PREFIX_meta` DROP INDEX `meta_name`;

UPDATE `PREFIX_orders` SET module = 'free_order' WHERE total_paid = 0 AND module LIKE '';

UPDATE `PREFIX_tab` SET `position` = '1' WHERE `id_tab` =48;
UPDATE `PREFIX_tab` SET `position` = '0' WHERE `id_tab` =49;

ALTER TABLE `PREFIX_theme` CHANGE `product_per_page` `product_per_page` INT( 10 ) UNSIGNED NOT NULL DEFAULT '1';

ALTER TABLE  `PREFIX_customer_message` ADD  `date_upd` DATETIME NOT NULL AFTER  `date_add`;

ALTER TABLE  `PREFIX_employee` ADD  `preselect_date_range` VARCHAR( 32 ) NULL DEFAULT NULL AFTER  `stats_compare_option`;

/* PHP:ps1604_update_employee_date(); */;
