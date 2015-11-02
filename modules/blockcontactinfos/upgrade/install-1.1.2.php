<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1_2($object)
{
	Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'configuration SET `name` = UPPER(`name`) WHERE `name` LIKE "BLOCKCONTACTINFOS\_%"');
	return true;
}
