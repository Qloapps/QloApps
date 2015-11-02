<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_4($object)
{
	return ($object->registerHook('actionAdminStoresControllerUpdate_optionsAfter'));
}
