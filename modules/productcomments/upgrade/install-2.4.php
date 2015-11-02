<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_4($object)
{
	return ($object->registerHook('displayProductListReviews') && $object->registerHook('top'));
}
