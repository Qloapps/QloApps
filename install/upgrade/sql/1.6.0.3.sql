SET NAMES 'utf8';

ALTER TABLE `PREFIX_theme` ADD `default_left_column` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `PREFIX_theme` ADD `default_right_column` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `PREFIX_theme` ADD `product_per_page` INT UNSIGNED NOT NULL;

ALTER TABLE `PREFIX_tab_lang` CHANGE `name` `name` VARCHAR(64) DEFAULT NULL;

ALTER TABLE `PREFIX_attachment` ADD `file_size` bigint(10) unsigned NOT NULL DEFAULT 0 AFTER `file_name`;
/* PHP:p1603_add_attachment_size(); */;

DROP TABLE IF EXISTS `PREFIX_help_access`;

ALTER TABLE `PREFIX_theme_meta` CHANGE `left_column` `left_column` TINYINT( 1 ) NOT NULL DEFAULT '1',
CHANGE `right_column` `right_column` TINYINT( 1 ) NOT NULL DEFAULT '1';

ALTER TABLE `PREFIX_employee` ADD `bo_css` varchar(64) default 'admin-theme.css' AFTER `bo_theme`;

INSERT INTO `PREFIX_web_browser` (name) VALUES ('IE 11');

DELETE FROM `PREFIX_theme` WHERE `directory` = 'hotel-reservation-theme';

INSERT INTO `PREFIX_theme` (`name`, `directory`, `responsive`, `default_left_column`, `default_right_column`, `product_per_page`)
VALUES ('hotel-reservation-theme', 'hotel-reservation-theme', 1, 1, 0, 12);

INSERT IGNORE INTO `PREFIX_theme_meta` ( `id_theme` , `id_meta` , `left_column` , `right_column` )
  SELECT `PREFIX_theme`.`id_theme` , `PREFIX_meta`.`id_meta` , `default_left_column` , `default_right_column`
  FROM `PREFIX_theme` , `PREFIX_meta`;

ALTER TABLE `PREFIX_import_match` ADD UNIQUE (`name` ( 32 ));