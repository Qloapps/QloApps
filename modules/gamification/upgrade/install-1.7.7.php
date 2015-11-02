<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_7_7($object)
{
	$cols = array(
		'start_day' => array('exist' => false, 'sql' => 'ALTER TABLE `'._DB_PREFIX_.'advice` ADD `start_day` INT NULL DEFAULT 0 '),
		'stop_day' => array('exist' => false, 'sql' => 'ALTER TABLE `'._DB_PREFIX_.'advice` ADD `stop_day` INT NULL DEFAULT 0 '),
		'start_date' =>  array('exist' => false, 'sql' => 'ALTER TABLE `'._DB_PREFIX_.'advice` DROP `start_date`'),
		'stop_date' =>  array('exist' => false, 'sql' => 'ALTER TABLE `'._DB_PREFIX_.'advice` DROP `stop_date`'),
	);
	
	$columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.'advice` ');
	foreach ($columns as $c)
		if (in_array($c['Field'], array_keys($cols)))
			$cols[$c['Field']]['exist'] = true;
	
	foreach ($cols as $name => $co)
		if (in_array($name, array('start_day', 'stop_day')))
		{
			if (!$co['exist'])
				Db::getInstance()->execute($co['sql']);
		}
		elseif ($co['exist'])
			Db::getInstance()->execute($co['sql']);

	return true;
}