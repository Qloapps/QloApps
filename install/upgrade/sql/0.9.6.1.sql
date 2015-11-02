/* STRUCTURE */

CREATE TABLE `PREFIX_alias` (
	alias varchar(255) NOT NULL,
	search varchar(255) NOT NULL,
	active tinyint(1) NOT NULL default 1,
	id_alias int(10) NOT NULL auto_increment,
	PRIMARY KEY (id_alias),
	UNIQUE KEY alias (alias)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_configuration`
	ADD UNIQUE `name` (`name`);
ALTER TABLE `PREFIX_product`
	ADD `wholesale_price` DECIMAL( 13, 6 ) NOT NULL AFTER `price`;
ALTER TABLE `PREFIX_range_weight`
	CHANGE `delimiter1` `delimiter1` DECIMAL( 13, 6 ) NOT NULL DEFAULT '0.000000';
ALTER TABLE `PREFIX_range_weight`
	CHANGE `delimiter2` `delimiter2` DECIMAL( 13, 6 ) NOT NULL DEFAULT '0.000000';
 ALTER TABLE `PREFIX_discount_type_lang`
	CHANGE `name` `name` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
 ALTER TABLE `PREFIX_product`
	CHANGE `bargain` `on_sale` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `PREFIX_image_type`
	ADD `suppliers` BOOL NOT NULL DEFAULT 1;

/*  CONTENTS */

/* Adding tab alias */
INSERT INTO `PREFIX_tab` (`id_parent`, `class_name`, `position`) VALUES ((SELECT tmp.id_tab FROM (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminTools' LIMIT 1) AS tmp), 'AdminAliases', 9);
INSERT INTO `PREFIX_tab_lang` (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminAliases' LIMIT 1),
	'Alias' FROM `PREFIX_lang`);
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`)
	VALUES ('1', (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminAliases' LIMIT 1), '1', '1', '1', '1');

/* Adding tab import */
INSERT INTO `PREFIX_tab` (`id_parent`, `class_name`, `position`) VALUES ((SELECT tmp.id_tab FROM  (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminTools' LIMIT 1) AS tmp), 'AdminImport', 10);
INSERT INTO `PREFIX_tab_lang` (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminImport' LIMIT 1),
	'Import' FROM `PREFIX_lang`);
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`)
	VALUES ('1', (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminImport' LIMIT 1), '1', '1', '1', '1');

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`)
VALUES ('adminOrder', 'Display in Back-Office, tab AdminOrder', 'Launch modules when the tab AdminOrder is displayed on back-office.');


/* CONFIGURATION VARIABLE */