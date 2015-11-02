<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_0_8($object)
{
	if (!$object->isRegisteredInHook('header'))
		return $object->registerHook('header');
	return true;
}
