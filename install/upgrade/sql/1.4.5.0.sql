SET NAMES 'utf8';

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_RESTRICT_DELIVERED_COUNTRIES', '0', NOW(), NOW());

UPDATE `PREFIX_country_lang` SET `name` = 'United States' WHERE `name` = 'United State';

ALTER TABLE `PREFIX_discount` ADD `include_tax` TINYINT(1) NOT NULL DEFAULT '0';

UPDATE `PREFIX_order_detail` SET `product_price` = `product_price` /( 1-(`group_reduction`/100));

DELETE FROM `PREFIX_configuration` WHERE name IN ('PS_LAYERED_BITLY_USERNAME', 'PS_LAYERED_BITLY_API_KEY', 'PS_LAYERED_SHARE') LIMIT 3;

ALTER TABLE `PREFIX_delivery` CHANGE `price` `price` DECIMAL(20, 6) NOT NULL;

ALTER TABLE `PREFIX_store` CHANGE `latitude` `latitude` DECIMAL(10, 8) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_store` CHANGE `longitude` `longitude` DECIMAL(10, 8) NULL DEFAULT NULL;

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`, `live_edit`) VALUES
('attributeGroupForm', 'Add fields to the form "attribute group"', 'Add fields to the form "attribute group"', 0, 0),
('afterSaveAttributeGroup', 'On saving attribute group', 'On saving attribute group', 0, 0),
('afterDeleteAttributeGroup', 'On deleting attribute group', 'On deleting "attribute group', 0, 0),
('featureForm', 'Add fields to the form "feature"', 'Add fields to the form "feature"', 0, 0),
('afterSaveFeature', 'On saving attribute feature', 'On saving attribute feature', 0, 0),
('afterDeleteFeature', 'On deleting attribute feature', 'On deleting attribute feature', 0, 0),
('afterSaveProduct', 'On saving products', 'On saving products', 0, 0),
('productListAssign', 'Assign product list to a category', 'Assign product list to a category', 0, 0),
('postProcessAttributeGroup', 'On post-process in admin attribute group', 'On post-process in admin attribute group', 0, 0),
('postProcessFeature', 'On post-process in admin feature', 'On post-process in admin feature', 0, 0),
('featureValueForm', 'Add fields to the form "feature value"', 'Add fields to the form "feature value"', 0, 0),
('postProcessFeatureValue', 'On post-process in admin feature value', 'On post-process in admin feature value', 0, 0),
('afterDeleteFeatureValue', 'On deleting attribute feature value', 'On deleting attribute feature value', 0, 0),
('afterSaveFeatureValue', 'On saving attribute feature value', 'On saving attribute feature value', 0, 0),
('attributeForm', 'Add fields to the form "feature value"', 'Add fields to the form "feature value"', 0, 0),
('postProcessAttribute', 'On post-process in admin feature value', 'On post-process in admin feature value', 0, 0),
('afterDeleteAttribute', 'On deleting attribute feature value', 'On deleting attribute feature value', 0, 0),
('afterSaveAttribute', 'On saving attribute feature value', 'On saving attribute feature value', 0, 0);

ALTER TABLE `PREFIX_employee` ADD `bo_show_screencast` TINYINT(1) NOT NULL DEFAULT '1' AFTER `bo_uimode`;

UPDATE `PREFIX_country` SET id_zone = (SELECT id_zone FROM `PREFIX_zone` WHERE name = 'Oceania' LIMIT 1) WHERE iso_code = 'KI' LIMIT 1;

ALTER TABLE `PREFIX_lang` ADD `date_format_lite` char(32) NOT NULL DEFAULT 'Y-m-d' AFTER language_code;
ALTER TABLE `PREFIX_lang` ADD `date_format_full` char(32) NOT NULL DEFAULT 'Y-m-d H:i:s' AFTER date_format_lite;
UPDATE `PREFIX_lang` SET `date_format_lite` = 'd/m/Y' WHERE `iso_code` IN ('fr', 'es', 'it');
UPDATE `PREFIX_lang` SET `date_format_full` = 'd/m/Y H:i:s' WHERE `iso_code` IN ('fr', 'es', 'it');
UPDATE `PREFIX_lang` SET `date_format_lite` = 'd.m.Y' WHERE `iso_code` = 'de';
UPDATE `PREFIX_lang` SET `date_format_full` = 'd.m.Y H:i:s' WHERE `iso_code` = 'de';
UPDATE `PREFIX_lang` SET `date_format_lite` = 'm/d/Y' WHERE `iso_code` = 'en';
UPDATE `PREFIX_lang` SET `date_format_full` = 'm/d/Y H:i:s' WHERE `iso_code` = 'en';

ALTER IGNORE TABLE `PREFIX_specific_price_priority` ADD UNIQUE (
`id_product`
);

