<?php

// File Example for upgrade

if (!defined('_PS_VERSION_'))
	exit;

// object module ($this) available
function upgrade_module_0_8($object)
{
	$upgrade_version = '0.8';

	$object->upgrade_detail[$upgrade_version] = array();

	// Change url type from varchar to text to avoid url length issues
	$query = 'ALTER TABLE  `'._DB_PREFIX_.'themeconfigurator` CHANGE  `url`  `url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';

	if (!Db::getInstance()->execute($query))
		$object->upgrade_detail[$upgrade_version][] = $object->l(sprintf('Can\'t change %s type', _DB_PREFIX_.'themeconfigurator.url'));


	return (bool)!count($object->upgrade_detail[$upgrade_version]);
}