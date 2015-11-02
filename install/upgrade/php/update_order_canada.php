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

function update_order_canada()
{
    $sql ='SHOW TABLES LIKE "'.str_replace('_', '\_', _DB_PREFIX_).'order\_tax"';
    $table = Db::getInstance()->executeS($sql);

    if (!count($table)) {
        Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'order_tax` (
		  `id_order` int(11) NOT NULL,
		  `tax_name` varchar(40) NOT NULL,
		  `tax_rate` decimal(6,3) NOT NULL,
		  `amount` decimal(20,6) NOT NULL
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');


        $address_field = Db::getInstance()->getValue('SELECT value
			FROM `'._DB_PREFIX_.'configuration`
			WHERE name="PS_TAX_ADDRESS_TYPE"');
        $address_field = str_replace('`', '\`', $address_field);

        $sql = 'SELECT `id_order`, `price_display_method`
					FROM `'._DB_PREFIX_.'orders` o
					LEFT JOIN `'._DB_PREFIX_.'customer` cus ON (o.id_customer = cus.id_customer)
					LEFT JOIN `'._DB_PREFIX_.'group` g ON (g.id_group = cus.id_default_group)
					LEFT JOIN `'._DB_PREFIX_.'address` a ON (a.`id_address` = o.`'.$address_field.'`)
					LEFT JOIN `'._DB_PREFIX_.'country` c ON (c.`id_country` = a.`id_country`)
					WHERE c.`iso_code` = "CA"';

        $id_order_list = Db::getInstance()->executeS($sql);
        $default_price_display_method = Db::getInstance()->getValue('SELECT price_display_method
			FROM `'._DB_PREFIX_.'group` WHERE id_group=1');
        $values = '';
        if (is_array($id_order_list)) {
            foreach ($id_order_list as $order) {
                $amount = array();
                $id_order = $order['id_order'];
            // in Order class, getTaxCalculationMethod
            // 	returns Group::getDefaultPriceDisplayMethod
            $tax_calculation_method = $order['price_display_method'];

                $products = Db::getInstance()->executeS('
				SELECT * FROM `'._DB_PREFIX_.'order_detail` od
				WHERE od.`id_order` = '.(int)$id_order);

                foreach ($products as $product) {
                    if (!array_key_exists($product['tax_name'], $amount)) {
                        $amount[$product['tax_name']] = array('amount' => 0, 'rate' => $product['tax_rate']);
                    }

                // PS_TAX_EXC = 1, PS_TAX_INC = 0
                if ($tax_calculation_method == 1) {
                    $total_product = $product['product_price'] * $product['product_quantity'];
                    $amount_tmp = update_order_canada_ps_round($total_product * ($product['tax_rate'] / 100), 2);
                    $amount[$product['tax_name']]['amount'] += update_order_canada_ps_round($total_product * ($product['tax_rate'] / 100), 2);
                } else {
                    $total_product = $product['product_price'] * $product['product_quantity'];
                    $amount_tmp = update_order_canada_ps_round($total_product - ($total_product / (1 + ($product['tax_rate'] / 100))), 2);
                    $amount[$product['tax_name']]['amount'] += update_order_canada_ps_round($total_product - ($total_product / (1 + ($product['tax_rate'] / 100))), 2);
                }
                }

                foreach ($amount as $tax_name => $tax_infos) {
                    $values .= '('.(int)$id_order.', "'.$tax_name.'\', "'.$tax_infos['rate'].'", '.(float)$tax_infos['amount'].'),';
                }
                unset($order);
            }
        }

        if (!empty($values)) {
            $values = rtrim($values, ",");

            Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'order_tax` (id_order, tax_name, tax_rate, amount)
			VALUES '.$values);
        }
    }
}

function update_order_canada_ps_round($val)
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
