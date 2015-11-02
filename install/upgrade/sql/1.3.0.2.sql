SET NAMES 'utf8';

/* ##################################### */
/* 				STRUCTURE			 	 */
/* ##################################### */

ALTER TABLE `PREFIX_product_attachment` 
CHANGE `id_product` `id_product` INT(10) UNSIGNED NOT NULL,
CHANGE `id_attachment` `id_attachment` INT(10) UNSIGNED NOT NULL;

ALTER TABLE `PREFIX_attribute_impact` 
CHANGE `id_product` `id_product` INT(11) UNSIGNED NOT NULL,
CHANGE `id_attribute` `id_attribute` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `PREFIX_block_cms` 
CHANGE `id_block` `id_block` INT(10) UNSIGNED NOT NULL,
CHANGE `id_cms` `id_cms` INT(10) UNSIGNED NOT NULL;

ALTER TABLE `PREFIX_customization` 
CHANGE `id_cart` `id_cart` int(10) unsigned NOT NULL,
CHANGE `id_product_attribute` `id_product_attribute` int(10) unsigned NOT NULL default '0';

ALTER TABLE `PREFIX_customization_field` 
CHANGE `id_product` `id_product` int(10) unsigned NOT NULL;

ALTER TABLE `PREFIX_customization_field_lang` 
CHANGE `id_customization_field` `id_customization_field` int(10) unsigned NOT NULL,
CHANGE `id_lang` `id_lang` int(10) unsigned NOT NULL;

ALTER TABLE `PREFIX_customized_data` 
CHANGE `id_customization` `id_customization` int(10) unsigned NOT NULL;

ALTER TABLE `PREFIX_discount_category` 
CHANGE `id_category` `id_category` int(11) unsigned NOT NULL,
CHANGE `id_discount` `id_discount` int(11) unsigned NOT NULL;

ALTER TABLE `PREFIX_module_group` 
CHANGE `id_group` `id_group` int(11) unsigned NOT NULL;

ALTER TABLE `PREFIX_order_return_detail` 
CHANGE `id_customization` `id_customization` int(10) unsigned NOT NULL default '0';

ALTER TABLE `PREFIX_product_attribute_image` 
CHANGE `id_product_attribute` `id_product_attribute` int(10) unsigned NOT NULL,
CHANGE `id_image` `id_image` int(10) unsigned NOT NULL;

ALTER TABLE `PREFIX_referrer_cache` 
CHANGE `id_connections_source` `id_connections_source` int(11) unsigned NOT NULL,
CHANGE `id_referrer` `id_referrer` int(11) unsigned NOT NULL;

ALTER TABLE `PREFIX_scene_category` 
CHANGE `id_scene` `id_scene` int(10) unsigned NOT NULL,
CHANGE `id_category` `id_category` int(10) unsigned NOT NULL;

ALTER TABLE `PREFIX_scene_lang` 
CHANGE `id_scene` `id_scene` int(10) unsigned NOT NULL,
CHANGE `id_lang` `id_lang` int(10) unsigned NOT NULL;

ALTER TABLE `PREFIX_scene_products` 
CHANGE `id_scene` `id_scene` int(10) unsigned NOT NULL,
CHANGE `id_product` `id_product` int(10) unsigned NOT NULL;

ALTER TABLE `PREFIX_search_index` 
CHANGE `id_product` `id_product` int(11) unsigned NOT NULL,
CHANGE `id_word` `id_word` int(11) unsigned NOT NULL;

ALTER TABLE `PREFIX_state` 
CHANGE `id_country` `id_country` int(11) unsigned NOT NULL,
CHANGE `id_zone` `id_zone` int(11) unsigned NOT NULL;

ALTER TABLE `PREFIX_category_lang` 
CHANGE `meta_keywords` `meta_keywords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
CHANGE `meta_description` `meta_description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

ALTER TABLE `PREFIX_supplier_lang` 
CHANGE `meta_title` `meta_title` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
CHANGE `meta_keywords` `meta_keywords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
CHANGE `meta_description` `meta_description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

ALTER TABLE `PREFIX_manufacturer_lang` 
CHANGE `meta_title` `meta_title` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
CHANGE `meta_keywords` `meta_keywords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
CHANGE `meta_description` `meta_description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

