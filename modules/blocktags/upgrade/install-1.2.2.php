<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_2_2($object)
{
	return Configuration::updateValue('BLOCKTAGS_MAX_LEVEL', 3);
}
