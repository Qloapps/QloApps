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

function migrate_orders()
{
    $array_errors = array();
    $res = true;
    if (!defined('PS_TAX_EXC')) {
        define('PS_TAX_EXC', 1);
    }

    if (!defined('PS_TAX_INC')) {
        define('PS_TAX_INC', 0);
    }

    $col_order_detail_old = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'order_detail`');
    foreach ($col_order_detail_old as $k => $field) {
        if ($field['Field'] != 'id_order_invoice') {
            $col_order_detail[$k] = $field['Field'];
        }
    }

    if (!$col_order_detail_old) {
        return array('error' => 1, 'msg' => 'unable to get fields list from order_detail table');
    }

    $insert_order_detail = 'INSERT INTO `'._DB_PREFIX_.'order_detail_2` (`'.implode('`, `', $col_order_detail).'`) VALUES ';

    $col_orders = array();
    $col_orders_old = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'orders`');

    if (!$col_orders_old) {
        return array('error' => 1, 'msg' => 'unable to get fields list from orders table');
    }

    foreach ($col_orders_old as $k => $field) {
        $col_orders[$k] = $field['Field'];
    }

    $insert_order = 'INSERT INTO `'._DB_PREFIX_.'orders_2` (`'.implode('`, `', $col_orders).'`) VALUES ';

    // create temporary tables
    $res = mo_duplicateTables();
    if (!$res) {
        $array_errors[] = 'unable to duplicate tables orders and order_detail';
    }

    // this was done like that previously
    $wrapping_tax_rate = 1 + ((float)Db::getInstance()->getValue('SELECT value
		FROM `'._DB_PREFIX_.'configuration`
		WHERE name = "PS_GIFT_WRAPPING_TAX"') / 100);

    $step = 3000;
    $count_orders = Db::getInstance()->getValue('SELECT count(id_order) FROM '._DB_PREFIX_.'orders');
    $nb_loop = $start = 0;
    if ($count_orders > 0) {
        $nb_loop = ceil($count_orders / $step);
    }
    for ($i = 0; $i < $nb_loop; $i++) {
        $order_res = Db::getInstance()->query('SELECT * FROM `'._DB_PREFIX_.'orders` LIMIT '.(int)$start.', '.(int)$step);
        $start = intval(($i+1) * $step);
        $cpt = 0;
        $flush_limit = 200;
        while ($order = Db::getInstance()->nextRow($order_res)) {
            $sum_total_products = 0;
            $sum_tax_amount = 0;
            $default_group_id = mo_getCustomerDefaultGroup((int)$order['id_customer']);
            $price_display_method = mo_getPriceDisplayMethod((int)$default_group_id);
            $order_details_list = Db::getInstance()->query('
			SELECT od.*
			FROM `'._DB_PREFIX_.'order_detail` od
			WHERE od.`id_order` = '.(int)$order['id_order']);

            while ($order_details = Db::getInstance()->nextRow($order_details_list)) {
                // we don't want to erase order_details data in order to create the insert query
                $products = mo_setProductPrices($order_details, $price_display_method);
                $tax_rate = 1 + ((float)$products['tax_rate'] / 100);
                $reduction_amount_tax_incl = (float)$products['reduction_amount'];
    
                // cart::getTaxesAverageUsed equivalent
                $sum_total_products += $products['total_price'];
                
                $sum_tax_amount += $products['total_wt'] - $products['total_price'];
    
                $order_details['reduction_amount_tax_incl'] = $reduction_amount_tax_incl;
                $order_details['reduction_amount_tax_excl'] = (float)mo_ps_round($reduction_amount_tax_incl / $tax_rate);
                $order_details['total_price_tax_incl'] = (float)$products['total_wt'];
                $order_details['total_price_tax_excl'] = (float)$products['total_price'];
                $order_details['unit_price_tax_incl'] = (float)$products['product_price_wt'];
                $order_details['unit_price_tax_excl'] = (float)$products['product_price'];

                foreach (array_keys($order_details) as $k) {
                    if (!in_array($k, $col_order_detail)) {
                        unset($order_details[$k]);
                    } else {
                        if (in_array($order_details[$k], array('product_price', 'reduction_percent', 'reduction_amount', 'group_reduction', 'product_quantity_discount', 'tax_rate', 'ecotax', 'ecotax_tax_rate'))) {
                            $order_details[$k] = (float)$order_details[$k];
                        } else {
                            $order_details[$k] = Db::getInstance()->escape($order_details[$k]);
                        }
                    }
                }
                if (count($order_details)) {
                    $values_order_detail[] = '(\''.implode('\', \'', $order_details).'\')';
                }
                unset($order_details);
            }

            $average_tax_used = 1;
            if ($sum_total_products > 0) {
                $average_tax_used +=  $sum_tax_amount / $sum_total_products;
            }
            $average_tax_used = round($average_tax_used, 4);
            $carrier_tax_rate = 1;
            if (isset($order['carrier_tax_rate'])) {
                $carrier_tax_rate + ((float)$order['carrier_tax_rate'] / 100);
            }
    
            $total_discount_tax_excl = $order['total_discounts'] / $average_tax_used;
            $order['total_discounts_tax_incl'] = (float)$order['total_discounts'];
            $order['total_discounts_tax_excl'] = (float)$total_discount_tax_excl;
    
            $order['total_shipping_tax_incl'] = (float)$order['total_shipping'];
            $order['total_shipping_tax_excl'] = (float)($order['total_shipping'] / $carrier_tax_rate);
            $shipping_taxes = $order['total_shipping_tax_incl'] - $order['total_shipping_tax_excl'];
    
            $order['total_wrapping_tax_incl'] = (float)$order['total_wrapping'];
            $order['total_wrapping_tax_excl'] = ((float)$order['total_wrapping'] / $wrapping_tax_rate);
            $wrapping_taxes = $order['total_wrapping_tax_incl'] - $order['total_wrapping_tax_excl'];
    
            $product_taxes = $order['total_products_wt'] - $order['total_products'];
            $order['total_paid_tax_incl'] = (float)$order['total_paid'];
            $order['total_paid_tax_excl'] = (float)$order['total_paid'] - $shipping_taxes - $wrapping_taxes - $product_taxes;
            // protect text and varchar fields
            $order['gift_message'] = Db::getInstance()->escape($order['gift_message']);
            $order['payment'] = Db::getInstance()->escape($order['payment']);
            $order['module'] = Db::getInstance()->escape($order['module']);
    
            $values_order[] = '(\''.implode('\', \'', $order).'\')';
    
            unset($order);
            $cpt++;
            
            // limit to $cpt
            if ($cpt >= $flush_limit) {
                $cpt = 0;
                if (isset($values_order_detail) && count($values_order_detail) && !Db::getInstance()->execute($insert_order_detail. implode(',', $values_order_detail))) {
                    $res = false;
                    $array_errors[] = '[insert order detail 1] - '.Db::getInstance()->getMsgError();
                }
                if (isset($values_order) && count($values_order) && !Db::getInstance()->execute($insert_order. implode(',', $values_order))) {
                    $res = false;
                    $array_errors[] = '[insert order 2] - '.Db::getInstance()->getMsgError();
                }
                if (isset($values_order)) {
                    unset($values_order);
                }
                if (isset($values_order_detail)) {
                    unset($values_order_detail);
                }
            }
        }
    }

    if (isset($values_order_detail) && count($values_order_detail) && !Db::getInstance()->execute($insert_order_detail. implode(',', $values_order_detail))) {
        $res = false;
        $array_errors[] = '[insert order detail 3] - '.Db::getInstance()->getMsgError();
    }
    if (isset($values_order) && count($values_order) && !Db::getInstance()->execute($insert_order. implode(',', $values_order))) {
        $res = false;
        $array_errors[] = '[insert order 4] - '.Db::getInstance()->getMsgError();
    }
    if (isset($values_order)) {
        unset($values_order);
    }
    if (isset($values_order_detail)) {
        unset($values_order_detail);
    }
    if (!mo_renameTables()) {
        $res = false;
        $array_errors[] = 'unable to rename tables orders_2 and order_detail_2 to orders and order_detail';
    }

    if (!$res) {
        return array('error' => 1, 'msg' => count($array_errors).' error(s) : <br/>'.implode('<br/>', $array_errors));
    }
}

/**
 * mo_ps_round is a simplification of Tools::ps_round:
 * - round is always 2
 * - no call to Configuration class
 *
 * @param mixed $val
 * @return void
 */
function mo_ps_round($val)
{
    static $ps_price_round_mode;
    if (empty($ps_price_round_mode)) {
        $ps_price_round_mode = Db::getInstance()->getValue('SELECT value
			FROM `'._DB_PREFIX_.'configuration`
			WHERE name = "PS_PRICE_ROUND_MODE"');
    }

    switch ($ps_price_round_mode) {
        case 0:
            return ceil($val * 100)/100;
        case 1:
            return floor($val * 100)/100;
        default:
            return round($val, 2);
    }
}

function mo_duplicateTables()
{
    $res = true;
    $res &= Db::getInstance()->execute('CREATE TABLE
		`'._DB_PREFIX_.'orders_2` LIKE `'._DB_PREFIX_.'orders`');
    $res &= Db::getInstance()->execute('CREATE TABLE
		`'._DB_PREFIX_.'order_detail_2` LIKE `'._DB_PREFIX_.'order_detail`');
    return $res;
}

function mo_renameTables()
{
    $res = true;
    $res &= Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'orders`');
    $res &= Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'order_detail`');

    $res &= Db::getInstance()->execute('RENAME TABLE `'._DB_PREFIX_.'orders_2` TO `'._DB_PREFIX_.'orders`');
    $res &= Db::getInstance()->execute('RENAME TABLE `'._DB_PREFIX_.'order_detail_2` TO `'._DB_PREFIX_.'order_detail`');
    return $res;
}

function mo_getCustomerDefaultGroup($id_customer)
{
    static $cache;
    if (!isset($cache[$id_customer])) {
        $cache[$id_customer] = Db::getInstance()->getValue('SELECT `id_default_group` FROM `'._DB_PREFIX_.'customer` WHERE `id_customer` = '.(int)$id_customer);
    }

    return $cache[$id_customer];
}

function mo_getPriceDisplayMethod($id_group)
{
    static $cache;

    if (!isset($cache[$id_group])) {
        $cache[$id_group] = Db::getInstance()->getValue('
			SELECT `price_display_method`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);
    }

    return $cache[$id_group];
}

function mo_setProductPrices($row, $tax_calculation_method)
{
    if ($tax_calculation_method == PS_TAX_EXC) {
        $row['product_price'] = mo_ps_round($row['product_price']);
    } else {
        $row['product_price_wt'] = mo_ps_round($row['product_price'] * (1 + $row['tax_rate'] / 100));
    }

    $group_reduction = 1;
    if ($row['group_reduction'] > 0) {
        $group_reduction =  1 - $row['group_reduction'] / 100;
    }

    if ($row['reduction_percent'] != 0) {
        if ($tax_calculation_method == PS_TAX_EXC) {
            $row['product_price'] = ($row['product_price'] - $row['product_price'] * ($row['reduction_percent'] * 0.01));
        } else {
            $reduction = mo_ps_round($row['product_price_wt'] * ($row['reduction_percent'] * 0.01));
            $row['product_price_wt'] = mo_ps_round(($row['product_price_wt'] - $reduction));
        }
    }

    if ($row['reduction_amount'] != 0) {
        if ($tax_calculation_method == PS_TAX_EXC) {
            $row['product_price'] = ($row['product_price'] - ($row['reduction_amount'] / (1 + $row['tax_rate'] / 100)));
        } else {
            $row['product_price_wt'] = mo_ps_round(($row['product_price_wt'] - $row['reduction_amount']));
        }
    }

    if ($row['group_reduction'] > 0) {
        if ($tax_calculation_method == PS_TAX_EXC) {
            $row['product_price'] = $row['product_price'] * $group_reduction;
        } else {
            $row['product_price_wt'] = mo_ps_round($row['product_price_wt'] * $group_reduction);
        }
    }

    if (($row['reduction_percent'] or $row['reduction_amount'] or $row['group_reduction']) and $tax_calculation_method == PS_TAX_EXC) {
        $row['product_price'] = mo_ps_round($row['product_price']);
    }

    if ($tax_calculation_method == PS_TAX_EXC) {
        $row['product_price_wt'] = mo_ps_round($row['product_price'] * (1 + ($row['tax_rate'] * 0.01))) + mo_ps_round($row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100));
    } else {
        $row['product_price_wt_but_ecotax'] = $row['product_price_wt'];
        $row['product_price_wt'] = mo_ps_round($row['product_price_wt'] + $row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100));
    }

    if ($tax_calculation_method != PS_TAX_EXC) {
        $row['product_price'] = $row['product_price_wt'] / (1 + $row['tax_rate'] / 100);
    }

    $row['total_wt'] = $row['product_quantity'] * $row['product_price_wt'];
    $row['total_price'] = $row['product_quantity'] * $row['product_price'];

    return $row;
}
