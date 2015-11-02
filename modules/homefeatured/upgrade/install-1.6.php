<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_6($object)
{
	return Configuration::updateValue('HOME_FEATURED_CAT', (int)Context::getContext()->shop->getCategory()) && Configuration::updateValue('HOME_FEATURED_RANDOMIZE', false);
}
