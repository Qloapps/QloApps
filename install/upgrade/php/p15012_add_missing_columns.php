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

function p15012_add_missing_columns()
{
    $errors = array();
    $db = Db::getInstance();
    $q_list = array();
    // columns must exists
    $q_list['carrier']['id_reference']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'carrier`
		CHANGE `id_reference` `id_reference` int(10) unsigned NOT NULL';
    $q_list['carrier']['id_reference']['add'] = 'ALTER TABLE `'._DB_PREFIX_.'carrier`
		ADD `id_reference` int(10) unsigned NOT NULL';
    $q_list['carrier']['id_tax_rules_group']['add'] = 'ALTER TABLE `'._DB_PREFIX_.'carrier`
		ADD `id_tax_rules_group` INT(10) unsigned DEFAULT "0" AFTER `id_reference`';

    $q_list['cart']['order_reference']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'cart`
		DROP COLUMN `order_reference`';

    $q_list['cart']['id_shop_group']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'cart`
		CHANGE `id_shop_group` `id_shop_group` int(11) unsigned NOT NULL DEFAULT "1"';
    $q_list['cart']['id_shop_group']['add'] = 'ALTER TABLE `'._DB_PREFIX_.'cart`
		ADD `id_shop_group` int(11) unsigned NOT NULL DEFAULT "1"';

    $q_list['cart_product']['id_shop']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'cart_product`
		CHANGE `id_shop` `id_shop` int(10) unsigned NOT NULL DEFAULT "1" AFTER `id_address_delivery`';
    $q_list['cart_product']['id_shop']['add'] = 'ALTER TABLE `'._DB_PREFIX_.'cart_product`
		ADD `id_shop` int(10) unsigned NOT NULL DEFAULT "1" AFTER `id_address_delivery`';
    $q_list['cart_product']['id_product_attribute']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'cart_product`
		CHANGE `id_product_attribute` `id_product_attribute` int(10) unsigned DEFAULT NULL AFTER `id_shop`';
    $q_list['cart_product']['id_product_attribute']['add'] = 'ALTER TABLE `'._DB_PREFIX_.'cart_product`
		ADD `id_product_attribute` int(10) unsigned DEFAULT NULL AFTER `id_shop`';
    
    $q_list['cart_rule_product_rule']['quantity']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'cart_rule_product_rule`
		DROP COLUMN `quantity`';

    $q_list['connections']['id_shop_group']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'connections`
		CHANGE id_shop_group `id_shop_group` int(11) unsigned NOT NULL DEFAULT "1" AFTER id_connections';
    
    $q_list['country']['display_tax_label']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'country`
		CHANGE display_tax_label `display_tax_label` tinyint(1) NOT NULL AFTER zip_code_format';

    $q_list['currency']['active']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'currency`
		CHANGE active active tinyint(1) unsigned NOT NULL DEFAULT "1"';

    $q_list['customer']['id_shop_group']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'customer`
		CHANGE id_shop_group id_shop_group int(11) unsigned NOT NULL DEFAULT "1"';

    $q_list['employee']['bo_uimode']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'employee`
		DROP `bo_uimode`';

    $q_list['meta_lang']['url_rewrite']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'meta_lang`
		CHANGE url_rewrite url_rewrite varchar(254) NOT NULL';

    $q_list['order_detail']['id_warehouse']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `id_warehouse` id_warehouse  int(10) unsigned DEFAULT "0"';
    $q_list['order_detail']['reduction_amount_tax_incl']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `reduction_amount_tax_incl` reduction_amount_tax_incl DEC(20,6) NOT NULL DEFAULT "0.000000"';
    $q_list['order_detail']['reduction_amount_tax_excl']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `reduction_amount_tax_excl` reduction_amount_tax_excl DEC(20,6) NOT NULL DEFAULT "0.000000"';
    
    $q_list['order_detail']['ecotax_tax_rate']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `ecotax_tax_rate` ecotax_tax_rate DEC(5,3) NOT NULL DEFAULT "0.000"';

    $q_list['order_detail']['total_price_tax_incl']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `total_price_tax_incl` total_price_tax_incl DEC(20,6) NOT NULL DEFAULT "0.000000"';
    $q_list['order_detail']['total_price_tax_excl']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `total_price_tax_excl` total_price_tax_excl DEC(20,6) NOT NULL DEFAULT "0.000000"';
    $q_list['order_detail']['unit_price_tax_incl']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `unit_price_tax_incl` unit_price_tax_incl DEC(20,6) NOT NULL DEFAULT "0.000000"';
    $q_list['order_detail']['unit_price_tax_excl']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `unit_price_tax_excl` unit_price_tax_excl DEC(20,6) NOT NULL DEFAULT "0.000000"';
    $q_list['order_detail']['total_shipping_price_tax_incl']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `total_shipping_price_tax_incl` total_shipping_price_tax_incl DEC(20,6) NOT NULL DEFAULT "0.000000"';
    $q_list['order_detail']['total_shipping_price_tax_excl']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `total_shipping_price_tax_excl` total_shipping_price_tax_excl DEC(20,6) NOT NULL DEFAULT "0.000000"';
    $q_list['order_detail']['purchase_supplier_price']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `purchase_supplier_price` purchase_supplier_price DEC(20,6) NOT NULL DEFAULT "0.000000"';
    $q_list['order_detail']['original_product_price']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail`
		CHANGE `original_product_price` original_product_price DEC(20,6) NOT NULL DEFAULT "0.000000"';


    $q_list['order_detail_tax']['unit_amount']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail_tax`
		CHANGE `unit_amount` unit_amount DEC(10,6) NOT NULL DEFAULT "0.000000"';
    $q_list['order_detail_tax']['total_amount']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_detail_tax`
		CHANGE `total_amount` total_amount DEC(10,6) NOT NULL DEFAULT "0.000000"';

    $q_list['order_invoice']['note']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_invoice`
		CHANGE `note` note text';

    $q_list['order_payment']['id_order_invoice']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'order_payment`
		CHANGE `id_order_invoice` id_order_invoice int(10) unsigned NOT NULL DEFAULT 0';

    $q_list['orders']['reference']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'orders`
		CHANGE `reference` reference varchar(9) DEFAULT NULL';
    $q_list['orders']['id_shop_group']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'orders`
		CHANGE `id_shop_group` id_shop_group int(11) unsigned NOT NULL DEFAULT "1"';

    $q_list['product']['unity']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'product`
		CHANGE `unity` unity varchar(255) DEFAULT NULL';

    $q_list['product']['width']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'product`
		CHANGE `width` `width` float NOT NULL DEFAULT "0"';

    $q_list['product']['height']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'product`
		CHANGE `height` `height` float NOT NULL DEFAULT "0"';

    $q_list['product']['depth']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'product`
		CHANGE `depth` `depth` float NOT NULL DEFAULT "0"';

    $q_list['product']['minimal_quantity']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'product`
		CHANGE `minimal_quantity` `minimal_quantity` int(10) unsigned NOT NULL DEFAULT "1"';

    $q_list['product_attribute']['ecotax']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'product_attribute`
		CHANGE `ecotax` ecotax decimal(17,6) NOT NULL DEFAULT "0.000000"';

    $q_list['stock_mvt_reason']['id_stock_mvt_reason']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'stock_mvt_reason`
		CHANGE `id_stock_mvt_reason` id_stock_mvt_reason int(11) unsigned NOT NULL';

    $q_list['stock_mvt_reason_lang']['id_stock_mvt_reason']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'stock_mvt_reason_lang`
		CHANGE `id_stock_mvt_reason` id_stock_mvt_reason int(11) unsigned NOT NULL';

    $q_list['stock_mvt_reason_lang']['id_lang']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'stock_mvt_reason_lang`
		CHANGE `id_lang` id_lang int(11) unsigned NOT NULL';

    $q_list['supply_order']['reference']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'supply_order`
		CHANGE `reference` reference varchar(64) NOT NULL';

    $q_list['tax']['deleted']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'tax`
		CHANGE `deleted` deleted tinyint(1) unsigned NOT NULL DEFAULT "0"';

    $q_list['carrier']['need_range']['mod'] = 'ALTER TABLE `'._DB_PREFIX_.'carrier`
		CHANGE `need_range` need_range tinyint(1) unsigned NOT NULL DEFAULT "0" AFTER shipping_external';

    foreach ($q_list as $table => $cols) {
        if (empty($table)) {
            continue;
        }
        $list_fields = $db->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.$table.'`');
        if (is_array($list_fields)) {
            foreach ($list_fields as $k => $field) {
                $list_fields[$k] = $field['Field'];
            }
        }
        if (is_array($cols)) {
            foreach ($cols as $col => $q) {
                // do only if column exists
                if (is_array($list_fields) && in_array($col, $list_fields)) {
                    $do = 'mod';
                } else {
                    $do = 'add';
                }
    
                if (!empty($q[$do])) {
                    if (!$db->execute($q[$do])) {
                        $errors[] = '<subquery><query>'.$q[$do].'</query><error>'.$db->getMsgError().'</error></subquery>';
                    }
                }
            }
        }
    }

    if (sizeof($errors) > 0) {
        $msg = implode("\r", $errors);
        return array('error' => 1, 'msg' => $msg);
    } else {
        return true;
    }
}
