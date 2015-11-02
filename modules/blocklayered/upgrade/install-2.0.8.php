<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_8($object)
{
	$return = Configuration::updateValue('PS_LAYERED_FILTER_PRICE_ROUNDING', 1);
	$query = 'ALTER TABLE `'._DB_PREFIX_.'layered_indexable_attribute_group_lang_value` CHANGE `url_name` `url_name` VARCHAR( 128 ) NULL DEFAULT NULL , CHANGE `meta_title` `meta_title` VARCHAR( 128 ) NULL DEFAULT NULL';
	$return &= Db::getInstance()->execute($query);

	$query = 'ALTER TABLE `'._DB_PREFIX_.'layered_indexable_attribute_lang_value` CHANGE `url_name` `url_name` VARCHAR( 128 ) NULL DEFAULT NULL , CHANGE `meta_title` `meta_title` VARCHAR( 128 ) NULL DEFAULT NULL';
	$return &= Db::getInstance()->execute($query);

	$query = 'ALTER TABLE `'._DB_PREFIX_.'layered_indexable_feature_lang_value` CHANGE `url_name` `url_name` VARCHAR( 128 ) NULL DEFAULT NULL , CHANGE `meta_title` `meta_title` VARCHAR( 128 ) NULL DEFAULT NULL';
	$return &= Db::getInstance()->execute($query);

	$query = 'ALTER TABLE `'._DB_PREFIX_.'layered_indexable_feature_value_lang_value` CHANGE `url_name` `url_name` VARCHAR( 128 ) NULL DEFAULT NULL , CHANGE `meta_title` `meta_title` VARCHAR( 128 ) NULL DEFAULT NULL';
	$return &= Db::getInstance()->execute($query);

	$query = 'ALTER TABLE `'._DB_PREFIX_.'layered_product_attribute` ADD PRIMARY KEY (`id_attribute`, `id_product`), DROP KEY `id_attribute`, ADD UNIQUE KEY `id_attribute_group` (`id_attribute_group`,`id_attribute`,`id_product`)';
	$return &= Db::getInstance()->execute($query);

	return $return;
}
