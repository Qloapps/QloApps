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

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}

require(_PS_ADMIN_DIR_.'/../config/config.inc.php');
require(_PS_ADMIN_DIR_.'/functions.php');

// For retrocompatibility with "tab" parameter
if (!isset($_GET['controller']) && isset($_GET['tab'])) {
    $_GET['controller'] = strtolower($_GET['tab']);
}
if (!isset($_POST['controller']) && isset($_POST['tab'])) {
    $_POST['controller'] = strtolower($_POST['tab']);
}
if (!isset($_REQUEST['controller']) && isset($_REQUEST['tab'])) {
    $_REQUEST['controller'] = strtolower($_REQUEST['tab']);
}
// Retrocompatibility with 1.4
$_REQUEST['ajaxMode'] = $_POST['ajaxMode'] = $_GET['ajaxMode'] = $_REQUEST['ajax'] = $_POST['ajax'] = $_GET['ajax'] = 1;

Dispatcher::getInstance()->dispatch();
