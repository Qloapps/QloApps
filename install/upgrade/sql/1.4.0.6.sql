SET NAMES 'utf8';

ALTER TABLE `PREFIX_customer` DROP INDEX `customer_email`;
ALTER TABLE `PREFIX_customer` ADD INDEX  `customer_email` (`email`);

ALTER TABLE `PREFIX_lang` ADD `language_code` char(5) NULL AFTER `iso_code`;
UPDATE `PREFIX_lang` SET language_code = iso_code;
ALTER TABLE `PREFIX_lang` MODIFY `language_code` char(5) NOT NULL;

DELETE FROM `PREFIX_module` WHERE `name` = 'gridextjs' LIMIT 1;
DELETE FROM `PREFIX_hook_module` WHERE `id_module` NOT IN (SELECT id_module FROM `PREFIX_module`);
UPDATE `PREFIX_configuration` SET `value` = 'gridhtml' WHERE `name` = 'PS_STATS_GRID_RENDER' AND `value` = 'gridextjs' LIMIT 1;
