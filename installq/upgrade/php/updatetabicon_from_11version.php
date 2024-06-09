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

function updatetabicon_from_11version()
{
    global $oldversion;
    if (version_compare($oldversion, '1.5.0.0', '<')) {
        $rows = Db::getInstance()->executeS('SELECT `id_tab`,`class_name` FROM '._DB_PREFIX_.'tab');
        if (sizeof($rows)) {
            $img_dir = scandir(_PS_ROOT_DIR_.'/img/t/');
            $result = true;
            foreach ($rows as $tab) {
                if (file_exists(_PS_ROOT_DIR_.'/img/t/'.$tab['id_tab'].'.gif')
                    and !file_exists(_PS_ROOT_DIR_.'/img/t/'.$tab['class_name'].'.gif')) {
                    $result &= rename(_PS_ROOT_DIR_.'/img/t/'.$tab['id_tab'].'.gif', _PS_ROOT_DIR_.'/img/t/'.$tab['class_name'].'.gif');
                }
            }
        }
    }
    return true;
}
