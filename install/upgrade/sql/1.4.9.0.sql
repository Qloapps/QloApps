SET NAMES 'utf8';

ALTER TABLE `PREFIX_image` ADD UNIQUE KEY `idx_product_image` (`id_image` , `id_product` , `cover`);

/* PHP:clean_category_product(); */;
ALTER TABLE `PREFIX_category_product` DROP INDEX `category_product_index`, ADD PRIMARY KEY (`id_category`, `id_product`);

ALTER TABLE `PREFIX_cms_category_lang` DROP INDEX `category_lang_index`, ADD PRIMARY KEY (`id_cms_category`, `id_lang`);
ALTER TABLE `PREFIX_order_tax` ADD `id_order_tax` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `PREFIX_feature_lang` ADD INDEX feature_name (`id_lang`, `name`);
ALTER TABLE `PREFIX_state` ADD INDEX statename (`name`);
ALTER TABLE `PREFIX_category` ADD INDEX nleftrightactive (`nleft`, `nright`, `active`);
ALTER TABLE `PREFIX_category` ADD INDEX level_depth (`level_depth`);
ALTER TABLE `PREFIX_category` ADD INDEX nright (`nright`);
ALTER TABLE `PREFIX_category` ADD INDEX nleft (`nleft`);
ALTER TABLE `PREFIX_specific_price` ADD INDEX from_quantity (`from_quantity`);
ALTER TABLE `PREFIX_product` ADD INDEX indexed (`indexed`);

UPDATE `PREFIX_country` SET `zip_code_format` = 'NNNNN' WHERE `iso_code` = 'MC' LIMIT 1;
UPDATE `PREFIX_county_zip_code` SET `to_zip_code` = `from_zip_code` WHERE `to_zip_code` = 0;
UPDATE `PREFIX_configuration` SET `value` = 0 WHERE `name` = 'PS_HIGH_HTML_THEME_COMPRESSION' LIMIT 1;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`)(SELECT 'PS_TAX_DISPLAY_ALL', `value`, NOW(), NOW() FROM `PREFIX_configuration` WHERE name = 'PS_TAX_DISPLAY' LIMIT 1);

DELETE FROM `PREFIX_referrer_cache` WHERE id_referrer NOT IN (SELECT id_referrer FROM `PREFIX_referrer`);

/* PHP:update_module_blocklayered(); */;