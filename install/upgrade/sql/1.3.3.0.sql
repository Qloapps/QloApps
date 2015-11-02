SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_detail` ADD `group_reduction` DECIMAL(10, 2) NOT NULL AFTER `reduction_amount`;
ALTER TABLE `PREFIX_order_detail` ADD `ecotax_tax_rate` DECIMAL(5, 3) NOT NULL AFTER `ecotax`;
ALTER TABLE `PREFIX_product` CHANGE `ecotax` `ecotax` DECIMAL(21, 6) NOT NULL DEFAULT '0.00';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) SELECT 'PS_LOCALE_LANGUAGE', l.`iso_code`, NOW(), NOW() FROM `PREFIX_configuration` c INNER JOIN `PREFIX_lang` l ON (l.`id_lang` = c.`value`) WHERE c.`name` = 'PS_LANG_DEFAULT';
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) SELECT 'PS_LOCALE_COUNTRY', co.`iso_code`, NOW(), NOW() FROM `PREFIX_configuration` c INNER JOIN `PREFIX_country` co ON (co.`id_country` = c.`value`) WHERE c.`name` = 'PS_COUNTRY_DEFAULT';
