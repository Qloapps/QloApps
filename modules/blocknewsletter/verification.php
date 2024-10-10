<?php
require_once(dirname(__FILE__).'/../../config/config.inc.php');
Tools::displayFileAsDeprecated();

require_once('blocknewsletter.php');

$module = new Blocknewsletter();

if (!Module::isInstalled($module->name))
	exit;

$token = Tools::getValue('token');

require_once(dirname(__FILE__).'/../../header.php');
echo $module->confirmEmail($token);
require_once(dirname(__FILE__).'/../../footer.php');
