<?php

include('../config/config.inc.php');
header('content-type: application/x-javascript');

$jquery_folder = dirname(__FILE__).'/jquery/';
$plugins_folder = $jquery_folder.'plugins/';

$plugins = array(
                'ajaxfileupload.js' =>
                    array('new_file' => $plugins_folder.'ajaxfileupload/jquery.ajaxfileupload.js', 'name' => 'ajaxfileupload'),
                'jquery-colorpicker.js' =>
                    array('new_file' => $plugins_folder.'jquery.colorpicker.js', 'name' => 'colorpicker'),
                'jquery.cluetip.js' =>
                    array('new_file' => $plugins_folder.'cluetip/jquery.cluetip.js', 'name' => 'cluetip'),
                'jquery-fieldselection.js' =>
                    array('new_file' => $plugins_folder.'jquery.fieldselection.js', 'name' => 'fieldselection'),
                'jquery.dimensions.js' =>
                    array('new_file' => $plugins_folder.'jquery.dimensions.js', 'name' => 'dimensions'),
                'jquery.idTabs.modified.js' =>
                    array('new_file' => $plugins_folder.'jquery.idTabs.js', 'name' => 'idTabs'),
                'jquery.pngFix.pack.js' =>
                    array('new_file' => $plugins_folder.'jquery.pngFix.js', 'name' => 'pngFix'),
                'thickbox-modified.js' =>
                    array('new_file' => $plugins_folder.'thickbox/jquery.thickbox.js', 'name' => 'thickbox'),
                'excanvas.min.js' =>
                    array('new_file' => $plugins_folder.'jquery.excanvas.js', 'name' => 'excanvas'),
                'jquery-typewatch.pack.js' =>
                    array('new_file' => $plugins_folder.'jquery.typewatch.js', 'name' => 'typewatch'),
                'jquery.easing.1.3.js' =>
                    array('new_file' => $plugins_folder.'jquery.easing.js', 'name' => 'easing'),
                'jquery.jgrowl-1.2.1.min.js' =>
                    array('new_file' => $plugins_folder.'jquery.jgrowl.js', 'name' => 'jgrowl'),
                'jquery.scrollTo-1.4.2-min.js' =>
                    array('new_file' => $plugins_folder.'jquery.scrollTo.js', 'name' => 'scrollTo'),
                'jqminmax-compressed.js' =>
                    array('new_file' => $plugins_folder.'jquery.jqminmax.js', 'name' => 'jqminmax'),
                'jquery.fancybox-1.3.4.js' =>
                    array('new_file' => $plugins_folder.'fancybox/jquery.fancybox.js', 'name' => 'fancybox'),
                'jquery.jqzoom.js' =>
                    array('new_file' => $plugins_folder.'jquery.jqzoom.js', 'name' => 'jqzoom'),
                'jquery.serialScroll-1.2.2-min.js' =>
                    array('new_file' => $plugins_folder.'jquery.serialScroll.js', 'name' => 'serialScroll'),
                'ifxtransfer.js' =>
                    array('new_file' => $plugins_folder.'jquery.ifxtransfer.js', 'name' => 'ifxtransfer'),
                'jquery.autocomplete.js' =>
                    array('new_file' => $plugins_folder.'autocomplete/jquery.autocomplete.js', 'name' => 'autocomplete'),
                'jquery.flot.min.js' =>
                    array('new_file' => $plugins_folder.'jquery.flot.js', 'name' => 'flot'),
                'jquery.tablednd_0_5.js' =>
                    array('new_file' => $plugins_folder.'jquery.tablednd.js', 'name' => 'tablednd'),
                'jquery.hoverIntent.minified.js' =>
                    array('new_file' => $plugins_folder.'jquery.hoverIntent.js', 'name' => 'hoverIntent'),
                'jquery-ui-1.8.10.custom.min.js' =>
                    array('new_file' => $jquery_folder.'ui/jquery.ui.core.min.js', 'name' => ''),
                'jquery.treeview.async.js' =>
                    array('new_file' => $plugins_folder.'treeview-categories/jquery.treeview-categories.async.js', 'name' => 'treeview-categories.async'),
                'jquery.treeview.edit.js' =>
                    array('new_file' => $plugins_folder.'treeview-categories/jquery.treeview-categories.edit.js', 'name' => 'treeview-categories.edit'),
                'jquery.treeview.js' =>
                    array('new_file' => $plugins_folder.'treeview-categories/jquery.treeview-categories.js', 'name' => 'treeview-categories'),
                'jquery.treeview.sortable.js' =>
                    array('new_file' => $plugins_folder.'treeview-categories/jquery.treeview-categories.sortable.js', 'name' => 'treeview-categories.sortable'),
                'tabpane.js' =>
                    array('new_file' => $plugins_folder.'tabpane/jquery.tabpane.js', 'name' => 'tabpane'),
                'admin-themes.js' =>
                    array('new_file' => 'admin/themes.js', 'name' => 'themes'),
                'admin-dashboard.js' =>
                    array('new_file' => 'admin/dashboard.js', 'name' => 'dashboard'),
                'admin-products.js' =>
                    array('new_file' => 'admin/products.js', 'name' => 'products'),
                'adminImport.js' =>
                    array('new_file' => 'admin/import.js', 'name' => 'import'),
                'admin_carrier_wizard.js' =>
                    array('new_file' => 'admin/carrier_wizard.js', 'name' => 'carrier_wizard'),
                'admin_order.js' =>
                    array('new_file' => 'admin/orders.js', 'name' => 'orders'),
                'attributesBack.js' =>
                    array('new_file' => 'admin/attributes.js', 'name' => 'attributes'),
                'admin-scene-cropping.js' =>
                    array('new_file' => 'admin/scenes.js', 'name' => 'scenes'),
                'admin-dnd.js' =>
                    array('new_file' => 'admin/dnd.js', 'name' => 'dnd'),
                'login.js' =>
                    array('new_file' => 'admin/login.js', 'name' => 'login'),
                'notifications.js' =>
                    array('new_file' => 'admin/notifications.js', 'name' => 'login'),
                'price.js' =>
                    array('new_file' => 'admin/price.js', 'name' => 'login'),
                'tinymce.inc.js' =>
                    array('new_file' => 'admin/tinymce.inc.js', 'name' => 'tinymce'),
);


$file = $_GET['file'];
if (!array_key_exists($file, $plugins)) {
    //check if file is a real prestashop native JS
    die('file_not_found');
} elseif ($file == 'jquery-ui-1.8.10.custom.min.js') {
    //jquery-ui cannot be call directly, now to include query UI, use Media::addJqueryUI('component_name');
    $html = '$(document).ready( function () {
	'.(_PS_MODE_DEV_ ? 'if (!$.browser.msie)console.log(\'MODE DEV : This file : "jquery-ui-1.8.10.custom.min.js" cannot be call directly please use Media::addJqueryUI("component_name")  \')' : '').'
	});';
    $html .= file_get_contents($plugins[$file]['new_file']);
} else {
    $html = '$(document).ready( function () {
		'.(_PS_MODE_DEV_ ? 'if (!$.browser.msie)console.log(\'MODE DEV : This file : "'.$file.'" has been moved to this folder '.$plugins[$file]['new_file'].'  To include this plugin use Media::addJqueryPlugin("'.$plugins[$file]['name'].'")\')' : '').'
	});';
    $html .= file_get_contents($plugins[$file]['new_file']);
}
echo $html ;
