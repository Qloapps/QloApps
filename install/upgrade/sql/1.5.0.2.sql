SET NAMES 'utf8';

DELETE FROM `PREFIX_tab` WHERE `id_tab` = 59;
DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` = 59 AND `id_lang` = 1;
DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` = 59 AND `id_lang` = 2;
DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` = 59 AND `id_lang` = 3;
DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` = 59 AND `id_lang` = 4;
DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` = 59 AND `id_lang` = 5;

ALTER TABLE `PREFIX_module` ADD `version` VARCHAR( 8 ) NOT NULL;

CREATE TABLE IF NOT EXISTS `PREFIX_specific_price_rule` (
	`id_specific_price_rule` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`id_shop` int(11) unsigned NOT NULL DEFAULT '1',
	`id_currency` int(10) unsigned NOT NULL,
	`id_country` int(10) unsigned NOT NULL,
	`id_group` int(10) unsigned NOT NULL,
	`from_quantity` mediumint(8) unsigned NOT NULL,
	`price` DECIMAL(20,6),
	`reduction` decimal(20,6) NOT NULL,
	`reduction_type` enum('amount','percentage') NOT NULL,
	`from` datetime NOT NULL,
	`to` datetime NOT NULL,
	PRIMARY KEY (`id_specific_price_rule`),
	KEY `id_product` (`id_shop`,`id_currency`,`id_country`,`id_group`,`from_quantity`,`from`,`to`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_specific_price_rule_condition_group` (
	`id_specific_price_rule_condition_group` INT(11) UNSIGNED NOT NULL,
	`id_specific_price_rule` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY ( `id_specific_price_rule_condition_group`, `id_specific_price_rule` )
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_specific_price_rule_condition` (
	`id_specific_price_rule_condition` INT(11) UNSIGNED NOT NULL,
	`id_specific_price_rule_condition_group` INT(11) UNSIGNED NOT NULL,
	`type` VARCHAR(255) NOT NULL,
	`value` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id_specific_price_rule_condition`),
