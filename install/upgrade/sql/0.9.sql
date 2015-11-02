/* STRUCTURE */

ALTER TABLE `PREFIX_currency` ADD `iso_code` VARCHAR( 3 ) NOT NULL DEFAULT '0' AFTER `name`;
ALTER TABLE `PREFIX_product_attribute` ADD `default_on` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `weight`;
ALTER TABLE `PREFIX_carrier` ADD `tax` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `deleted`;

ALTER TABLE `PREFIX_order_detail`
 ADD `download_hash` VARCHAR(255) default NULL AFTER `tax_rate`,
 ADD `download_nb` INT(10) unsigned default 0 AFTER `tax_rate`,
 ADD `download_deadline` DATETIME DEFAULT NULL AFTER `tax_rate`;

CREATE TABLE `PREFIX_product_download` (
 `id_product_download` INT(10) unsigned NOT NULL auto_increment,
 `id_product` INT(10) unsigned NOT NULL,
 `display_filename` VARCHAR(255) default NULL,
 `physically_filename` VARCHAR(255) default NULL,
 `date_deposit` DATETIME NOT NULL,
 `date_expiration` DATETIME default NULL,
 `nb_days_accessible` int(10) unsigned default NULL,
 `nb_downloadable` int(10) unsigned default 1,
 PRIMARY KEY  (`id_product_download`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/*  CONTENTS */

/* Adding tab Appearance */
UPDATE `PREFIX_tab` SET `class_name` = 'AdminAppearance' WHERE class_name = 'AdminHomepage';
UPDATE `PREFIX_tab_lang` SET `name` = 'Appearance'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminAppearance');
UPDATE `PREFIX_tab_lang` SET `name` = 'Apparence'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminAppearance')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');

/* Adding iso_code to currency */
UPDATE `PREFIX_currency` SET `iso_code` = 'XXX';

/* Conf vars */
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_DISPLAY_QTIES', '1', NOW(), NOW());