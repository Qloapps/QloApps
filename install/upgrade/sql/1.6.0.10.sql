SET NAMES 'utf8';

CREATE TABLE `PREFIX_smarty_cache` (
  `id_smarty_cache` char(40) NOT NULL,
  `name` char(40),
  `cache_id` varchar(254) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id_smarty_cache`),
  KEY `name` (`name`),
  KEY `cache_id` (`cache_id`),
  KEY `modified` (`modified`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_configuration` (`name` , `value` , `date_add` , `date_upd`) VALUES
('PS_SMARTY_CACHING_TYPE', 'filesystem', NOW(), NOW()),
('PS_SMARTY_CLEAR_CACHE', 'everytime', NOW(), NOW()),
('PS_DETECT_LANG', '1', NOW(), NOW()),
('PS_DETECT_COUNTRY', '1', NOW(), NOW());

ALTER TABLE `PREFIX_quick_access` CHANGE `link` `link` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `PREFIX_order_slip_detail` CHANGE `amount_tax_excl` `amount_tax_excl` DECIMAL(20, 6) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_order_slip_detail` CHANGE  `amount_tax_incl`  `amount_tax_incl` DECIMAL( 20, 6 ) NULL DEFAULT NULL;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_ROUND_TYPE', 2, now(), now());

ALTER TABLE `PREFIX_order_slip` ADD `total_products_tax_excl` DECIMAL(20, 6) NULL AFTER `id_order`, ADD `total_products_tax_incl` DECIMAL(20, 6) NULL AFTER `total_products_tax_excl`,ADD `total_shipping_tax_excl` DECIMAL(20, 6) NULL AFTER `total_products_tax_incl`, ADD `total_shipping_tax_incl` DECIMAL(20, 6) NULL AFTER `total_shipping_tax_excl`;
ALTER TABLE `PREFIX_order_slip_detail` ADD `unit_price_tax_excl` DECIMAL(20, 6) NULL AFTER `product_quantity`, ADD `unit_price_tax_incl` DECIMAL(20, 6) NULL AFTER  `unit_price_tax_excl`, ADD `total_price_tax_excl` DECIMAL(20, 6) NULL AFTER `unit_price_tax_incl`, ADD `total_price_tax_incl` DECIMAL(20, 6) NULL AFTER `total_price_tax_excl`;

CREATE TABLE IF NOT EXISTS `PREFIX_order_slip_detail_tax` (
  `id_order_slip_detail` int(11) unsigned NOT NULL,
  `id_tax` int(11) unsigned NOT NULL,
  `unit_amount` decimal(16,6) NOT NULL DEFAULT '0.000000',
  `total_amount` decimal(16,6) NOT NULL DEFAULT '0.000000',
  KEY (`id_order_slip_detail`),
  KEY `id_tax` (`id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_tax_rules_group` ADD `deleted` TINYINT(1) UNSIGNED NOT NULL, ADD `date_add` DATETIME NOT NULL, ADD `date_upd` DATETIME NOT NULL;

UPDATE `PREFIX_tax_rules_group` SET `date_add` = NOW(), `date_upd` = NOW();

ALTER TABLE `PREFIX_order_detail` ADD `id_tax_rules_group` INT(11) UNSIGNED DEFAULT '0' AFTER `product_weight`, ADD INDEX `id_tax_rules_group` (`id_tax_rules_group`);

CREATE TABLE IF NOT EXISTS `PREFIX_mail` (
  `id_mail` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipient` varchar(126) NOT NULL,
  `template` varchar(62) NOT NULL,
  `subject` varchar(254) NOT NULL,
  `id_lang` int(11) unsigned NOT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mail`),
  KEY `recipient` (`recipient`(10))
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_LOG_EMAILS', 1, now(), now());

ALTER TABLE `PREFIX_employee` ADD `last_connection_date` date NOT NULL DEFAULT '0000-00-00';

UPDATE `PREFIX_category` SET `is_root_category` = 0 WHERE `id_parent` != (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_ROOT_CATEGORY' LIMIT 1) AND `is_root_category` = 1;

ALTER TABLE `PREFIX_orders` ADD INDEX (`reference`);

UPDATE `PREFIX_meta` SET `page` = 'pagenotfound' WHERE `page` = '404';
