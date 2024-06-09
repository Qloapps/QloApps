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

function update_image_size_in_db()
{
    $logo_name = Db::getInstance()->getValue('SELECT `value` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \'PS_LOGO\'');
    $logo_name = (!empty($logo_name)) ? $logo_name : 'logo.png';

    if (file_exists(realpath(INSTALL_PATH.'/../img').'/'.$logo_name)) {
        list($width, $height, $type, $attr) = getimagesize(realpath(INSTALL_PATH.'/../img').'/'.$logo_name);
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)round($width).'" WHERE `name` = \'SHOP_LOGO_WIDTH\'');
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)round($height).'" WHERE `name` = \'SHOP_LOGO_HEIGHT\'');
    }
    if (file_exists(realpath(INSTALL_PATH.'/../modules/editorial').'/homepage_logo.jpg')) {
        list($width, $height, $type, $attr) = getimagesize(realpath(INSTALL_PATH.'/../modules/editorial').'/homepage_logo.jpg');
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)round($width).'" WHERE `name` = \'EDITORIAL_IMAGE_WIDTH\'');
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)round($height).'" WHERE `name` = \'EDITORIAL_IMAGE_HEIGHT\'');
    }
}
