SET NAMES 'utf8';

ALTER TABLE `PREFIX_cart_rule` ADD `shop_restriction` tinyint(1) unsigned NOT NULL default 0 AFTER `product_restriction`;

CREATE TABLE `PREFIX_cart_rule_shop` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_shop` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_configuration`(`id_group_shop`, `id_shop`, `name`, `value`, `date_add`, `date_upd`)
VALUES
	(NULL, NULL, 'PS_LOGO_MAIL', 'logo_mail.jpg', NOw(), NOW()),
	(NULL, NULL, 'PS_LOGO_INVOICE', 'logo_invoice.jpg', NOW(), NOW()),
	(NULL, NULL, 'PS_FAVICON', 'favicon.jpg', NOW(), NOW()),
	(NULL, NULL, 'PS_STORES_ICON', 'logo_stores.gif', NOW(), NOW());

SET @id_configuration = (SELECT id_configuration FROM `PREFIX_configuration` WHERE `name` LIKE 'PS_LOGO' LIMIT 1);

REPLACE INTO `PREFIX_configuration`(`id_group_shop`, `id_shop`, `name`, `value`, `date_add`, `date_upd`)
VALUES((SELECT IFNULL(@id_configuration, NULL)), NULL, 'PS_LOGO', 'logo.png', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

CREATE TABLE `PREFIX_module_preference` (
  `id_module_preference` int(11) NOT NULL auto_increment,
  `id_employee` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  `interest` tinyint(1) default NULL,
  `favorite` tinyint(1) default NULL,
  PRIMARY KEY (`id_module_preference`),
  UNIQUE KEY `employee_module` (`id_employee`, `module`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_category_shop` ADD `position` int(10) unsigned NOT NULL default 0 AFTER `id_shop`;