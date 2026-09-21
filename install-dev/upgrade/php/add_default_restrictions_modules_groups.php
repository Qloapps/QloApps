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

function add_default_restrictions_modules_groups()
{
    $res = true;
    // Table module_group had another use in previous versions, we need to clean it up.
    $res &= Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'module_group`');

    $groups = Db::getInstance()->executeS('
		SELECT `id_group`
		FROM `'._DB_PREFIX_.'group`');
    $modules = Db::getInstance()->executeS('
		SELECT m.*
		FROM `'._DB_PREFIX_.'module` m');
    $shops = Db::getInstance()->executeS('
		SELECT `id_shop`
		FROM `'._DB_PREFIX_.'shop`');
    foreach ($groups as $group) {
        if (!is_array($modules) || !is_array($shops)) {
            return false;
        } else {
            $sql = 'INSERT INTO `'._DB_PREFIX_.'module_group` (`id_module`, `id_shop`, `id_group`) VALUES ';
            foreach ($modules as $mod) {
                foreach ($shops as $s) {
                    $sql .= '("'.(int)$mod['id_module'].'", "'.(int)$s.'", "'.(int)$group['id_group'].'"),';
                }
            }
                // removing last comma to avoid SQL error
                $sql = substr($sql, 0, strlen($sql) - 1);
            $res &= Db::getInstance()->execute($sql);
        }
    }
    return $res;
}
