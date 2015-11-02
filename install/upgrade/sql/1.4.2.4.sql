SET NAMES 'utf8';

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_BACKUP_DROP_TABLE', 1, NOW(), NOW());

UPDATE `PREFIX_tab_lang` SET `name` = 'SEO & URLs' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminMeta' LIMIT 1) AND `id_lang` IN (SELECT `id_lang` FROM `PREFIX_lang` WHERE `iso_code` IN ('en','fr','es','de','it'));

/* These 3 lines (remove_duplicate, drop index, add unique) MUST stay together in this order */
/* PHP:remove_duplicate_category_groups(); */;
ALTER TABLE `PREFIX_category_group` DROP INDEX `category_group_index`;
ALTER TABLE `PREFIX_category_group` ADD UNIQUE `category_group_index` (`id_category`,`id_group`);