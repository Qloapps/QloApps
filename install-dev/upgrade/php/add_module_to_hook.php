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

function add_module_to_hook($module_name, $hook_name)
{
    $res = true;

    $id_module = Db::getInstance()->getValue('
	SELECT `id_module` FROM `'._DB_PREFIX_.'module`
	WHERE `name` = "'.$module_name.'"'
    );

    if ((int)$id_module > 0) {
        $id_hook = Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = "'.$hook_name.'"');
        if (!$id_hook) {
            if (!Db::getInstance()->execute('
			INSERT IGNORE INTO `'._DB_PREFIX_.'hook` (`name`, `title`)
			VALUES ("'.pSQL($hook_name).'", "'.pSQL($hook_name).'")')) {
                $res = false;
            } else {
                $id_hook = Db::getInstance()->Insert_ID();
            }
        }

        if ((int)$id_hook > 0) {
            if (!Db::getInstance()->execute('
			INSERT IGNORE INTO `'._DB_PREFIX_.'hook_module` (`id_module`, `id_hook`, `position`)
			VALUES (
			'.(int)$id_module.',
			'.(int)$id_hook.',
			(SELECT IFNULL(
				(SELECT max_position from (SELECT MAX(position)+1 as max_position  FROM `'._DB_PREFIX_.'hook_module`  WHERE `id_hook` = '.(int)$id_hook.') AS max_position), 1))
			)')) {
                $res = false;
            }
        }
    }
    return $res;
}
