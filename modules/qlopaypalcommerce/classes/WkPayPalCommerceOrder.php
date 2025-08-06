<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
*/

class WKPayPalCommerceOrder extends ObjectModel
{
    // Write all table fields here
    public $id_cart;
    public $order_reference;
    public $id_currency;
    public $environment;
    public $id_customer;
    public $order_total;
    public $pp_paid_total;
    public $pp_paid_currency;
    public $checkout_currency;
    public $pp_reference_id;
    public $pp_order_id;
    public $pp_transaction_id;
    public $pp_payment_status;
    public $response;
    public $order_date;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' =>  'wk_paypal_commerce_order',
        'primary'   =>  'id_paypal_commerce_order',
        'multilang' =>  false,
        'fields'    =>  array(
            'order_reference'   =>  array(
                'type'  =>  self::TYPE_INT,
                'validate'  =>  'isUnsignedInt'
            ),
            'id_cart'   =>  array(
                'type'  =>  self::TYPE_INT,
                'validate'  =>  'isUnsignedInt'
            ),
            'id_currency'   =>  array(
                'type'  =>  self::TYPE_INT,
                'validate'  =>  'isUnsignedInt'
            ),
            'environment' =>  array(
                'type'  =>  self::TYPE_STRING,
                'size' => 15
            ),
            'id_customer'   =>  array(
                'type'  =>  self::TYPE_INT,
                'validate'  =>  'isUnsignedInt'
            ),
            'order_total' =>  array(
                'type'  =>  self::TYPE_FLOAT
            ),
            'pp_paid_total' =>  array(
                'type'  =>  self::TYPE_FLOAT
            ),
            'checkout_currency' =>  array(
                'type'  =>  self::TYPE_STRING,
                'size' => 5
            ),
            'pp_paid_currency' =>  array(
                'type'  =>  self::TYPE_STRING,
                'size' => 5
            ),
            'pp_reference_id' =>  array(
                'type'  =>  self::TYPE_STRING,
                'size' => 50
            ),
            'pp_order_id' =>  array(
                'type'  =>  self::TYPE_STRING
            ),
            'pp_transaction_id' =>  array(
                'type'  =>  self::TYPE_STRING
            ),
            'pp_payment_status' =>  array(
                'type'  =>  self::TYPE_STRING
            ),
            'response' =>  array(
                'type'  =>  self::TYPE_STRING
            ),
            'order_date'    =>  array(
                'type'  =>  self::TYPE_DATE,
                'required'  =>  false,
                'validate'  =>  'isDateFormat'
            ),
            'date_add'  =>  array(
                'type'  =>  self::TYPE_DATE,
                'required'  =>  false,
                'validate'  =>  'isDateFormat'
            ),
            'date_upd'  =>  array(
                'type'  =>  self::TYPE_DATE,
                'required'  =>  false,
                'validate'  =>  'isDateFormat'
            )
        )
    );

    public $ppStatusDetail = array();

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->module = Module::getInstanceByName('qlopaypalcommerce');

        $this->ppStatusDetail['BUYER_COMPLAINT'] = $this->module->l('The payer initiated a dispute for this captured payment with PayPal.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['CHARGEBACK'] = $this->module->l('The captured funds were reversed in response to the payer disputing this captured payment with the issuer of the financial instrument used to pay for this captured payment.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['ECHECK'] = $this->module->l('The payer paid by an eCheck that has not yet cleared.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['INTERNATIONAL_WITHDRAWAL'] = $this->module->l('Visit your online account. In your **Account Overview**, accept and deny this payment.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['OTHER'] = $this->module->l('No additional specific reason can be provided. For more information about this captured payment, visit your account online or contact PayPal.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['PENDING_REVIEW'] = $this->module->l('The captured payment is pending manual review.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['RECEIVING_PREFERENCE_MANDATES_MANUAL_ACTION'] = $this->module->l('The payee has not yet set up appropriate receiving preferences for their account. For more information about how to accept or deny this payment, visit your account online. This reason is typically offered in scenarios such as when the currency of the captured payment is different from the primary holding currency of the payee.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['REFUNDED'] = $this->module->l('The captured funds were refunded.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['TRANSACTION_APPROVED_AWAITING_FUNDING'] = $this->module->l('The payer must send the funds for this captured payment. This code generally appears for manual EFTs.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['UNILATERAL'] = $this->module->l('The payee does not have a PayPal account.', 'WKPayPalCommerceOrder');
        $this->ppStatusDetail['VERIFICATION_REQUIRED'] = $this->module->l('The payee\'s PayPal account is not verified', 'WKPayPalCommerceOrder');

        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getTransactionDetailsByPaypalTransaction($idPPTransaction)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_paypal_commerce_order` WHERE `pp_transaction_id` = \'' . pSQL($idPPTransaction) . '\''
        );
    }

    public static function getTransactionDetails($idPaypalCommerceOrder)
    {
        $transactions = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT pco.*, CONCAT(cus.`firstname`, \' \', cus.`lastname`) as customer_name, cus.`email` FROM `' . _DB_PREFIX_ . 'wk_paypal_commerce_order` pco LEFT JOIN `' . _DB_PREFIX_ . 'customer` cus ON (cus.`id_customer` = pco.`id_customer`) WHERE pco.`id_paypal_commerce_order` = ' . (int)$idPaypalCommerceOrder
        );

        if ($transactions) {
            $transactions['pp_paid_total_formated'] = self::getFormattedPrice(
                $transactions['pp_paid_total'],
                $transactions['id_currency']
            );

            $transactions['customer_link'] = Context::getContext()->link->getAdminLink(
                'AdminCustomers',
                Tools::getAdminTokenLite('AdminCustomers')
            ).'&id_customer=' . (int)$transactions['id_customer'] . '&viewcustomer';

            return $transactions;
        }

        return false;
    }

    public static function updateOrderReference($paypalOrderID, $orderRef)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
            'UPDATE `' . _DB_PREFIX_ . 'wk_paypal_commerce_order`
            SET `order_reference` = \''. pSQL($orderRef) .'\'
            WHERE `pp_order_id` = \'' . pSQL($paypalOrderID) . '\''
        );
    }

    /**
     * Get currency formatted price
     * @param  float $price
     * @param  int $id_currency
     * @return string Return formatted price
     */
    public static function getFormattedPrice($price, $id_currency)
    {
        return Tools::displayPrice(
            $price,
            new Currency((int)$id_currency)
        );
    }
}
