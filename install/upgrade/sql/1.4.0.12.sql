ALTER TABLE `PREFIX_product` CHANGE `ecotax` `ecotax` DECIMAL( 17, 6 ) NOT NULL DEFAULT '0.00';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_LAST_SHOP_UPDATE', NOW(), NOW(), NOW());

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_STORES_DISPLAY_SITEMAP', 1, NOW(), NOW());

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_COOKIE_CHECKIP', 1, NOW(), NOW());