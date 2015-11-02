SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_detail_tax` CHANGE `unit_amount` `unit_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.000000';
ALTER TABLE `PREFIX_order_detail_tax` CHANGE `total_amount` `total_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.000000';

ALTER TABLE `PREFIX_customer_message` ADD `read` tinyint(1) NOT NULL default '0' AFTER `private`;

INSERT INTO `PREFIX_configuration`(`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_ALLOW_MOBILE_DEVICE', '1', NOW(), NOW());

/* PHP:p15017_add_id_shop_to_primary_key(); */;

UPDATE `PREFIX_tab_lang` SET `name` = 'Menus' WHERE `name` = 'tabs' AND `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminTabs' LIMIT 1) AND `id_lang` IN (SELECT `id_lang` FROM `PREFIX_lang` WHERE `iso_code` IN ('en','fr','es','de','it'));

/* PHP:clean_tabs_15(); */;