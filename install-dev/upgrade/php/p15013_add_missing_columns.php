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

function p15013_add_missing_columns()
{
    $errors = array();
    $db = Db::getInstance();
    $id_module = $db->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name="statssearch"');

    if ($id_module) {
        $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'statssearch`');
        foreach ($list_fields as $k => $field) {
            $list_fields[$k] = $field['Field'];
        }

        if (!in_array('id_group_shop', $list_fields)) {
            if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'statssearch`
				ADD `id_group_shop` INT(10) NOT NULL default "1" AFTER id_statssearch')) {
                $errors[] = $db->getMsgError();
            }
        }
        if (!in_array('id_shop', $list_fields)) {
            if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'statssearch`
				ADD `id_shop` INT(10) NOT NULL default "1" AFTER id_statssearch')) {
                $errors[] = $db->getMsgError();
            }
        }
    }
    if (count($errors)) {
        return array('error' => 1, 'msg' => implode(',', $errors)) ;
    }
}
