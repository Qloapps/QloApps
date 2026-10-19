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

/** remove the uncompatible module gridextjs (1.4.0.8 upgrade)
 */
function gridextjs_deprecated()
{
    // if exists, use _PS_MODULE_DIR_ or _PS_ROOT_DIR_
    // instead of guessing the modules dir
    if (defined('_PS_MODULE_DIR_')) {
        $gridextjs_path = _PS_MODULE_DIR_ . 'gridextjs';
    } elseif (defined('_PS_ROOT_DIR_')) {
        $gridextjs_path = _PS_ROOT_DIR_ . '/modules/gridextjs';
    } else {
        $gridextjs_path = dirname(__FILE__).'/../../../modules/gridextjs';
    }

    if (file_exists($gridextjs_path)) {
        return rename($gridextjs_path, str_replace('gridextjs', 'gridextjs.deprecated', $gridextjs_path));
    }

    return true;
}
