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

function update_genders_images()
{
    if (@file_exists(_PS_ROOT_DIR_.'/img/genders/Mr.jpg')) {
        @rename(_PS_ROOT_DIR_.'/img/genders/Mr.jpg', _PS_ROOT_DIR_.'/img/genders/1.jpg');
    }
    if (@file_exists(_PS_ROOT_DIR_.'/img/genders/Ms.jpg')) {
        @rename(_PS_ROOT_DIR_.'/img/genders/Ms.jpg', _PS_ROOT_DIR_.'/img/genders/2.jpg');
    }
    if (@file_exists(_PS_ROOT_DIR_.'/img/genders/Miss.jpg')) {
        @rename(_PS_ROOT_DIR_.'/img/genders/Miss.jpg', _PS_ROOT_DIR_.'/img/genders/3.jpg');
    }
    if (@file_exists(_PS_ROOT_DIR_.'/img/genders/unknown.jpg')) {
        @rename(_PS_ROOT_DIR_.'/img/genders/unknown.jpg', _PS_ROOT_DIR_.'/img/genders/Unknown.jpg');
    }
}
