SET NAMES 'utf8';

UPDATE `PREFIX_configuration` SET `value` = CONCAT('#', `value`) WHERE `name` LIKE 'PS_%_PREFIX' AND `value` NOT LIKE '#%';

UPDATE `PREFIX_configuration_lang` SET `value` = CONCAT('#', `value`) WHERE `id_configuration` IN (SELECT `id_configuration` FROM `PREFIX_configuration` WHERE `name` LIKE 'PS_%_PREFIX') AND `value` NOT LIKE '#%';

ALTER TABLE `PREFIX_orders` CHANGE `invoice_number` `invoice_number` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `delivery_number` `delivery_number` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';

/* taxes-patch */

ALTER TABLE `PREFIX_order_invoice`
CHANGE COLUMN `total_discount_tax_excl` `total_discount_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_discount_tax_incl` `total_discount_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_paid_tax_excl` `total_paid_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_paid_tax_incl` `total_paid_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_products` `total_products` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_products_wt` `total_products_wt` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_shipping_tax_excl` `total_shipping_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_shipping_tax_incl` `total_shipping_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_wrapping_tax_excl` `total_wrapping_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_wrapping_tax_incl` `total_wrapping_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ;

ALTER TABLE `PREFIX_orders` ADD `round_type` TINYINT(1) NOT NULL DEFAULT '1' AFTER `round_mode`;


ALTER TABLE PREFIX_product_tag ADD `id_lang` int(10) unsigned NOT NULL, ADD KEY (id_lang, id_tag);
UPDATE PREFIX_product_tag, PREFIX_tag SET PREFIX_product_tag.id_lang=PREFIX_tag.id_lang WHERE PREFIX_tag.id_tag=PREFIX_product_tag.id_tag;

DROP TABLE IF EXISTS `PREFIX_tag_count`;
CREATE TABLE `PREFIX_tag_count` (
  `id_group` int(10) unsigned NOT NULL DEFAULT 0,
  `id_tag` int(10) unsigned NOT NULL DEFAULT 0,
  `id_lang` int(10) unsigned NOT NULL DEFAULT 0,
  `id_shop` int(11) unsigned NOT NULL DEFAULT 0,
  `counter` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_group`, `id_tag`),
  KEY (`id_group`, `id_lang`, `id_shop`, `counter`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
REPLACE INTO `PREFIX_tag_count` (id_group, id_tag, id_lang, id_shop, counter)
SELECT cg.id_group, t.id_tag, t.id_lang, ps.id_shop, COUNT(pt.id_tag) AS times
    FROM `PREFIX_product_tag` pt
    LEFT JOIN `PREFIX_tag` t ON (t.id_tag = pt.id_tag)
    LEFT JOIN `PREFIX_product` p ON (p.id_product = pt.id_product)
    INNER JOIN `PREFIX_product_shop` product_shop
        ON (product_shop.id_product = p.id_product)
    JOIN (SELECT DISTINCT id_group FROM `PREFIX_category_group`) cg
    JOIN (SELECT DISTINCT id_shop FROM `PREFIX_shop`) ps
    WHERE pt.`id_lang` = 1 AND product_shop.`active` = 1
    AND EXISTS(SELECT 1 FROM `PREFIX_category_product` cp
                        LEFT JOIN `PREFIX_category_group` cgo ON (cp.`id_category` = cgo.`id_category`)
                        WHERE cgo.`id_group` = cg.id_group AND p.`id_product` = cp.`id_product`)
    AND product_shop.id_shop = ps.id_shop
    GROUP BY pt.id_tag, cg.id_group;
REPLACE INTO `PREFIX_tag_count` (id_group, id_tag, id_lang, id_shop, counter)
SELECT 0, t.id_tag, t.id_lang, ps.id_shop, COUNT(pt.id_tag) AS times
    FROM `PREFIX_product_tag` pt
    LEFT JOIN `PREFIX_tag` t ON (t.id_tag = pt.id_tag)
    LEFT JOIN `PREFIX_product` p ON (p.id_product = pt.id_product)
    INNER JOIN `PREFIX_product_shop` product_shop
        ON (product_shop.id_product = p.id_product)
    JOIN (SELECT DISTINCT id_shop FROM `PREFIX_shop`) ps
    WHERE pt.`id_lang` = 1 AND product_shop.`active` = 1
    AND product_shop.id_shop = ps.id_shop
    GROUP BY pt.id_tag;


/* PHP:ps1610_safe_remove_indexes(); */;

ALTER TABLE `PREFIX_shop_group` ADD KEY `deleted` (`deleted`, `name`);
ALTER TABLE `PREFIX_shop` DROP KEY `id_shop_group`;
ALTER TABLE `PREFIX_shop` ADD KEY `id_shop_group` (`id_shop_group`, `deleted`);
ALTER TABLE `PREFIX_shop_url` DROP KEY `id_shop`;
ALTER TABLE `PREFIX_shop_url` ADD KEY `id_shop` (`id_shop`, `main`);
ALTER TABLE `PREFIX_customization` ADD KEY `id_cart` (`id_cart`);
ALTER TABLE `PREFIX_product_sale` ADD KEY `quantity` (`quantity`);
ALTER TABLE `PREFIX_cart_rule` ADD KEY `id_customer` (`id_customer`, `active`, `date_to`);
ALTER TABLE `PREFIX_cart_rule` ADD KEY `group_restriction` (`group_restriction`, `active`, `date_to`);
ALTER TABLE `PREFIX_hook_module` ADD KEY `position` (`id_shop`, `position`);
ALTER IGNORE TABLE `PREFIX_cart_product` ADD PRIMARY KEY (`id_cart`,`id_product`,`id_product_attribute`,`id_address_delivery`);
ALTER TABLE `PREFIX_cart_product` ADD KEY `id_cart_order` (`id_cart`, `date_add`, `id_product`, `id_product_attribute`);
ALTER TABLE `PREFIX_customization` DROP KEY id_cart;
ALTER IGNORE TABLE `PREFIX_customization` ADD KEY `id_cart_product` (`id_cart`, `id_product`, `id_product_attribute`);
ALTER TABLE `PREFIX_category` DROP KEY nleftright, DROP KEY nleft;
ALTER TABLE `PREFIX_category` ADD KEY `activenleft` (`active`,`nleft`), ADD KEY `activenright` (`active`,`nright`);
ALTER IGNORE TABLE `PREFIX_image_shop` DROP KEY `id_image`, ADD PRIMARY KEY (`id_image`, `id_shop`, `cover`);
ALTER TABLE PREFIX_product_attribute_shop ADD `id_product` int(10) unsigned NOT NULL, ADD KEY `id_product` (`id_product`, `id_shop`, `default_on`);
UPDATE PREFIX_product_attribute_shop, PREFIX_product_attribute
    SET PREFIX_product_attribute_shop.id_product=PREFIX_product_attribute.id_product
    WHERE PREFIX_product_attribute_shop.id_product_attribute=PREFIX_product_attribute.id_product_attribute;
ALTER TABLE PREFIX_image_shop ADD `id_product` int(10) unsigned NOT NULL, ADD KEY `id_product` (`id_product`, `id_shop`, `cover`);
UPDATE PREFIX_image_shop, PREFIX_image
    SET PREFIX_image_shop.id_product=PREFIX_image.id_product
    WHERE PREFIX_image_shop.id_image=PREFIX_image.id_image;
ALTER IGNORE TABLE `PREFIX_image_shop` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_image`, `id_shop`);
ALTER TABLE `PREFIX_product_supplier` ADD KEY `id_supplier` (`id_supplier`,`id_product`);
ALTER TABLE `PREFIX_product` DROP KEY `product_manufacturer`;
ALTER TABLE `PREFIX_product` ADD KEY `product_manufacturer` (`id_manufacturer`, `id_product`);

DROP TABLE IF EXISTS `PREFIX_smarty_lazy_cache`;
CREATE TABLE `PREFIX_smarty_lazy_cache` (
  `template_hash` varchar(32) NOT NULL DEFAULT '',
  `cache_id` varchar(255) NOT NULL DEFAULT '',
  `compile_id` varchar(32) NOT NULL DEFAULT '',
  `filepath` varchar(255) NOT NULL DEFAULT '',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`template_hash`, `cache_id`, `compile_id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX_smarty_last_flush`;
CREATE TABLE `PREFIX_smarty_last_flush` (
  `type` ENUM('compile', 'template'),
  `last_flush` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`type`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX_modules_perfs`;
CREATE TABLE `PREFIX_modules_perfs` (
  `id_modules_perfs` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `session` int(11) unsigned NOT NULL,
  `module` varchar(62) NOT NULL,
  `method` varchar(126) NOT NULL,
  `time_start` double unsigned NOT NULL,
  `time_end` double unsigned NOT NULL,
  `memory_start` int unsigned NOT NULL,
  `memory_end` int unsigned NOT NULL,
  PRIMARY KEY (`id_modules_perfs`),
  KEY (`session`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_image` CHANGE `cover` `cover` tinyint(1) unsigned NULL DEFAULT NULL;
UPDATE `PREFIX_image` SET `cover`=NULL WHERE `cover`=0;
CREATE TEMPORARY TABLE `image_transform` SELECT `id_product`, COUNT(*) c  FROM `PREFIX_image` WHERE `cover`=1 GROUP BY `id_product` HAVING c>1;
UPDATE `image_transform` JOIN `PREFIX_image` USING (`id_product`) SET `PREFIX_image`.`cover`=NULL;
ALTER TABLE `PREFIX_image` DROP KEY `id_product_cover`;
ALTER IGNORE TABLE `PREFIX_image` ADD UNIQUE KEY `id_product_cover` (`id_product`,`cover`);

ALTER TABLE `PREFIX_image_shop` CHANGE `cover` `cover` tinyint(1) unsigned NULL DEFAULT NULL;
UPDATE `PREFIX_image_shop` SET `cover`=NULL WHERE `cover`=0;
CREATE TEMPORARY TABLE `image_shop_transform` SELECT `id_product`, `id_shop`, COUNT(*) c  FROM `PREFIX_image_shop` WHERE `cover`=1 GROUP BY `id_product`, `id_shop` HAVING c>1;
UPDATE `image_shop_transform` JOIN `PREFIX_image_shop` USING (`id_product`, `id_shop`) SET `PREFIX_image_shop`.`cover`=NULL;
ALTER TABLE `PREFIX_image_shop` DROP KEY `id_product`;
ALTER IGNORE TABLE `PREFIX_image_shop` ADD UNIQUE KEY `id_product` (`id_product`, `id_shop`, `cover`);

ALTER TABLE `PREFIX_product_attribute` CHANGE `default_on` `default_on` tinyint(1) unsigned NULL DEFAULT NULL;
UPDATE `PREFIX_product_attribute` SET `default_on`=NULL WHERE `default_on`=0;
CREATE TEMPORARY TABLE `attribute_transform` SELECT `id_product`, COUNT(*) c  FROM `PREFIX_product_attribute` WHERE `default_on`=1 GROUP BY `id_product` HAVING c>1;
UPDATE `attribute_transform` JOIN `PREFIX_product_attribute` USING (`id_product`) SET `PREFIX_product_attribute`.`default_on`=NULL;
ALTER TABLE `PREFIX_product_attribute` DROP KEY `product_default`;
ALTER IGNORE TABLE `PREFIX_product_attribute` ADD UNIQUE KEY `product_default` (`id_product`,`default_on`);

ALTER TABLE `PREFIX_product_attribute_shop` CHANGE `default_on` `default_on` tinyint(1) unsigned NULL DEFAULT NULL;
UPDATE `PREFIX_product_attribute_shop` SET `default_on`=NULL WHERE `default_on`=0;
CREATE TEMPORARY TABLE `attribute_shop_transform` SELECT `id_product`, `id_shop`, COUNT(*) c  FROM `PREFIX_product_attribute_shop` WHERE `default_on`=1 GROUP BY `id_product`, `id_shop` HAVING c>1;
UPDATE `attribute_shop_transform` JOIN `PREFIX_product_attribute_shop` USING (`id_product`, `id_shop`) SET `PREFIX_product_attribute_shop`.`default_on`=NULL;
ALTER TABLE `PREFIX_product_attribute_shop` DROP KEY `id_product`;
ALTER IGNORE TABLE `PREFIX_product_attribute_shop` ADD UNIQUE KEY `id_product` (`id_product`, `id_shop`, `default_on`);

ALTER IGNORE TABLE `PREFIX_product_download` ADD UNIQUE KEY `id_product` (`id_product`);

ALTER TABLE `PREFIX_customer` DROP KEY `id_shop`;
ALTER TABLE `PREFIX_customer` ADD KEY `id_shop` (`id_shop`, `date_add`);

ALTER TABLE `PREFIX_cart` DROP KEY `id_shop`;
ALTER TABLE `PREFIX_cart` ADD KEY `id_shop_2` (`id_shop`,`date_upd`), ADD KEY `id_shop` (`id_shop`,`date_add`);
ALTER TABLE `PREFIX_product_shop` ADD KEY `indexed` (`indexed`, `active`, `id_product`);
UPDATE `PREFIX_product_shop` SET `date_add` = NOW() WHERE `date_add` = "0000-00-00 00:00:00";

INSERT INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`, `live_edit`) VALUES
(NULL, 'actionAdminLoginControllerSetMedia', 'Set media on admin login page header', 'This hook is called after adding media to admin login page header', '1', '0'),
(NULL, 'actionOrderEdited', 'Order edited', 'This hook is called when an order is edited.', '1', '0'),
(NULL, 'displayAdminNavBarBeforeEnd', 'Admin Nav-bar before end', 'Called before the end of the nav-bar.', '1', '0'),
(NULL, 'displayAdminAfterHeader', 'Admin after header', 'Hook called just after the header of the backoffice.', '1', '0'),
(NULL, 'displayAdminLogin', 'Admin login', 'Hook called just after login of the backoffice.', '1', '0');

ALTER TABLE `PREFIX_cart_rule` ADD KEY `id_customer_2` (`id_customer`,`active`,`highlight`,`date_to`);
ALTER TABLE `PREFIX_cart_rule` ADD KEY `group_restriction_2` (`group_restriction`,`active`,`highlight`,`date_to`);

ALTER TABLE `PREFIX_configuration_kpi` CHANGE `name` `name` varchar(64);

ALTER TABLE `PREFIX_smarty_lazy_cache` CHANGE `cache_id` `cache_id` varchar(255) NOT NULL DEFAULT '';
TRUNCATE TABLE `PREFIX_smarty_lazy_cache`;

/* Advanced EU Compliance tables */
CREATE TABLE IF NOT EXISTS `PREFIX_cms_role` (
  `id_cms_role` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `id_cms` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_cms_role`, `id_cms`),
  UNIQUE KEY `name` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_cms_role_lang` (
  `id_cms_role` int(11) unsigned NOT NULL,
  `id_lang` int(11) unsigned NOT NULL,
  `id_shop` int(11) unsigned NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_cms_role`,`id_lang`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_order_invoice` ADD `company_address` TEXT DEFAULT NULL AFTER `total_wrapping_tax_incl`;

ALTER TABLE `PREFIX_order_invoice` ADD `shop_address` TEXT DEFAULT NULL AFTER `total_wrapping_tax_incl`;
ALTER TABLE `PREFIX_order_invoice` ADD `invoice_address` TEXT DEFAULT NULL AFTER `shop_address`;
ALTER TABLE `PREFIX_order_invoice` ADD `delivery_address` TEXT DEFAULT NULL AFTER `invoice_address`;


INSERT INTO `PREFIX_hook` (`name`, `title`, `description`) VALUES ('displayInvoiceLegalFreeText', 'PDF Invoice - Legal Free Text', 'This hook allows you to modify the legal free text on PDF invoices');

UPDATE `PREFIX_hook` SET position = 0 WHERE name LIKE 'action%';

ALTER IGNORE TABLE `PREFIX_specific_price` ADD UNIQUE KEY `id_product_2` (`id_cart`, `id_product`,`id_shop`,`id_shop_group`,`id_currency`,`id_country`,`id_group`,`id_customer`,`id_product_attribute`,`from_quantity`,`id_specific_price_rule`,`from`,`to`);

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`)
  VALUES ('PS_INVCE_INVOICE_ADDR_RULES', '{"avoid":["vat_number","phone","phone_mobile"]}', NOW(), NOW());
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`)
  VALUES ('PS_INVCE_DELIVERY_ADDR_RULES', '{"avoid":["vat_number","phone","phone_mobile"]}', NOW(), NOW());

ALTER TABLE `PREFIX_pack` ADD KEY `product_item` (`id_product_item`,`id_product_attribute_item`);

ALTER TABLE `PREFIX_supply_order_detail` DROP KEY `id_supply_order`, DROP KEY `id_product`, ADD KEY `id_supply_order` (`id_supply_order`, `id_product`);

ALTER TABLE `PREFIX_carrier` ADD KEY `reference` (`id_reference`, `deleted`, `active`);
