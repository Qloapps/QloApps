<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_3($object)
{
	return Configuration::updateValue('PS_BLOCK_CART_XSELL_LIMIT', 12);
}
