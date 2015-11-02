<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_2($object)
{
	return ($object->registerHook('addproduct') && $object->registerHook('updateproduct') && $object->registerHook('deleteproduct') && $object->registerHook('actionOrderStatusPostUpdate'));
}
