<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1($object)
{
	$object->registerHook('displayProductListFunctionalButtons');
	$object->registerHook('top');
	$list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'wishlist`');
	
	if (is_array($list_fields))
	{
		foreach ($list_fields as $k => $field)
			$list_fields[$k] = $field['Field'];
		if (!in_array('id_shop_group', $list_fields))
			return (bool)Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'wishlist` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) NOT NULL DEFAULT "1"');
	}
	return true;
}
