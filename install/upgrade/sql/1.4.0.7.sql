SET NAMES 'utf8';

ALTER TABLE `PREFIX_product_attribute` ADD `minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1' AFTER `default_on`;

ALTER TABLE `PREFIX_orders` ADD `reference` VARCHAR(14) NOT NULL AFTER `id_address_invoice`;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES('PS_DISPLAY_SUPPLIERS', '1', NOW(), NOW());

/* PHP:update_products_ecotax_v133(); */;

