SET NAMES 'utf8';
ALTER TABLE `PREFIX_product` CHANGE `ecotax` `ecotax` DECIMAL(21, 6) NOT NULL DEFAULT '0.00';
/* PHP:move_crossselling(); */;

UPDATE `PREFIX_cms` SET `id_cms_category` = 1;

/* PHP:add_new_tab(AdminStores, fr:Magasins|es:Tiendas|en:Stores|de:Shops|it:Negozi, 9); */;

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) SELECT 'PS_LOCALE_LANGUAGE', l.`iso_code`, NOW(), NOW() FROM `PREFIX_configuration` c INNER JOIN `PREFIX_lang` l ON (l.`id_lang` = c.`value`) WHERE c.`name` = 'PS_LANG_DEFAULT';
INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) SELECT 'PS_LOCALE_COUNTRY', co.`iso_code`, NOW(), NOW() FROM `PREFIX_configuration` c INNER JOIN `PREFIX_country` co ON (co.`id_country` = c.`value`) WHERE c.`name` = 'PS_COUNTRY_DEFAULT';
/* PHP:reorderpositions(); */;

ALTER TABLE `PREFIX_webservice_permission` CHANGE `method` `method` ENUM( 'GET', 'POST', 'PUT', 'DELETE', 'HEAD' ) NOT NULL;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_ATTACHMENT_MAXIMUM_SIZE', '2', NOW(), NOW()),
('PS_SMARTY_CACHE', '1', NOW(), NOW());

ALTER TABLE `PREFIX_product_attribute` CHANGE `price` `price` decimal(20,6) NOT NULL default '0.000000';
UPDATE `PREFIX_product_attribute` pa SET pa.`price` = pa.`price` / (1 + IFNULL((SELECT t.`rate` FROM `PREFIX_tax` t INNER JOIN `PREFIX_product` p ON (p.`id_tax` = t.`id_tax`) WHERE p.`id_product` = pa.`id_product`) ,0) / 100);

