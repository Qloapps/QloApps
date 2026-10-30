SET NAMES 'utf8';

CREATE TABLE `PREFIX_cart_rule_hotel` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_hotel` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id_cart_rule`, `id_hotel`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_tax_configuration` (
  `id_tax` int(11) unsigned NOT NULL,
  `tax_value` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `calculation_type` tinyint(1) NOT NULL DEFAULT '0',
  `per_night` tinyint(1) NOT NULL DEFAULT '1',
  `per_person` tinyint(1) NOT NULL DEFAULT '0',
  `has_tiered_pricing` tinyint(1) NOT NULL DEFAULT '0',
  `apply_on_child` tinyint(1) NOT NULL DEFAULT '0',
  `has_child_age_range` tinyint(1) NOT NULL DEFAULT '0',
  `child_calculation_type` tinyint(1) NOT NULL DEFAULT '0',
  `has_multiple_valid_ranges` tinyint(1) NOT NULL DEFAULT '0',
  `special_days` text,
  PRIMARY KEY (`id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_tax_price_tier` (
  `id_tax_price_tier` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_tax` int(11) unsigned NOT NULL,
  `min_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `max_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `tax_value` decimal(20,6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`id_tax_price_tier`),
  KEY `id_tax` (`id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_tax_child_range` (
  `id_tax_child_range` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_tax` int(11) unsigned NOT NULL,
  `min_age` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_age` tinyint(3) unsigned NOT NULL DEFAULT '17',
  `tax_value` decimal(20,6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`id_tax_child_range`),
  KEY `id_tax` (`id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_tax_validity_range` (
  `id_tax_validity_range` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_tax` int(11) unsigned NOT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  PRIMARY KEY (`id_tax_validity_range`),
  KEY `id_tax` (`id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_order_tax_detail` (
  `id_order_tax_detail` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(11) unsigned NOT NULL,
  `id_order_detail` int(11) unsigned NOT NULL,
  `id_htl_booking` int(11) unsigned NOT NULL,
  `id_service_product_order_detail` int(11) unsigned NOT NULL DEFAULT '0',
  `id_tax` int(11) unsigned NOT NULL,
  `unit_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_order_tax_detail`),
  KEY `id_order` (`id_order`),
  KEY `id_order_detail` (`id_order_detail`),
  KEY `id_htl_booking` (`id_htl_booking`),
  KEY `id_service_product_order_detail` (`id_service_product_order_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_order_tax_exemption` (
  `id_order_tax_exemption` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_htl_booking` int(11) unsigned NOT NULL DEFAULT '0',
  `id_service_product_order_detail` int(11) unsigned NOT NULL DEFAULT '0',
  `id_order` int(11) unsigned NOT NULL,
  `id_employee` int(11) unsigned NOT NULL,
  `note` text,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_order_tax_exemption`),
  KEY `id_htl_booking` (`id_htl_booking`),
  KEY `id_service_product_order_detail` (`id_service_product_order_detail`),
  KEY `id_order` (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_business_source` (
  `id_business_source` int(10) unsigned NOT NULL auto_increment,
  `code` varchar(64) NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `unremovable` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_business_source`),
  UNIQUE KEY `code` (`code`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_business_source_lang` (
  `id_business_source` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id_business_source`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_source` (
  `id_source` int(10) unsigned NOT NULL auto_increment,
  `id_business_source` int(10) unsigned NOT NULL,
  `code` varchar(64) NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `unremovable` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_source`),
  UNIQUE KEY `code` (`code`),
  KEY `id_business_source` (`id_business_source`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_source_lang` (
  `id_source` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id_source`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_room_type_selling_object` (
  `id_room_type_selling_object` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_room_type_selling_object`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_room_type_selling_object_lang` (
  `id_room_type_selling_object` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `plural_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_room_type_selling_object`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_access`
	ADD COLUMN `kpi` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_cart_rule`
	ADD COLUMN `hotel_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_customer`
	ADD COLUMN `id_country` int(10) NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_orders`
	ADD COLUMN `id_source` int(10) unsigned DEFAULT NULL,
	ADD KEY `id_source` (`id_source`);

ALTER TABLE `PREFIX_order_detail`
	ADD COLUMN `id_tourism_tax_rule_group` INT(11) UNSIGNED DEFAULT '0';

ALTER TABLE `PREFIX_order_return`
	ADD COLUMN `event_type` tinyint(1) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_order_slip`
	ADD COLUMN `remark` text;

ALTER TABLE `PREFIX_order_payment_detail`
	ADD COLUMN `receipt_number` INT(10) NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_product`
	ADD COLUMN `id_tourism_tax_rules_group` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	ADD COLUMN `id_selling_object` int(10) unsigned DEFAULT NULL;

ALTER TABLE `PREFIX_product_shop`
	ADD COLUMN `id_tourism_tax_rules_group` INT(11) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_tax`
	ADD COLUMN `is_tourism_tax` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_tax_rules_group`
	ADD COLUMN `is_tourism_tax_rule_group` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_customer`
	MODIFY COLUMN `passwd` varchar(60) NOT NULL;

ALTER TABLE `PREFIX_employee`
	MODIFY COLUMN `passwd` varchar(60) NOT NULL;

ALTER TABLE `PREFIX_referrer`
	MODIFY COLUMN `passwd` varchar(60) DEFAULT NULL;

ALTER TABLE `PREFIX_order_payment`
	MODIFY COLUMN `amount` DECIMAL(20,6) NOT NULL;

ALTER TABLE `PREFIX_order_payment_detail`
	MODIFY COLUMN `amount` DECIMAL(20,6) NOT NULL;

ALTER TABLE `PREFIX_feature_product`
	DROP PRIMARY KEY,
	ADD PRIMARY KEY (`id_feature`,`id_product`,`id_feature_value`);

INSERT INTO `PREFIX_order_tax_detail`
	(`id_order`, `id_order_detail`, `id_htl_booking`, `id_service_product_order_detail`, `id_tax`, `unit_amount`, `total_amount`, `date_add`)
SELECT
	o.`id_order`, odt.`id_order_detail`,
	COALESCE(hbd.`id`, 0), COALESCE(spod.`id_service_product_order_detail`, 0),
	odt.`id_tax`, odt.`unit_amount`, odt.`total_amount`, o.`date_add`
FROM `PREFIX_order_detail_tax` odt
INNER JOIN `PREFIX_order_detail` od ON od.`id_order_detail` = odt.`id_order_detail`
INNER JOIN `PREFIX_orders` o ON o.`id_order` = od.`id_order`
LEFT JOIN `PREFIX_htl_booking_detail` hbd ON hbd.`id_order_detail` = od.`id_order_detail`
LEFT JOIN `PREFIX_service_product_order_detail` spod ON spod.`id_order_detail` = od.`id_order_detail`;

DROP TABLE IF EXISTS `PREFIX_order_detail_tax`;

/* PHP:add_new_tabs_180(); */;
