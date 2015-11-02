<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1($object)
{
	return ($object->registerHook('displayBanner') && $object->unregisterHook('displayTop'));
}
