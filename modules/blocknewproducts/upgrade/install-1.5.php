<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_5($object)
{
	return ($object->unregisterHook('rightColumn') && $object->registerHook('displayHomeTab') && $object->registerHook('displayHomeTabContent'));
}
