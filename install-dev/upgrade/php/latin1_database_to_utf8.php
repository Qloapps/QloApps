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

if (!defined('_PS_MAGIC_QUOTES_GPC_')) {
    define('_PS_MAGIC_QUOTES_GPC_', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
}
if (!defined('_PS_MYSQL_REAL_ESCAPE_STRING_')) {
    define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));
}

function latin1_database_to_utf8()
{
    global $requests, $warningExist;

    $tables = array(
                    array('name' => 'address', 'id' => 'id_address', 'fields' => array('alias', 'company', 'name', 'surname', 'address1', 'address2', 'postcode', 'city', 'other', 'phone', 'phone_mobile')),
                    array('name' => 'alias', 'id' => 'id_alias', 'fields' => array('alias', 'search')),
                    array('name' => 'attribute_group_lang', 'id' => 'id_attribute_group', 'lang' => true, 'fields' => array('name', 'public_name')),
                    array('name' => 'attribute_lang', 'id' => 'id_attribute', 'lang' => true, 'fields' => array('name')),
                    array('name' => 'carrier', 'id' => 'id_carrier', 'fields' => array('name', 'url')),
                    array('name' => 'carrier_lang', 'id' => 'id_carrier', 'lang' => true, 'fields' => array('delay')),
                    array('name' => 'cart', 'id' => 'id_cart', 'fields' => array('gift_message')),
                    array('name' => 'category_lang', 'id' => 'id_category', 'lang' => true, 'fields' => array('name', 'description', 'link_rewrite', 'meta_title', 'meta_keywords', 'meta_description')),
                    array('name' => 'configuration', 'id' => 'id_configuration', 'fields' => array('name', 'value')),
                    array('name' => 'configuration_lang', 'id' => 'id_configuration', 'lang' => true, 'fields' => array('value')),
                    array('name' => 'contact', 'id' => 'id_contact', 'fields' => array('email')),
                    array('name' => 'contact_lang', 'id' => 'id_contact', 'lang' => true, 'fields' => array('name', 'description')),
                    array('name' => 'country', 'id' => 'id_country', 'fields' => array('iso_code')),
                    array('name' => 'country_lang', 'id' => 'id_country', 'lang' => true, 'fields' => array('name')),
                    array('name' => 'currency', 'id' => 'id_currency', 'fields' => array('name', 'iso_code', 'sign')),
                    array('name' => 'customer', 'id' => 'id_customer', 'fields' => array('email', 'passwd', 'name', 'surname')),
                    array('name' => 'discount', 'id' => 'id_discount', 'fields' => array('name')),
                    array('name' => 'discount_lang', 'id' => 'id_discount', 'lang' => true, 'fields' => array('description')),
                    array('name' => 'discount_type_lang', 'id' => 'id_discount_type', 'lang' => true, 'fields' => array('name')),
                    array('name' => 'employee', 'id' => 'id_employee', 'fields' => array('name', 'surname', 'email', 'passwd')),
                    array('name' => 'feature_lang', 'id' => 'id_feature', 'lang' => true, 'fields' => array('name')),
                    array('name' => 'feature_value_lang', 'id' => 'id_feature_value', 'lang' => true, 'fields' => array('value')),
                    array('name' => 'hook', 'id' => 'id_hook', 'fields' => array('name', 'title', 'description')),
                    array('name' => 'hook_module_exceptions', 'id' => 'id_hook_module_exceptions', 'fields' => array('file_name')),
                    array('name' => 'image_lang', 'id' => 'id_image', 'lang' => true, 'fields' => array('legend')),
                    array('name' => 'image_type', 'id' => 'id_image_type', 'fields' => array('name')),
                    array('name' => 'lang', 'id' => 'id_lang', 'fields' => array('name', 'iso_code')),
                    array('name' => 'manufacturer', 'id' => 'id_manufacturer', 'fields' => array('name')),
                    array('name' => 'message', 'id' => 'id_message', 'fields' => array('message')),
                    array('name' => 'module', 'id' => 'id_module', 'fields' => array('name')),
                    array('name' => 'orders', 'id' => 'id_order', 'fields' => array('payment', 'module', 'gift_message', 'shipping_number')),
                    array('name' => 'order_detail', 'id' => 'id_order_detail', 'fields' => array('product_name', 'product_reference', 'tax_name', 'download_hash')),
                    array('name' => 'order_discount', 'id' => 'id_order_discount', 'fields' => array('name')),
                    array('name' => 'order_state', 'id' => 'id_order_state', 'fields' => array('color')),
                    array('name' => 'order_state_lang', 'id' => 'id_order_state', 'lang' => true, 'fields' => array('name', 'template')),
                    array('name' => 'product', 'id' => 'id_product', 'fields' => array('ean13', 'reference')),
                    array('name' => 'product_attribute', 'id' => 'id_product_attribute', 'fields' => array('reference', 'ean13')),
                    array('name' => 'product_download', 'id' => 'id_product_download', 'fields' => array('display_filename', 'physically_filename')),
                    array('name' => 'product_lang', 'id' => 'id_product', 'lang' => true, 'fields' => array('description', 'description_short', 'link_rewrite', 'meta_description', 'meta_keywords', 'meta_title', 'name', 'availability')),
                    array('name' => 'profile_lang', 'id' => 'id_profile', 'lang' => true, 'fields' => array('name')),
                    array('name' => 'quick_access', 'id' => 'id_quick_access', 'fields' => array('link')),
                    array('name' => 'quick_access_lang', 'id' => 'id_quick_access', 'lang' => true, 'fields' => array('name')),
                    array('name' => 'supplier', 'id' => 'id_supplier', 'fields' => array('name')),
                    array('name' => 'tab', 'id' => 'id_tab', 'fields' => array('class_name')),
                    array('name' => 'tab_lang', 'id' => 'id_tab', 'lang' => true, 'fields' => array('name')),
                    array('name' => 'tag', 'id' => 'id_tag', 'fields' => array('name')),
                    array('name' => 'tax_lang', 'id' => 'id_tax', 'lang' => true, 'fields' => array('name')),
                    array('name' => 'zone', 'id' => 'id_zone', 'fields' => array('name'))
                );

    foreach ($tables as $table) {
        /* Latin1 datas' selection */
        if (!Db::getInstance()->execute('SET NAMES latin1')) {
            echo 'Cannot change the sql encoding to latin1!';
        }
        $query = 'SELECT `'.$table['id'].'`';
        foreach ($table['fields'] as $field) {
            $query .= ', `'.$field.'`';
        }
        if (isset($table['lang']) and $table['lang']) {
            $query .= ', `id_lang`';
        }
        $query .= ' FROM `'._DB_PREFIX_.$table['name'].'`';
        $latin1Datas = Db::getInstance()->executeS($query);
        if ($latin1Datas === false) {
            $warningExist = true;
            $requests .= '
				<request result="fail">
					<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
					<sqlMsgError><![CDATA['.htmlentities(Db::getInstance()->getMsgError()).']]></sqlMsgError>
					<sqlNumberError><![CDATA['.htmlentities(Db::getInstance()->getNumberError()).']]></sqlNumberError>
				</request>'."\n";
        }

        if (Db::getInstance()->NumRows()) {
            /* Utf-8 datas' restitution */
            if (!Db::getInstance()->execute('SET NAMES utf8')) {
                echo 'Cannot change the sql encoding to utf8!';
            }
            foreach ($latin1Datas as $latin1Data) {
                $query = 'UPDATE `'._DB_PREFIX_.$table['name'].'` SET';
                foreach ($table['fields'] as $field) {
                    $query .= ' `'.$field.'` = \''.pSQL($latin1Data[$field]).'\',';
                }
                $query = rtrim($query, ',');
                $query .= ' WHERE `'.$table['id'].'` = '.(int)($latin1Data[$table['id']]);
                if (isset($table['lang']) and $table['lang']) {
                    $query .= ' AND `id_lang` = '.(int)($latin1Data['id_lang']);
                }
                if (!Db::getInstance()->execute($query)) {
                    $warningExist = true;
                    $requests .= '
						<request result="fail">
							<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
							<sqlMsgError><![CDATA['.htmlentities(Db::getInstance()->getMsgError()).']]></sqlMsgError>
							<sqlNumberError><![CDATA['.htmlentities(Db::getInstance()->getNumberError()).']]></sqlNumberError>
						</request>'."\n";
                }
            }
        }
    }
}
