<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_9($object)
{
	return $object->registerHook('displayBackOfficeHeader');
}
