SET NAMES 'utf8';

DELETE FROM `PREFIX_tax_state` WHERE `id_tax` NOT IN (SELECT `id_tax` FROM `PREFIX_tax`);

ALTER TABLE `PREFIX_product` CHANGE `reduction_from` `reduction_from` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
CHANGE `reduction_to` `reduction_to` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00';

UPDATE `PREFIX_product` 
SET `reduction_to` = DATE_ADD(reduction_to, INTERVAL 1 DAY) 
WHERE `reduction_from` != `reduction_to`;

ALTER TABLE `PREFIX_discount` ADD `id_currency` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `id_customer`;
UPDATE `PREFIX_discount` SET `id_currency` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_CURRENCY_DEFAULT' LIMIT 1) WHERE `id_discount_type` = 2;

ALTER TABLE `PREFIX_address` ADD INDEX (id_country);
ALTER TABLE `PREFIX_address` ADD INDEX (id_state);
ALTER TABLE `PREFIX_address` ADD INDEX (id_manufacturer);
ALTER TABLE `PREFIX_address` ADD INDEX (id_supplier);
ALTER TABLE `PREFIX_carrier` ADD INDEX (id_tax);
ALTER TABLE `PREFIX_cart` ADD INDEX (id_address_delivery);
ALTER TABLE `PREFIX_cart` ADD INDEX (id_address_invoice);
ALTER TABLE `PREFIX_cart` ADD INDEX (id_carrier);
ALTER TABLE `PREFIX_cart` ADD INDEX (id_lang);
ALTER TABLE `PREFIX_cart` ADD INDEX (id_currency);
ALTER TABLE `PREFIX_cart_product` ADD INDEX (id_product_attribute);
ALTER TABLE `PREFIX_connections` ADD INDEX (id_page);
ALTER TABLE `PREFIX_customer` ADD INDEX (id_gender);
ALTER TABLE `PREFIX_customization` ADD INDEX (id_product_attribute);
ALTER TABLE `PREFIX_customization_field` ADD INDEX (id_product);
ALTER TABLE `PREFIX_delivery` ADD INDEX (id_range_price);
ALTER TABLE `PREFIX_delivery` ADD INDEX (id_range_weight);
ALTER TABLE `PREFIX_discount` ADD INDEX (id_discount_type);
ALTER TABLE `PREFIX_discount_quantity` ADD INDEX (id_discount_type);
ALTER TABLE `PREFIX_discount_quantity` ADD INDEX (id_product);
ALTER TABLE `PREFIX_discount_quantity` ADD INDEX (id_product_attribute);
ALTER TABLE `PREFIX_employee` ADD INDEX (id_profile);
ALTER TABLE `PREFIX_feature_product` ADD INDEX (id_feature_value);
ALTER TABLE `PREFIX_guest` ADD INDEX (id_operating_system);
ALTER TABLE `PREFIX_guest` ADD INDEX (id_web_browser);
ALTER TABLE `PREFIX_hook_module_exceptions` ADD INDEX (id_module);
ALTER TABLE `PREFIX_hook_module_exceptions` ADD INDEX (id_hook);
ALTER TABLE `PREFIX_message` ADD INDEX (id_cart);
ALTER TABLE `PREFIX_message` ADD INDEX (id_customer);
ALTER TABLE `PREFIX_message` ADD INDEX (id_employee);
ALTER TABLE `PREFIX_order_detail` ADD INDEX (product_attribute_id);
ALTER TABLE `PREFIX_order_discount` ADD INDEX (id_discount);
ALTER TABLE `PREFIX_order_history` ADD INDEX (id_employee);
ALTER TABLE `PREFIX_order_history` ADD INDEX (id_order_state);
ALTER TABLE `PREFIX_order_return` ADD INDEX (id_order);
ALTER TABLE `PREFIX_order_slip` ADD INDEX (id_order);
ALTER TABLE `PREFIX_orders` ADD INDEX (id_carrier);
ALTER TABLE `PREFIX_orders` ADD INDEX (id_lang);
ALTER TABLE `PREFIX_orders` ADD INDEX (id_currency);
ALTER TABLE `PREFIX_orders` ADD INDEX (id_address_delivery);
ALTER TABLE `PREFIX_orders` ADD INDEX (id_address_invoice);
ALTER TABLE `PREFIX_product` ADD INDEX (id_tax);
ALTER TABLE `PREFIX_product` ADD INDEX (id_category_default);
ALTER TABLE `PREFIX_product` ADD INDEX (id_color_default);
ALTER TABLE `PREFIX_state` ADD INDEX (id_country);
ALTER TABLE `PREFIX_state` ADD INDEX (id_zone);
ALTER TABLE `PREFIX_tab` ADD INDEX (id_parent);
ALTER TABLE `PREFIX_cart` ADD INDEX (id_guest);

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) 
(
	SELECT 'MA_LAST_QTIES', '3', NOW(), NOW() 
	FROM `PREFIX_module` WHERE `name` = 'mailalerts'
);

ALTER TABLE `PREFIX_customer` ADD `id_default_group` INT UNSIGNED NOT NULL DEFAULT '1' AFTER `id_gender`;

UPDATE `PREFIX_customer` c SET `id_default_group` = (
	SELECT (
	IFNULL(
		(SELECT g.`id_group`
		FROM `PREFIX_group` g
		LEFT JOIN `PREFIX_customer_group` cg ON (cg.`id_group` = g.`id_group`)
		WHERE g.`reduction` > 0 AND cg.`id_customer` = c.`id_customer`
		ORDER BY g.`reduction`
		LIMIT 1)
	, 1)
	)
);
