SET NAMES 'utf8';

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`, `live_edit`) VALUES ('afterSaveAdminMeta', 'After save configuration in AdminMeta', 'After save configuration in AdminMeta', 0, 0);

ALTER TABLE `PREFIX_webservice_account` ADD `is_module` TINYINT( 2 ) NOT NULL DEFAULT '0' AFTER `class_name` ,
ADD `module_name` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `is_module`;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_IMG_UPDATE_TIME', UNIX_TIMESTAMP(), NOW(), NOW());

UPDATE `PREFIX_cms_lang` set link_rewrite = "uber-uns" where link_rewrite like "%ber-uns";

ALTER TABLE `PREFIX_connections` CHANGE `ip_address` `ip_address` BIGINT NULL DEFAULT NULL;


UPDATE `PREFIX_meta_lang`
SET `title` = 'Angebote', `keywords` = 'besonders, Angebote', `url_rewrite` = 'angebote' WHERE url_rewrite = 'preise-fallen';

ALTER TABLE `PREFIX_country` ADD `display_tax_label` BOOLEAN NOT NULL DEFAULT '1';
DROP TABLE IF EXISTS `PREFIX_country_tax`;

ALTER TABLE `PREFIX_order_detail`
CHANGE `product_quantity_in_stock` `product_quantity_in_stock` INT(10) NOT NULL DEFAULT '0';

CREATE TABLE `PREFIX_address_format` (
  `id_country` int(10) unsigned NOT NULL,
  `format` varchar(255) NOT NULL DEFAULT '',
  KEY `country` (`id_country`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_address_format` (`id_country`, `format`)
(SELECT `id_country` as id_country, 'firstname lastname\ncompany\nvat_number\naddress1\naddress2\npostcode city\ncountry\nphone' as format FROM PREFIX_country);

UPDATE `PREFIX_address_format` set `format`='firstname lastname
company
address1
address2
city State:name postcode 
country
phone' where `id_country`=21;

/* PHP:alter_cms_block(); */;
/* PHP:add_module_to_hook(blockcategories, afterSaveAdminMeta); */;
