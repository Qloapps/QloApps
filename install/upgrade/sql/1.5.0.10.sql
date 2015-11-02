/* PHP:module_blockwishlist_multishop(); */;

/* PHP:p15010_drop_column_id_address_if_exists(); */;

UPDATE `PREFIX_meta` SET `page` = 'contact' WHERE `page` = 'contact-form';

DROP TABLE IF EXISTS `PREFIX_shop_group`;

RENAME TABLE `PREFIX_group_shop` TO `PREFIX_shop_group`;
ALTER TABLE `PREFIX_shop_group` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_shop` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE `PREFIX_stock_available` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE `PREFIX_cart` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE `PREFIX_configuration` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NULL;
ALTER TABLE `PREFIX_connections` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE `PREFIX_customer` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE `PREFIX_delivery` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NULL;
ALTER TABLE `PREFIX_orders` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE `PREFIX_page_viewed` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE `PREFIX_specific_price` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE `PREFIX_product` ADD  `id_tax_rules_group` int(10) unsigned NOT NULL;

CREATE TABLE IF NOT EXISTS `PREFIX_product_shop_TMP` (
  `id_product` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  `id_category_default` int(10) unsigned DEFAULT NULL,
  `id_tax_rules_group` INT(11) UNSIGNED NOT NULL,
  `on_sale` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `online_only` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ecotax` decimal(17,6) NOT NULL DEFAULT '0.000000',
  `minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unity` varchar(255) DEFAULT NULL,
  `unit_price_ratio` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `additional_shipping_cost` decimal(20,2) NOT NULL DEFAULT '0.00',
  `customizable` tinyint(2) NOT NULL DEFAULT '0',
  `text_fields` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `available_for_order` tinyint(1) NOT NULL DEFAULT '1',
  `available_date` date NOT NULL,
  `condition` enum('new','used','refurbished') NOT NULL DEFAULT 'new',
  `show_price` tinyint(1) NOT NULL DEFAULT '1',
  `indexed` tinyint(1) NOT NULL DEFAULT '0',
  `visibility` enum('both','catalog','search','none') NOT NULL DEFAULT 'both',
  `cache_default_attribute` int(10) unsigned DEFAULT NULL,
  `advanced_stock_management` tinyint(1) default '0' NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_product`, `id_shop`),
  KEY `id_category_default` (`id_category_default`),
  KEY `date_add` (`date_add`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_product_shop_TMP` (`id_product`, `id_shop`, `id_category_default`, `on_sale`, `online_only`, `ecotax`, `minimal_quantity`, `price`, `wholesale_price`, `unity`, `unit_price_ratio`, `additional_shipping_cost`, `customizable`, `text_fields`, `active`, `available_for_order`, `available_date`, `condition`, `show_price`, `indexed`, `visibility`, `cache_default_attribute`, `advanced_stock_management`, `date_add`, `date_upd`, `id_tax_rules_group`)
	(SELECT a.`id_product`, a.`id_shop`, b.`id_category_default`, b.`on_sale`, b.`online_only`, b.`ecotax`, b.`minimal_quantity`, b.`price`, b.`wholesale_price`, b.`unity`, b.`unit_price_ratio`, b.`additional_shipping_cost`, b.`customizable`, b.`text_fields`, b.`active`, b.`available_for_order`, b.`available_date`, b.`condition`, b.`show_price`, b.`indexed`, b.`visibility`, b.`cache_default_attribute`, b.`advanced_stock_management`, b.`date_add`, b.`date_upd`, c.`id_tax_rules_group` FROM `PREFIX_product_shop` a INNER JOIN `PREFIX_product` b ON a.id_product = b.id_product LEFT JOIN `PREFIX_product_tax_rules_group_shop` c ON b.id_product = c.id_product AND a.id_shop = c.id_shop);
DROP TABLE `PREFIX_product_shop`;
DROP TABLE `PREFIX_product_tax_rules_group_shop`;
RENAME TABLE `PREFIX_product_shop_TMP` TO `PREFIX_product_shop`;

