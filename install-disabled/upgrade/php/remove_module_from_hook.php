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

function remove_module_from_hook($module_name, $hook_name)
{
    $result = true;

    $id_module = Db::getInstance()->getValue('
	SELECT `id_module` FROM `'._DB_PREFIX_.'module`
	WHERE `name` = \''.pSQL($module_name).'\''
    );

    if ((int)$id_module > 0) {
        $id_hook = Db::getInstance()->getValue('
		SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \''.pSQL($hook_name).'\'
		');

        if ((int)$id_hook > 0) {
            $result &= Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'hook_module`
			WHERE `id_module` = '.(int)$id_module.' AND `id_hook` = '.(int)$id_hook);
        }
    }
    
    return $result;
}
