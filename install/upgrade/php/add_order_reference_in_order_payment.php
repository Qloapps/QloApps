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

function add_order_reference_in_order_payment()
{
    $res = true;
    $payments = Db::getInstance()->query('
	SELECT op.id_order_payment, o.reference
	FROM `'._DB_PREFIX_.'order_payment` op
	INNER JOIN `'._DB_PREFIX_.'orders` o
	ON o.id_order = op.id_order');
    
    if (!is_resource($payments) || !$payments) {
        return true;
    }
    
    $errors = array();
    // Populate "order_reference"
    while ($payment = Db::getInstance()->nextRow($payments)) {
        if (isset($payment['id_order_payment']) && $payment['id_order_payment']) {
            $res = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'order_payment`
			SET order_reference = \''.pSQL($payment['reference']).'\'
			WHERE id_order_payment = '.(int)$payment['id_order_payment']);
            if (!$res) {
                $errors[] = Db::getInstance()->getMsgError();
            }
        }
    }
    
    if (count($errors)) {
        return array('error' => true, 'msg' => implode('<br/>', $errors));
    }

    // Get lines to merge (with multishipping on, durring the payment one line was added by order, only one is necessary by cart)
    $duplicate_lines = Db::getInstance()->query('
	SELECT GROUP_CONCAT(id_order_payment) as id_order_payments
	FROM `'._DB_PREFIX_.'order_payment`
	GROUP BY order_reference, date_add
	HAVING COUNT(*) > 1');
    
    if (!is_resource($duplicate_lines) || !$duplicate_lines) {
        return true;
    }

    $order_payments_to_remove = array();
    while ($order_payments = Db::getInstance()->nextRow($duplicate_lines)) {
        $order_payments_array = array();
        if (isset($order_payments['id_order_payments'])) {
            $order_payments_array = explode(',', $order_payments['id_order_payments']);
        }
        // Remove the first item (we want to keep one line)
        $id_order_payment_keep = array_shift($order_payments_array);
        
        $res = Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'order_invoice_payment`
		SET id_order_payement = '.(int)$id_order_payment_keep.'
		WHERE id_order_payment IN ('.implode(',', $order_payments_array).')');
        
        $order_payments_to_remove = array_merge($order_payments_to_remove, $order_payments_array);
    }
    // Remove the duplicate lines (because of the multishipping)
    if (count($order_payments_to_remove)) {
        $res = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_payment` WHERE id_order_payment IN ('.implode(',', $order_payments_to_remove).')');
    }
    
    if (!$res) {
        return array('errors' => true, 'msg' =>  Db::getInstance()->getMsgError());
    }
    return true;
}
