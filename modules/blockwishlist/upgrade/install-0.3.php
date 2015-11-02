<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_0_3($object)
{
	return ($object->registerHook('displayProductListFunctionalButtons') && $object->registerHook('top'));
}
