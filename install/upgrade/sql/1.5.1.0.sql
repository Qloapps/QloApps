SET NAMES 'utf8';

/* PHP:image_shop1510(); */;
ALTER TABLE `PREFIX_image_shop` DROP PRIMARY KEY;
ALTER TABLE `PREFIX_image_shop` ADD INDEX (`id_image`, `id_shop`, `cover`);
UPDATE `PREFIX_image_shop` image_shop SET image_shop.`cover`=1 WHERE `id_image` IN (SELECT `id_image` FROM `PREFIX_image` i WHERE i.`cover`=1);

INSERT INTO `PREFIX_configuration`(`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_ONE_PHONE_AT_LEAST', '1', NOW(), NOW());

/* PHP:p15018_change_image_types(); */;

ALTER TABLE `PREFIX_product` CHANGE `width` `width` DECIMAL(20, 6) NOT NULL DEFAULT '0', CHANGE `height` `height` DECIMAL(20, 6) NOT NULL DEFAULT '0', CHANGE `depth` `depth` DECIMAL(20, 6) NOT NULL DEFAULT '0', CHANGE `weight` `weight` DECIMAL(20, 6) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_product_attribute` CHANGE `weight` `weight` DECIMAL(20, 6) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_product_attribute_shop` CHANGE `weight` `weight` DECIMAL(20, 6) NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `PREFIX_order_carrier` CHANGE `weight` `weight` DECIMAL(20, 6) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_attribute_impact` CHANGE `weight` `weight` DECIMAL(20, 6) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_order_detail` CHANGE `product_weight` `product_weight` DECIMAL(20, 6) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_stock_available` DROP INDEX `product_sqlstock`;
ALTER TABLE `PREFIX_stock_available` ADD UNIQUE `product_sqlstock` (`id_product`, `id_product_attribute`, `id_shop`);
ALTER TABLE `PREFIX_cms` ADD INDEX (`id_cms_category`);
