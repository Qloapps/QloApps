SET NAMES 'utf8';

/* PHP:p15016_add_missing_columns(); */;

ALTER TABLE `PREFIX_order_detail` ADD `id_shop` INT(11) UNSIGNED NOT NULL AFTER `id_warehouse`, ADD INDEX (`id_shop`);

UPDATE `PREFIX_order_detail` od SET `id_shop`=(SELECT `id_shop` FROM `PREFIX_orders` WHERE `id_order`=od.`id_order`);

DELETE FROM `PREFIX_tab` WHERE `class_name` = 'AdminAddonsMyAccount';
DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` NOT IN (SELECT `id_tab` FROM `PREFIX_tab`);
DELETE FROM `PREFIX_access` WHERE `id_tab` NOT IN (SELECT `id_tab` FROM `PREFIX_tab`);

UPDATE `PREFIX_employee` SET bo_theme = 'default';

ALTER TABLE `PREFIX_tax_rule` ADD INDEX `category_getproducts` ( `id_tax_rules_group` , `id_country` , `id_state` , `zipcode_from` );
ALTER TABLE `PREFIX_stock_available` ADD INDEX `product_sqlstock` ( `id_product` , `id_product_attribute` , `id_shop` );

ALTER TABLE `PREFIX_product` ADD `id_shop_default` int(10) unsigned NOT NULL default 1 AFTER `id_category_default`;
UPDATE `PREFIX_product` p SET `id_shop_default` = IFNULL((SELECT MIN(id_shop) FROM `PREFIX_product_shop` ps WHERE ps.`id_product` = p.`id_product`), 1);
ALTER TABLE `PREFIX_category` ADD `id_shop_default` int(10) unsigned NOT NULL default 1 AFTER `id_parent`;
UPDATE `PREFIX_category` c SET `id_shop_default` = IFNULL((SELECT MIN(id_shop) FROM `PREFIX_category_shop` cs WHERE cs.`id_category` = c.`id_category`), 1);
