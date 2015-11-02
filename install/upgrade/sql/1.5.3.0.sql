SET NAMES 'utf8';

/* PHP:outstanding_allow_amount1530(); */;

/* PHP:blocknewsletter1530(); */;

/* PHP:block_category_1521(); */;

/* PHP:update_order_messages(); */;

UPDATE `PREFIX_order_state` SET `delivery` = 0 WHERE `id_order_state` = 3;

ALTER TABLE  `PREFIX_product_shop` ADD `id_product_redirected` int(10) unsigned NOT NULL default '0' AFTER `active` ;

ALTER TABLE  `PREFIX_product` ADD `id_product_redirected` int(10) unsigned NOT NULL default '0' AFTER `active` ;

UPDATE `PREFIX_order_state` SET `send_email` = 1 WHERE `id_order_state` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_WS_PAYMENT' LIMIT 1);

UPDATE `PREFIX_order_state_lang` SET `template` = 'payment' WHERE `id_order_state` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_WS_PAYMENT' LIMIT 1);

DELETE FROM `PREFIX_configuration` WHERE `name`= 'PS_HIGH_HTML_THEME_COMPRESSION';

INSERT INTO `PREFIX_configuration`(`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_MAIL_COLOR', '#db3484', NOW(), NOW());

ALTER TABLE `PREFIX_order_cart_rule` CHANGE `name` `name` VARCHAR(254);

ALTER IGNORE TABLE `PREFIX_cart` CHANGE `delivery_option` `delivery_option` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `PREFIX_currency_shop` ADD `conversion_rate` DECIMAL( 13, 6 ) NOT NULL;
UPDATE `PREFIX_currency_shop` a SET `conversion_rate` = (SELECT `conversion_rate` FROM `PREFIX_currency` b WHERE a.id_currency = b.id_currency);

INSERT INTO `PREFIX_configuration`(`name`, `value`, `id_shop`, `id_shop_group`, `date_add`, `date_upd`) 
	(SELECT 'PS_GIFT_WRAPPING_TAX_RULES_GROUP', b.`id_tax_rules_group`, a.`id_shop`, a.`id_shop_group`, NOW(), NOW()
		FROM `PREFIX_configuration` a
		JOIN `PREFIX_tax_rule` b ON (a.value = b.id_tax)
		WHERE a.name='PS_GIFT_WRAPPING_TAX' 
		GROUP BY a.`id_shop`, a.`id_shop_group`
	);

DELETE FROM `PREFIX_configuration` WHERE name='PS_GIFT_WRAPPING_TAX';

ALTER TABLE  `PREFIX_cart_rule` ADD `highlight` tinyint(1) unsigned NOT NULL default 0 AFTER `gift_product_attribute`;