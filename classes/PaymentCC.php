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

/**
 *
 * @deprecated 1.5.0.1
 * @see OrderPaymentCore
 *
 */
class PaymentCCCore extends OrderPayment
{
    public $id_order;
    public $id_currency;
    public $amount;
    public $transaction_id;
    public $card_number;
    public $card_brand;
    public $card_expiration;
    public $card_holder;
    public $date_add;

    protected $fieldsRequired = array('id_currency', 'amount');
    protected $fieldsSize = array('transaction_id' => 254, 'card_number' => 254, 'card_brand' => 254, 'card_expiration' => 254, 'card_holder' => 254);
    protected $fieldsValidate = array(
        'id_order' => 'isUnsignedId', 'id_currency' => 'isUnsignedId', 'amount' => 'isPrice',
        'transaction_id' => 'isAnything', 'card_number' => 'isAnything', 'card_brand' => 'isAnything', 'card_expiration' => 'isAnything', 'card_holder' => 'isAnything');

    public static $definition = array(
        'table' => 'payment_cc',
        'primary' => 'id_payment_cc',
    );


    /**
     * @deprecated 1.5.0.2
     * @see OrderPaymentCore
     */
    public function add($autodate = true, $nullValues = false)
    {
        Tools::displayAsDeprecated();
        return parent::add($autodate, $nullValues);
    }

    /**
    * Get the detailed payment of an order
    * @param int $id_order
    * @return array
    * @deprecated 1.5.0.1
    * @see OrderPaymentCore
    */
    public static function getByOrderId($id_order)
    {
        Tools::displayAsDeprecated();
        $order = new Order($id_order);
        return OrderPayment::getByOrderReference($order->reference);
    }
}
