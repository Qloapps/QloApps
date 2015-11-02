SET NAMES 'utf8';

CREATE TABLE IF NOT EXISTS `PREFIX_group_shop` (
  `id_group_shop` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `share_customer` TINYINT(1) NOT NULL,
  `share_order` TINYINT(1) NOT NULL,
  `share_stock` TINYINT(1) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_group_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_group_shop` (`id_group_shop`, `name`, `active`, `deleted`, `share_stock`, `share_customer`, `share_order`) VALUES (1, 'Default', 1, 0, 0, 0, 0);

CREATE TABLE IF NOT EXISTS `PREFIX_shop` (
  `id_shop` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_group_shop` int(11) unsigned NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `id_category` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_theme` INT(1) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_shop`),
  KEY `id_group_shop` (`id_group_shop`),
  KEY `id_category` (`id_category`),
  KEY `id_theme` (`id_theme`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_shop` 
	(`id_shop`, `id_group_shop`, `name`, `id_category`, `id_theme`, `active`, `deleted`) 
	VALUES 
	(1, 1, (SELECT value FROM `PREFIX_configuration` WHERE name = 'PS_SHOP_NAME'), 1, 1, 1, 0);

ALTER TABLE `PREFIX_configuration` ADD `id_group_shop` INT(11) UNSIGNED  DEFAULT  NULL AFTER `id_configuration` , ADD `id_shop` INT(11) UNSIGNED DEFAULT NULL AFTER `id_group_shop`;
ALTER TABLE `PREFIX_configuration` DROP INDEX `name` , ADD INDEX `name` ( `name` ) ;
ALTER TABLE `PREFIX_configuration` ADD INDEX (`id_group_shop`);
ALTER TABLE `PREFIX_configuration` ADD INDEX (`id_shop`);
INSERT INTO `PREFIX_configuration` (`id_configuration`, `name`, `value`, `date_add`, `date_upd`) VALUES (NULL, 'PS_SHOP_DEFAULT', '1', NOW(), NOW());

CREATE TABLE IF NOT EXISTS `PREFIX_shop_url` (
  `id_shop_url` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) unsigned NOT NULL,
  `domain` varchar(255) NOT NULL,
  `domain_ssl` varchar(255) NOT NULL,
  `physical_uri` varchar(64) NOT NULL,
  `virtual_uri` varchar(64) NOT NULL,
  `main` TINYINT(1) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_shop_url`),
  KEY `id_shop` (`id_shop`),
	UNIQUE KEY `shop_url` (`domain`, `virtual_uri`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_theme` (
  `id_theme` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id_theme`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_theme` (`id_theme`, `name`) VALUES (1, 'prestashop');

CREATE TABLE IF NOT EXISTS `PREFIX_theme_specific` (
  `id_theme` int(11) unsigned NOT NULL,
	`id_shop` INT(11) UNSIGNED NOT NULL,
  `entity` int(11) unsigned NOT NULL,
  `id_object` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_theme`,`id_shop`, `entity`,`id_object`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_stock` (
`id_stock` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_product` INT( 11 ) UNSIGNED NOT NULL,
`id_product_attribute` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT(11) UNSIGNED NOT NULL,
`quantity` INT(11) NOT NULL,
  PRIMARY KEY (`id_stock`),
  KEY `id_product` (`id_product`),
  KEY `id_product_attribute` (`id_product_attribute`),
  KEY `id_shop` (`id_shop`),
  UNIQUE KEY `product_stock` (`id_product` ,`id_product_attribute` ,`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_stock` (id_product, id_product_attribute, id_shop, quantity) (SELECT id_product, id_product_attribute, 1, quantity FROM PREFIX_product_attribute);
INSERT INTO `PREFIX_stock` (id_product, id_product_attribute, id_shop, quantity) (SELECT id_product, 0, 1, IF(
	(SELECT COUNT(*) FROM PREFIX_product_attribute pa WHERE p.id_product = pa.id_product) > 0,
	(SELECT SUM(pa2.quantity) FROM PREFIX_product_attribute pa2 WHERE p.id_product = pa2.id_product),
	quantity
) FROM PREFIX_product p);

ALTER TABLE PREFIX_stock_mvt ADD id_stock INT UNSIGNED NOT NULL AFTER id_stock_mvt;
UPDATE PREFIX_stock_mvt sm SET sm.id_stock = IFNULL((
	SELECT IFNULL(s.id_stock, 0)
	FROM PREFIX_stock s
	WHERE s.id_product = sm.id_product
	AND s.id_product_attribute = sm.id_product_attribute
	ORDER BY s.id_shop
), 0);
DELETE FROM PREFIX_stock_mvt WHERE id_stock = 0;
ALTER TABLE PREFIX_stock_mvt DROP id_product, DROP id_product_attribute;

CREATE TABLE `PREFIX_country_shop` (
`id_country` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_country`, `id_shop`),
  KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_country_shop` (id_shop, id_country) (SELECT 1, id_country FROM PREFIX_country);

CREATE TABLE `PREFIX_carrier_shop` (
`id_carrier` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_carrier`, `id_shop`),
  KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_carrier_shop` (id_shop, id_carrier) (SELECT 1, id_carrier FROM PREFIX_carrier);

/* PHP:upgrade_cms_15(); */;

CREATE TABLE `PREFIX_lang_shop` (
`id_lang` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_lang`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_lang_shop` (id_shop, id_lang) (SELECT 1, id_lang FROM PREFIX_lang);

CREATE TABLE `PREFIX_currency_shop` (
`id_currency` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_currency`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_currency_shop` (id_shop, id_currency) (SELECT 1, id_currency FROM PREFIX_currency);

ALTER TABLE `PREFIX_cart` ADD `id_group_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_cart` , ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_group_shop`, ADD INDEX `id_group_shop` (`id_group_shop`), ADD INDEX `id_shop` (`id_shop`);

ALTER TABLE `PREFIX_customer` ADD `id_group_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_customer` , ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_group_shop`, ADD INDEX `id_group_shop` (`id_group_shop`), ADD INDEX `id_shop` (`id_shop`);

ALTER TABLE `PREFIX_orders` ADD `id_group_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_order` , ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_group_shop`, ADD INDEX `id_group_shop` (`id_group_shop`), ADD INDEX `id_shop` (`id_shop`);

ALTER TABLE `PREFIX_customer_thread` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_customer_thread`;
ALTER TABLE `PREFIX_customer_thread` ADD INDEX `id_shop` (`id_shop`), ADD INDEX `id_lang` (`id_lang`), ADD INDEX `id_contact` (`id_contact`), ADD INDEX `id_customer` (`id_customer`), ADD INDEX `id_order` (`id_order`),	ADD INDEX `id_product` (`id_product`);
	
ALTER TABLE `PREFIX_customer_message` ADD INDEX `id_employee` (`id_employee`);

ALTER TABLE `PREFIX_meta_lang` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_meta`;
ALTER TABLE `PREFIX_meta_lang` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_meta`, `id_shop`, `id_lang`), ADD INDEX `id_shop` (`id_shop`), ADD INDEX `id_lang` (`id_lang`);

CREATE TABLE `PREFIX_contact_shop` (
	`id_contact` INT(11) UNSIGNED NOT NULL,
	`id_shop` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_contact`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_contact_shop` (id_shop, id_contact) (SELECT 1, id_contact FROM `PREFIX_contact`);

CREATE TABLE `PREFIX_image_shop` (
`id_image` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_image`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_image_shop` (id_shop, id_image) (SELECT 1, id_image FROM `PREFIX_image`);

CREATE TABLE `PREFIX_attribute_group_shop` (
`id_attribute` INT(11) UNSIGNED NOT NULL,
`id_group_shop` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_attribute`, `id_group_shop`),
	KEY `id_group_shop` (`id_group_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_attribute_group_shop` (id_group_shop, id_attribute) (SELECT 1, id_attribute FROM `PREFIX_attribute`);

CREATE TABLE `PREFIX_feature_group_shop` (
`id_feature` INT(11) UNSIGNED NOT NULL,
`id_group_shop` INT(11) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_feature`, `id_group_shop`),
	KEY `id_group_shop` (`id_group_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_feature_group_shop` (id_group_shop, id_feature) (SELECT 1, id_feature FROM `PREFIX_feature`);

CREATE TABLE `PREFIX_group_group_shop` (
`id_group` INT( 11 ) UNSIGNED NOT NULL,
`id_group_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_group`, `id_group_shop`),
	KEY `id_group_shop` (`id_group_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_group_group_shop` (id_group_shop, id_group) (SELECT 1, id_group FROM `PREFIX_group`);

CREATE TABLE `PREFIX_attribute_group_group_shop` (
`id_attribute_group` INT( 11 ) UNSIGNED NOT NULL ,
`id_group_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_attribute_group`, `id_group_shop`),
	KEY `id_group_shop` (`id_group_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_attribute_group_group_shop` (id_group_shop, id_attribute_group) (SELECT 1, id_attribute_group FROM `PREFIX_attribute_group`);

CREATE TABLE `PREFIX_tax_rules_group_group_shop` (
	`id_tax_rules_group` INT( 11 ) UNSIGNED NOT NULL,
	`id_group_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_tax_rules_group`, `id_group_shop`),
	KEY `id_group_shop` (`id_group_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_tax_rules_group_group_shop` (`id_tax_rules_group`, `id_group_shop`) (SELECT `id_tax_rules_group`, 1 FROM `PREFIX_tax_rules_group`);

CREATE TABLE `PREFIX_zone_group_shop` (
`id_zone` INT( 11 ) UNSIGNED NOT NULL ,
`id_group_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_zone`, `id_group_shop`),
	KEY `id_group_shop` (`id_group_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_zone_group_shop` (id_group_shop, id_zone) (SELECT 1, id_zone FROM `PREFIX_zone`);

CREATE TABLE `PREFIX_manufacturer_group_shop` (
`id_manufacturer` INT( 11 ) UNSIGNED NOT NULL ,
`id_group_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_manufacturer`, `id_group_shop`),
	KEY `id_group_shop` (`id_group_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_manufacturer_group_shop` (id_group_shop, id_manufacturer) (SELECT 1, id_manufacturer FROM `PREFIX_manufacturer`);

CREATE TABLE `PREFIX_supplier_group_shop` (
`id_supplier` INT( 11 ) UNSIGNED NOT NULL,
`id_group_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_supplier`, `id_group_shop`),
	KEY `id_group_shop` (`id_group_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_supplier_group_shop` (id_group_shop, id_supplier) (SELECT 1, id_supplier FROM `PREFIX_supplier`);

CREATE TABLE `PREFIX_store_shop` (
`id_store` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_store`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_store_shop` (id_shop, id_store) (SELECT 1, id_store FROM `PREFIX_store`);

CREATE TABLE `PREFIX_product_shop` (
`id_product` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY ( `id_shop` , `id_product` ),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_product_shop` (id_shop, id_product) (SELECT 1, id_product FROM `PREFIX_product`);

ALTER TABLE `PREFIX_category_lang` ADD `id_shop` INT( 11 ) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_category`;
ALTER TABLE `PREFIX_category_lang` ADD UNIQUE KEY( `id_category`, `id_shop`, `id_lang`);

ALTER TABLE `PREFIX_product_lang` ADD `id_shop` INT( 11 ) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_product`;
ALTER TABLE `PREFIX_product_lang` ADD UNIQUE KEY ( `id_product`, `id_shop` , `id_lang`);

ALTER TABLE `PREFIX_specific_price` CHANGE `id_shop` `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1';

ALTER TABLE `PREFIX_hook_module` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_hook`;
ALTER TABLE `PREFIX_hook_module` DROP PRIMARY KEY;
ALTER TABLE `PREFIX_hook_module` ADD PRIMARY KEY (`id_module`,`id_hook`,`id_shop` );
ALTER TABLE `PREFIX_hook_module_exceptions` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_hook`;

ALTER TABLE `PREFIX_connections` ADD `id_group_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1', ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `PREFIX_page_viewed` ADD `id_group_shop` INT UNSIGNED NOT NULL DEFAULT '1' AFTER `id_page`, ADD `id_shop` INT UNSIGNED NOT NULL DEFAULT '1' AFTER `id_group_shop`;
ALTER TABLE `PREFIX_page_viewed` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_page`, `id_date_range`, `id_shop`); 

CREATE TABLE `PREFIX_module_shop` (
`id_module` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_module` , `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_module_shop` (`id_module`, `id_shop`) (SELECT `id_module`, 1 FROM `PREFIX_module` WHERE active = 1);

ALTER TABLE `PREFIX_module_currency` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_module`;
ALTER TABLE `PREFIX_module_currency` DROP PRIMARY KEY;
ALTER TABLE `PREFIX_module_currency` ADD PRIMARY KEY (`id_module`, `id_shop`, `id_currency`);

ALTER TABLE `PREFIX_module_country` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_module`;
ALTER TABLE `PREFIX_module_country` DROP PRIMARY KEY;
ALTER TABLE `PREFIX_module_country` ADD PRIMARY KEY (`id_module`, `id_shop`, `id_country`);

ALTER TABLE `PREFIX_module_group` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_module`;
ALTER TABLE `PREFIX_module_group` DROP PRIMARY KEY;
ALTER TABLE `PREFIX_module_group` ADD PRIMARY KEY (`id_module`, `id_shop`, `id_group`);

ALTER TABLE `PREFIX_carrier_lang` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_carrier`;

/* PHP:add_id_shop_to_shipper_lang_index(); */;

ALTER TABLE `PREFIX_search_word` ADD `id_shop` INT(11) NOT NULL DEFAULT '1' AFTER `id_word`;
ALTER TABLE `PREFIX_search_word` DROP INDEX `id_lang`, ADD UNIQUE `id_lang` (`id_lang`,`id_shop`,`word`);

CREATE TABLE `PREFIX_referrer_shop` (
  `id_referrer` int(10) unsigned NOT NULL auto_increment,
  `id_shop` int(10) unsigned NOT NULL default '1',
  `cache_visitors` int(11) default NULL,
  `cache_visits` int(11) default NULL,
  `cache_pages` int(11) default NULL,
  `cache_registrations` int(11) default NULL,
  `cache_orders` int(11) default NULL,
  `cache_sales` decimal(17,2) default NULL,
  `cache_reg_rate` decimal(5,4) default NULL,
  `cache_order_rate` decimal(5,4) default NULL,
  PRIMARY KEY  (`id_referrer`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_referrer_shop` (`id_referrer`, `id_shop`) SELECT `id_referrer`, 1 FROM `PREFIX_referrer`;
ALTER TABLE `PREFIX_referrer` DROP `cache_visitors`, DROP `cache_visits`, DROP `cache_pages`, DROP `cache_registrations`, DROP `cache_orders`, DROP `cache_sales`, DROP `cache_reg_rate`, DROP `cache_order_rate`;

ALTER TABLE `PREFIX_cart_product` ADD `id_shop` INT NOT NULL DEFAULT '1' AFTER `id_product`;

ALTER TABLE `PREFIX_customization` ADD `in_cart` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0';

CREATE TABLE `PREFIX_scene_shop` (
`id_scene` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_scene`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_scene_shop` (id_shop, id_scene) (SELECT 1, id_scene FROM PREFIX_scene);

/* PHP:create_multistore(); */;

UPDATE `PREFIX_customization` INNER JOIN `PREFIX_orders` USING(id_cart) SET in_cart = 1;