INDEX (`id_specific_price_rule_condition_group`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_specific_price` ADD `id_specific_price_rule` INT(11) UNSIGNED NOT NULL AFTER `id_specific_price`, ADD INDEX (`id_specific_price_rule`);
/* PHP:add_new_tab(AdminSpecificPriceRule, es:Catalog price rules|it:Catalog price rules|en:Catalog price rules|de:Catalog price rules|fr:Règles de prix catalogue,  1); */;

ALTER TABLE `PREFIX_order_invoice` ADD `note` TEXT NOT NULL AFTER `total_wrapping_tax_incl`;

/* ORDER STATES */
ALTER TABLE `PREFIX_order_state` ADD `paid` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `shipped`;
UPDATE `PREFIX_order_state` SET `paid` = 1 WHERE `id_order_state` IN (2, 3, 4, 5, 9, 12);

/* SPECIFIC PRICE */
ALTER TABLE `PREFIX_specific_price` ADD `id_customer` INT UNSIGNED NOT NULL AFTER `id_group`;
ALTER TABLE `PREFIX_specific_price` DROP INDEX `id_product` ,
ADD INDEX `id_product` (`id_product`, `id_shop`, `id_currency`, `id_country`, `id_group`, `id_customer`, `from_quantity`, `from`, `to`);

/************************
 * STOCK MANAGEMENT
*************************/

/* PHP:add_stock_tab(); */;

UPDATE `PREFIX_access` SET `view` = '1' WHERE `id_profile` = 5 AND `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminStock' LIMIT 1);

UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminSupplyOrders' LIMIT 1) AND `id_profile` = '4';
UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminSupplyOrders' LIMIT 1) AND `id_profile` = '5';
UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminStockConfiguration' LIMIT 1) AND `id_profile` = '4';
UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminStockConfiguration' LIMIT 1) AND `id_profile` = '5';

/* New tables */
CREATE TABLE IF NOT EXISTS `PREFIX_product_supplier` (
  `id_product_supplier` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) unsigned NOT NULL,
  `id_product_attribute` int(11) unsigned NOT NULL DEFAULT '0',
  `id_supplier` int(11) unsigned NOT NULL,
  `product_supplier_reference` varchar(32) DEFAULT NULL,
  `product_supplier_price_te` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `id_currency` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_product_supplier`),
  UNIQUE KEY `id_product` (`id_product`,`id_product_attribute`,`id_supplier`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_stock_available` (
  `id_stock_available` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) unsigned NOT NULL,
  `id_product_attribute` int(11) unsigned NOT NULL,
  `id_shop` int(11) unsigned NOT NULL,
  `id_group_shop` int(11) unsigned NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT '0',
  `depends_on_stock` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `out_of_stock` int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stock_available`),
  KEY `id_shop` (`id_shop`),
  KEY `id_group_shop` (`id_group_shop`),
  KEY `id_product` (`id_product`),
  KEY `id_product_attribute` (`id_product_attribute`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_supply_order` (
  `id_supply_order` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) unsigned NOT NULL,
  `supplier_name` varchar(64) NOT NULL,
  `id_lang` int(11) unsigned NOT NULL,
  `id_warehouse` int(11) unsigned NOT NULL,
  `id_supply_order_state` int(11) unsigned NOT NULL,
  `id_currency` int(11) unsigned NOT NULL,
  `id_ref_currency` int(11) unsigned NOT NULL,
  `reference` varchar(32) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `date_delivery_expected` datetime DEFAULT NULL,
  `total_te` decimal(20,6) DEFAULT '0.000000',
  `total_with_discount_te` decimal(20,6) DEFAULT '0.000000',
  `total_tax` decimal(20,6) DEFAULT '0.000000',
  `total_ti` decimal(20,6) DEFAULT '0.000000',
  `discount_rate` decimal(20,6) DEFAULT '0.000000',
  `discount_value_te` decimal(20,6) DEFAULT '0.000000',
  `is_template` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_supply_order`),
  KEY `id_supplier` (`id_supplier`),
  KEY `id_warehouse` (`id_warehouse`),
  KEY `reference` (`reference`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_supply_order_detail` (
  `id_supply_order_detail` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_supply_order` int(11) unsigned NOT NULL,
  `id_currency` int(11) unsigned NOT NULL,
  `id_product` int(11) unsigned NOT NULL,
  `id_product_attribute` int(11) unsigned NOT NULL,
  `reference` varchar(32) NOT NULL,
  `supplier_reference` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  `ean13` varchar(13) DEFAULT NULL,
  `upc` varchar(12) DEFAULT NULL,
  `exchange_rate` decimal(20,6) DEFAULT '0.000000',
  `unit_price_te` decimal(20,6) DEFAULT '0.000000',
  `quantity_expected` int(11) unsigned NOT NULL,
  `quantity_received` int(11) unsigned NOT NULL,
  `price_te` decimal(20,6) DEFAULT '0.000000',
  `discount_rate` decimal(20,6) DEFAULT '0.000000',
  `discount_value_te` decimal(20,6) DEFAULT '0.000000',
  `price_with_discount_te` decimal(20,6) DEFAULT '0.000000',
  `tax_rate` decimal(20,6) DEFAULT '0.000000',
  `tax_value` decimal(20,6) DEFAULT '0.000000',
  `price_ti` decimal(20,6) DEFAULT '0.000000',
  `tax_value_with_order_discount` decimal(20,6) DEFAULT '0.000000',
  `price_with_order_discount_te` decimal(20,6) DEFAULT '0.000000',
  PRIMARY KEY (`id_supply_order_detail`),
  KEY `id_supply_order` (`id_supply_order`),
  KEY `id_product` (`id_product`),
  KEY `id_product_attribute` (`id_product_attribute`),
  KEY `id_product_product_attribute` (`id_product`,`id_product_attribute`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_supply_order_history` (
  `id_supply_order_history` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_supply_order` int(11) unsigned NOT NULL,
  `id_employee` int(11) unsigned NOT NULL,
  `employee_lastname` varchar(32) DEFAULT '',
  `employee_firstname` varchar(32) DEFAULT '',
  `id_state` int(11) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_supply_order_history`),
  KEY `id_supply_order` (`id_supply_order`),
  KEY `id_employee` (`id_employee`),
  KEY `id_state` (`id_state`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_supply_order_receipt_history` (
  `id_supply_order_receipt_history` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_supply_order_detail` int(11) unsigned NOT NULL,
  `id_employee` int(11) unsigned NOT NULL,
  `employee_lastname` varchar(32) DEFAULT '',
  `employee_firstname` varchar(32) DEFAULT '',
  `id_supply_order_state` int(11) unsigned NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_supply_order_receipt_history`),
  KEY `id_supply_order_detail` (`id_supply_order_detail`),
  KEY `id_supply_order_state` (`id_supply_order_state`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_supply_order_state` (
  `id_supply_order_state` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_note` tinyint(1) NOT NULL DEFAULT '0',
  `editable` tinyint(1) NOT NULL DEFAULT '0',
  `receipt_state` tinyint(1) NOT NULL DEFAULT '0',
  `pending_receipt` tinyint(1) NOT NULL DEFAULT '0',
  `enclosed` tinyint(1) NOT NULL DEFAULT '0',
  `color` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id_supply_order_state`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_supply_order_state_lang` (
  `id_supply_order_state` int(11) unsigned NOT NULL,
  `id_lang` int(11) unsigned NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_supply_order_state`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_warehouse` (
  `id_warehouse` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_currency` int(11) unsigned NOT NULL,
  `id_address` int(11) unsigned NOT NULL,
  `id_employee` int(11) unsigned NOT NULL,
  `reference` varchar(32) DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `management_type` enum('WA','FIFO','LIFO') NOT NULL DEFAULT 'WA',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_warehouse`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_warehouse_carrier` (
  `id_carrier` int(11) unsigned NOT NULL,
  `id_warehouse` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_warehouse`,`id_carrier`),
  KEY `id_warehouse` (`id_warehouse`),
  KEY `id_carrier` (`id_carrier`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_warehouse_product_location` (
  `id_warehouse_product_location` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) unsigned NOT NULL,
  `id_product_attribute` int(11) unsigned NOT NULL,
  `id_warehouse` int(11) unsigned NOT NULL,
  `location` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_warehouse_product_location`),
  UNIQUE KEY `id_product` (`id_product`,`id_product_attribute`,`id_warehouse`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_warehouse_shop` (
  `id_shop` int(11) unsigned NOT NULL,
  `id_warehouse` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_warehouse`,`id_shop`),
  KEY `id_warehouse` (`id_warehouse`),
  KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* Update records before alter tables */
/* PHP:set_stock_available(); */;
/* PHP:set_product_suppliers(); */;

/* Update tables */
DROP TABLE IF EXISTS `PREFIX_stock`;
CREATE TABLE IF NOT EXISTS `PREFIX_stock` (
`id_stock` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_warehouse` INT(11) UNSIGNED NOT NULL,
`id_product` INT(11) UNSIGNED NOT NULL,
`id_product_attribute` INT(11) UNSIGNED NOT NULL,
`reference`  VARCHAR(32) NOT NULL,
`ean13`  VARCHAR(13) DEFAULT NULL,
`upc`  VARCHAR(12) DEFAULT NULL,
`physical_quantity` INT(11) UNSIGNED NOT NULL,
`usable_quantity` INT(11) UNSIGNED NOT NULL,
`price_te` DECIMAL(20,6) DEFAULT '0.000000',
  PRIMARY KEY (`id_stock`),
  KEY `id_warehouse` (`id_warehouse`),
  KEY `id_product` (`id_product`),
  KEY `id_product_attribute` (`id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX_stock_mvt`;
CREATE TABLE IF NOT EXISTS `PREFIX_stock_mvt` (
  `id_stock_mvt` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_stock` INT(11) UNSIGNED NOT NULL,
  `id_order` INT(11) UNSIGNED DEFAULT NULL,
  `id_supply_order` INT(11) UNSIGNED DEFAULT NULL,
  `id_stock_mvt_reason` INT(11) UNSIGNED NOT NULL,
  `id_employee` INT(11) UNSIGNED NOT NULL,
  `employee_lastname` varchar(32) DEFAULT '',
  `employee_firstname` varchar(32) DEFAULT '',
  `physical_quantity` INT(11) UNSIGNED NOT NULL,
  `date_add` DATETIME NOT NULL,
  `sign` tinyint(1) NOT NULL DEFAULT 1,
  `price_te` DECIMAL(20,6) DEFAULT '0.000000',
  `last_wa` DECIMAL(20,6) DEFAULT '0.000000',
  `current_wa` DECIMAL(20,6) DEFAULT '0.000000',
  `referer` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_stock_mvt`),
  KEY `id_stock` (`id_stock`),
  KEY `id_stock_mvt_reason` (`id_stock_mvt_reason`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_orders` DROP COLUMN `id_warehouse`;
ALTER TABLE `PREFIX_supplier` ADD COLUMN `id_address` int(10) unsigned NOT NULL default '0' AFTER `id_supplier`;
ALTER TABLE `PREFIX_address` ADD COLUMN `id_warehouse` int(10) unsigned NOT NULL DEFAULT 0 AFTER `id_supplier`;
ALTER TABLE `PREFIX_order_detail` ADD COLUMN `id_warehouse` int(10) unsigned NOT NULL default '0' AFTER `id_order_invoice`;


ALTER TABLE `PREFIX_stock_mvt_reason` ADD COLUMN `deleted` tinyint(1) unsigned NOT NULL default '0' AFTER `date_upd`;
ALTER TABLE `PREFIX_product` ADD COLUMN `advanced_stock_management` tinyint(1) default '0' NOT NULL;

/* Update records after alter tables */
/* PHP:update_stock_mvt_reason(); */;

DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_PDF_ENCODING';
DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_PDF_FONT';


ALTER TABLE  `PREFIX_order_detail`
ADD `reduction_amount_tax_incl` DECIMAL( 20, 6 ) NOT NULL AFTER  `reduction_amount` ,
ADD `reduction_amount_tax_excl` DECIMAL( 20, 6 ) NOT NULL AFTER  `reduction_amount_tax_incl`,
ADD `total_price_tax_incl` DECIMAL(20, 6) NOT NULL AFTER  `download_deadline`,
ADD `total_price_tax_excl` DECIMAL(20, 6) NOT NULL AFTER  `total_price_tax_incl`,
ADD `unit_price_tax_incl` DECIMAL(20, 6) NOT NULL AFTER  `total_price_tax_excl`,
ADD `unit_price_tax_excl` DECIMAL(20, 6) NOT NULL AFTER  `unit_price_tax_incl`,
ADD `total_shipping_price_tax_incl` DECIMAL(20, 6) NOT NULL AFTER  `unit_price_tax_excl`,
ADD `total_shipping_price_tax_excl` DECIMAL(20, 6) NOT NULL AFTER  `total_shipping_price_tax_incl`,
ADD `purchase_supplier_price` DECIMAL(20, 6) NOT NULL AFTER  `total_shipping_price_tax_excl`,
ADD `original_product_price` DECIMAL(20, 6) NOT NULL AFTER  `purchase_supplier_price`;

ALTER TABLE  `PREFIX_orders`
ADD `total_discounts_tax_excl` decimal(17,2) NOT NULL AFTER  `total_discounts`,
ADD `total_discounts_tax_incl` decimal(17,2) NOT NULL AFTER  `total_discounts_tax_excl`,
ADD `total_paid_tax_excl` decimal(17,2) NOT NULL AFTER  `total_paid`,
ADD `total_paid_tax_incl` decimal(17,2) NOT NULL AFTER  `total_paid_tax_excl`,
ADD `total_shipping_tax_excl` decimal(17,2) NOT NULL AFTER  `total_shipping`,
ADD `total_shipping_tax_incl` decimal(17,2) NOT NULL AFTER  `total_shipping_tax_excl`,
ADD `total_wrapping_tax_excl` decimal(17,2) NOT NULL AFTER  `total_wrapping`,
ADD `total_wrapping_tax_incl` decimal(17,2) NOT NULL AFTER  `total_wrapping_tax_excl`;

ALTER TABLE `PREFIX_order_cart_rule` ADD `value_tax_excl` DECIMAL(17, 2) NOT NULL DEFAULT '0.00';
ALTER TABLE `PREFIX_order_cart_rule` ADD `id_order_invoice` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `id_cart_rule`;

ALTER TABLE `PREFIX_specific_price` ADD `id_group_shop` INT(11) UNSIGNED NOT NULL AFTER `id_shop`;

/* Generate order references */
UPDATE `PREFIX_orders` SET `reference` = id_order;

ALTER TABLE `PREFIX_order_detail` ADD `tax_computation_method` tinyint(1) unsigned NOT NULL default '0' AFTER `product_weight`;

/* PHP:migrate_orders(); */;

ALTER TABLE `PREFIX_order_invoice` ADD `delivery_number` int(0) NOT NULL DEFAULT '0' AFTER `number`;
ALTER TABLE `PREFIX_order_invoice` ADD `delivery_date` datetime AFTER `delivery_number`;

INSERT INTO `PREFIX_order_invoice` (`id_order`, `number`, `total_discount_tax_excl`, `total_discount_tax_incl`, `total_paid_tax_excl`, `total_paid_tax_incl`, `total_products`, `total_products_wt`, `total_shipping_tax_excl`, `total_shipping_tax_incl`, `total_wrapping_tax_excl`, `total_wrapping_tax_incl`, `note`, `date_add`) (
	SELECT `id_order`, `invoice_number`, `total_discounts_tax_excl`, `total_discounts_tax_incl`, `total_paid_tax_excl`, `total_paid_tax_incl`, `total_products`, `total_products_wt`, `total_shipping_tax_excl`, `total_shipping_tax_incl`, `total_wrapping_tax_excl`, `total_wrapping_tax_incl`, '', `invoice_date`
	FROM `PREFIX_orders`
	WHERE `invoice_number` != 0
);

ALTER TABLE `PREFIX_tab` ADD `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1';

UPDATE `PREFIX_order_detail` od
SET od.`id_order_invoice` = IFNULL((
	SELECT oi.`id_order_invoice`
	FROM `PREFIX_order_invoice` oi
	WHERE oi.`id_order` = od.`id_order` LIMIT 1
), 0);

INSERT INTO `PREFIX_order_carrier` (`id_order`, `id_carrier`, `id_order_invoice`, `weight`, `shipping_cost_tax_excl`, `shipping_cost_tax_incl`, `tracking_number`, `date_add`) (
	SELECT `id_order`, `id_carrier`, (
		SELECT oi.`id_order_invoice`
		FROM `PREFIX_order_invoice` oi
		WHERE oi.`id_order` = o.`id_order` LIMIT 1
	), (
		SELECT SUM(`product_weight`)
		FROM `PREFIX_order_detail` od
		WHERE od.`id_order` = o.`id_order` LIMIT 1
	), `total_shipping_tax_excl`, `total_shipping_tax_incl`, `shipping_number`, `date_add`
	FROM `PREFIX_orders` o
);

INSERT IGNORE INTO `PREFIX_order_payment` (`id_order_invoice`, `id_order`, `id_currency`, `amount`, `payment_method`, `conversion_rate`, `date_add`)
	(
		SELECT
		(
			SELECT oi.`id_order_invoice`
			  FROM `PREFIX_order_invoice` oi
			  WHERE oi.`id_order` = o.`id_order` LIMIT 1
		),
		o.`id_order`, o.`id_currency`, o.`total_paid_real`, o.`payment`, o.`conversion_rate`, o.`date_add`
		FROM `PREFIX_orders` o
		LEFT JOIN `PREFIX_order_payment` op ON (op.`id_order` = o.`id_order`)
		WHERE op.`id_order_payment` IS NULL
	);

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_SMARTY_CONSOLE', '0', NOW(), NOW()),('PS_INVOICE_MODEL', 'invoice', NOW(), NOW());
ALTER TABLE `PREFIX_specific_price` ADD `id_cart` INT(11) UNSIGNED NOT NULL AFTER `id_specific_price_rule`;
ALTER TABLE `PREFIX_specific_price` ADD INDEX `id_cart` (`id_cart`);
/* PHP:update_modules_multishop(); */;

UPDATE `PREFIX_tab`
SET `position` = (
	SELECT `position` FROM (
		SELECT MAX(`position`)+1 as `position`
		FROM `PREFIX_tab`
		WHERE `id_parent` = 0
	) tmp
)
WHERE `class_name` = 'AdminStock';

UPDATE `PREFIX_tab`
SET `position` = (
	SELECT `position` FROM (
		SELECT MAX(`position`)+1 as `position`
		FROM `PREFIX_tab`
		WHERE `id_parent` = 0
	) tmp
)
WHERE `class_name` = 'AdminAccounting';

ALTER TABLE `PREFIX_order_slip_detail` CHANGE `amount` `amount_tax_excl` DECIMAL( 10, 2 ) default NULL;
ALTER TABLE `PREFIX_order_slip_detail` ADD COLUMN `amount_tax_incl` DECIMAL(10,2) default NULL AFTER `amount_tax_excl`;
/* PHP:drop_image_type_non_unique_index(); */;
ALTER TABLE `PREFIX_image_type` ADD `id_theme` INT(11) NOT NULL AFTER `id_image_type`;
ALTER TABLE `PREFIX_image_type` ADD UNIQUE (`id_theme` ,`name`);
UPDATE `PREFIX_image_type` SET `id_theme`=1;

CREATE TABLE IF NOT EXISTS `PREFIX_webservice_account_shop` (
`id_webservice_account` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_webservice_account` , `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_group` ADD `show_prices` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `price_display_method`;

