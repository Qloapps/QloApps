<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_0_7($object)
{
	return $object->registerHook('actionOrderStatusPostUpdate');
}
