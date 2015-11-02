SET NAMES 'utf8';

/* PHP:add_column_order_state_deleted_if_not_exists(); */;

ALTER TABLE `PREFIX_category` ADD COLUMN `is_root_category` tinyint(1) NOT NULL default '0' AFTER `position`;

UPDATE `PREFIX_category` SET `is_root_category` = 1 WHERE `id_category` = 1;

ALTER TABLE `PREFIX_image_type` DROP `id_theme`;

ALTER TABLE `PREFIX_specific_price_rule_condition` CHANGE `id_specific_price_rule_condition` `id_specific_price_rule_condition` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_specific_price_rule_condition_group` CHANGE `id_specific_price_rule_condition_group` `id_specific_price_rule_condition_group` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;

UPDATE `PREFIX_supply_order_state_lang` 
SET `name` = "order canceled" 
WHERE `name` = "order fenced" AND (`id_lang` = 1 OR `id_lang` = 3 OR `id_lang` = 4 OR `id_lang` = 5);

UPDATE `PREFIX_supply_order_state_lang` 
SET `name` = "Commande cloturée" 
WHERE `name` = "order fenced" AND id_lang = 2;


CREATE TABLE `PREFIX_category_shop` (
  `id_category` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  PRIMARY KEY (`id_category`, `id_shop`),
  UNIQUE KEY `id_category_shop` (`id_category`,`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

/* PHP:generate_root_category_for_multishop(); */;

/* PHP:update_mailalerts_add_column_idshop(); */;

CREATE TABLE `PREFIX_cart_rule_product_rule_group` (
	`id_product_rule_group` int(10) unsigned NOT NULL auto_increment,
	`id_cart_rule` int(10) unsigned NOT NULL,
	`quantity` int(10) unsigned NOT NULL default 1,
	PRIMARY KEY  (`id_product_rule_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_cart_rule_product_rule_group` (`id_product_rule_group`, `id_cart_rule`, `quantity`) (
	SELECT `id_cart_rule`, `id_cart_rule`, `quantity` FROM `PREFIX_cart_rule_product_rule`
);

ALTER TABLE `PREFIX_cart_rule_product_rule` CHANGE `id_cart_rule` `id_product_rule_group` int(10) unsigned NOT NULL;
ALTER TABLE `PREFIX_cart_rule_product_rule` CHANGE `type` `type` ENUM('products', 'categories', 'attributes', 'manufacturers', 'suppliers') NOT NULL;

ALTER TABLE `PREFIX_cart_rule` ADD `partial_use` tinyint(1) unsigned NOT NULL default 0 AFTER `priority`;

ALTER TABLE `PREFIX_orders` ADD `current_state` int(10) unsigned NOT NULL default 0 AFTER `id_address_invoice`;
