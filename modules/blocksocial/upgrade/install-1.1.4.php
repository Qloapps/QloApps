<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1_4($object)
{
	Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET name = \'BLOCKSOCIAL_FACEBOOK\' WHERE name = \'blocksocial_facebook\'');
	Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET name = \'BLOCKSOCIAL_TWITTER\' WHERE name = \'blocksocial_twitter\'');
	Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET name = \'BLOCKSOCIAL_RSS\' WHERE name = \'blocksocial_rss\'');
	return true;
}