SET NAMES 'utf8';

/* PHP:p15015_blockadvertising_extension(); */;

ALTER TABLE `PREFIX_order_state` ADD `module_name` VARCHAR(255) NULL DEFAULT NULL AFTER `send_email`;

UPDATE `PREFIX_order_state` SET `module_name` = 'cheque' WHERE `id_order_state` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_CHEQUE' LIMIT 1);

UPDATE `PREFIX_order_state` SET `module_name` = 'bankwire' WHERE `id_order_state` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_BANKWIRE' LIMIT 1);

ALTER TABLE `PREFIX_product_shop` ADD `uploadable_files` TINYINT NOT NULL DEFAULT 0 AFTER `customizable`;

UPDATE `PREFIX_product_shop` product_shop SET `uploadable_files` = (SELECT uploadable_files FROM `PREFIX_product` WHERE `id_product` = product_shop.`id_product`);