SET NAMES 'utf8';

ALTER TABLE `PREFIX_customer_message` CHANGE `message` `message` MEDIUMTEXT NOT NULL;
UPDATE `PREFIX_tax_rules_group` SET `date_add` = NOW(), `date_upd` = NOW() WHERE `date_add` = '0000-00-00 00:00:00';

ALTER TABLE  `PREFIX_order_detail` ADD  `original_wholesale_price` DECIMAL( 20, 6 ) NOT NULL DEFAULT  '0.000000';

ALTER TABLE `PREFIX_specific_price` DROP KEY `id_product_2`;

ALTER IGNORE TABLE `PREFIX_specific_price` ADD UNIQUE KEY `id_product_2` (`id_cart`, `id_product`,`id_shop`,`id_shop_group`,`id_currency`,`id_country`,`id_group`,`id_customer`,`id_product_attribute`,`from_quantity`,`id_specific_price_rule`,`from`,`to`);
