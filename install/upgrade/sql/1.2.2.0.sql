SET NAMES 'utf8';

ALTER TABLE `PREFIX_discount_category` ADD INDEX ( `id_discount` );

DELETE FROM `PREFIX_delivery` WHERE `id_range_weight` != NULL AND `id_range_weight` NOT IN (SELECT `id_range_weight` FROM `PREFIX_range_weight`);
DELETE FROM `PREFIX_delivery` WHERE `id_range_price` != NULL AND `id_range_weight` NOT IN (SELECT `id_range_price` FROM `PREFIX_range_price`);
