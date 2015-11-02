SET NAMES 'utf8';

SET @alias = (SELECT IFNULL((SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = "AdminAliases" LIMIT 1), '0'));
UPDATE `PREFIX_tab` SET `id_parent` = 8 WHERE `id_tab` = @alias LIMIT 1;
SET @stores = (SELECT IFNULL((SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = "AdminStores" LIMIT 1), '0'));
UPDATE `PREFIX_tab` SET `id_parent` = 9 WHERE `id_tab` = @stores LIMIT 1;
SET @pdf = (SELECT IFNULL((SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = "AdminPDF" LIMIT 1), '0'));
UPDATE `PREFIX_tab` SET `id_parent` = 3 WHERE `id_tab` = @pdf LIMIT 1;
SET @tabs = (SELECT IFNULL((SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = "AdminTabs" LIMIT 1), '0'));
UPDATE `PREFIX_tab` SET `id_parent` = 29 WHERE `id_tab` = @tabs LIMIT 1;

ALTER TABLE `PREFIX_image_type` ADD `stores` tinyint(1) NOT NULL default '1';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_FORCE_SMARTY_2', '0', NOW(), NOW()),
('PS_DIMENSION_UNIT', 'cm', NOW(), NOW());

ALTER TABLE `PREFIX_product`
ADD `width` FLOAT NOT NULL AFTER `location`,
ADD `height` FLOAT NOT NULL AFTER `width`,
ADD `depth` FLOAT NOT NULL AFTER `height`;

SET @id_module = (SELECT IFNULL((SELECT `id_module` FROM `PREFIX_module` WHERE `name` = "statshome" LIMIT 1), '0'));
DELETE FROM `PREFIX_module` WHERE `id_module` = @id_module;
DELETE FROM `PREFIX_hook_module` WHERE `id_module` = @id_module;

ALTER TABLE `PREFIX_customer` ADD `is_guest` TINYINT(1) NOT NULL DEFAULT '0' AFTER `deleted`;
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_GUEST_CHECKOUT_ENABLED', '0', NOW(), NOW());

ALTER TABLE `PREFIX_category` ADD `nleft` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `level_depth`;
ALTER TABLE `PREFIX_category` ADD `nright` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `nleft`;
ALTER TABLE `PREFIX_category` ADD INDEX `nleftright` (`nleft`, `nright`);

ALTER TABLE `PREFIX_product` ADD  `id_tax_rules_group` int(10) unsigned NOT NULL AFTER `id_tax`;
ALTER TABLE `PREFIX_carrier` ADD  `id_tax_rules_group` int(10) unsigned NOT NULL AFTER `id_tax`;
ALTER TABLE `PREFIX_carrier` ADD INDEX ( `id_tax_rules_group` ) ;

CREATE TABLE `PREFIX_tax_rule` (
`id_tax_rules_group` INT NOT NULL ,
`id_country` INT NOT NULL ,
`id_state` INT NOT NULL ,
`id_tax` INT NOT NULL ,
`state_behavior` INT NOT NULL ,
PRIMARY KEY ( `id_tax_rules_group`, `id_country` , `id_state` )
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_tax_rules_group` (
`id_tax_rules_group` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 32 ) NOT NULL ,
`active` INT NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_help_access` (
  `id_help_access` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(45) NOT NULL,
  `version` varchar(8) NOT NULL,
  PRIMARY KEY (`id_help_access`),
  UNIQUE KEY `label` (`label`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

/* PHP:add_new_tab(AdminTaxRulesGroup, it:Regimi fiscali|es:Reglas de Impuestos|fr:Règles de taxes|de:Steuerregeln|en:Tax Rules,  4); */;
/* PHP:generate_ntree(); */;
/* PHP:generate_tax_rules(); */;
/* PHP:id_currency_country_fix(); */;
/* PHP:update_modules_sql(); */;

ALTER TABLE `PREFIX_product` DROP `id_tax`;
ALTER TABLE `PREFIX_carrier` DROP `id_tax`;

DROP TABLE `PREFIX_tax_state`, `PREFIX_tax_zone`, `PREFIX_country_tax`;
ALTER TABLE `PREFIX_orders` ADD `carrier_tax_rate` DECIMAL(10, 3) NOT NULL default '0.00' AFTER `total_shipping`;

INSERT INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES (NULL, 'beforeAuthentication', 'Before Authentication', 'Before authentication', 0);
