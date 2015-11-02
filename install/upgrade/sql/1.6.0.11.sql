SET NAMES 'utf8';

INSERT INTO `PREFIX_order_state` (`invoice`, `send_email`, `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `deleted`) VALUES ('0', '1', NULL, '#FF69B4', '1', '0', '0', '0', '0', '0', '0');
SET @id_order_state = LAST_INSERT_ID();
SET @id_order_state_oos = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_OUTOFSTOCK' LIMIT 1);
INSERT INTO `PREFIX_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) (SELECT @id_order_state, id_lang, name, template FROM `PREFIX_order_state_lang` WHERE id_order_state = @id_order_state_oos);

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_OS_OUTOFSTOCK_PAID', @id_order_state_oos, NOW(), NOW()),
('PS_OS_OUTOFSTOCK_UNPAID', @id_order_state, NOW(), NOW());

ALTER TABLE  `PREFIX_module_access` ADD  `uninstall` TINYINT( 1 ) NOT NULL AFTER  `configure`;

ALTER TABLE  `PREFIX_specific_price` ADD  `reduction_tax` TINYINT( 1 ) NOT NULL DEFAULT 1 AFTER  `reduction`;
ALTER TABLE  `PREFIX_specific_price_rule` ADD  `reduction_tax` TINYINT( 1 ) NOT NULL DEFAULT 1 AFTER  `reduction`;

INSERT INTO `PREFIX_hook` (`id_hook` , `name` , `title` , `description` , `position` , `live_edit`)
VALUES (NULL , 'displayCustomerIdentityForm', 'Customer identity form displayed in Front Office', 'This hook displays new elements on the form to update a customer identity', '1', '0');

ALTER TABLE `PREFIX_orders` ADD `round_mode` TINYINT(1) NOT NULL DEFAULT '2' AFTER `total_wrapping_tax_excl`;

ALTER TABLE `PREFIX_product_attribute` MODIFY `unit_price_impact` DECIMAL(20,6);
ALTER TABLE `PREFIX_product_attribute_shop` MODIFY `unit_price_impact` DECIMAL(20,6);

/* PHP:p16011_media_server(); */;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_PRICE_DISPLAY_PRECISION', 2, NOW(), NOW());

/* Precision used to be 2, now it can be set freely, so the DB must be ready to accept more decimals */
ALTER TABLE `PREFIX_orders`
CHANGE COLUMN `total_discounts` `total_discounts` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_discounts_tax_incl` `total_discounts_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_discounts_tax_excl` `total_discounts_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_paid` `total_paid` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_paid_tax_incl` `total_paid_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_paid_tax_excl` `total_paid_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_paid_real` `total_paid_real` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_products` `total_products` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_products_wt` `total_products_wt` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_shipping` `total_shipping` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_shipping_tax_incl` `total_shipping_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_shipping_tax_excl` `total_shipping_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_wrapping` `total_wrapping` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_wrapping_tax_incl` `total_wrapping_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_wrapping_tax_excl` `total_wrapping_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00';
ALTER IGNORE TABLE `PREFIX_product` CHANGE `ean13` `ean13` BIGINT( 15 ) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_product` CHANGE `ean13` `ean13` VARCHAR( 13 ) NULL DEFAULT NULL;

ALTER IGNORE TABLE `PREFIX_product_attribute` CHANGE `ean13` `ean13` BIGINT( 15 ) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_product_attribute` CHANGE `ean13` `ean13` VARCHAR( 13 ) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_order_slip` ADD `order_slip_type` TINYINT(1) NOT NULL DEFAULT 0 AFTER `partial`;
