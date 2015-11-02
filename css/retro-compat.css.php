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

header('content-type: text/css');
$css_folder = dirname(__FILE__).'/../js/jquery/';

$css_files = array(
                'datepicker.css' =>
                    array('new_file' => $css_folder.'ui/themes/base/jquery.ui.datepicker.css'),
                'fileuploader.css' =>
                    array('new_file' => $css_folder.'plugins/ajaxfileupload/jquery.ajaxfileupload.css'),
                'jquery.autocomplete.css' =>
                    array('new_file' => $css_folder.'plugins/autocomplete/jquery.autocomplete.css'),
                'jquery.cluetip.css' =>
                    array('new_file' => $css_folder.'plugins/cluetip/jquery.cluetip.css'),
                'jquery.fancybox-1.3.4.css' =>
                    array('new_file' => $css_folder.'plugins/fancybox/jquery.fancybox.css'),
                'jquery.jgrowl.css'=>
                    array('new_file' => $css_folder.'plugins/jgrowl/jquery.jgrowl.css'),
                'jquery.treeview.css' =>
                    array('new_file' => $css_folder.'plugins/treeview-categories/jquery.treeview-categories.css'),
                'jqzoom.css' =>
                    array('new_file' => $css_folder.'plugins/jqzoom/jquery.jqzoom.css'),
                'tabpane.css' =>
                    array('new_file' => $css_folder.'plugins/tabpane/jquery.tabpane.css'),
                'thickbox.css' =>
                    array('new_file' => $css_folder.'plugins/thickbox/jquery.thickbox.css'),
                'jquery.fancybox.css' =>
                    array('new_file' => $css_folder.'plugins/fancybox/jquery.fancybox.css'),
                );
                
                
                

$file = $_GET['file'];

if (!array_key_exists($file, $css_files)) { //check if file is a real prestashop native CSS
    die('file_not_found');
} else {
    $html = file_get_contents($css_files[$file]['new_file']);
}

if ($file == 'datepicker.css') {
    $html = file_get_contents($css_folder.'ui/themes/base/jquery.ui.theme.css');
    $html .= file_get_contents($css_folder.'ui/themes/base/jquery.ui.datepicker.css');
    $html = str_replace('url(images', 'url(../ui/themes/base/images', $html);
}
echo $html ;
