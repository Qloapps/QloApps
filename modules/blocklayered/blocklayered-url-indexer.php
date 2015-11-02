<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/blocklayered.php');

if (substr(Tools::encrypt('blocklayered/index'),0,10) != Tools::getValue('token') || !Module::isInstalled('blocklayered'))
	die('Bad token');

$blockLayered = new BlockLayered();
$cursor = Tools::jsonDecode(Tools::getValue('cursor', '{}'), true);
echo $blockLayered->indexUrl($cursor, (int)Tools::getValue('truncate'));