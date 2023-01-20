SET SESSION sql_mode = '';
SET NAMES 'utf8';

CREATE TABLE `PREFIX_access` (
  `id_profile` int(10) unsigned NOT NULL,
  `id_tab` int(10) unsigned NOT NULL,
  `view` int(11) NOT NULL,
  `add` int(11) NOT NULL,
  `edit` int(11) NOT NULL,
  `delete` int(11) NOT NULL,
  PRIMARY KEY (`id_profile`,`id_tab`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_accessory` (
  `id_product_1` int(10) unsigned NOT NULL,
  `id_product_2` int(10) unsigned NOT NULL,
  KEY `accessory_product` (`id_product_1`,`id_product_2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_address` (
  `id_address` int(10) unsigned NOT NULL auto_increment,
  `id_country` int(10) unsigned NOT NULL,
  `id_state` int(10) unsigned DEFAULT NULL,
  `id_customer` int(10) unsigned NOT NULL DEFAULT '0',
  `id_manufacturer` int(10) unsigned NOT NULL DEFAULT '0',
  `id_supplier` int(10) unsigned NOT NULL DEFAULT '0',
  `id_warehouse` int(10) unsigned NOT NULL DEFAULT '0',
  `id_hotel` int(10) unsigned NOT NULL DEFAULT '0',
  `alias` varchar(32) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `lastname` varchar(32) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `postcode` varchar(12) DEFAULT NULL,
  `city` varchar(64) NOT NULL,
  `other` text,
  `phone` varchar(32) DEFAULT NULL,
  `phone_mobile` varchar(32) DEFAULT NULL,
  `vat_number` varchar(32) DEFAULT NULL,
  `dni` varchar(16) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_generated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_address`),
  KEY `address_customer` (`id_customer`),
  KEY `id_country` (`id_country`),
  KEY `id_state` (`id_state`),
  KEY `id_manufacturer` (`id_manufacturer`),
  KEY `id_supplier` (`id_supplier`),
  KEY `id_warehouse` (`id_warehouse`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_alias` (
  `id_alias` int(10) unsigned NOT NULL auto_increment,
  `alias` varchar(255) NOT NULL,
  `search` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_alias`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_attachment` (
  `id_attachment` int(10) unsigned NOT NULL auto_increment,
  `file` varchar(40) NOT NULL,
  `file_name` varchar(128) NOT NULL,
  `file_size` bigint(10) unsigned NOT NULL DEFAULT '0',
  `mime` varchar(128) NOT NULL,
  PRIMARY KEY (`id_attachment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_attachment_lang` (
  `id_attachment` int(10) unsigned NOT NULL auto_increment,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `description` TEXT,
  PRIMARY KEY (`id_attachment`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_attachment` (
  `id_product` int(10) unsigned NOT NULL,
  `id_attachment` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_product`,`id_attachment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_attribute` (
  `id_attribute` int(10) unsigned NOT NULL auto_increment,
  `id_attribute_group` int(10) unsigned NOT NULL,
  `color` varchar(32) DEFAULT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_attribute`),
  KEY `attribute_group` (`id_attribute_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_attribute_group` (
  `id_attribute_group` int(10) unsigned NOT NULL auto_increment,
  `is_color_group` tinyint(1) NOT NULL DEFAULT '0',
  `group_type` ENUM('select', 'radio', 'color') NOT NULL DEFAULT  'select',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_attribute_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_attribute_group_lang` (
  `id_attribute_group` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `public_name` varchar(64) NOT NULL,
  PRIMARY KEY (`id_attribute_group`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_attribute_impact` (
  `id_attribute_impact` int(10) unsigned NOT NULL auto_increment,
  `id_product` int(11) unsigned NOT NULL,
  `id_attribute` int(11) unsigned NOT NULL,
  `weight` DECIMAL(20,6) NOT NULL,
  `price` decimal(17,2) NOT NULL,
  PRIMARY KEY (`id_attribute_impact`),
  UNIQUE KEY `id_product` (`id_product`,`id_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_attribute_lang` (
  `id_attribute` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id_attribute`,`id_lang`),
  KEY `id_lang` (`id_lang`,`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_carrier` (
  `id_carrier` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_reference` int(10) unsigned NOT NULL,
  `id_tax_rules_group` int(10) unsigned DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_handling` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `range_behavior` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_module` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_free` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_external` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `need_range` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `external_module_name` varchar(64) DEFAULT NULL,
  `shipping_method` int(2) NOT NULL DEFAULT '0',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `max_width` int(10) DEFAULT '0',
  `max_height` int(10)  DEFAULT '0',
  `max_depth` int(10)  DEFAULT '0',
  `max_weight` DECIMAL(20,6) DEFAULT '0',
  `grade` int(10)  DEFAULT '0',
  PRIMARY KEY (`id_carrier`),
  KEY `deleted` (`deleted`,`active`),
  KEY `id_tax_rules_group` (`id_tax_rules_group`),
  KEY `reference` (`id_reference`, `deleted`, `active`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_carrier_lang` (
  `id_carrier` int(10) unsigned NOT NULL,
  `id_shop` int(11) unsigned NOT NULL DEFAULT '1',
  `id_lang` int(10) unsigned NOT NULL,
  `delay` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id_lang`,`id_shop`, `id_carrier`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_carrier_zone` (
  `id_carrier` int(10) unsigned NOT NULL,
  `id_zone` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_carrier`,`id_zone`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart` (
  `id_cart` int(10) unsigned NOT NULL auto_increment,
  `id_shop_group` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_carrier` int(10) unsigned NOT NULL,
  `delivery_option` TEXT NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `id_address_delivery` int(10) unsigned NOT NULL,
  `id_address_invoice` int(10) unsigned NOT NULL,
  `id_currency` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  `id_guest` int(10) unsigned NOT NULL,
  `secure_key` varchar(32) NOT NULL DEFAULT '-1',
  `recyclable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `gift` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gift_message` text,
  `mobile_theme` tinyint(1) NOT NULL DEFAULT '0',
  `allow_seperated_package` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `is_advance_payment` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_cart`),
  KEY `cart_customer` (`id_customer`),
  KEY `id_address_delivery` (`id_address_delivery`),
  KEY `id_address_invoice` (`id_address_invoice`),
  KEY `id_carrier` (`id_carrier`),
  KEY `id_lang` (`id_lang`),
  KEY `id_currency` (`id_currency`),
  KEY `id_guest` (`id_guest`),
  KEY `id_shop_group` (`id_shop_group`),
  KEY `id_shop_2` (`id_shop`,`date_upd`),
  KEY `id_shop` (`id_shop`,`date_add`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule` (
	`id_cart_rule` int(10) unsigned NOT NULL auto_increment,
	`id_customer` int unsigned NOT NULL DEFAULT '0',
	`date_from` datetime NOT NULL,
	`date_to` datetime NOT NULL,
	`description` text,
	`quantity` int(10) unsigned NOT NULL DEFAULT '0',
	`quantity_per_user` int(10) unsigned NOT NULL DEFAULT '0',
	`priority` int(10) unsigned NOT NULL DEFAULT 1,
	`partial_use` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`code` varchar(254) NOT NULL,
	`minimum_amount` decimal(17,2) NOT NULL DEFAULT '0',
	`minimum_amount_tax` tinyint(1) NOT NULL DEFAULT '0',
	`minimum_amount_currency` int unsigned NOT NULL DEFAULT '0',
	`minimum_amount_shipping` tinyint(1) NOT NULL DEFAULT '0',
	`country_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`carrier_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`group_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`cart_rule_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`product_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`shop_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`free_shipping` tinyint(1) NOT NULL DEFAULT '0',
	`reduction_percent` decimal(5,2) NOT NULL DEFAULT '0',
	`reduction_amount` decimal(17,2) NOT NULL DEFAULT '0',
	`reduction_tax` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`reduction_currency` int(10) unsigned NOT NULL DEFAULT '0',
	`reduction_product` int(10) NOT NULL DEFAULT '0',
	`gift_product` int(10) unsigned NOT NULL DEFAULT '0',
	`gift_product_attribute` int(10) unsigned NOT NULL DEFAULT '0',
	`highlight` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_cart_rule`),
	KEY `id_customer` (`id_customer`, `active`, `date_to`),
	KEY `group_restriction` (`group_restriction`, `active`, `date_to`),
	KEY `id_customer_2` (`id_customer`,`active`,`highlight`,`date_to`),
  KEY `group_restriction_2` (`group_restriction`,`active`,`highlight`,`date_to`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule_lang` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`name` varchar(254) NOT NULL,
	PRIMARY KEY (`id_cart_rule`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule_country` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_country` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id_cart_rule`, `id_country`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule_group` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_group` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id_cart_rule`, `id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule_carrier` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_carrier` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id_cart_rule`, `id_carrier`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule_combination` (
	`id_cart_rule_1` int(10) unsigned NOT NULL,
	`id_cart_rule_2` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id_cart_rule_1`, `id_cart_rule_2`),
	KEY `id_cart_rule_1` (`id_cart_rule_1`),
	KEY `id_cart_rule_2` (`id_cart_rule_2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule_product_rule_group` (
	`id_product_rule_group` int(10) unsigned NOT NULL auto_increment,
	`id_cart_rule` int(10) unsigned NOT NULL,
	`quantity` int(10) unsigned NOT NULL DEFAULT 1,
	PRIMARY KEY (`id_product_rule_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule_product_rule` (
	`id_product_rule` int(10) unsigned NOT NULL auto_increment,
	`id_product_rule_group` int(10) unsigned NOT NULL,
	`type` ENUM('products', 'categories', 'attributes', 'manufacturers', 'suppliers') NOT NULL,
	PRIMARY KEY (`id_product_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule_product_rule_value` (
	`id_product_rule` int(10) unsigned NOT NULL,
	`id_item` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id_product_rule`, `id_item`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_cart_rule` (
  `id_cart` int(10) unsigned NOT NULL,
  `id_cart_rule` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_cart`,`id_cart_rule`),
  KEY (`id_cart_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_rule_shop` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_shop` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id_cart_rule`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_product` (
  `id_cart` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `id_address_delivery` int(10) UNSIGNED DEFAULT '0',
  `id_shop` int(10) unsigned NOT NULL DEFAULT '1',
  `id_product_attribute` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_cart`,`id_product`,`id_product_attribute`,`id_address_delivery`),
  KEY `id_product_attribute` (`id_product_attribute`),
  KEY `id_cart_order` (`id_cart`, `date_add`, `id_product`, `id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_category` (
  `id_category` int(10) unsigned NOT NULL auto_increment,
  `id_parent` int(10) unsigned NOT NULL,
  `id_shop_default` int(10) unsigned NOT NULL DEFAULT 1,
  `level_depth` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `nleft` int(10) unsigned NOT NULL DEFAULT '0',
  `nright` int(10) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `is_root_category` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_category`),
  KEY `category_parent` (`id_parent`),
  KEY `nleftrightactive` (`nleft`, `nright`, `active`),
  KEY `level_depth` (`level_depth`),
  KEY `nright` (`nright`),
  KEY `activenleft` (`active`,`nleft`),
  KEY `activenright` (`active`,`nright`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_category_group` (
  `id_category` int(10) unsigned NOT NULL,
  `id_group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_category`,`id_group`),
  KEY `id_category` (`id_category`),
  KEY `id_group` (`id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_category_lang` (
  `id_category` int(10) unsigned NOT NULL,
  `id_shop` INT( 11 ) UNSIGNED NOT NULL DEFAULT '1',
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text,
  `link_rewrite` varchar(128) NOT NULL,
  `meta_title` varchar(128) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_category`,`id_shop`, `id_lang`),
  KEY `category_name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_category_product` (
  `id_category` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_category`,`id_product`),
  INDEX (`id_product`),
  INDEX (`id_category`, `position`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cms` (
  `id_cms` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_cms_category` int(10) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `indexation` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_cms`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cms_lang` (
  `id_cms` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL DEFAULT '1',
  `meta_title` varchar(128) NOT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `content` longtext,
  `link_rewrite` varchar(128) NOT NULL,
  PRIMARY KEY (`id_cms`, `id_shop`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cms_category` (
  `id_cms_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) unsigned NOT NULL,
  `level_depth` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_cms_category`),
  KEY `category_parent` (`id_parent`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cms_category_lang` (
  `id_cms_category` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL DEFAULT '1',
  `name` varchar(128) NOT NULL,
  `description` text,
  `link_rewrite` varchar(128) NOT NULL,
  `meta_title` varchar(128) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_cms_category`, `id_shop`, `id_lang`),
  KEY `category_name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cms_category_shop` (
  `id_cms_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_cms_category`, `id_shop`),
  KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_compare` (
  `id_compare` int(10) unsigned NOT NULL auto_increment,
  `id_customer` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_compare`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_compare_product` (
  `id_compare` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_compare`,`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_configuration` (
  `id_configuration` int(10) unsigned NOT NULL auto_increment,
  `id_shop_group` INT(11) UNSIGNED DEFAULT NULL,
  `id_shop` INT(11) UNSIGNED DEFAULT NULL,
  `name` varchar(254) NOT NULL,
  `value` text,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_configuration`),
  KEY `name` (`name`),
  KEY `id_shop` (`id_shop`),
  KEY `id_shop_group` (`id_shop_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_configuration_lang` (
  `id_configuration` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `value` text,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_configuration`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_configuration_kpi` (
  `id_configuration_kpi` int(10) unsigned NOT NULL auto_increment,
  `id_shop_group` INT(11) UNSIGNED DEFAULT NULL,
  `id_shop` INT(11) UNSIGNED DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  `value` text,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_configuration_kpi`),
  KEY `name` (`name`),
  KEY `id_shop` (`id_shop`),
  KEY `id_shop_group` (`id_shop_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_configuration_kpi_lang` (
  `id_configuration_kpi` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `value` text,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_configuration_kpi`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_connections` (
  `id_connections` int(10) unsigned NOT NULL auto_increment,
  `id_shop_group` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_guest` int(10) unsigned NOT NULL,
  `id_page` int(10) unsigned NOT NULL,
  `ip_address` BIGINT NULL DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `http_referer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_connections`),
  KEY `id_guest` (`id_guest`),
  KEY `date_add` (`date_add`),
  KEY `id_page` (`id_page`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_connections_page` (
  `id_connections` int(10) unsigned NOT NULL,
  `id_page` int(10) unsigned NOT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime DEFAULT NULL,
  PRIMARY KEY (`id_connections`,`id_page`,`time_start`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_connections_source` (
  `id_connections_source` int(10) unsigned NOT NULL auto_increment,
  `id_connections` int(10) unsigned NOT NULL,
  `http_referer` varchar(255) DEFAULT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_connections_source`),
  KEY `connections` (`id_connections`),
  KEY `orderby` (`date_add`),
  KEY `http_referer` (`http_referer`),
  KEY `request_uri` (`request_uri`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_contact` (
  `id_contact` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(128) NOT NULL,
  `customer_service` tinyint(1) NOT NULL DEFAULT '0',
  `position` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_contact`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_contact_lang` (
  `id_contact` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` text,
  PRIMARY KEY (`id_contact`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_country` (
  `id_country` int(10) unsigned NOT NULL auto_increment,
  `id_zone` int(10) unsigned NOT NULL,
  `id_currency` int(10) unsigned NOT NULL DEFAULT '0',
  `iso_code` varchar(3) NOT NULL,
  `call_prefix` int(10) NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `contains_states` tinyint(1) NOT NULL DEFAULT '0',
  `need_identification_number` tinyint(1) NOT NULL DEFAULT '0',
  `need_zip_code` tinyint(1) NOT NULL DEFAULT '1',
  `zip_code_format` varchar(12) NOT NULL DEFAULT '',
  `display_tax_label` BOOLEAN NOT NULL,
  PRIMARY KEY (`id_country`),
  KEY `country_iso_code` (`iso_code`),
  KEY `country_` (`id_zone`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_country_lang` (
  `id_country` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id_country`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_currency` (
  `id_currency` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `iso_code` varchar(3) NOT NULL DEFAULT '0',
  `iso_code_num` varchar(3) NOT NULL DEFAULT '0',
  `sign` varchar(8) NOT NULL,
  `blank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `format` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `decimals` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `conversion_rate` decimal(13,6) NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_currency`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_customer` (
  `id_customer` int(10) unsigned NOT NULL auto_increment,
  `id_shop_group` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_gender` int(10) unsigned NOT NULL,
  `id_default_group` int(10) unsigned NOT NULL DEFAULT '1',
  `id_lang` int(10) unsigned NULL,
  `id_risk` int(10) unsigned NOT NULL DEFAULT '1',
  `company` varchar(64),
  `siret` varchar(14),
  `ape` varchar(5),
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `last_passwd_gen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `birthday` date DEFAULT NULL,
  `newsletter` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ip_registration_newsletter` varchar(15) DEFAULT NULL,
  `newsletter_date_add` datetime DEFAULT NULL,
  `optin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `website` varchar(128),
  `outstanding_allow_amount` DECIMAL( 20,6 ) NOT NULL DEFAULT '0.00',
  `show_public_prices` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `max_payment_days` int(10) unsigned NOT NULL DEFAULT '60',
  `secure_key` varchar(32) NOT NULL DEFAULT '-1',
  `note` text,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_guest` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_customer`),
  KEY `customer_email` (`email`),
  KEY `customer_login` (`email`,`passwd`),
  KEY `id_customer_passwd` (`id_customer`,`passwd`),
  KEY `id_gender` (`id_gender`),
  KEY `id_shop_group` (`id_shop_group`),
  KEY `id_shop` (`id_shop`, `date_add`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_customer_group` (
  `id_customer` int(10) unsigned NOT NULL,
  `id_group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_customer`,`id_group`),
  INDEX customer_login(id_group),
  KEY `id_customer` (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_customer_message` (
  `id_customer_message` int(10) unsigned NOT NULL auto_increment,
  `id_customer_thread` int(11) DEFAULT NULL,
  `id_employee` int(10) unsigned DEFAULT NULL,
  `message` MEDIUMTEXT NOT NULL,
  `file_name` varchar(18) DEFAULT NULL,
  `ip_address`  varchar(16) DEFAULT NULL,
  `user_agent` varchar(128) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `private` TINYINT NOT NULL DEFAULT  '0',
  `read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_customer_message`),
  KEY `id_customer_thread` (`id_customer_thread`),
  KEY `id_employee` (`id_employee`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_customer_message_sync_imap` (
  `md5_header` varbinary(32) NOT NULL,
  KEY `md5_header_index` (`md5_header`(4))
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_customer_thread` (
  `id_customer_thread` int(11) unsigned NOT NULL auto_increment,
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_lang` int(10) unsigned NOT NULL,
  `id_contact` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned DEFAULT NULL,
  `id_order` int(10) unsigned DEFAULT NULL,
  `id_product` int(10) unsigned DEFAULT NULL,
  `status` enum('open','closed','pending1','pending2') NOT NULL DEFAULT 'open',
  `email` varchar(128) NOT NULL,
  `token` varchar(12) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_customer_thread`),
	KEY `id_shop` (`id_shop`),
	KEY `id_lang` (`id_lang`),
	KEY `id_contact` (`id_contact`),
	KEY `id_customer` (`id_customer`),
	KEY `id_order` (`id_order`),
	KEY `id_product` (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_customer_guest_detail` (
  `id_order_customer_guest_detail` int(10) unsigned NOT NULL auto_increment,
  `id_order` int(10) unsigned NOT NULL,
  `id_gender` int(10) unsigned NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_order_customer_guest_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cart_customer_guest_detail` (
  `id_customer_guest_detail` int(10) unsigned NOT NULL auto_increment,
  `id_cart` int(10) unsigned NOT NULL,
  `id_gender` int(10) unsigned NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_customer_guest_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_customization` (
  `id_customization` int(10) unsigned NOT NULL auto_increment,
  `id_product_attribute` int(10) unsigned NOT NULL DEFAULT '0',
  `id_address_delivery` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id_cart` int(10) unsigned NOT NULL,
  `id_product` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `quantity_refunded` INT NOT NULL DEFAULT '0',
  `quantity_returned` INT NOT NULL DEFAULT '0',
  `in_cart` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_customization`,`id_cart`,`id_product`, `id_address_delivery`),
  KEY `id_product_attribute` (`id_product_attribute`),
  KEY `id_cart_product` (`id_cart`, `id_product`, `id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_customization_field` (
  `id_customization_field` int(10) unsigned NOT NULL auto_increment,
  `id_product` int(10) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL,
  `required` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_customization_field`),
  KEY `id_product` (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_customization_field_lang` (
  `id_customization_field` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `id_shop` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_customization_field`,`id_lang`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_customized_data` (
  `id_customization` int(10) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL,
  `index` int(3) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id_customization`,`type`,`index`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_date_range` (
  `id_date_range` int(10) unsigned NOT NULL auto_increment,
  `time_start` datetime NOT NULL,
  `time_end` datetime NOT NULL,
  PRIMARY KEY (`id_date_range`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_delivery` (
  `id_delivery` int(10) unsigned NOT NULL auto_increment,
  `id_shop` INT UNSIGNED NULL DEFAULT NULL,
  `id_shop_group` INT UNSIGNED NULL DEFAULT NULL,
  `id_carrier` int(10) unsigned NOT NULL,
  `id_range_price` int(10) unsigned DEFAULT NULL,
  `id_range_weight` int(10) unsigned DEFAULT NULL,
  `id_zone` int(10) unsigned NOT NULL,
  `price` decimal(20,6) NOT NULL,
  PRIMARY KEY (`id_delivery`),
  KEY `id_zone` (`id_zone`),
  KEY `id_carrier` (`id_carrier`,`id_zone`),
  KEY `id_range_price` (`id_range_price`),
  KEY `id_range_weight` (`id_range_weight`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_employee` (
  `id_employee` int(10) unsigned NOT NULL auto_increment,
  `id_profile` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL DEFAULT '0',
  `lastname` varchar(32) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `last_passwd_gen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stats_date_from` date DEFAULT NULL,
  `stats_date_to` date DEFAULT NULL,
  `stats_compare_from` date DEFAULT NULL,
  `stats_compare_to` date DEFAULT NULL,
  `stats_compare_option` int(1) unsigned NOT NULL DEFAULT 1,
  `preselect_date_range` varchar(32) DEFAULT NULL,
  `bo_color` varchar(32) DEFAULT NULL,
  `bo_theme` varchar(32) DEFAULT NULL,
  `bo_css` varchar(64) DEFAULT NULL,
  `default_tab` int(10) unsigned NOT NULL DEFAULT '0',
  `bo_width` int(10) unsigned NOT NULL DEFAULT '0',
  `bo_menu` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `optin` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `id_last_order` int(10) unsigned NOT NULL DEFAULT '0',
  `id_last_customer_message` int(10) unsigned NOT NULL DEFAULT '0',
  `id_last_customer` int(10) unsigned NOT NULL DEFAULT '0',
  `last_connection_date` date DEFAULT '0000-00-00',
  PRIMARY KEY (`id_employee`),
  KEY `employee_login` (`email`,`passwd`),
  KEY `id_employee_passwd` (`id_employee`,`passwd`),
  KEY `id_profile` (`id_profile`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_employee_shop` (
`id_employee` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
  PRIMARY KEY ( `id_employee` , `id_shop` ),
  KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_feature` (
  `id_feature` int(10) unsigned NOT NULL auto_increment,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_feature`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_feature_lang` (
  `id_feature` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_feature`,`id_lang`),
  KEY (`id_lang`,`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_feature_product` (
  `id_feature` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `id_feature_value` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_feature`,`id_product`),
  KEY `id_feature_value` (`id_feature_value`),
  KEY `id_product` (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_feature_value` (
  `id_feature_value` int(10) unsigned NOT NULL auto_increment,
  `id_feature` int(10) unsigned NOT NULL,
  `custom` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_feature_value`),
  KEY `feature` (`id_feature`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_feature_value_lang` (
  `id_feature_value` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_feature_value`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_gender` (
  `id_gender` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_gender`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_gender_lang` (
  `id_gender` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id_gender`,`id_lang`),
  KEY `id_gender` (`id_gender`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_group` (
  `id_group` int(10) unsigned NOT NULL auto_increment,
  `reduction` decimal(17,2) NOT NULL DEFAULT '0.00',
  `price_display_method` TINYINT NOT NULL DEFAULT '0',
  `show_prices` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_group_lang` (
  `id_group` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_group`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_group_reduction` (
	`id_group_reduction` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_group` INT(10) UNSIGNED NOT NULL,
	`id_category` INT(10) UNSIGNED NOT NULL,
	`reduction` DECIMAL(4, 3) NOT NULL,
	PRIMARY KEY (`id_group_reduction`),
	UNIQUE KEY(`id_group`, `id_category`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_group_reduction_cache` (
	`id_product` INT UNSIGNED NOT NULL,
	`id_group` INT UNSIGNED NOT NULL,
	`reduction` DECIMAL(4, 3) NOT NULL,
	PRIMARY KEY (`id_product`, `id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_carrier` (
  `id_product` int(10) unsigned NOT NULL,
  `id_carrier_reference` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_product`, `id_carrier_reference`, `id_shop`)
) ENGINE = ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_guest` (
  `id_guest` int(10) unsigned NOT NULL auto_increment,
  `id_operating_system` int(10) unsigned DEFAULT NULL,
  `id_web_browser` int(10) unsigned DEFAULT NULL,
  `id_customer` int(10) unsigned DEFAULT NULL,
  `javascript` tinyint(1) DEFAULT '0',
  `screen_resolution_x` smallint(5) unsigned DEFAULT NULL,
  `screen_resolution_y` smallint(5) unsigned DEFAULT NULL,
  `screen_color` tinyint(3) unsigned DEFAULT NULL,
  `sun_java` tinyint(1) DEFAULT NULL,
  `adobe_flash` tinyint(1) DEFAULT NULL,
  `adobe_director` tinyint(1) DEFAULT NULL,
  `apple_quicktime` tinyint(1) DEFAULT NULL,
  `real_player` tinyint(1) DEFAULT NULL,
  `windows_media` tinyint(1) DEFAULT NULL,
  `accept_language` varchar(8) DEFAULT NULL,
  `mobile_theme` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_guest`),
  KEY `id_customer` (`id_customer`),
  KEY `id_operating_system` (`id_operating_system`),
  KEY `id_web_browser` (`id_web_browser`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_hook` (
  `id_hook` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` text,
  `position` tinyint(1) NOT NULL DEFAULT '1',
  `live_edit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_hook`),
  UNIQUE KEY `hook_name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_hook_alias` (
  `id_hook_alias` int(10) unsigned NOT NULL auto_increment,
  `alias` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id_hook_alias`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_hook_module` (
  `id_module` int(10) unsigned NOT NULL,
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_hook` int(10) unsigned NOT NULL,
  `position` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id_module`,`id_hook`,`id_shop`),
  KEY `id_hook` (`id_hook`),
  KEY `id_module` (`id_module`),
  KEY `position` (`id_shop`, `position`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_hook_module_exceptions` (
  `id_hook_module_exceptions` int(10) unsigned NOT NULL auto_increment,
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_module` int(10) unsigned NOT NULL,
  `id_hook` int(10) unsigned NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_hook_module_exceptions`),
  KEY `id_module` (`id_module`),
  KEY `id_hook` (`id_hook`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_image` (
  `id_image` int(10) unsigned NOT NULL auto_increment,
  `id_product` int(10) unsigned NOT NULL,
  `position` smallint(2) unsigned NOT NULL DEFAULT '0',
  `cover` tinyint(1) unsigned NULL DEFAULT NULL,
  PRIMARY KEY (`id_image`),
  KEY `image_product` (`id_product`),
  UNIQUE KEY `id_product_cover` (`id_product`,`cover`),
  UNIQUE KEY `idx_product_image` (`id_image`, `id_product`, `cover`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_image_lang` (
  `id_image` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `legend` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_image`,`id_lang`),
  KEY `id_image` (`id_image`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_image_type` (
  `id_image_type` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `products` tinyint(1) NOT NULL DEFAULT '1',
  `categories` tinyint(1) NOT NULL DEFAULT '1',
  `hotels` tinyint(1) NOT NULL DEFAULT '1',
  `manufacturers` tinyint(1) NOT NULL DEFAULT '1',
  `suppliers` tinyint(1) NOT NULL DEFAULT '1',
  `scenes` tinyint(1) NOT NULL DEFAULT '1',
  `stores` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_image_type`),
  KEY `image_type_name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_lang` (
  `id_lang` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `iso_code` char(2) NOT NULL,
  `language_code` char(5) NOT NULL,
  `date_format_lite` char(32) NOT NULL DEFAULT 'Y-m-d',
  `date_format_full` char(32) NOT NULL DEFAULT 'Y-m-d H:i:s',
  `is_rtl` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_lang`),
  KEY `lang_iso_code` (`iso_code`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_manufacturer` (
  `id_manufacturer` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_manufacturer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_manufacturer_lang` (
  `id_manufacturer` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `description` text,
  `short_description` text,
  `meta_title` varchar(128) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_manufacturer`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_message` (
  `id_message` int(10) unsigned NOT NULL auto_increment,
  `id_cart` int(10) unsigned DEFAULT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  `id_employee` int(10) unsigned DEFAULT NULL,
  `id_order` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `private` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_message`),
  KEY `message_order` (`id_order`),
  KEY `id_cart` (`id_cart`),
  KEY `id_customer` (`id_customer`),
  KEY `id_employee` (`id_employee`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_message_readed` (
  `id_message` int(10) unsigned NOT NULL,
  `id_employee` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_message`,`id_employee`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_meta` (
  `id_meta` int(10) unsigned NOT NULL auto_increment,
  `page` varchar(64) NOT NULL,
	`configurable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_meta`),
  UNIQUE KEY `page` (`page`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_meta_lang` (
  `id_meta` int(10) unsigned NOT NULL,
   `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_lang` int(10) unsigned NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `url_rewrite` varchar(254) NOT NULL,
  PRIMARY KEY (`id_meta`, `id_shop`, `id_lang`),
  KEY `id_shop` (`id_shop`),
  KEY `id_lang` (`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_module` (
  `id_module` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `version` VARCHAR(8) NOT NULL,
  PRIMARY KEY (`id_module`),
  KEY `name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_module_access` (
  `id_profile` int(10) unsigned NOT NULL,
  `id_module` int(10) unsigned NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT '0',
  `configure` tinyint(1) NOT NULL DEFAULT '0',
  `uninstall` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_profile`,`id_module`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_module_country` (
  `id_module` int(10) unsigned NOT NULL,
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_country` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_module`,`id_shop`, `id_country`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_module_currency` (
  `id_module` int(10) unsigned NOT NULL,
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_currency` int(11) NOT NULL,
  PRIMARY KEY (`id_module`,`id_shop`, `id_currency`),
  KEY `id_module` (`id_module`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_module_group` (
  `id_module` int(10) unsigned NOT NULL,
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_group` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_module`,`id_shop`, `id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_operating_system` (
  `id_operating_system` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_operating_system`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_orders` (
  `id_order` int(10) unsigned NOT NULL auto_increment,
  `reference` VARCHAR(9),
  `id_shop_group` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_carrier` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  `id_cart` int(10) unsigned NOT NULL,
  `id_currency` int(10) unsigned NOT NULL,
  `id_address_delivery` int(10) unsigned NOT NULL,
  `id_address_invoice` int(10) unsigned NOT NULL,
  `id_address_tax` int(10) unsigned NOT NULL,
  `current_state` int(10) unsigned NOT NULL,
  `secure_key` varchar(32) NOT NULL DEFAULT '-1',
  `payment` varchar(255) NOT NULL,
  `conversion_rate` decimal(13,6) NOT NULL DEFAULT 1,
  `module` varchar(255) DEFAULT NULL,
  `recyclable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gift` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gift_message` text,
  `mobile_theme` tinyint(1) NOT NULL DEFAULT '0',
  `shipping_number` varchar(64) DEFAULT NULL,
  `total_discounts` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_discounts_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_discounts_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_paid` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_paid_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_paid_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_paid_real` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_products` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_products_wt` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_shipping` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_shipping_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_shipping_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `carrier_tax_rate` DECIMAL(10, 3) NOT NULL DEFAULT '0.00',
  `total_wrapping` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_wrapping_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_wrapping_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `round_mode` tinyint(1) NOT NULL DEFAULT '2',
  `round_type` tinyint(1) NOT NULL DEFAULT '1',
  `invoice_number` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_number` int(10) unsigned NOT NULL DEFAULT '0',
  `invoice_date` datetime NOT NULL,
  `delivery_date` datetime NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `valid` int(1) unsigned NOT NULL DEFAULT '0',
  `is_advance_payment` tinyint(1) NOT NULL DEFAULT '0',
  `advance_paid_amount` decimal(20,6) NOT NULL DEFAULT '0.00',
  `with_occupancy` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_order`),
  KEY `reference` (`reference`),
  KEY `id_customer` (`id_customer`),
  KEY `id_cart` (`id_cart`),
  KEY `invoice_number` (`invoice_number`),
  KEY `id_carrier` (`id_carrier`),
  KEY `id_lang` (`id_lang`),
  KEY `id_currency` (`id_currency`),
  KEY `id_address_delivery` (`id_address_delivery`),
  KEY `id_address_invoice` (`id_address_invoice`),
  KEY `id_shop_group` (`id_shop_group`),
  KEY (`current_state`),
  KEY `id_shop` (`id_shop`),
  INDEX `date_add`(`date_add`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_detail_tax` (
  `id_order_detail` int(11) NOT NULL,
  `id_tax` int(11) NOT NULL,
  `unit_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.00',
  `total_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.00',
   KEY (`id_order_detail`),
   KEY `id_tax` (`id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_invoice` (
  `id_order_invoice` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_order` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `delivery_number` int(11) NOT NULL,
  `delivery_date` datetime,
  `total_discount_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_discount_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_paid_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.00',
  `total_paid_tax_incl` decimal(20, 6) NOT NULL DEFAULT '0.00',
  `total_products` decimal(20, 6) NOT NULL DEFAULT '0.00',
  `total_products_wt` decimal(20, 6) NOT NULL DEFAULT '0.00',
  `total_shipping_tax_excl` decimal(20, 6) NOT NULL DEFAULT '0.00',
  `total_shipping_tax_incl` decimal(20, 6) NOT NULL DEFAULT '0.00',
  `shipping_tax_computation_method` int(10) unsigned NOT NULL,
  `total_wrapping_tax_excl` decimal(20, 6) NOT NULL DEFAULT '0.00',
  `total_wrapping_tax_incl` decimal(20, 6) NOT NULL DEFAULT '0.00',
  `shop_address` text DEFAULT NULL,
  `invoice_address` text DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `note` text,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_order_invoice`),
  KEY `id_order` (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_order_invoice_tax` (
  `id_order_invoice` int(11) NOT NULL,
  `type` varchar(15) NOT NULL,
  `id_tax` int(11) NOT NULL,
  `amount` decimal(10,6) NOT NULL DEFAULT '0.000000',
  KEY `id_tax` (`id_tax`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_detail` (
  `id_order_detail` int(10) unsigned NOT NULL auto_increment,
  `id_order` int(10) unsigned NOT NULL,
  `id_order_invoice` int(11) DEFAULT NULL,
  `id_warehouse` int(10) unsigned DEFAULT '0',
  `id_shop` int(11) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `product_attribute_id` int(10) unsigned DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `product_quantity_in_stock` int(10) NOT NULL DEFAULT '0',
  `product_quantity_refunded` int(10) unsigned NOT NULL DEFAULT '0',
  `product_quantity_return` int(10) unsigned NOT NULL DEFAULT '0',
  `product_quantity_reinjected` int(10) unsigned NOT NULL DEFAULT '0',
  `product_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `reduction_percent` DECIMAL(10, 2) NOT NULL DEFAULT '0.00',
  `reduction_amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `reduction_amount_tax_incl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `reduction_amount_tax_excl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `group_reduction` DECIMAL(10, 2) NOT NULL DEFAULT '0.000000',
  `product_quantity_discount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `product_ean13` varchar(13) DEFAULT NULL,
  `product_upc` varchar(12) DEFAULT NULL,
  `product_reference` varchar(32) DEFAULT NULL,
  `product_supplier_reference` varchar(32) DEFAULT NULL,
  `product_weight` DECIMAL(20,6) NOT NULL,
  `id_tax_rules_group` INT(11) UNSIGNED DEFAULT '0',
  `tax_computation_method` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `tax_name` varchar(16) NOT NULL,
  `tax_rate` DECIMAL(10,3) NOT NULL DEFAULT '0.000',
  `ecotax` decimal(21,6) NOT NULL DEFAULT '0.00',
  `ecotax_tax_rate` DECIMAL(5,3) NOT NULL DEFAULT '0.000',
  `discount_quantity_applied` TINYINT(1) NOT NULL DEFAULT '0',
  `download_hash` varchar(255) DEFAULT NULL,
  `download_nb` int(10) unsigned DEFAULT '0',
  `download_deadline` datetime DEFAULT NULL,
  `total_price_tax_incl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `total_price_tax_excl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `unit_price_tax_incl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `unit_price_tax_excl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `total_shipping_price_tax_incl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `total_shipping_price_tax_excl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `purchase_supplier_price` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `original_product_price` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  `original_wholesale_price` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`id_order_detail`),
  KEY `order_detail_order` (`id_order`),
  KEY `product_id` (`product_id`),
  KEY `product_attribute_id` (`product_attribute_id`),
  KEY `id_tax_rules_group` (`id_tax_rules_group`),
  KEY `id_order_id_order_detail` (`id_order`, `id_order_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_cart_rule` (
  `id_order_cart_rule` int(10) unsigned NOT NULL auto_increment,
  `id_order` int(10) unsigned NOT NULL,
  `id_cart_rule` int(10) unsigned NOT NULL,
  `id_order_invoice` int(10) unsigned DEFAULT '0',
  `name` varchar(254) NOT NULL,
  `value` decimal(17,2) NOT NULL DEFAULT '0.00',
  `value_tax_excl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `free_shipping` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_order_cart_rule`),
  KEY `id_order` (`id_order`),
  KEY `id_cart_rule` (`id_cart_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_history` (
  `id_order_history` int(10) unsigned NOT NULL auto_increment,
  `id_employee` int(10) unsigned NOT NULL,
  `id_order` int(10) unsigned NOT NULL,
  `id_order_state` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_order_history`),
  KEY `order_history_order` (`id_order`),
  KEY `id_employee` (`id_employee`),
  KEY `id_order_state` (`id_order_state`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_message` (
  `id_order_message` int(10) unsigned NOT NULL auto_increment,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_order_message`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_message_lang` (
  `id_order_message` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id_order_message`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_return` (
  `id_order_return` int(10) unsigned NOT NULL auto_increment,
  `id_customer` int(10) unsigned NOT NULL,
  `id_order` int(10) unsigned NOT NULL,
  `state` int(10) unsigned NOT NULL DEFAULT '1',
	`id_transaction` varchar(100) NOT NULL DEFAULT '',
	`payment_mode` varchar(255) NOT NULL DEFAULT '',
	`refunded_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `question` text NOT NULL,
  `by_admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_return_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `return_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_order_return`),
  KEY `order_return_customer` (`id_customer`),
  KEY `id_order` (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_return_detail` (
  `id_order_return_detail` int(10) unsigned NOT NULL auto_increment,
  `id_order_return` int(10) unsigned NOT NULL,
	`id_htl_booking` int(11) NOT NULL,
  `refunded_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `id_order_detail` int(10) unsigned NOT NULL DEFAULT '0',
  `id_customization` int(10) unsigned NOT NULL DEFAULT '0',
  `product_quantity` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `id_htl_booking` (`id_htl_booking`),
  PRIMARY KEY (`id_order_return_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_return_state` (
  `id_order_return_state` int(10) unsigned NOT NULL auto_increment,
  `color` varchar(32) DEFAULT NULL,
  `send_email_to_customer` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `send_email_to_superadmin` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `send_email_to_employee` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `send_email_to_hotelier` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `denied` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `refunded` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `module_name` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id_order_return_state`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_return_state_lang` (
  `id_order_return_state` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `customer_template` varchar(64) NOT NULL,
  `admin_template` varchar(64) NOT NULL,
  PRIMARY KEY (`id_order_return_state`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_slip` (
  `id_order_slip` int(10) unsigned NOT NULL auto_increment,
  `conversion_rate` decimal(13,6) NOT NULL DEFAULT 1,
  `id_customer` int(10) unsigned NOT NULL,
  `id_order` int(10) unsigned NOT NULL,
  `total_products_tax_excl` DECIMAL(20, 6) NULL,
  `total_products_tax_incl` DECIMAL(20, 6) NULL,
  `total_shipping_tax_excl` DECIMAL(20, 6) NULL,
  `total_shipping_tax_incl` DECIMAL(20, 6) NULL,
  `shipping_cost` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `amount` DECIMAL(10,2) NOT NULL,
  `shipping_cost_amount` DECIMAL(10,2) NOT NULL,
  `partial` TINYINT(1) NOT NULL,
  `redeem_status` TINYINT(1) NOT NULL DEFAULT '0',
  `id_cart_rule` int(10) unsigned NOT NULL DEFAULT '0',
  `order_slip_type` TINYINT(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_order_slip`),
  KEY `order_slip_customer` (`id_customer`),
  KEY `id_order` (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_slip_detail` (
  `id_order_slip` int(10) unsigned NOT NULL,
  `id_order_detail` int(10) unsigned NOT NULL,
  `id_htl_booking` int(10) unsigned NOT NULL,
  `product_quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `unit_price_tax_excl` DECIMAL(20, 6) NULL,
  `unit_price_tax_incl` DECIMAL(20, 6) NULL,
  `total_price_tax_excl` DECIMAL(20, 6) NULL,
  `total_price_tax_incl` DECIMAL(20, 6),
  `amount_tax_excl` DECIMAL(20, 6) DEFAULT NULL,
  `amount_tax_incl` DECIMAL(20, 6) DEFAULT NULL,
  PRIMARY KEY (`id_order_slip`,`id_htl_booking`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_state` (
  `id_order_state` int(10) UNSIGNED NOT NULL auto_increment,
  `invoice` tinyint(1) UNSIGNED DEFAULT '0',
	`send_email` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
	`module_name` VARCHAR(255) NULL DEFAULT NULL,
  `color` varchar(32) DEFAULT NULL,
  `unremovable` tinyint(1) UNSIGNED NOT NULL,
  `hidden` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `logable` tinyint(1) NOT NULL DEFAULT '0',
  `delivery` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `shipped` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `paid` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `pdf_invoice` tinyint(1) UNSIGNED NOT NULL default '0',
  `pdf_delivery` tinyint(1) UNSIGNED NOT NULL default '0',
  `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_order_state`),
  KEY `module_name` (`module_name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_state_lang` (
  `id_order_state` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `template` varchar(64) NOT NULL,
  PRIMARY KEY (`id_order_state`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_pack` (
  `id_product_pack` int(10) unsigned NOT NULL,
  `id_product_item` int(10) unsigned NOT NULL,
  `id_product_attribute_item` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_product_pack`, `id_product_item`, `id_product_attribute_item`),
  KEY `product_item` (`id_product_item`,`id_product_attribute_item`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_page` (
  `id_page` int(10) unsigned NOT NULL auto_increment,
  `id_page_type` int(10) unsigned NOT NULL,
  `id_object` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_page`),
  KEY `id_page_type` (`id_page_type`),
  KEY `id_object` (`id_object`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_page_type` (
  `id_page_type` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_page_type`),
  KEY `name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_page_viewed` (
  `id_page` int(10) unsigned NOT NULL,
  `id_shop_group` INT UNSIGNED NOT NULL DEFAULT '1',
  `id_shop` INT UNSIGNED NOT NULL DEFAULT '1',
  `id_date_range` int(10) unsigned NOT NULL,
  `counter` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_page`, `id_date_range`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_payment` (
	`id_order_payment` INT NOT NULL auto_increment,
	`order_reference` VARCHAR(9),
	`id_currency` INT UNSIGNED NOT NULL,
	`amount` DECIMAL(10,2) NOT NULL,
	`payment_method` varchar(255) NOT NULL,
	`conversion_rate` decimal(13,6) NOT NULL DEFAULT 1,
	`transaction_id` VARCHAR(254) NULL,
	`card_number` VARCHAR(254) NULL,
	`card_brand` VARCHAR(254) NULL,
	`card_expiration` CHAR(7) NULL,
	`card_holder` VARCHAR(254) NULL,
	`date_add` DATETIME NOT NULL,
	PRIMARY KEY (`id_order_payment`),
	KEY `order_reference`(`order_reference`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_payment_detail` (
	`id_order_payment_detail` INT(10) unsigned NOT NULL auto_increment,
	`id_order_payment` INT(10) unsigned NOT NULL,
	`id_order` INT(10) unsigned NOT NULL,
	`amount` DECIMAL(10,2) NOT NULL,
	`date_add` DATETIME NOT NULL,
	PRIMARY KEY (`id_order_payment_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product` (
  `id_product` int(10) unsigned NOT NULL auto_increment,
  `id_supplier` int(10) unsigned DEFAULT NULL,
  `id_manufacturer` int(10) unsigned DEFAULT NULL,
  `id_category_default` int(10) unsigned DEFAULT NULL,
  `id_shop_default` int(10) unsigned NOT NULL DEFAULT 1,
  `id_tax_rules_group` INT(11) UNSIGNED NOT NULL,
  `on_sale` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `online_only` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ean13` varchar(13) DEFAULT NULL,
  `upc` varchar(12) DEFAULT NULL,
  `ecotax` decimal(17,6) NOT NULL DEFAULT '0.00',
  `quantity` int(10) NOT NULL DEFAULT '0',
  `minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unity` varchar(255) DEFAULT NULL,
  `unit_price_ratio` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `additional_shipping_cost` decimal(20,2) NOT NULL DEFAULT '0.00',
  `reference` varchar(32) DEFAULT NULL,
  `supplier_reference` varchar(32) DEFAULT NULL,
  `location` varchar(64) DEFAULT NULL,
  `width` DECIMAL(20, 6) NOT NULL DEFAULT '0',
  `height` DECIMAL(20, 6) NOT NULL DEFAULT '0',
  `depth` DECIMAL(20, 6) NOT NULL DEFAULT '0',
  `weight` DECIMAL(20, 6) NOT NULL DEFAULT '0',
  `out_of_stock` int(10) unsigned NOT NULL DEFAULT '2',
  `quantity_discount` tinyint(1) DEFAULT '0',
  `customizable` tinyint(2) NOT NULL DEFAULT '0',
  `uploadable_files` tinyint(4) NOT NULL DEFAULT '0',
  `text_fields` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `redirect_type` ENUM('', '404', '301', '302') NOT NULL DEFAULT '',
  `id_product_redirected` int(10) unsigned NOT NULL DEFAULT '0',
  `available_for_order` tinyint(1) NOT NULL DEFAULT '1',
  `available_date` date NOT NULL DEFAULT '0000-00-00',
  `condition` ENUM('new', 'used', 'refurbished') NOT NULL DEFAULT 'new',
  `show_price` tinyint(1) NOT NULL DEFAULT '1',
  `indexed` tinyint(1) NOT NULL DEFAULT '0',
  `visibility` ENUM('both', 'catalog', 'search', 'none') NOT NULL DEFAULT 'both',
  `cache_is_pack` tinyint(1) NOT NULL DEFAULT '0',
  `cache_has_attachments` tinyint(1) NOT NULL DEFAULT '0',
  `is_virtual` tinyint(1) NOT NULL DEFAULT '0',
  `cache_default_attribute` int(10) unsigned DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `advanced_stock_management` tinyint(1) DEFAULT '0' NOT NULL,
  `pack_stock_type` int(11) unsigned DEFAULT '3' NOT NULL,
  PRIMARY KEY (`id_product`),
  KEY `product_supplier` (`id_supplier`),
  KEY `product_manufacturer` (`id_manufacturer`, `id_product`),
  KEY `id_category_default` (`id_category_default`),
  KEY `indexed` (`indexed`),
  KEY `date_add` (`date_add`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_product_shop` (
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
  `uploadable_files` tinyint(4) NOT NULL DEFAULT '0',
  `text_fields` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `redirect_type` ENUM('', '404', '301', '302') NOT NULL DEFAULT '',
  `id_product_redirected` int(10) unsigned NOT NULL DEFAULT '0',
  `available_for_order` tinyint(1) NOT NULL DEFAULT '1',
  `available_date` date NOT NULL DEFAULT '0000-00-00',
  `condition` enum('new','used','refurbished') NOT NULL DEFAULT 'new',
  `show_price` tinyint(1) NOT NULL DEFAULT '1',
  `indexed` tinyint(1) NOT NULL DEFAULT '0',
  `visibility` enum('both','catalog','search','none') NOT NULL DEFAULT 'both',
  `cache_default_attribute` int(10) unsigned DEFAULT NULL,
  `advanced_stock_management` tinyint(1) DEFAULT '0' NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `pack_stock_type` int(11) unsigned DEFAULT '3' NOT NULL,
  PRIMARY KEY (`id_product`, `id_shop`),
  KEY `id_category_default` (`id_category_default`),
  KEY `date_add` (`date_add` , `active` , `visibility`),
  KEY `indexed` (`indexed`, `active`, `id_product`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_attribute` (
  `id_product_attribute` int(10) unsigned NOT NULL auto_increment,
  `id_product` int(10) unsigned NOT NULL,
  `reference` varchar(32) DEFAULT NULL,
  `supplier_reference` varchar(32) DEFAULT NULL,
  `location` varchar(64) DEFAULT NULL,
  `ean13` varchar(13) DEFAULT NULL,
  `upc` varchar(12) DEFAULT NULL,
  `wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `ecotax` decimal(17,6) NOT NULL DEFAULT '0.00',
  `quantity` int(10) NOT NULL DEFAULT '0',
  `weight` DECIMAL(20,6) NOT NULL DEFAULT '0',
  `unit_price_impact` DECIMAL(20,6) NOT NULL DEFAULT '0.00',
  `default_on` tinyint(1) unsigned NULL DEFAULT NULL,
  `minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `available_date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_product_attribute`),
  KEY `product_attribute_product` (`id_product`),
  KEY `reference` (`reference`),
  KEY `supplier_reference` (`supplier_reference`),
  UNIQUE KEY `product_default` (`id_product`,`default_on`),
  KEY `id_product_id_product_attribute` (`id_product_attribute` , `id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_attribute_shop` (
  `id_product` int(10) unsigned NOT NULL,
  `id_product_attribute` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  `wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `ecotax` decimal(17,6) NOT NULL DEFAULT '0.00',
  `weight` DECIMAL(20,6) NOT NULL DEFAULT '0',
  `unit_price_impact` DECIMAL(20,6) NOT NULL DEFAULT '0.00',
  `default_on` tinyint(1) unsigned NULL DEFAULT NULL,
  `minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `available_date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_product_attribute`, `id_shop`),
  UNIQUE KEY `id_product` (`id_product`, `id_shop`, `default_on`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_attribute_combination` (
  `id_attribute` int(10) unsigned NOT NULL,
  `id_product_attribute` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_attribute`,`id_product_attribute`),
  KEY `id_product_attribute` (`id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_attribute_image` (
  `id_product_attribute` int(10) unsigned NOT NULL,
  `id_image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_product_attribute`,`id_image`),
  KEY `id_image` (`id_image`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_download` (
  `id_product_download` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `display_filename` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_expiration` datetime DEFAULT NULL,
  `nb_days_accessible` int(10) unsigned DEFAULT NULL,
  `nb_downloadable` int(10) unsigned DEFAULT '1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_shareable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_product_download`),
  KEY `product_active` (`id_product`,`active`),
  UNIQUE KEY `id_product` (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_lang` (
  `id_product` int(10) unsigned NOT NULL,
  `id_shop` INT( 11 ) UNSIGNED NOT NULL DEFAULT '1',
  `id_lang` int(10) unsigned NOT NULL,
  `description` text,
  `description_short` text,
  `link_rewrite` varchar(128) NOT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_title` varchar(128) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `available_now` varchar(255) DEFAULT NULL,
  `available_later` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_product`, `id_shop` , `id_lang`),
  KEY `id_lang` (`id_lang`),
  KEY `name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_sale` (
  `id_product` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `sale_nbr` int(10) unsigned NOT NULL DEFAULT '0',
  `date_upd` date NOT NULL,
  PRIMARY KEY (`id_product`),
  KEY `quantity` (`quantity`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_tag` (
  `id_product` int(10) unsigned NOT NULL,
  `id_tag` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_product`,`id_tag`),
  KEY `id_tag` (`id_tag`),
  KEY `id_lang` (`id_lang`,`id_tag`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_profile` (
  `id_profile` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY (`id_profile`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_profile_lang` (
  `id_lang` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id_profile`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_quick_access` (
  `id_quick_access` int(10) unsigned NOT NULL auto_increment,
  `new_window` tinyint(1) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id_quick_access`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_quick_access_lang` (
  `id_quick_access` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_quick_access`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_range_price` (
  `id_range_price` int(10) unsigned NOT NULL auto_increment,
  `id_carrier` int(10) unsigned NOT NULL,
  `delimiter1` decimal(20,6) NOT NULL,
  `delimiter2` decimal(20,6) NOT NULL,
  PRIMARY KEY (`id_range_price`),
  UNIQUE KEY `id_carrier` (`id_carrier`,`delimiter1`,`delimiter2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_range_weight` (
  `id_range_weight` int(10) unsigned NOT NULL auto_increment,
  `id_carrier` int(10) unsigned NOT NULL,
  `delimiter1` decimal(20,6) NOT NULL,
  `delimiter2` decimal(20,6) NOT NULL,
  PRIMARY KEY (`id_range_weight`),
  UNIQUE KEY `id_carrier` (`id_carrier`,`delimiter1`,`delimiter2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_referrer` (
  `id_referrer` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `http_referer_regexp` varchar(64) DEFAULT NULL,
  `http_referer_like` varchar(64) DEFAULT NULL,
  `request_uri_regexp` varchar(64) DEFAULT NULL,
  `request_uri_like` varchar(64) DEFAULT NULL,
  `http_referer_regexp_not` varchar(64) DEFAULT NULL,
  `http_referer_like_not` varchar(64) DEFAULT NULL,
  `request_uri_regexp_not` varchar(64) DEFAULT NULL,
  `request_uri_like_not` varchar(64) DEFAULT NULL,
  `base_fee` decimal(5,2) NOT NULL DEFAULT '0.00',
  `percent_fee` decimal(5,2) NOT NULL DEFAULT '0.00',
  `click_fee` decimal(5,2) NOT NULL DEFAULT '0.00',
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_referrer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_referrer_cache` (
  `id_connections_source` int(11) unsigned NOT NULL,
  `id_referrer` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_connections_source`, `id_referrer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_referrer_shop` (
  `id_referrer` int(10) unsigned NOT NULL auto_increment,
  `id_shop` int(10) unsigned NOT NULL DEFAULT '1',
  `cache_visitors` int(11) DEFAULT NULL,
  `cache_visits` int(11) DEFAULT NULL,
  `cache_pages` int(11) DEFAULT NULL,
  `cache_registrations` int(11) DEFAULT NULL,
  `cache_orders` int(11) DEFAULT NULL,
  `cache_sales` decimal(17,2) DEFAULT NULL,
  `cache_reg_rate` decimal(5,4) DEFAULT NULL,
  `cache_order_rate` decimal(5,4) DEFAULT NULL,
  PRIMARY KEY (`id_referrer`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_request_sql` (
  `id_request_sql` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sql` text NOT NULL,
  PRIMARY KEY (`id_request_sql`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_scene` (
  `id_scene` int(10) unsigned NOT NULL auto_increment,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_scene`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_scene_category` (
  `id_scene` int(10) unsigned NOT NULL,
  `id_category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_scene`,`id_category`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_scene_lang` (
  `id_scene` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id_scene`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_scene_products` (
  `id_scene` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `x_axis` int(4) NOT NULL,
  `y_axis` int(4) NOT NULL,
  `zone_width` int(3) NOT NULL,
  `zone_height` int(3) NOT NULL,
  PRIMARY KEY (`id_scene`, `id_product`, `x_axis`, `y_axis`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_search_engine` (
  `id_search_engine` int(10) unsigned NOT NULL auto_increment,
  `server` varchar(64) NOT NULL,
  `getvar` varchar(16) NOT NULL,
  PRIMARY KEY (`id_search_engine`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_search_index` (
  `id_product` int(11) unsigned NOT NULL,
  `id_word` int(11) unsigned NOT NULL,
  `weight` smallint(4) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_word`, `id_product`),
  KEY `id_product` (`id_product`,`weight`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_search_word` (
  `id_word` int(10) unsigned NOT NULL auto_increment,
  `id_shop` int(11) unsigned NOT NULL DEFAULT 1,
  `id_lang` int(10) unsigned NOT NULL,
  `word` varchar(15) NOT NULL,
  PRIMARY KEY (`id_word`),
  UNIQUE KEY `id_lang` (`id_lang`,`id_shop`, `word`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_specific_price` (
	`id_specific_price` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_specific_price_rule` INT(11) UNSIGNED NOT NULL,
	`id_cart` INT(11) UNSIGNED NOT NULL,
	`id_product` INT UNSIGNED NOT NULL,
	`id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
	`id_shop_group` INT(11) UNSIGNED NOT NULL,
	`id_currency` INT UNSIGNED NOT NULL,
	`id_country` INT UNSIGNED NOT NULL,
	`id_group` INT UNSIGNED NOT NULL,
	`id_customer` INT UNSIGNED NOT NULL,
	`id_product_attribute` INT UNSIGNED NOT NULL,
	`price` DECIMAL(20, 6) NOT NULL,
	`from_quantity` mediumint(8) UNSIGNED NOT NULL,
 	`reduction` DECIMAL(20, 6) NOT NULL,
 	`reduction_tax` tinyint(1) NOT NULL DEFAULT 1,
	`reduction_type` ENUM('amount', 'percentage') NOT NULL,
	`from` DATETIME NOT NULL,
	`to` DATETIME NOT NULL,
	PRIMARY KEY (`id_specific_price`),
	KEY (`id_product`, `id_shop`, `id_currency`, `id_country`, `id_group`, `id_customer`, `from_quantity`, `from`, `to`),
	KEY `from_quantity` (`from_quantity`),
	KEY (`id_specific_price_rule`),
	KEY (`id_cart`),
  KEY `id_product_attribute` (`id_product_attribute`),
  KEY `id_shop` (`id_shop`),
  KEY `id_customer` (`id_customer`),
  KEY `from` (`from`),
  KEY `to` (`to`),
  UNIQUE KEY `id_product_2` (`id_product`,`id_product_attribute`,`id_customer`,`id_cart`,`from`,`to`,`id_shop`,`id_shop_group`,`id_currency`,`id_country`,`id_group`,`from_quantity`,`id_specific_price_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_state` (
  `id_state` int(10) unsigned NOT NULL auto_increment,
  `id_country` int(11) unsigned NOT NULL,
  `id_zone` int(11) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `iso_code` varchar(7) NOT NULL,
  `tax_behavior` smallint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_state`),
  KEY `id_country` (`id_country`),
  KEY `name` (`name`),
  KEY `id_zone` (`id_zone`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_supplier` (
  `id_supplier` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_supplier`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_supplier_lang` (
  `id_supplier` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `description` text,
  `meta_title` varchar(128) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_supplier`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tab` (
  `id_tab` int(10) unsigned NOT NULL auto_increment,
  `id_parent` int(11) NOT NULL,
  `class_name` varchar(64) NOT NULL,
  `module` varchar(64) NULL,
  `position` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `hide_host_mode` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tab`),
  KEY `class_name` (`class_name`),
  KEY `id_parent` (`id_parent`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tab_lang` (
  `id_tab` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_tab`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tag` (
  `id_tag` int(10) unsigned NOT NULL auto_increment,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_tag`),
  KEY `tag_name` (`name`),
  KEY `id_lang` (`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tag_count` (
  `id_group` int(10) unsigned NOT NULL DEFAULT 0,
  `id_tag` int(10) unsigned NOT NULL DEFAULT 0,
  `id_lang` int(10) unsigned NOT NULL DEFAULT 0,
  `id_shop` int(11) unsigned NOT NULL DEFAULT 0,
  `counter` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_group`, `id_tag`),
  KEY (`id_group`, `id_lang`, `id_shop`, `counter`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tax` (
  `id_tax` int(10) unsigned NOT NULL auto_increment,
  `rate` DECIMAL(10, 3) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tax_lang` (
  `id_tax` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_tax`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_timezone` (
	id_timezone int(10) unsigned NOT NULL auto_increment,
	name VARCHAR(32) NOT NULL,
	PRIMARY KEY (`id_timezone`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_web_browser` (
  `id_web_browser` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_web_browser`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_zone` (
  `id_zone` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_zone`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_carrier_group` (
  `id_carrier` int(10) unsigned NOT NULL,
  `id_group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_carrier`,`id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_store` (
  `id_store` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_country` int(10) unsigned NOT NULL,
  `id_state` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `city` varchar(64) NOT NULL,
  `postcode` varchar(12) NOT NULL,
  `latitude` decimal(13,8) DEFAULT NULL,
  `longitude` decimal(13,8) DEFAULT NULL,
  `hours` varchar(254) DEFAULT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `fax` varchar(16) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `note` text,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_store`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_webservice_account` (
  `id_webservice_account` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `description` text NULL,
  `class_name` VARCHAR( 50 ) NOT NULL DEFAULT 'WebserviceRequest',
  `is_module` TINYINT( 2 ) NOT NULL DEFAULT '0',
  `module_name` VARCHAR( 50 ) NULL DEFAULT NULL,
  `active` tinyint(2) NOT NULL,
  PRIMARY KEY (`id_webservice_account`),
  KEY `key` (`key`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_webservice_permission` (
  `id_webservice_permission` int(11) NOT NULL AUTO_INCREMENT,
  `resource` varchar(50) NOT NULL,
  `method` enum('GET','POST','PUT','DELETE','HEAD') NOT NULL,
  `id_webservice_account` int(11) NOT NULL,
  PRIMARY KEY (`id_webservice_permission`),
  UNIQUE KEY `resource_2` (`resource`,`method`,`id_webservice_account`),
  KEY `resource` (`resource`),
  KEY `method` (`method`),
  KEY `id_webservice_account` (`id_webservice_account`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_required_field` (
  `id_required_field` int(11) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(32) NOT NULL,
  `field_name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_required_field`),
  KEY `object_name` (`object_name`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_memcached_servers` (
`id_memcached_server` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ip` VARCHAR( 254 ) NOT NULL ,
`port` INT(11) UNSIGNED NOT NULL ,
`weight` INT(11) UNSIGNED NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_country_tax` (
  `id_product` int(11) NOT NULL,
  `id_country` int(11) NOT NULL,
  `id_tax` int(11) NOT NULL,
  PRIMARY KEY (`id_product`,`id_country`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tax_rule` (
  `id_tax_rule` int(11) NOT NULL AUTO_INCREMENT,
  `id_tax_rules_group` int(11) NOT NULL,
  `id_country` int(11) NOT NULL,
  `id_state` int(11) NOT NULL,
  `zipcode_from` VARCHAR(12) NOT NULL,
  `zipcode_to` VARCHAR(12) NOT NULL,
  `id_tax` int(11) NOT NULL,
  `behavior` int(11) NOT NULL,
  `description` VARCHAR( 100 ) NOT NULL,
  PRIMARY KEY (`id_tax_rule`),
  KEY `id_tax_rules_group` (`id_tax_rules_group`),
  KEY `id_tax` (`id_tax`),
  KEY `category_getproducts` ( `id_tax_rules_group` , `id_country` , `id_state` , `zipcode_from` )
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tax_rules_group` (
`id_tax_rules_group` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`name` VARCHAR( 50 ) NOT NULL,
`active` INT NOT NULL,
`deleted` TINYINT(1) UNSIGNED NOT NULL,
`date_add` DATETIME NOT NULL,
`date_upd` DATETIME NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_specific_price_priority` (
	`id_specific_price_priority` INT NOT NULL AUTO_INCREMENT ,
	`id_product` INT NOT NULL ,
	`priority` VARCHAR( 80 ) NOT NULL ,
	PRIMARY KEY ( `id_specific_price_priority` , `id_product` ),
	UNIQUE KEY `id_product` (`id_product`)
)  ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_log` (
	`id_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`severity` tinyint(1) NOT NULL,
	`error_code` int(11) DEFAULT NULL,
	`message` text NOT NULL,
	`object_type` varchar(32) DEFAULT NULL,
	`object_id` int(10) unsigned DEFAULT NULL,
	`id_employee` int(10) unsigned DEFAULT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_log`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_import_match` (
  `id_import_match` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `match` text NOT NULL,
  `skip` int(2) NOT NULL,
  PRIMARY KEY (`id_import_match`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_shop_group` (
  `id_shop_group` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `share_customer` TINYINT(1) NOT NULL,
  `share_order` TINYINT(1) NOT NULL,
  `share_stock` TINYINT(1) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_shop_group`),
  KEY `deleted` (`deleted`, `name`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_shop` (
  `id_shop` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop_group` int(11) unsigned NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `id_category` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_theme` INT(1) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_shop`),
  KEY `id_shop_group` (`id_shop_group`, `deleted`),
  KEY `id_category` (`id_category`),
  KEY `id_theme` (`id_theme`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_shop_url` (
  `id_shop_url` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) unsigned NOT NULL,
  `domain` varchar(150) NOT NULL,
  `domain_ssl` varchar(150) NOT NULL,
  `physical_uri` varchar(64) NOT NULL,
  `virtual_uri` varchar(64) NOT NULL,
  `main` TINYINT(1) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_shop_url`),
  KEY `id_shop` (`id_shop`, `main`),
  UNIQUE KEY `full_shop_url` (`domain`, `physical_uri`, `virtual_uri`),
  UNIQUE KEY `full_shop_url_ssl` (`domain_ssl`, `physical_uri`, `virtual_uri`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_theme` (
  `id_theme` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `directory` varchar(64) NOT NULL,
  `responsive` tinyint(1) NOT NULL DEFAULT '0',
  `default_left_column` tinyint(1) NOT NULL DEFAULT '0',
  `default_right_column` tinyint(1) NOT NULL DEFAULT '0',
  `product_per_page` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_theme`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_theme_meta` (
  `id_theme_meta` int(11) NOT NULL AUTO_INCREMENT,
  `id_theme` int(11) NOT NULL,
  `id_meta` int(10) unsigned NOT NULL,
  `left_column` tinyint(1) NOT NULL DEFAULT '1',
  `right_column` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_theme_meta`),
  UNIQUE KEY `id_theme_2` (`id_theme`,`id_meta`),
  KEY `id_theme` (`id_theme`),
  KEY `id_meta` (`id_meta`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `PREFIX_theme_specific` (
  `id_theme` int(11) unsigned NOT NULL,
	`id_shop` INT(11) UNSIGNED NOT NULL,
  `entity` int(11) unsigned NOT NULL,
  `id_object` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_theme`,`id_shop`, `entity`,`id_object`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_country_shop` (
`id_country` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_country`, `id_shop`),
  KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_carrier_shop` (
`id_carrier` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
PRIMARY KEY (`id_carrier`, `id_shop`),
  KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_address_format` (
  `id_country` int(10) unsigned NOT NULL,
  `format` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_country`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_cms_shop` (
`id_cms` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_cms`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_lang_shop` (
`id_lang` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_lang`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_currency_shop` (
`id_currency` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
`conversion_rate` decimal(13,6) NOT NULL,
  PRIMARY KEY (`id_currency`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_contact_shop` (
	`id_contact` INT(11) UNSIGNED NOT NULL,
	`id_shop` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_contact`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_image_shop` (
  `id_product` int(10) unsigned NOT NULL,
	`id_image` INT( 11 ) UNSIGNED NOT NULL,
	`id_shop` INT( 11 ) UNSIGNED NOT NULL,
	`cover` tinyint(1) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`id_image`, `id_shop`),
	UNIQUE KEY `id_product` (`id_product`, `id_shop`, `cover`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_attribute_shop` (
`id_attribute` INT(11) UNSIGNED NOT NULL,
`id_shop` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_attribute`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_feature_shop` (
`id_feature` INT(11) UNSIGNED NOT NULL,
`id_shop` INT(11) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_feature`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_group_shop` (
`id_group` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_group`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_attribute_group_shop` (
`id_attribute_group` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_attribute_group`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tax_rules_group_shop` (
	`id_tax_rules_group` INT( 11 ) UNSIGNED NOT NULL,
	`id_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_tax_rules_group`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_zone_shop` (
`id_zone` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_zone`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_manufacturer_shop` (
`id_manufacturer` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_manufacturer`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_supplier_shop` (
`id_supplier` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_supplier`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_store_shop` (
`id_store` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_store`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_module_shop` (
`id_module` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
`enable_device` TINYINT(1) NOT NULL DEFAULT  '7',
PRIMARY KEY (`id_module` , `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_webservice_account_shop` (
`id_webservice_account` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_webservice_account` , `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_scene_shop` (
`id_scene` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_scene`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_stock_mvt` (
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
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_stock_mvt_reason` (
  `id_stock_mvt_reason` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sign` tinyint(1) NOT NULL DEFAULT 1,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stock_mvt_reason`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_stock_mvt_reason_lang` (
  `id_stock_mvt_reason` INT(11) UNSIGNED NOT NULL,
  `id_lang` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id_stock_mvt_reason`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_stock` (
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
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_warehouse` (
`id_warehouse` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_currency` INT(11) UNSIGNED NOT NULL,
`id_address` INT(11) UNSIGNED NOT NULL,
`id_employee` INT(11) UNSIGNED NOT NULL,
`reference` VARCHAR(32) DEFAULT NULL,
`name` VARCHAR(45) NOT NULL,
`management_type` ENUM('WA', 'FIFO', 'LIFO') NOT NULL DEFAULT 'WA',
`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_warehouse`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_warehouse_product_location` (
  `id_warehouse_product_location` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) unsigned NOT NULL,
  `id_product_attribute` int(11) unsigned NOT NULL,
  `id_warehouse` int(11) unsigned NOT NULL,
  `location` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_warehouse_product_location`),
  UNIQUE KEY `id_product` (`id_product`,`id_product_attribute`,`id_warehouse`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_warehouse_shop` (
`id_shop` INT(11) UNSIGNED NOT NULL,
`id_warehouse` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_warehouse`, `id_shop`),
  KEY `id_warehouse` (`id_warehouse`),
  KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_warehouse_carrier` (
`id_carrier` INT(11) UNSIGNED NOT NULL,
`id_warehouse` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_warehouse`, `id_carrier`),
  KEY `id_warehouse` (`id_warehouse`),
  KEY `id_carrier` (`id_carrier`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_stock_available` (
`id_stock_available` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_product` INT(11) UNSIGNED NOT NULL,
`id_product_attribute` INT(11) UNSIGNED NOT NULL,
`id_shop` INT(11) UNSIGNED NOT NULL,
`id_shop_group` INT(11) UNSIGNED NOT NULL,
`quantity` INT(10) NOT NULL DEFAULT '0',
`depends_on_stock` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
`out_of_stock` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stock_available`),
  KEY `id_shop` (`id_shop`),
  KEY `id_shop_group` (`id_shop_group`),
  KEY `id_product` (`id_product`),
  KEY `id_product_attribute` (`id_product_attribute`),
  UNIQUE `product_sqlstock` (`id_product` , `id_product_attribute` , `id_shop`, `id_shop_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_supply_order` (
`id_supply_order` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_supplier` INT(11) UNSIGNED NOT NULL,
`supplier_name` VARCHAR(64) NOT NULL,
`id_lang` INT(11) UNSIGNED NOT NULL,
`id_warehouse` INT(11) UNSIGNED NOT NULL,
`id_supply_order_state` INT(11) UNSIGNED NOT NULL,
`id_currency` INT(11) UNSIGNED NOT NULL,
`id_ref_currency` INT(11) UNSIGNED NOT NULL,
`reference` VARCHAR(64) NOT NULL,
`date_add` DATETIME NOT NULL,
`date_upd` DATETIME NOT NULL,
`date_delivery_expected` DATETIME DEFAULT NULL,
`total_te` DECIMAL(20,6) DEFAULT '0.000000',
`total_with_discount_te` DECIMAL(20,6) DEFAULT '0.000000',
`total_tax` DECIMAL(20,6) DEFAULT '0.000000',
`total_ti` DECIMAL(20,6) DEFAULT '0.000000',
`discount_rate` DECIMAL(20,6) DEFAULT '0.000000',
`discount_value_te` DECIMAL(20,6) DEFAULT '0.000000',
`is_template` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_supply_order`),
  KEY `id_supplier` (`id_supplier`),
  KEY `id_warehouse` (`id_warehouse`),
  KEY `reference` (`reference`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_supply_order_detail` (
`id_supply_order_detail` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_supply_order` INT(11) UNSIGNED NOT NULL,
`id_currency` INT(11) UNSIGNED NOT NULL,
`id_product` INT(11) UNSIGNED NOT NULL,
`id_product_attribute` INT(11) UNSIGNED NOT NULL,
`reference`  VARCHAR(32) NOT NULL,
`supplier_reference`  VARCHAR(32) NOT NULL,
`name`  varchar(128) NOT NULL,
`ean13`  VARCHAR(13) DEFAULT NULL,
`upc`  VARCHAR(12) DEFAULT NULL,
`exchange_rate` DECIMAL(20,6) DEFAULT '0.000000',
`unit_price_te` DECIMAL(20,6) DEFAULT '0.000000',
`quantity_expected` INT(11) UNSIGNED NOT NULL,
`quantity_received` INT(11) UNSIGNED NOT NULL,
`price_te` DECIMAL(20,6) DEFAULT '0.000000',
`discount_rate` DECIMAL(20,6) DEFAULT '0.000000',
`discount_value_te` DECIMAL(20,6) DEFAULT '0.000000',
`price_with_discount_te` DECIMAL(20,6) DEFAULT '0.000000',
`tax_rate` DECIMAL(20,6) DEFAULT '0.000000',
`tax_value` DECIMAL(20,6) DEFAULT '0.000000',
`price_ti` DECIMAL(20,6) DEFAULT '0.000000',
`tax_value_with_order_discount` DECIMAL(20,6) DEFAULT '0.000000',
`price_with_order_discount_te` DECIMAL(20,6) DEFAULT '0.000000',
  PRIMARY KEY (`id_supply_order_detail`),
  KEY `id_supply_order` (`id_supply_order`, `id_product`),
  KEY `id_product_attribute` (`id_product_attribute`),
  KEY `id_product_product_attribute` (`id_product`, `id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_supply_order_history` (
`id_supply_order_history` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_supply_order` INT(11) UNSIGNED NOT NULL,
`id_employee` INT(11) UNSIGNED NOT NULL,
`employee_lastname` varchar(32) DEFAULT '',
`employee_firstname` varchar(32) DEFAULT '',
`id_state` INT(11) UNSIGNED NOT NULL,
`date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id_supply_order_history`),
  KEY `id_supply_order` (`id_supply_order`),
  KEY `id_employee` (`id_employee`),
  KEY `id_state` (`id_state`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_supply_order_state` (
`id_supply_order_state` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`delivery_note` tinyint(1) NOT NULL DEFAULT '0',
`editable` tinyint(1) NOT NULL DEFAULT '0',
`receipt_state` tinyint(1) NOT NULL DEFAULT '0',
`pending_receipt` tinyint(1) NOT NULL DEFAULT '0',
`enclosed` tinyint(1) NOT NULL DEFAULT '0',
`color` VARCHAR(32) DEFAULT NULL,
  PRIMARY KEY (`id_supply_order_state`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_supply_order_state_lang` (
`id_supply_order_state` INT(11) UNSIGNED NOT NULL,
`id_lang` INT(11) UNSIGNED NOT NULL,
`name` VARCHAR(128) DEFAULT NULL,
  PRIMARY KEY (`id_supply_order_state`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_supply_order_receipt_history` (
`id_supply_order_receipt_history` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_supply_order_detail` INT(11) UNSIGNED NOT NULL,
`id_employee` INT(11) UNSIGNED NOT NULL,
`employee_lastname` varchar(32) DEFAULT '',
`employee_firstname` varchar(32) DEFAULT '',
`id_supply_order_state` INT(11) UNSIGNED NOT NULL,
`quantity` INT(11) UNSIGNED NOT NULL,
`date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id_supply_order_receipt_history`),
  KEY `id_supply_order_detail` (`id_supply_order_detail`),
  KEY `id_supply_order_state` (`id_supply_order_state`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_product_supplier` (
  `id_product_supplier` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_product` int(11) UNSIGNED NOT NULL,
  `id_product_attribute` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `id_supplier` int(11) UNSIGNED NOT NULL,
  `product_supplier_reference` varchar(32) DEFAULT NULL,
  `product_supplier_price_te` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `id_currency` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_product_supplier`),
  UNIQUE KEY `id_product` (`id_product`,`id_product_attribute`,`id_supplier`),
  KEY `id_supplier` (`id_supplier`,`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_carrier` (
  `id_order_carrier` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(11) unsigned NOT NULL,
  `id_carrier` int(11) unsigned NOT NULL,
  `id_order_invoice` int(11) unsigned DEFAULT NULL,
  `weight` DECIMAL(20,6) DEFAULT NULL,
  `shipping_cost_tax_excl` decimal(20,6) DEFAULT NULL,
  `shipping_cost_tax_incl` decimal(20,6) DEFAULT NULL,
  `tracking_number` varchar(64) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_order_carrier`),
  KEY `id_order` (`id_order`),
  KEY `id_carrier` (`id_carrier`),
  KEY `id_order_invoice` (`id_order_invoice`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

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
 	`reduction_tax` tinyint(1) NOT NULL DEFAULT 1,
	`reduction_type` enum('amount','percentage') NOT NULL,
	`from` datetime NOT NULL,
	`to` datetime NOT NULL,
	PRIMARY KEY (`id_specific_price_rule`),
	KEY `id_product` (`id_shop`,`id_currency`,`id_country`,`id_group`,`from_quantity`,`from`,`to`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_specific_price_rule_condition_group` (
	`id_specific_price_rule_condition_group` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_specific_price_rule` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY ( `id_specific_price_rule_condition_group`, `id_specific_price_rule` )
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_specific_price_rule_condition` (
	`id_specific_price_rule_condition` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_specific_price_rule_condition_group` INT(11) UNSIGNED NOT NULL,
	`type` VARCHAR(255) NOT NULL,
	`value` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id_specific_price_rule_condition`),
INDEX (`id_specific_price_rule_condition_group`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_risk` (
  `id_risk` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `percent` tinyint(3) NOT NULL,
  `color` varchar(32) NULL,
  PRIMARY KEY (`id_risk`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_risk_lang` (
  `id_risk` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id_risk`,`id_lang`),
  KEY `id_risk` (`id_risk`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_category_shop` (
  `id_category` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_category`, `id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_module_preference` (
  `id_module_preference` int(11) NOT NULL auto_increment,
  `id_employee` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  `interest` tinyint(1) DEFAULT NULL,
  `favorite` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_module_preference`),
  UNIQUE KEY `employee_module` (`id_employee`, `module`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tab_module_preference` (
  `id_tab_module_preference` int(11) NOT NULL auto_increment,
  `id_employee` int(11) NOT NULL,
  `id_tab` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  PRIMARY KEY (`id_tab_module_preference`),
  UNIQUE KEY `employee_module` (`id_employee`, `id_tab`, `module`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_carrier_tax_rules_group_shop` (
	`id_carrier` int( 11 ) unsigned NOT NULL,
	`id_tax_rules_group` int(11) unsigned NOT NULL,
	`id_shop` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id_carrier`, `id_tax_rules_group`, `id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_order_invoice_payment` (
	`id_order_invoice` int(11) unsigned NOT NULL,
	`id_order_payment` int(11) unsigned NOT NULL,
	`id_order_payment_detail` int(11) unsigned NOT NULL,
	`id_order` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id_order_invoice`,`id_order_payment`),
	KEY `order_payment` (`id_order_payment`),
	KEY `id_order` (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_smarty_cache` (
  `id_smarty_cache` char(40) NOT NULL,
  `name` char(40) NOT NULL,
  `cache_id` varchar(254) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id_smarty_cache`),
  KEY `name` (`name`),
  KEY `cache_id` (`cache_id`),
  KEY `modified` (`modified`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_order_slip_detail_tax` (
  `id_order_slip_detail` int(11) unsigned NOT NULL,
  `id_tax` int(11) unsigned NOT NULL,
  `unit_amount` decimal(16,6) NOT NULL DEFAULT '0.000000',
  `total_amount` decimal(16,6) NOT NULL DEFAULT '0.000000',
  KEY (`id_order_slip_detail`),
  KEY `id_tax` (`id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_mail` (
  `id_mail` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipient` varchar(126) NOT NULL,
  `template` varchar(62) NOT NULL,
  `subject` varchar(254) NOT NULL,
  `id_lang` int(11) unsigned NOT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mail`),
  KEY `recipient` (`recipient`(10))
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_smarty_lazy_cache` (
  `template_hash` varchar(32) NOT NULL DEFAULT '',
  `cache_id` varchar(255) NOT NULL DEFAULT '',
  `compile_id` varchar(32) NOT NULL DEFAULT '',
  `filepath` varchar(255) NOT NULL DEFAULT '',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`template_hash`, `cache_id`, `compile_id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_smarty_last_flush` (
  `type` ENUM('compile', 'template'),
  `last_flush` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`type`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_modules_perfs` (
  `id_modules_perfs` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `session` int(11) unsigned NOT NULL,
  `module` varchar(62) NOT NULL,
  `method` varchar(126) NOT NULL,
  `time_start` double unsigned NOT NULL,
  `time_end` double unsigned NOT NULL,
  `memory_start` int unsigned NOT NULL,
  `memory_end` int unsigned NOT NULL,
  PRIMARY KEY (`id_modules_perfs`),
  KEY (`session`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_cms_role` (
  `id_cms_role` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `id_cms` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_cms_role`, `id_cms`),
  UNIQUE KEY `name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_cms_role_lang` (
  `id_cms_role` int(11) unsigned NOT NULL,
  `id_lang` int(11) unsigned NOT NULL,
  `id_shop` int(11) unsigned NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_cms_role`,`id_lang`, id_shop)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `PREFIX_maintenance_access` (
  `id_maintenance_access` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(50) NOT NULL,
  `email` varchar(128) NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_maintenance_access`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
