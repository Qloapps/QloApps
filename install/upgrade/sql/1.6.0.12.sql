SET NAMES 'utf8';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_CUSTOMER_NWSL', 1, NOW(), NOW()), ('PS_CUSTOMER_OPTIN', 1, NOW(), NOW());

INSERT INTO `PREFIX_order_state` (`invoice`, `send_email`, `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `deleted`) VALUES ('0', '0', 'cashondelivery', '#4169E1', '1', '0', '0', '0', '0', '0', '0');
SET @id_order_state = LAST_INSERT_ID();
INSERT INTO `PREFIX_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) (SELECT @id_order_state, id_lang, 'Waiting cod validation', '' FROM `PREFIX_lang`);

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_OS_COD_VALIDATION', @id_order_state, NOW(), NOW());

UPDATE `PREFIX_hook` set `position` = 1 where `name` in ('displayBanner', 'displayNav', 'displayTopColumn');

ALTER TABLE `PREFIX_product` ADD `pack_stock_type` int(11) UNSIGNED DEFAULT '3';
ALTER TABLE `PREFIX_product_shop` ADD `pack_stock_type` int(11) UNSIGNED DEFAULT '3';
ALTER TABLE `PREFIX_pack` ADD `id_product_attribute_item` int(10) UNSIGNED NOT NULL AFTER `id_product_item`;
ALTER TABLE `PREFIX_pack` DROP PRIMARY KEY;

/* PHP:p16012_pack_rework(); */;

ALTER TABLE `PREFIX_pack` ADD PRIMARY KEY (`id_product_pack`, `id_product_item`, `id_product_attribute_item`);

ALTER TABLE `PREFIX_order_state` ADD `pdf_delivery` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `paid`;
ALTER TABLE `PREFIX_order_state` ADD `pdf_invoice` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `pdf_delivery`;

ALTER TABLE `PREFIX_orders` CHANGE `shipping_number` `shipping_number` VARCHAR( 64 );

/* PHP:ps16012_update_alias(); */;

ALTER TABLE `PREFIX_store` CHANGE `hours` `hours` TEXT;

ALTER TABLE `PREFIX_cms_lang` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_lang`;
ALTER TABLE `PREFIX_cms_lang` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_cms`, `id_shop`, `id_lang`);

ALTER TABLE `PREFIX_cms_category_lang` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_lang`;
ALTER TABLE `PREFIX_cms_category_lang` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_cms_category`, `id_shop`, `id_lang`);

CREATE TABLE `PREFIX_cms_category_shop` (
	`id_cms_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`id_shop` INT(11) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_cms_category`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

/* PHP:cms_multishop(); */;

ALTER TABLE `PREFIX_customization_field_lang` ADD `id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_lang`;
ALTER TABLE `PREFIX_customization_field_lang` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_customization_field`, `id_shop`, `id_lang`);

/* PHP:customization_field_multishop_lang(); */;

ALTER TABLE `PREFIX_product` CHANGE `available_date` `available_date` DATE NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `PREFIX_product_shop` CHANGE `available_date` `available_date` DATE NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `PREFIX_product_attribute` CHANGE `available_date` `available_date` DATE NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `PREFIX_product_attribute_shop` CHANGE `available_date` `available_date` DATE NOT NULL DEFAULT '0000-00-00';

ALTER TABLE `PREFIX_module_access` CHANGE `view` `view` TINYINT( 1 ) NOT NULL DEFAULT '0',
CHANGE `configure` `configure` TINYINT( 1 ) NOT NULL DEFAULT '0',
CHANGE `uninstall` `uninstall` TINYINT( 1 ) NOT NULL DEFAULT '0';