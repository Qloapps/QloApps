<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_1($object)
{
	return $object->registerHook('displayBackOfficeCategory');
}
