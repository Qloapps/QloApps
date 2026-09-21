<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function p15016_add_missing_columns()
{
    $errors = array();

    $id_module = Db::getInstance()->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name="blockreinsurance"');
    if ($id_module) {
        $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'reinsurance`');
        foreach ($list_fields as $k => $field) {
            $list_fields[$k] = $field['Field'];
        }
            
        if (in_array('id_contactinfos', $list_fields)) {
            if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'reinsurance` CHANGE `id_contactinfos` `id_reinsurance` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT')) {
                $errors[] = Db::getInstance()->getMsgError();
            }
        }
        if (!in_array('id_shop', $list_fields)) {
            if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'reinsurance` ADD `id_shop` INT(10) NOT NULL default "1" AFTER id_reinsurance')) {
                $errors[] = Db::getInstance()->getMsgError();
            }
        }
        if (in_array('filename', $list_fields)) {
            if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'reinsurance` CHANGE `filename` `file_name` VARCHAR(100) NOT NULL')) {
                $errors[] = Db::getInstance()->getMsgError();
            }
        }
        
        $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'reinsurance_lang`');
        
        if (!is_array($list_fields) || $list_fields == false) {
            $return = Db::getInstance()->execute('
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'reinsurance_lang` (
					`id_reinsurance` INT UNSIGNED NOT NULL AUTO_INCREMENT,
					`id_lang` int(10) unsigned NOT NULL ,
					`text` VARCHAR(300) NOT NULL,
					PRIMARY KEY (`id_reinsurance`, `id_lang`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');
            if (!$return) {
                $errors[] = Db::getInstance()->getMsgError();
            }
        }
    }
    
    $id_module = Db::getInstance()->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name="blocktopmenu"');
    if ($id_module) {
        $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'linksmenutop`');
        foreach ($list_fields as $k => $field) {
            $list_fields[$k] = $field['Field'];
        }

        if (in_array('id_link', $list_fields) && !in_array('id_linksmenutop', $list_fields)) {
            if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'linksmenutop` CHANGE `id_link` `id_linksmenutop` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT')) {
                $errors[] = Db::getInstance()->getMsgError();
            }
        }
                
        $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'linksmenutop_lang`');
        foreach ($list_fields as $k => $field) {
            $list_fields[$k] = $field['Field'];
        }

        if (in_array('id_link', $list_fields) && !in_array('id_linksmenutop', $list_fields)) {
            if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'linksmenutop_lang` CHANGE `id_link` `id_linksmenutop` INT(10) UNSIGNED NOT NULL')) {
                $errors[] = Db::getInstance()->getMsgError();
            }
        }
    }

    if (count($errors)) {
        return array('error' => 1, 'msg' => implode(',', $errors)) ;
    }
}
