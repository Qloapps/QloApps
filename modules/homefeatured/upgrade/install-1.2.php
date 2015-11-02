<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_2($object)
{
	return ($object->registerHook('displayHomeTab') && $object->registerHook('displayHomeTabContent') && $object->registerHook('categoryUpdate'));
}
