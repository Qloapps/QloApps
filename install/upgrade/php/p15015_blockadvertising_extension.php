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

function p15015_blockadvertising_extension()
{
    if (!defined('_PS_ROOT_DIR_')) {
        define('_PS_ROOT_DIR_', realpath(INSTALL_PATH.'/../'));
    }

    // Try to update with the extension of the image that exists in the module directory
    if (@file_exists(_PS_ROOT_DIR_.'/modules/blockadvertising')) {
        foreach (@scandir(_PS_ROOT_DIR_.'/modules/blockadvertising') as $file) {
            if (in_array($file, array('advertising.jpg', 'advertising.gif', 'advertising.png'))) {
                $exist = Db::getInstance()->getValue('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \'BLOCKADVERT_IMG_EXT\'');
                if ($exist) {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.pSQL(substr($file, strrpos($file, '.') + 1)).'" WHERE `name` = \'BLOCKADVERT_IMG_EXT\'');
                } else {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (name, value) VALUES ("BLOCKADVERT_IMG_EXT", "'.pSQL(substr($file, strrpos($file, '.') + 1)).'"');
                }
            }
        }
    }
    return true;
}
