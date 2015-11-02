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

function update_module_pagesnotfound()
{
    $id_pagesnotfound = (int)Db::getInstance()->getValue('SELECT id_module FROM  `'._DB_PREFIX_.'module` WHERE name = \'pagesnotfound\'');
    if ($id_pagesnotfound) {
        $id_hook = (int)Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'frontCanonicalRedirect\'');
        if ($id_hook) {
            $position = (int)Db::getInstance()->getValue('SELECT IFNULL(MAX(`position`), 0) + 1 FROM `'._DB_PREFIX_.'hook_module` WHERE `id_hook` = '.(int)$id_hook);
            if ($position) {
                return Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'hook_module` (`id_hook`, `id_module`, `position`) VALUES ('.(int)$id_hook.', '.(int)$id_pagesnotfound.', '.(int)$position.')');
            }
        }
    }
    return true;
}