CREATE TABLE `PREFIX_product_attribute_shop` (
  `id_product_attribute` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  `wholesale_price` decimal(20,6) NOT NULL default '0.000000',
  `price` decimal(20,6) NOT NULL default '0.000000',
  `ecotax` decimal(17,6) NOT NULL default '0.00',
  `weight` float NOT NULL default '0',
  `unit_price_impact` decimal(17,2) NOT NULL default '0.00',
  `default_on` tinyint(1) unsigned NOT NULL default '0',
  `minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `available_date` date NOT NULL,
  PRIMARY KEY  (`id_product_attribute`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_product_attribute_shop` (`id_product_attribute`, `id_shop`, `wholesale_price`, `price`, `ecotax`, `weight`, `unit_price_impact`, `default_on`, `minimal_quantity`, `available_date`) (SELECT `id_product_attribute`, (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_SHOP_DEFAULT'), `wholesale_price`, `price`, `ecotax`, `weight`, `unit_price_impact`, `default_on`, `minimal_quantity`, `available_date` FROM `PREFIX_product_attribute`);

CREATE TABLE `PREFIX_attribute_shop` (
`id_attribute` INT(11) UNSIGNED NOT NULL,
`id_shop` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_attribute`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_attribute_shop` (`id_attribute`, `id_shop`) (SELECT a.id_attribute, c.id_shop FROM PREFIX_attribute_group_shop a LEFT JOIN PREFIX_shop_group b ON a.id_group_shop = b.id_shop_group INNER JOIN PREFIX_shop c ON b.id_shop_group = c.id_shop_group);
DROP TABLE `PREFIX_attribute_group_shop`;

CREATE TABLE `PREFIX_feature_shop` (
`id_feature` INT(11) UNSIGNED NOT NULL,
`id_shop` INT(11) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_feature`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_feature_shop` (`id_feature`, `id_shop`) (SELECT a.id_feature, c.id_shop FROM PREFIX_feature_group_shop a LEFT JOIN PREFIX_shop_group b ON a.id_group_shop = b.id_shop_group INNER JOIN PREFIX_shop c ON b.id_shop_group = c.id_shop_group);
DROP TABLE `PREFIX_feature_group_shop`;

CREATE TABLE `PREFIX_group_shop` (
`id_group` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_group`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_group_shop` (`id_group`, `id_shop`) (SELECT a.id_group, c.id_shop FROM PREFIX_group_group_shop a LEFT JOIN PREFIX_shop_group b ON a.id_group_shop = b.id_shop_group INNER JOIN PREFIX_shop c ON b.id_shop_group = c.id_shop_group);
DROP TABLE `PREFIX_group_group_shop`;

CREATE TABLE `PREFIX_attribute_group_shop` (
`id_attribute_group` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_attribute_group`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_attribute_group_shop` (`id_attribute_group`, `id_shop`) (SELECT a.id_attribute_group, c.id_shop FROM PREFIX_attribute_group_group_shop a LEFT JOIN PREFIX_shop_group b ON a.id_group_shop = b.id_shop_group INNER JOIN PREFIX_shop c ON b.id_shop_group = c.id_shop_group);
DROP TABLE `PREFIX_attribute_group_group_shop`;

CREATE TABLE `PREFIX_tax_rules_group_shop` (
	`id_tax_rules_group` INT( 11 ) UNSIGNED NOT NULL,
	`id_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_tax_rules_group`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_tax_rules_group_shop` (`id_tax_rules_group`, `id_shop`) (SELECT a.id_tax_rules_group, c.id_shop FROM PREFIX_tax_rules_group_group_shop a LEFT JOIN PREFIX_shop_group b ON a.id_group_shop = b.id_shop_group INNER JOIN PREFIX_shop c ON b.id_shop_group = c.id_shop_group);
DROP TABLE `PREFIX_tax_rules_group_group_shop`;

CREATE TABLE `PREFIX_zone_shop` (
`id_zone` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_zone`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_zone_shop` (`id_zone`, `id_shop`) (SELECT a.id_zone, c.id_shop FROM PREFIX_zone_group_shop a LEFT JOIN PREFIX_shop_group b ON a.id_group_shop = b.id_shop_group INNER JOIN PREFIX_shop c ON b.id_shop_group = c.id_shop_group);
DROP TABLE `PREFIX_zone_group_shop`;

CREATE TABLE `PREFIX_manufacturer_shop` (
`id_manufacturer` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_manufacturer`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_manufacturer_shop` (`id_manufacturer`, `id_shop`) (SELECT a.id_manufacturer, c.id_shop FROM PREFIX_manufacturer_group_shop a LEFT JOIN PREFIX_shop_group b ON a.id_group_shop = b.id_shop_group INNER JOIN PREFIX_shop c ON b.id_shop_group = c.id_shop_group);
DROP TABLE `PREFIX_manufacturer_group_shop`;

CREATE TABLE `PREFIX_supplier_shop` (
`id_supplier` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_supplier`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_supplier_shop` (`id_supplier`, `id_shop`) (SELECT a.id_supplier, c.id_shop FROM PREFIX_supplier_group_shop a LEFT JOIN PREFIX_shop_group b ON a.id_group_shop = b.id_shop_group INNER JOIN PREFIX_shop c ON b.id_shop_group = c.id_shop_group);
DROP TABLE `PREFIX_supplier_group_shop`;

ALTER TABLE `PREFIX_product_download` DROP COLUMN `id_product_attribute`;
