<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_9_7($object)
{
	return ($object->unregisterHook('top'));
}
