INSERT INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`)
VALUES (NULL, 'afterCreateHtaccess', 'After htaccess creation', 'After htaccess creation', 0);

UPDATE  `PREFIX_meta_lang` SET  `url_rewrite` =  'kontaktieren-sie-uns' WHERE id_meta = 3 AND id_lang = 4 AND url_rewrite = 'Kontaktieren Sie uns';
UPDATE  `PREFIX_meta_lang` SET  `url_rewrite` =  'kennwort-wiederherstellung' WHERE id_meta = 7 AND id_lang = 4 AND url_rewrite = 'Kennwort Wiederherstellung';
UPDATE  `PREFIX_meta_lang` SET  `url_rewrite` =  'il-mio-account' WHERE id_meta = 18 AND id_lang = 5 AND url_rewrite = 'il mio-account';
UPDATE  `PREFIX_meta_lang` SET  `url_rewrite` =  'nota-di-ordine' WHERE id_meta = 20 AND id_lang = 5 AND url_rewrite = 'nota di-ordine';

INSERT INTO `PREFIX_meta` (`page`) VALUES ('order-opc');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`)
(
	SELECT `id_lang`, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'order-opc'), 'Order', 'quick-order'
	FROM `PREFIX_lang`
);
INSERT INTO `PREFIX_meta` (`page`) VALUES ('guest-tracking');
INSERT INTO `PREFIX_meta_lang` (`id_lang`, `id_meta`, `title`, `url_rewrite`)
(
	SELECT `id_lang`, (SELECT `id_meta` FROM `PREFIX_meta` WHERE `page` = 'guest-tracking'), 'Guest tracking', 'guest-tracking'
	FROM `PREFIX_lang`
);

UPDATE  `PREFIX_hook` SET  `live_edit` =  '1' WHERE  `PREFIX_hook`.`name` IN ('productfooter', 'payment');

UPDATE `PREFIX_configuration` SET name = 'PS_GEOLOCATION_ENABLED' WHERE name = 'PS_GEOLOCALIZATION_ENABLED';
UPDATE `PREFIX_configuration` SET name = 'PS_GEOLOCATION_BEHAVIOR' WHERE name = 'PS_GEOLOCALIZATION_BEHAVIOR';
UPDATE `PREFIX_configuration` SET name = 'PS_GEOLOCATION_WHITELIST' WHERE name = 'PS_GEOLOCALIZATION_WHITELIST';
UPDATE `PREFIX_tab` SET class_name = 'AdminGeolocation' WHERE class_name = 'AdminGeolocalization';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_CANONICAL_REDIRECT', '0', NOW(), NOW());

ALTER TABLE `PREFIX_webservice_account` ADD `class_name` VARCHAR( 50 ) NOT NULL DEFAULT 'WebserviceRequest' AFTER `key`;
ALTER TABLE `PREFIX_webservice_account` ADD `description` text NULL AFTER `key`;

/* PHP:add_new_tab(AdminHome, en:Home|fr:Accueil|es:Home|de:Home|it:Home,  -1); */;
/* PHP:add_new_tab(AdminStockMvt, de:Lagerbewegungen|fr:Mouvements de Stock|it:Movimenti magazzino|en:Stock Movements,  1); */;
/* PHP:update_for_13version(); */;

