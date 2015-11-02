SET NAMES 'utf8';

ALTER TABLE `PREFIX_employee`
	MODIFY COLUMN `id_last_order` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	MODIFY COLUMN `id_last_customer_message` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	MODIFY COLUMN `id_last_customer` INT(10) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `PREFIX_employee` ADD `default_tab` int(10) unsigned NOT NULL default 0 AFTER `bo_theme`;

DROP TABLE `PREFIX_subdomain`;

/* PHP:migrate_tabs_15(); */;

DROP TABLE IF EXISTS `PREFIX_order_tax`;

