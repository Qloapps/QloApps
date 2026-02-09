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
$con = new AdminController();
$tab = new Tab((int)Tab::getIdFromClassName(Tools::getValue('controller')));
$con->id = $tab->id;
$con->init();
$con->initToolbar();
$con->initPageHeaderToolbar();
$con->setMedia();
$con->initHeader();
$con->initFooter();

$title = array($tab->getFieldByLang('name'));

Context::getContext()->smarty->assign(array(
    'navigationPipe', Configuration::get('PS_NAVIGATION_PIPE'),
    'meta_title' => implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $title),
    'display_header' => true,
    'display_header_javascript' => true,
    'display_footer' => true,
));
$dir = Context::getContext()->smarty->getTemplateDir(0).'controllers'.DIRECTORY_SEPARATOR.trim($con->override_folder, '\\/').DIRECTORY_SEPARATOR;
$header_tpl = file_exists($dir.'header.tpl') ? $dir.'header.tpl' : 'header.tpl';
$tool_tpl = file_exists($dir.'page_header_toolbar.tpl') ? $dir.'page_header_toolbar.tpl' : 'page_header_toolbar.tpl';
Context::getContext()->smarty->assign(array(
    'show_page_header_toolbar' => true,
    'title' => implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $title),
    'toolbar_btn' => array()
));
echo Context::getContext()->smarty->fetch($header_tpl);
echo Context::getContext()->smarty->fetch($tool_tpl);