ALTER TABLE `PREFIX_meta_lang` 
CHANGE `title` `title` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

/* ##################################### */
/* 				PRICE RANGE			 	 */
/* ##################################### */

ALTER TABLE `PREFIX_attribute_impact` CHANGE `price` `price` DECIMAL(17, 2) NOT NULL;

ALTER TABLE `PREFIX_delivery` CHANGE `price` `price` DECIMAL(17, 2) NOT NULL;

ALTER TABLE `PREFIX_discount` CHANGE `value` `value` DECIMAL(17, 2) NOT NULL DEFAULT '0.00',
CHANGE `minimal` `minimal` DECIMAL(17, 2) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_discount_quantity` CHANGE `value` `value` DECIMAL(17, 2) UNSIGNED NOT NULL;

ALTER TABLE `PREFIX_group` CHANGE `reduction` `reduction` DECIMAL(17, 2) NOT NULL DEFAULT '0.00';

ALTER TABLE `PREFIX_orders` CHANGE `total_discounts` `total_discounts` DECIMAL(17, 2) NOT NULL DEFAULT '0.00',
CHANGE `total_paid` `total_paid` DECIMAL(17, 2) NOT NULL DEFAULT '0.00',
CHANGE `total_paid_real` `total_paid_real` DECIMAL(17, 2) NOT NULL DEFAULT '0.00',
CHANGE `total_products` `total_products` DECIMAL(17, 2) NOT NULL DEFAULT '0.00',
CHANGE `total_products_wt` `total_products_wt` DECIMAL(17, 2) NOT NULL DEFAULT '0.00',
CHANGE `total_shipping` `total_shipping` DECIMAL(17, 2) NOT NULL DEFAULT '0.00',
CHANGE `total_wrapping` `total_wrapping` DECIMAL(17, 2) NOT NULL DEFAULT '0.00';

ALTER TABLE `PREFIX_order_detail` CHANGE `product_price` `product_price` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
CHANGE `product_quantity_discount` `product_quantity_discount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
CHANGE `ecotax` `ecotax` decimal(17,2) NOT NULL default '0.00';

ALTER TABLE `PREFIX_order_discount` CHANGE `value` `value` DECIMAL(17, 2) NOT NULL DEFAULT '0.00';

ALTER TABLE `PREFIX_product` CHANGE `price` `price` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
CHANGE `wholesale_price` `wholesale_price` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
CHANGE `ecotax` `ecotax` DECIMAL(17, 2) NOT NULL DEFAULT '0.00',
CHANGE `reduction_price` `reduction_price` DECIMAL(17, 2) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_product_attribute` CHANGE `wholesale_price` `wholesale_price` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000',
CHANGE `price` `price` DECIMAL(17, 2) NOT NULL DEFAULT '0.00',
CHANGE `ecotax` `ecotax` DECIMAL(17, 2) NOT NULL DEFAULT '0.00';

ALTER TABLE `PREFIX_range_price` CHANGE `delimiter1` `delimiter1` DECIMAL(20, 6) NOT NULL,
CHANGE `delimiter2` `delimiter2` DECIMAL(20, 6) NOT NULL;

ALTER TABLE `PREFIX_range_weight` CHANGE `delimiter1` `delimiter1` DECIMAL(20, 6) NOT NULL,
CHANGE `delimiter2` `delimiter2` DECIMAL(20, 6) NOT NULL;

ALTER TABLE `PREFIX_referrer` CHANGE `cache_sales` `cache_sales` DECIMAL(17, 2) NULL DEFAULT NULL;

UPDATE `PREFIX_configuration` 
SET `value` = IFNULL(ROUND(value / (1 + (
	SELECT `rate` 
	FROM `PREFIX_tax` 
	WHERE `id_tax` = (
		SELECT `value` 
		FROM (
			SELECT `value`
			FROM `PREFIX_configuration` 
			WHERE `name` = 'PS_GIFT_WRAPPING_TAX'
		)tmp
	)
) / 100), 2), 0) 
WHERE `name` = 'PS_GIFT_WRAPPING_PRICE';
