SET NAMES 'utf8';

ALTER TABLE `PREFIX_state` CHANGE `iso_code` `iso_code` varchar(7) NOT NULL;

DROP TABLE `PREFIX_accounting_export`;
DROP TABLE `PREFIX_accounting_zone_shop`;
DROP TABLE `PREFIX_accounting_product_zone_shop`;
ALTER TABLE `PREFIX_tax` DROP `account_number`;
ALTER TABLE `PREFIX_customer` DROP `account_number`;

DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` IN (
	SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` LIKE('%Accounting%')
);

DELETE FROM `PREFIX_tab` WHERE `class_name` LIKE('%Accounting%');

/* PHP:move_translations_module_file(); */;

ALTER TABLE `PREFIX_tax_rule` CHANGE `zipcode_from` `zipcode_from` VARCHAR(12) NOT NULL, CHANGE `zipcode_to` `zipcode_to` VARCHAR(12) NOT NULL;

UPDATE PREFIX_order_detail_tax odt
LEFT JOIN PREFIX_tax t ON (t.id_tax = odt.id_tax)
SET unit_amount = IFNULL(ROUND((t.rate / 100) * (
		SELECT od.unit_price_tax_excl - ( o.total_discounts_tax_excl * ( od.unit_price_tax_excl / o.total_products ))
		FROM PREFIX_order_detail od
		LEFT JOIN PREFIX_orders o ON ( o.id_order = od.id_order)
		WHERE odt.id_order_detail = od.id_order_detail
), 2), 0);


UPDATE PREFIX_order_detail_tax odt
LEFT JOIN PREFIX_order_detail od ON (od.id_order_detail = odt.id_order_detail)
SET total_amount = IFNULL(odt.unit_amount * od.product_quantity, 0);

/* PHP:add_missing_shop_column_pagenotfound(); */;

/* PHP:editorial_update_multishop(); */;
/* PHP:update_module_product_comments(); */;

/* PHP:add_missing_columns_customer(); */;

CREATE TABLE IF NOT EXISTS `PREFIX_risk` (
  `id_risk` int(11) NOT NULL AUTO_INCREMENT,
  `percent` tinyint(3) NOT NULL,
  `color` varchar(32) NULL,
  PRIMARY KEY (`id_risk`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_risk_lang` (
  `id_risk` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id_risk`,`id_lang`),
  KEY `id_risk` (`id_risk`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

UPDATE `PREFIX_tab` SET `class_name`="AdminShopGroup" WHERE class_name="AdminGroupShop";

/* PHP:upgrade_cms_15_rename(); */;
/* PHP:migrate_tabs_multi_shop(); */;
/* PHP:add_default_restrictions_modules_groups(); */;

UPDATE `PREFIX_order_payment` SET `id_order_invoice` = 0 WHERE `id_order_invoice` LIKE "" OR `id_order_invoice` IS NULL;

/* PHP:p15012_add_missing_columns(); */;

UPDATE `PREFIX_specific_price` SET `price` = -1 WHERE `price` = 0;