<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1_3()
{
	$list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'wishlist`');
	if (is_array($list_fields))
	{
		foreach ($list_fields as $k => $field)
			$list_fields[$k] = $field['Field'];
		if (in_array('is_default', $list_fields))
			return (bool)Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'wishlist` CHANGE `is_default` `default` INT( 11 ) NOT NULL DEFAULT "0"');
	}
	return true;
}
