<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_0($object)
{
	$return = true;

	$return &= $object->registerHook('displayCustomerIdentityForm');
	$return &= $object->unregisterHook('header');

	$langs = Language::getLanguages(false);
	$old_messages = array();
	foreach ($langs as $l)
		$old_messages[$l['id_lang']] = Configuration::get('CUSTPRIV_MESSAGE', $l['id_lang']);

	Configuration::updateValue('CUSTPRIV_MESSAGE', $old_messages);
	Configuration::updateValue('CUSTPRIV_MESSAGE', $old_messages);

	return $return;
}
