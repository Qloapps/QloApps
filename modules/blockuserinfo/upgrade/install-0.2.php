<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_0_2($object)
{
	return ($object->registerHook('displayNav'));
}
