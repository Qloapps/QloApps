SET NAMES 'utf8';

/* PHP:update_customer_default_group(); */;

UPDATE `PREFIX_meta` SET `page` = 'supplier' WHERE `page` = 'supply';

ALTER TABLE  `PREFIX_image_type` CHANGE  `name`  `name` VARCHAR( 64 ) NOT NULL;

ALTER TABLE `PREFIX_customer` ADD `id_lang` INT UNSIGNED NULL AFTER `id_default_group`;
UPDATE `PREFIX_customer` SET id_lang = (SELECT `value` FROM `PREFIX_configuration` WHERE name = 'PS_LANG_DEFAULT' LIMIT 1);
UPDATE `PREFIX_customer` c, `PREFIX_orders` o SET c.id_lang = o.id_lang WHERE c.id_customer = o.id_customer;

UPDATE `PREFIX_quick_access` SET `link` = 'index.php?controller=AdminCartRules&addcart_rule' WHERE `link` = 'index.php?tab=AdminDiscounts&adddiscount';

ALTER TABLE `PREFIX_order_cart_rule` ADD `free_shipping` tinyint(1) NOT NULL DEFAULT 0 AFTER `value_tax_excl`;

UPDATE `PREFIX_order_cart_rule` ocr, `PREFIX_cart_rule` cr SET ocr.free_shipping = 1 WHERE ocr.id_cart_rule = cr.id_cart_rule AND cr.free_shipping = 1;

UPDATE `PREFIX_orders` o, `PREFIX_order_cart_rule` ocr SET
	o.`total_discounts` = o.total_discounts + o.`total_shipping_tax_incl`,
	o.`total_discounts_tax_incl` = o.`total_discounts_tax_incl` + o.`total_shipping_tax_incl`,
	o.`total_discounts_tax_excl` = o.`total_discounts_tax_excl` + o.`total_shipping_tax_excl`
WHERE o.id_order = ocr.id_order AND ocr.free_shipping = 1;

CREATE TABLE `PREFIX_tab_module_preference` (
  `id_tab_module_preference` int(11) NOT NULL auto_increment,
  `id_employee` int(11) NOT NULL,
  `id_tab` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  PRIMARY KEY (`id_tab_module_preference`),
  UNIQUE KEY `employee_module` (`id_employee`, `id_tab`, `module`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

/* PHP:add_new_tab(AdminMarketing, es:Marketing|it:Marketing|en:Marketing|de:Marketing|fr:Marketing, 0, false, AdminPriceRule); */;

/* PHP:p1540_add_missing_columns(); */;

ALTER TABLE `PREFIX_stock_available` ADD UNIQUE `product_sqlstock` (`id_product`, `id_product_attribute`, `id_shop`, `id_shop_group`);

UPDATE PREFIX_configuration SET `value` = '8388608' WHERE `name` = 'PS_PRODUCT_PICTURE_MAX_SIZE' AND `value` <= '524288';

ALTER TABLE `PREFIX_guest` ADD `mobile_theme` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `PREFIX_orders` ADD `mobile_theme` tinyint(1) NOT NULL DEFAULT 0 AFTER `gift_message`;
ALTER TABLE `PREFIX_cart` ADD `mobile_theme` tinyint(1) NOT NULL DEFAULT 0 AFTER `gift_message`;

ALTER TABLE `PREFIX_address` CHANGE `phone` `phone` varchar(32) default NULL;
ALTER TABLE `PREFIX_address` CHANGE `phone_mobile` `phone_mobile` varchar(32) default NULL;

/* PHP:update_genders_images(); */;

UPDATE `PREFIX_customer` SET `id_gender` = 1 WHERE `email` LIKE 'pub@qloapps.com' AND `id_customer` = 1 AND `id_gender` = 4;

UPDATE `PREFIX_cart_rule_carrier` crc INNER JOIN `PREFIX_carrier` c ON crc.`id_carrier` = c.`id_carrier` SET crc.`id_carrier` = c.`id_reference`;

UPDATE `PREFIX_order_payment` SET `order_reference` = LPAD(order_reference, 9 , '0');