<?php
/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define('_PS_DO_NOT_LOAD_CONFIGURATION_', true);
if (Tools::getValue('bo')) {
    if (!is_dir(_PS_ROOT_DIR_.'/admin/')) {
        exit;
    }
    define('_PS_ADMIN_DIR_', _PS_ROOT_DIR_.'/admin/');
    $directory = _PS_ADMIN_DIR_.'themes/default/';
} else {
    $directory = _PS_THEME_DIR_;
}

require_once(_PS_ROOT_DIR_.'/config/smarty.config.inc.php');

$smarty->setTemplateDir($directory);
ob_start();
$smarty->compileAllTemplates('.tpl', false);
if (ob_get_level() && ob_get_length() > 0) {
    ob_end_clean();
}
