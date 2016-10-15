<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();
$sql[_DB_PREFIX_.'badge'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'badge` (
			  `id_badge` int(11) NOT NULL AUTO_INCREMENT,
			  `id_ps_badge` int(11) NOT NULL,
			  `type` varchar(32) NOT NULL,
			  `id_group` int(11) NOT NULL,
			  `group_position` int(11) NOT NULL,
			  `scoring` int(11) NOT NULL,
			  `awb` INT NULL DEFAULT  \'0\',
			  `validated` tinyint(1) unsigned NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id_badge`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'badge_lang'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'badge_lang` (
			  `id_badge` int(11) NOT NULL,
			  `id_lang` int(11) NOT NULL,
			  `name` varchar(64),
			  `description` varchar(255),
			  `group_name` varchar(255),
			  PRIMARY KEY (`id_badge`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'condition'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'condition` (
			  `id_condition` int(11) NOT NULL AUTO_INCREMENT,
			  `id_ps_condition` int(11) NOT NULL,
			  `type` enum(\'configuration\', \'install\', \'sql\') NOT NULL,
			  `request` text ,
			  `operator` varchar(32),
			  `value` varchar(64),
			  `result` varchar(64),
			  `calculation_type` enum(\'hook\', \'time\'),
			  `calculation_detail` varchar(64),
			  `validated` tinyint(1) unsigned NOT NULL DEFAULT 0,
			  `date_add` datetime NOT NULL,
			  `date_upd` datetime NOT NULL,
			  PRIMARY KEY (`id_condition`, `id_ps_condition`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'condition_badge'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'condition_badge` (
			  `id_condition` int(11) NOT NULL,
			  `id_badge` int(11) NOT NULL,
			  PRIMARY KEY (`id_condition`, `id_badge`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'advice'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'advice` (
			  `id_advice` int(11) NOT NULL AUTO_INCREMENT,
			  `id_ps_advice` int(11) NOT NULL,
			  `id_tab` int(11) NOT NULL,
			  `ids_tab` TEXT,
			  `validated` tinyint(1) unsigned NOT NULL DEFAULT 0,
			  `hide` tinyint(1) NOT NULL DEFAULT 0,
			  `location` enum(\'after\', \'before\') NOT NULL,
			  `selector` varchar(255),
			  `start_day` int(11) NOT NULL DEFAULT 0,
			  `stop_day` int(11) NOT NULL DEFAULT 0,
			  `weight` int(11) NULL DEFAULT  \'1\',
			  PRIMARY KEY (`id_advice`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'advice_lang'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'advice_lang` (
			  `id_advice` int(11) NOT NULL,
			  `id_lang` int(11) NOT NULL,
			  `html` TEXT,
			  PRIMARY KEY (`id_advice`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'condition_advice'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'condition_advice` (
			  `id_condition` int(11) NOT NULL,
			  `id_advice` int(11) NOT NULL,
			  `display` tinyint(1) unsigned NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id_condition`, `id_advice`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[_DB_PREFIX_.'tab_advice'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tab_advice` (
			  `id_tab` int(11) NOT NULL,
			  `id_advice` int(11) NOT NULL,
			  PRIMARY KEY (`id_tab`, `id_advice`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
