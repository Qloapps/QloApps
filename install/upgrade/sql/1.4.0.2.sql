SET NAMES 'utf8';

ALTER TABLE `PREFIX_employee` ADD `bo_color` varchar(32) default NULL AFTER `stats_date_to`;
ALTER TABLE `PREFIX_employee` ADD `bo_theme` varchar(32) default NULL AFTER `bo_color`;
ALTER TABLE `PREFIX_employee` ADD `bo_uimode` ENUM('hover','click') default 'click' AFTER `bo_theme`;
ALTER TABLE `PREFIX_employee` ADD `id_lang` int(10) unsigned NOT NULL default 0 AFTER `id_profile`;

ALTER TABLE `PREFIX_cms` ADD `id_cms_category` int(10) unsigned NOT NULL default '0' AFTER `id_cms`;
ALTER TABLE `PREFIX_cms` ADD `position` int(10) unsigned NOT NULL default '0' AFTER `id_cms_category`;

CREATE TABLE `PREFIX_cms_category` (
  `id_cms_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) unsigned NOT NULL,
  `level_depth` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `position` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`id_cms_category`),
  KEY `category_parent` (`id_parent`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_cms_category_lang` (
  `id_cms_category` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text,
  `link_rewrite` varchar(128) NOT NULL,
  `meta_title` varchar(128) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  UNIQUE KEY `category_lang_index` (`id_cms_category`,`id_lang`),
  KEY `category_name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_cms_category_lang` VALUES(1, 1, 'Home', '', 'home', NULL, NULL, NULL);
INSERT INTO `PREFIX_cms_category_lang` VALUES(1, 2, 'Accueil', '', 'home', NULL, NULL, NULL);
INSERT INTO `PREFIX_cms_category_lang` VALUES(1, 3, 'Inicio', '', 'home', NULL, NULL, NULL);

INSERT INTO `PREFIX_cms_category` VALUES(1, 0, 0, 1, NOW(), NOW(),0);

UPDATE `PREFIX_cms_category` SET `position` = 0;
UPDATE `PREFIX_cms` SET `position` = 0;
UPDATE `PREFIX_cms` SET `id_cms_category` = 0;

ALTER TABLE `PREFIX_category` ADD `position` int(10) unsigned NOT NULL default '0' AFTER `date_upd`;

UPDATE `PREFIX_employee` SET `id_lang` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = "PS_LANG_DEFAULT");

ALTER TABLE `PREFIX_customer` ADD `note` text AFTER `secure_key`;

ALTER TABLE `PREFIX_contact` ADD `customer_service` tinyint(1) NOT NULL DEFAULT 0 AFTER `email`;

CREATE TABLE `PREFIX_customer_thread` (
  `id_customer_thread` int(11) unsigned NOT NULL auto_increment,
  `id_lang` int(10) unsigned NOT NULL,
  `id_contact` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned default NULL,
  `id_order` int(10) unsigned default NULL,
  `id_product` int(10) unsigned default NULL,
  `status` enum('open','closed','pending1','pending2') NOT NULL default 'open',
  `email` varchar(128) NOT NULL,
  `token` varchar(12) default NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_customer_thread`),
  KEY `id_customer_thread` (`id_customer_thread`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_customer_message` (
  `id_customer_message` int(10) unsigned NOT NULL auto_increment,
  `id_customer_thread` int(11) default NULL,
  `id_employee` int(10) unsigned default NULL,
  `message` text NOT NULL,
  `file_name` varchar(18) DEFAULT NULL,
  `ip_address` int(11) default NULL,
  `user_agent` varchar(128) default NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY  (`id_customer_message`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_payment_cc` (
	`id_payment_cc` INT NOT NULL auto_increment,
	`id_order` INT UNSIGNED NULL,
	`id_currency` INT UNSIGNED NOT NULL,
	`amount` DECIMAL(10,2) NOT NULL,
	`transaction_id` VARCHAR(254) NULL,
	`card_number` VARCHAR(254) NULL,
	`card_brand` VARCHAR(254) NULL,
	`card_expiration` CHAR(7) NULL,
	`card_holder` VARCHAR(254) NULL,
	`date_add` DATETIME NOT NULL,
	PRIMARY KEY (`id_payment_cc`),
	KEY `id_order` (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_specific_price` (
	`id_specific_price` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_product` INT UNSIGNED NOT NULL,
	`id_shop` TINYINT UNSIGNED NOT NULL,
	`id_currency` INT UNSIGNED NOT NULL,
	`id_country` INT UNSIGNED NOT NULL,
	`id_group` INT UNSIGNED NOT NULL,
	`priority` SMALLINT UNSIGNED NOT NULL,
	`price` DECIMAL(20, 6) NOT NULL,
	`from_quantity` SMALLINT UNSIGNED NOT NULL,
	`reduction` DECIMAL(20, 6) NOT NULL,
	`reduction_type` ENUM('amount', 'percentage') NOT NULL,
	`from` DATETIME NOT NULL,
	`to` DATETIME NOT NULL,
	PRIMARY KEY(`id_specific_price`),
	KEY (`id_product`, `id_shop`, `id_currency`, `id_country`, `id_group`, `from_quantity`, `from`, `to`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_specific_price` (`id_product`, `id_shop`, `id_currency`, `id_country`, `id_group`, `priority`, `price`, `from_quantity`, `reduction`, `reduction_type`, `from`, `to`)
	(	SELECT dq.`id_product`, 1, 1, 0, 1, 0, 0.00, dq.`quantity`, IF(dq.`id_discount_type` = 2, dq.`value`, dq.`value` / 100), IF (dq.`id_discount_type` = 2, 'amount', 'percentage'), '0000-00-00 00:00:00', '0000-00-00 00:00:00'
		FROM `PREFIX_discount_quantity` dq
		INNER JOIN `PREFIX_product` p ON (p.`id_product` = dq.`id_product`)
	);
DROP TABLE `PREFIX_discount_quantity`;

INSERT INTO `PREFIX_specific_price` (`id_product`, `id_shop`, `id_currency`, `id_country`, `id_group`, `priority`, `price`, `from_quantity`, `reduction`, `reduction_type`, `from`, `to`) (
	SELECT
		p.`id_product`,
		1,
		0,
		0,
		0,
		0,
		0.00,
		1,
		IF(p.`reduction_price` > 0, p.`reduction_price`, p.`reduction_percent` / 100),
		IF(p.`reduction_price` > 0, 'amount', 'percentage'),
		IF (p.`reduction_from` = p.`reduction_to`, '0000-00-00 00:00:00', p.`reduction_from`),
		IF (p.`reduction_from` = p.`reduction_to`, '0000-00-00 00:00:00', p.`reduction_to`)
	FROM `PREFIX_product` p
	WHERE p.`reduction_price` OR p.`reduction_percent`
);
ALTER TABLE `PREFIX_product`
	DROP `reduction_price`,
	DROP `reduction_percent`,
	DROP `reduction_from`,
	DROP `reduction_to`;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_SPECIFIC_PRICE_PRIORITIES', 'id_shop;id_currency;id_country;id_group', NOW(), NOW()),
('PS_TAX_DISPLAY', 0, NOW(), NOW()),
('PS_SMARTY_FORCE_COMPILE', 1, NOW(), NOW()),
('PS_DISTANCE_UNIT', 'km', NOW(), NOW()),
('PS_STORES_DISPLAY_CMS', 0, NOW(), NOW()),
('PS_STORES_DISPLAY_FOOTER', 0, NOW(), NOW()),
('PS_STORES_SIMPLIFIED', 0, NOW(), NOW()),
('PS_STATSDATA_CUSTOMER_PAGESVIEWS', 1, NOW(), NOW()),
('PS_STATSDATA_PAGESVIEWS', 1, NOW(), NOW()),
('PS_STATSDATA_PLUGINS', 1, NOW(), NOW());

CREATE TABLE `PREFIX_group_reduction` (
	`id_group_reduction` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_group` INT(10) UNSIGNED NOT NULL,
	`id_category` INT(10) UNSIGNED NOT NULL,
	`reduction` DECIMAL(4, 3) NOT NULL,
	PRIMARY KEY(`id_group_reduction`),
	UNIQUE KEY(`id_group`, `id_category`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_product_group_reduction_cache` (
	`id_product` INT UNSIGNED NOT NULL,
	`id_group` INT UNSIGNED NOT NULL,
	`reduction` DECIMAL(4, 3) NOT NULL,
	PRIMARY KEY(`id_product`, `id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_currency` ADD `iso_code_num` varchar(3) NOT NULL default '0' AFTER `iso_code`;
UPDATE `PREFIX_currency` SET iso_code_num = '978' WHERE iso_code = 'EUR' LIMIT 1;
UPDATE `PREFIX_currency` SET iso_code_num = '840' WHERE iso_code = 'USD' LIMIT 1;
UPDATE `PREFIX_currency` SET iso_code_num = '826' WHERE iso_code = 'GBP' LIMIT 1;

ALTER TABLE `PREFIX_country` ADD `call_prefix` int(10) NOT NULL default '0' AFTER `iso_code`;

UPDATE `PREFIX_country` SET `call_prefix` = 49 WHERE `iso_code` = 'DE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 43 WHERE `iso_code` = 'AT' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 32 WHERE `iso_code` = 'BE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 1 WHERE `iso_code` = 'CA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 86 WHERE `iso_code` = 'CN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 34 WHERE `iso_code` = 'ES' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 358 WHERE `iso_code` = 'FI' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 33 WHERE `iso_code` = 'FR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 30 WHERE `iso_code` = 'GR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 39 WHERE `iso_code` = 'IT' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 81 WHERE `iso_code` = 'JP' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 352 WHERE `iso_code` = 'LU' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 31 WHERE `iso_code` = 'NL' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 48 WHERE `iso_code` = 'PL' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 351 WHERE `iso_code` = 'PT' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 420 WHERE `iso_code` = 'CZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 44 WHERE `iso_code` = 'GB' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 46 WHERE `iso_code` = 'SE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 41 WHERE `iso_code` = 'CH' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 45 WHERE `iso_code` = 'DK' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 1 WHERE `iso_code` = 'US' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 852 WHERE `iso_code` = 'HK' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 47 WHERE `iso_code` = 'NO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 61 WHERE `iso_code` = 'AU' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 65 WHERE `iso_code` = 'SG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 353 WHERE `iso_code` = 'IE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 64 WHERE `iso_code` = 'NZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 82 WHERE `iso_code` = 'KR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 972 WHERE `iso_code` = 'IL' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 27 WHERE `iso_code` = 'ZA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 234 WHERE `iso_code` = 'NG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 225 WHERE `iso_code` = 'CI' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 228 WHERE `iso_code` = 'TG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 591 WHERE `iso_code` = 'BO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 230 WHERE `iso_code` = 'MU' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 40 WHERE `iso_code` = 'RO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 421 WHERE `iso_code` = 'SK' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 213 WHERE `iso_code` = 'DZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 376 WHERE `iso_code` = 'AD' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 244 WHERE `iso_code` = 'AO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 54 WHERE `iso_code` = 'AR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 374 WHERE `iso_code` = 'AM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 297 WHERE `iso_code` = 'AW' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 994 WHERE `iso_code` = 'AZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 973 WHERE `iso_code` = 'BH' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 880 WHERE `iso_code` = 'BD' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 501 WHERE `iso_code` = 'BZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 229 WHERE `iso_code` = 'BJ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 975 WHERE `iso_code` = 'BT' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 267 WHERE `iso_code` = 'BW' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 55 WHERE `iso_code` = 'BR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 673 WHERE `iso_code` = 'BN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 226 WHERE `iso_code` = 'BF' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 95 WHERE `iso_code` = 'MM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 257 WHERE `iso_code` = 'BI' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 855 WHERE `iso_code` = 'KH' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 237 WHERE `iso_code` = 'CM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 238 WHERE `iso_code` = 'CV' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 236 WHERE `iso_code` = 'CF' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 235 WHERE `iso_code` = 'TD' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 56 WHERE `iso_code` = 'CL' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 57 WHERE `iso_code` = 'CO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 269 WHERE `iso_code` = 'KM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 242 WHERE `iso_code` = 'CD' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 243 WHERE `iso_code` = 'CG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 506 WHERE `iso_code` = 'CR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 385 WHERE `iso_code` = 'HR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 53 WHERE `iso_code` = 'CU' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 357 WHERE `iso_code` = 'CY' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 253 WHERE `iso_code` = 'DJ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 670 WHERE `iso_code` = 'TL' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 593 WHERE `iso_code` = 'EC' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 20 WHERE `iso_code` = 'EG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 503 WHERE `iso_code` = 'SV' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 240 WHERE `iso_code` = 'GQ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 291 WHERE `iso_code` = 'ER' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 372 WHERE `iso_code` = 'EE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 251 WHERE `iso_code` = 'ET' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 298 WHERE `iso_code` = 'FO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 679 WHERE `iso_code` = 'FJ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 241 WHERE `iso_code` = 'GA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 220 WHERE `iso_code` = 'GM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 995 WHERE `iso_code` = 'GE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 233 WHERE `iso_code` = 'GH' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 299 WHERE `iso_code` = 'GL' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 350 WHERE `iso_code` = 'GI' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 590 WHERE `iso_code` = 'GP' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 502 WHERE `iso_code` = 'GT' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 224 WHERE `iso_code` = 'GN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 245 WHERE `iso_code` = 'GW' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 592 WHERE `iso_code` = 'GY' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 509 WHERE `iso_code` = 'HT' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 379 WHERE `iso_code` = 'VA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 504 WHERE `iso_code` = 'HN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 354 WHERE `iso_code` = 'IS' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 91 WHERE `iso_code` = 'IN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 62 WHERE `iso_code` = 'ID' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 98 WHERE `iso_code` = 'IR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 964 WHERE `iso_code` = 'IQ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 962 WHERE `iso_code` = 'JO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 7 WHERE `iso_code` = 'KZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 254 WHERE `iso_code` = 'KE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 686 WHERE `iso_code` = 'KI' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 850 WHERE `iso_code` = 'KP' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 965 WHERE `iso_code` = 'KW' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 996 WHERE `iso_code` = 'KG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 856 WHERE `iso_code` = 'LA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 371 WHERE `iso_code` = 'LV' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 961 WHERE `iso_code` = 'LB' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 266 WHERE `iso_code` = 'LS' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 231 WHERE `iso_code` = 'LR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 218 WHERE `iso_code` = 'LY' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 423 WHERE `iso_code` = 'LI' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 370 WHERE `iso_code` = 'LT' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 853 WHERE `iso_code` = 'MO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 389 WHERE `iso_code` = 'MK' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 261 WHERE `iso_code` = 'MG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 265 WHERE `iso_code` = 'MW' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 60 WHERE `iso_code` = 'MY' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 960 WHERE `iso_code` = 'MV' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 223 WHERE `iso_code` = 'ML' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 356 WHERE `iso_code` = 'MT' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 692 WHERE `iso_code` = 'MH' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 596 WHERE `iso_code` = 'MQ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 222 WHERE `iso_code` = 'MR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 36 WHERE `iso_code` = 'HU' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 262 WHERE `iso_code` = 'YT' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 52 WHERE `iso_code` = 'MX' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 691 WHERE `iso_code` = 'FM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 373 WHERE `iso_code` = 'MD' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 377 WHERE `iso_code` = 'MC' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 976 WHERE `iso_code` = 'MN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 382 WHERE `iso_code` = 'ME' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 212 WHERE `iso_code` = 'MA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 258 WHERE `iso_code` = 'MZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 264 WHERE `iso_code` = 'NA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 674 WHERE `iso_code` = 'NR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 977 WHERE `iso_code` = 'NP' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 599 WHERE `iso_code` = 'AN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 687 WHERE `iso_code` = 'NC' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 505 WHERE `iso_code` = 'NI' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 227 WHERE `iso_code` = 'NE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 683 WHERE `iso_code` = 'NU' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 968 WHERE `iso_code` = 'OM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 92 WHERE `iso_code` = 'PK' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 680 WHERE `iso_code` = 'PW' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 507 WHERE `iso_code` = 'PA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 675 WHERE `iso_code` = 'PG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 595 WHERE `iso_code` = 'PY' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 51 WHERE `iso_code` = 'PE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 63 WHERE `iso_code` = 'PH' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 974 WHERE `iso_code` = 'QA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 262 WHERE `iso_code` = 'RE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 7 WHERE `iso_code` = 'RU' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 250 WHERE `iso_code` = 'RW' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 508 WHERE `iso_code` = 'PM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 685 WHERE `iso_code` = 'WS' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 378 WHERE `iso_code` = 'SM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 239 WHERE `iso_code` = 'ST' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 966 WHERE `iso_code` = 'SA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 221 WHERE `iso_code` = 'SN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 381 WHERE `iso_code` = 'RS' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 248 WHERE `iso_code` = 'SC' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 232 WHERE `iso_code` = 'SL' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 386 WHERE `iso_code` = 'SI' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 677 WHERE `iso_code` = 'SB' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 252 WHERE `iso_code` = 'SO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 94 WHERE `iso_code` = 'LK' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 249 WHERE `iso_code` = 'SD' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 597 WHERE `iso_code` = 'SR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 268 WHERE `iso_code` = 'SZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 963 WHERE `iso_code` = 'SY' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 886 WHERE `iso_code` = 'TW' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 992 WHERE `iso_code` = 'TJ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 255 WHERE `iso_code` = 'TZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 66 WHERE `iso_code` = 'TH' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 690 WHERE `iso_code` = 'TK' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 676 WHERE `iso_code` = 'TO' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 216 WHERE `iso_code` = 'TN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 90 WHERE `iso_code` = 'TR' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 993 WHERE `iso_code` = 'TM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 688 WHERE `iso_code` = 'TV' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 256 WHERE `iso_code` = 'UG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 380 WHERE `iso_code` = 'UA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 971 WHERE `iso_code` = 'AE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 598 WHERE `iso_code` = 'UY' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 998 WHERE `iso_code` = 'UZ' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 678 WHERE `iso_code` = 'VU' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 58 WHERE `iso_code` = 'VE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 84 WHERE `iso_code` = 'VN' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 681 WHERE `iso_code` = 'WF' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 967 WHERE `iso_code` = 'YE' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 260 WHERE `iso_code` = 'ZM' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 263 WHERE `iso_code` = 'ZW' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 355 WHERE `iso_code` = 'AL' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 93 WHERE `iso_code` = 'AF' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 387 WHERE `iso_code` = 'BA' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 359 WHERE `iso_code` = 'BG' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 682 WHERE `iso_code` = 'CK' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 594 WHERE `iso_code` = 'GF' LIMIT 1;
UPDATE `PREFIX_country` SET `call_prefix` = 689 WHERE `iso_code` = 'PF' LIMIT 1;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_CONDITIONS_CMS_ID', IFNULL((SELECT `id_cms` FROM `PREFIX_cms` WHERE `id_cms` = 3), 0), NOW(), NOW());
CREATE TEMPORARY TABLE `PREFIX_configuration_tmp` (
	`value` text
);
INSERT INTO `PREFIX_configuration_tmp` (SELECT value FROM (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PREFIX_CONDITIONS_CMS_ID') AS tmp);

UPDATE `PREFIX_configuration` SET `value` = IF((SELECT value FROM PREFIX_configuration_tmp), 1, 0) WHERE `name` = 'PREFIX_CONDITIONS';
DROP TABLE PREFIX_configuration_tmp;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_CIPHER_ALGORITHM', 0, NOW(), NOW());
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_ORDER_PROCESS_TYPE', 0, NOW(), NOW());

ALTER TABLE `PREFIX_product` ADD `minimal_quantity` INT NOT NULL DEFAULT '1' AFTER `quantity`;
ALTER TABLE `PREFIX_product` ADD `cache_default_attribute` int(10) unsigned default NULL AFTER `indexed`;
ALTER TABLE `PREFIX_product` ADD `cache_has_attachments` TINYINT(1) NOT NULL default '0' AFTER `indexed`;
ALTER TABLE `PREFIX_product` ADD `cache_is_pack` TINYINT(1) NOT NULL default '0' AFTER `indexed`;
ALTER TABLE `PREFIX_product` ADD `available_for_order` TINYINT(1) NOT NULL DEFAULT '1' AFTER  `active`;
ALTER TABLE `PREFIX_product` ADD `show_price` TINYINT(1) NOT NULL DEFAULT '1' AFTER `available_for_order`;
ALTER TABLE `PREFIX_product` ADD `online_only` TINYINT(1) NOT NULL DEFAULT '0' AFTER `on_sale`;
ALTER TABLE `PREFIX_product` ADD `condition` ENUM('new', 'used', 'refurbished') NOT NULL DEFAULT 'new' AFTER `available_for_order`;
ALTER TABLE `PREFIX_product` ADD `upc` VARCHAR( 12 ) NULL AFTER `ean13`;

ALTER TABLE `PREFIX_product_attribute` ADD `upc` VARCHAR( 12 ) NULL AFTER `ean13`;

SET @defaultOOS = (SELECT value FROM `PREFIX_configuration` WHERE name = 'PS_ORDER_OUT_OF_STOCK');
/* Set 0 for every non-attribute product */
UPDATE `PREFIX_product` p SET `cache_default_attribute` =  0 WHERE `id_product` NOT IN (SELECT `id_product` FROM `PREFIX_product_attribute`);
/* First default attribute in stock */
UPDATE `PREFIX_product` p SET `cache_default_attribute` = (SELECT `id_product_attribute` FROM `PREFIX_product_attribute` WHERE `id_product` = p.`id_product` AND default_on = 1 AND quantity > 0 LIMIT 1) WHERE `cache_default_attribute` IS NULL;
/* Then default attribute without stock if we don't care */
UPDATE `PREFIX_product` p SET `cache_default_attribute` = (SELECT `id_product_attribute` FROM `PREFIX_product_attribute` WHERE `id_product` = p.`id_product` AND default_on = 1 LIMIT 1) WHERE `cache_default_attribute` IS NULL AND `out_of_stock` = 1 OR `out_of_stock` = IF(@defaultOOS = 1, 2, 1);
/* Next, the default attribute can be any attribute with stock */
UPDATE `PREFIX_product` p SET `cache_default_attribute` = (SELECT `id_product_attribute` FROM `PREFIX_product_attribute` WHERE `id_product` = p.`id_product` AND quantity > 0 LIMIT 1) WHERE `cache_default_attribute` IS NULL;
/* If there is still no default attribute, then we go back to the default one */
UPDATE `PREFIX_product` p SET `cache_default_attribute` = (SELECT `id_product_attribute` FROM `PREFIX_product_attribute` WHERE `id_product` = p.`id_product` AND default_on = 1 LIMIT 1) WHERE `cache_default_attribute` IS NULL;

UPDATE `PREFIX_product` p SET
cache_is_pack = (SELECT IF(COUNT(*) > 0, 1, 0) FROM `PREFIX_pack` pp WHERE pp.`id_product_pack` = p.`id_product`),
cache_has_attachments = (SELECT IF(COUNT(*) > 0, 1, 0) FROM `PREFIX_product_attachment` pa WHERE pa.`id_product` = p.`id_product`);

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES ('deleteProductAttribute', 'Product Attribute Deletion', NULL, 0);
INSERT INTO `PREFIX_hook` (`name` ,`title` ,`description` ,`position`) VALUES ('beforeCarrier', 'Before carrier list', 'This hook is display before the carrier list on Front office', 1);
INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES ('orderDetailDisplayed', 'Order detail displayed', 'Displayed on order detail on front office', 1);

INSERT INTO `PREFIX_hook_module` (`id_module`, `id_hook`, `position`) VALUES
((SELECT IFNULL((SELECT `id_module` FROM `PREFIX_module` WHERE `name` = 'mailalerts'), 0)),
(SELECT `id_hook` FROM `PREFIX_hook` WHERE `name` = 'deleteProductAttribute'), 1);

DELETE FROM `PREFIX_hook_module` WHERE `id_module` = 0;

ALTER TABLE `PREFIX_country` ADD `need_zip_code` TINYINT(1) NOT NULL DEFAULT '1';
ALTER TABLE `PREFIX_country` ADD `zip_code_format` VARCHAR(12) NOT NULL DEFAULT '';

ALTER TABLE `PREFIX_product` ADD `unit_price` DECIMAL(20,6) NOT NULL DEFAULT '0.000000' AFTER `wholesale_price`;
ALTER TABLE `PREFIX_product` ADD `unity` VARCHAR(10) NOT NULL DEFAULT '0.000000' AFTER `unit_price` ;
ALTER TABLE `PREFIX_product_attribute` ADD `unit_price_impact` DECIMAL(17,2) NOT NULL DEFAULT '0.00' AFTER `weight`;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_VOLUME_UNIT', 'cl', NOW(), NOW());

ALTER TABLE `PREFIX_carrier` ADD `shipping_external` TINYINT( 1 ) UNSIGNED NOT NULL;
ALTER TABLE `PREFIX_carrier` ADD `external_module_name` varchar(64) DEFAULT NULL;
ALTER TABLE `PREFIX_carrier` ADD `need_range` TINYINT( 1 ) UNSIGNED NOT NULL;

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES ('processCarrier', 'Carrier Process', NULL, 0);
INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES ('orderDetail', 'Order Detail', 'To set the follow-up in smarty when order detail is called', 0);
INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES ('paymentCCAdded', 'Payment CC added', 'Payment CC added', '0');
INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES ('extraProductComparison', 'Extra Product Comparison', 'Extra Product Comparison', '0');

ALTER TABLE `PREFIX_address` ADD `vat_number` varchar(32) NULL DEFAULT NULL AFTER `phone_mobile`;
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_TAX_ADDRESS_TYPE', 'id_address_delivery', NOW(), NOW());


/* PHP:add_module_to_hook(blockpaymentlogo, header); */;
/* PHP:add_module_to_hook(blockpermanentlinks, header); */;
/* PHP:add_module_to_hook(blockviewed, header); */;
/* PHP:add_module_to_hook(blockcart, header); */;
/* PHP:add_module_to_hook(editorial, header); */;
/* PHP:add_module_to_hook(blockbestsellers, header); */;
/* PHP:add_module_to_hook(blockcategories, header); */;
/* PHP:add_module_to_hook(blockspecials, header); */;
/* PHP:add_module_to_hook(blockcurrencies, header); */;
/* PHP:add_module_to_hook(blocknewproducts, header); */;
/* PHP:add_module_to_hook(blockuserinfo, header); */;
/* PHP:migrate_block_info_to_cms_block(); */;
/* PHP:add_module_to_hook(blockcms, header); */;
/* PHP:add_module_to_hook(blocklanguages, header); */;
/* PHP:add_module_to_hook(blockmanufacturer, header); */;
/* PHP:add_module_to_hook(blockadvertising, header); */;
/* PHP:add_module_to_hook(blocktags, header); */;
/* PHP:add_module_to_hook(blockmyaccount, header); */;

ALTER TABLE `PREFIX_product` ADD `additional_shipping_cost` DECIMAL(20,2) NOT NULL DEFAULT '0.000000' AFTER `unit_price`;

ALTER TABLE `PREFIX_currency` ADD `active` TINYINT(1) NOT NULL DEFAULT '1';
ALTER TABLE `PREFIX_tax` ADD `active` TINYINT(1) NOT NULL DEFAULT '1';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_ATTRIBUTE_CATEGORY_DISPLAY', 1, NOW(), NOW());

ALTER TABLE `PREFIX_discount` ADD `cart_display` TINYINT( 4 ) NOT NULL AFTER `active` , ADD `date_add` DATETIME NOT NULL AFTER `cart_display` , ADD `date_upd` DATETIME NOT NULL AFTER `date_add` ;

ALTER TABLE `PREFIX_carrier` ADD `shipping_method` INT( 2 ) NOT NULL DEFAULT '0';

CREATE TABLE `PREFIX_stock_mvt` (
  `id_stock_mvt` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) unsigned DEFAULT NULL,
  `id_product_attribute` int(11) unsigned DEFAULT NULL,
  `id_order` int(11) unsigned DEFAULT NULL,
  `id_stock_mvt_reason` int(11) unsigned NOT NULL,
  `id_employee` int(11) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_stock_mvt`),
  KEY `id_order` (`id_order`),
  KEY `id_product` (`id_product`),
  KEY `id_product_attribute` (`id_product_attribute`),
  KEY `id_stock_mvt_reason` (`id_stock_mvt_reason`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_stock_mvt_reason` (
  `id_stock_mvt_reason` int(11) NOT NULL AUTO_INCREMENT,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_stock_mvt_reason`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;


ALTER TABLE `PREFIX_product` CHANGE `quantity` `quantity` INT( 10 ) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_product_attribute` CHANGE `quantity` `quantity` INT( 10 ) NOT NULL DEFAULT '0';

CREATE TABLE `PREFIX_stock_mvt_reason_lang` (
  `id_stock_mvt_reason` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id_stock_mvt_reason`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_stock_mvt_reason` (`id_stock_mvt_reason`, `date_add`, `date_upd`) VALUES
(1, NOW(), NOW()), (2, NOW(), NOW()), (3, NOW(), NOW());

INSERT INTO `PREFIX_stock_mvt_reason_lang` (`id_stock_mvt_reason`, `id_lang`, `name`) VALUES
(1, 1, 'Order'),
(1, 2, 'Commande'),
(2, 1, 'Missing Stock Movement'),
(2, 2, 'Mouvement de stock manquant'),
(3, 1, 'Restocking'),
(3, 2, 'Réassort');

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_COMPARATOR_MAX_ITEM', 0, NOW(), NOW());

ALTER TABLE `PREFIX_meta_lang` ADD `url_rewrite` VARCHAR( 255 ) NOT NULL , ADD INDEX ( `url_rewrite` );
INSERT INTO `PREFIX_meta` (`page`) VALUES ('address');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'address'), 'Address', 'address'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'address'), 'Adresse', 'adresse'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'address'), 'Dirección', 'direccion');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('addresses');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'addresses'), 'Addresses', 'addresses'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'addresses'), 'Adresses', 'adresses'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'addresses'), 'Direcciones', 'direcciones');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('authentication');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'authentication'), 'Authentication', 'authentication'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'authentication'), 'Authentification', 'authentification'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'authentication'), 'Autenticación', 'autenticacion');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('cart');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'cart'), 'Cart', 'cart'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'cart'), 'Panier', 'panier'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'cart'), 'Carro de la compra', 'carro-de-la-compra');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('discount');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'discount'), 'Discount', 'discount'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'discount'), 'Bons de réduction', 'bons-de-reduction'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'discount'), 'Descuento', 'descuento');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('history');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'history'), 'Order history', 'order-history'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'history'), 'Historique des commandes', 'historique-des-commandes'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'history'), 'Historial de pedidos', 'historial-de-pedidos');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('identity');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'identity'), 'Identity', 'identity'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'identity'), 'Identité', 'identite'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'identity'), 'Identidad', 'identidad');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('my-account');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'my-account'), 'My account', 'my-account'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'my-account'), 'Mon compte', 'mon-compte'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'my-account'), 'Mi Cuenta', 'mi-cuenta');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('order-follow');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order-follow'), 'Order follow', 'order-follow'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order-follow'), 'Détails de la commande', 'details-de-la-commande'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order-follow'), 'Devolución de productos', 'devolucion-de-productos');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('order-slip');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order-slip'), 'Order slip', 'order-slip'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order-slip'), 'Avoirs', 'avoirs'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order-slip'), 'Vales', 'vales');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('order');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order'), 'Order', 'order'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order'), 'Commande', 'commande'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order'), 'Carrito', 'carrito');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('search');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'search' LIMIT 1), 'Search', 'search'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'search' LIMIT 1), 'Recherche', 'recherche'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'search' LIMIT 1), 'Buscar', 'buscar');
INSERT INTO `PREFIX_meta` (`page`) VALUES ('stores');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`) VALUES
(1, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'stores'), 'Stores', 'stores'),
(2, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'stores'), 'Magagins', 'magasins'),
(3, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'stores'), 'Tiendas', 'tiendas');

ALTER TABLE `PREFIX_manufacturer` ADD `active` tinyint(1) NOT NULL default 0;
ALTER TABLE `PREFIX_supplier` ADD `active` tinyint(1) NOT NULL default 0;
UPDATE `PREFIX_manufacturer` SET `active` = 1;
UPDATE `PREFIX_supplier` SET `active` = 1;
ALTER TABLE `PREFIX_cms` ADD `active` tinyint(1) unsigned NOT NULL default 0;
UPDATE `PREFIX_cms` SET `active` = 1;

ALTER TABLE `PREFIX_cart` ADD `secure_key` varchar(32) NOT NULL default '-1' AFTER `id_guest`;

ALTER TABLE `PREFIX_order_detail` ADD `product_upc` varchar(12) default NULL AFTER `product_ean13`;

ALTER TABLE `PREFIX_discount` ADD `id_group` int(10) unsigned NOT NULL default 0;

CREATE TABLE `PREFIX_store` (
  `id_store` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_country` int(10) unsigned NOT NULL,
  `id_state` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `city` varchar(64) NOT NULL,
  `postcode` varchar(12) NOT NULL,
  `latitude` float(10,6) DEFAULT NULL,
  `longitude` float(10,6) DEFAULT NULL,
  `hours` varchar(254) DEFAULT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `fax` varchar(16) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `note` text,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_store`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES
('categoryAddition', '', 'Temporary hook. Must NEVER be used. Will soon be replaced by a generic CRUD hook system.', 0),
('categoryUpdate', '', 'Temporary hook. Must NEVER be used. Will soon be replaced by a generic CRUD hook system.', 0),
('categoryDeletion', '', 'Temporary hook. Must NEVER be used. Will soon be replaced by a generic CRUD hook system.', 0);


/* PHP:add_module_to_hook(blockcategories, categoryAddition); */;
/* PHP:add_module_to_hook(blockcategories, categoryUpdate); */;
/* PHP:add_module_to_hook(blockcategories, categoryDeletion); */;

DELETE FROM `PREFIX_hook_module` WHERE `id_module` = 0;

CREATE TABLE `PREFIX_required_field` (
  `id_required_field` int(11) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(32) NOT NULL,
  `field_name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_required_field`),
  KEY `object_name` (`object_name`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_memcached_servers` (
`id_memcached_server` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ip` VARCHAR( 254 ) NOT NULL ,
`port` INT(11) UNSIGNED NOT NULL ,
`weight` INT(11) UNSIGNED NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_webservice_account` (
  `id_webservice_account` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `active` tinyint(2) NOT NULL,
  PRIMARY KEY (`id_webservice_account`),
  KEY `key` (`key`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_webservice_permission` (
  `id_webservice_permission` int(11) NOT NULL AUTO_INCREMENT,
  `resource` varchar(50) NOT NULL,
  `method` enum('GET','POST','PUT','DELETE') NOT NULL,
  `id_webservice_account` int(11) NOT NULL,
  PRIMARY KEY (`id_webservice_permission`),
  UNIQUE KEY `resource_2` (`resource`,`method`,`id_webservice_account`),
  KEY `resource` (`resource`),
  KEY `method` (`method`),
  KEY `id_webservice_account` (`id_webservice_account`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;


/* PHP */
/* PHP:editorial_update(); */;
/* PHP:reorderpositions(); */;
/* PHP:update_image_size_in_db(); */;
/* PHP:update_order_details(); */;
/* PHP:add_new_tab(AdminInformation, en:Configuration Information|fr:Informations|es:Informations|it:Informazioni di configurazione|de:Konfigurationsinformationen,  9); */;
/* PHP:add_new_tab(AdminCustomerThreads, en:Customer Service|de:Kundenservice|fr:SAV|es:Servicio al cliente|it:Servizio clienti,  29); */;
/* PHP:add_new_tab(AdminAddonsCatalog, fr:Catalogue de modules et thèmes|de:Module und Themenkatalog|en:Modules & Themes Catalog|it:Moduli & Temi catalogo,  7); */;
/* PHP:add_new_tab(AdminAddonsMyAccount, it:Il mio Account|de:Mein Konto|fr:Mon compte|en:My Account,  7); */;
/* PHP:add_new_tab(AdminPerformance, de:Leistung|en:Performance|it:Performance|fr:Performances|es:Rendimiento,  8); */;
/* PHP:add_new_tab(AdminThemes, es:Temas|it:Temi|de:Themen|en:Themes|fr:Thèmes,  7); */;
/* PHP:add_new_tab(AdminWebservice, fr:Service web|es:Web service|en:Webservice|de:Webservice|it:Webservice, 9); */;

