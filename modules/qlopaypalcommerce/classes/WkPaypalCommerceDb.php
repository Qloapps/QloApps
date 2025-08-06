<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkPaypalCommerceDb
{
    public function getModuleSql()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_paypal_commerce_order` (
                `id_paypal_commerce_order` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `environment` varchar(15) CHARACTER SET utf8 NOT NULL,
                `order_reference` varchar(20) NOT NULL,
                `id_cart` int(10) UNSIGNED NOT NULL,
                `id_currency` int(10) UNSIGNED NOT NULL,
                `id_customer` int(10) UNSIGNED NOT NULL,
                `order_total` decimal(10,5) NOT NULL,
                `pp_paid_total` decimal(10,5) NOT NULL,
                `checkout_currency` varchar(5) CHARACTER SET utf8 NOT NULL,
                `pp_paid_currency` varchar(5) CHARACTER SET utf8 NOT NULL,
                `pp_reference_id` varchar(50) CHARACTER SET utf8 NOT NULL,
                `pp_order_id` varchar(50) CHARACTER SET utf8 NOT NULL,
                `pp_transaction_id` varchar(50) CHARACTER SET utf8 NOT NULL,
                `pp_payment_status` varchar(10) CHARACTER SET utf8 NOT NULL,
                `response` text CHARACTER SET utf8 NOT NULL,
                `order_date` datetime NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_paypal_commerce_order`)
              ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
              "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_paypal_commerce_refund` (
                `id_paypal_commerce_refund` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_trans_id` int(10) UNSIGNED NOT NULL,
                `paypal_refund_id` varchar(20) NOT NULL,
                `refund_amount` decimal(10,2) NOT NULL,
                `currency_code` varchar(5) NOT NULL,
                `refund_type` int(10) UNSIGNED NOT NULL,
                `refund_reason` varchar(255) NOT NULL,
                `response` text NOT NULL,
                `refund_status` varchar(20) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_paypal_commerce_refund`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;"
        );
    }

    public function createTables()
    {
        if ($sql = $this->getModuleSql()) {
            foreach ($sql as $query) {
                if ($query) {
                    if (!Db::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function deleteConfigVars()
    {
        $configKeys = array(
            'WK_PAYPAL_COMMERCE_MERCHANT_ID',
            'WK_PAYPAL_COMMERCE_EMAIL',
            'WK_PAYPAL_COMMERCE_CLIENT_ID',
            'WK_PAYPAL_COMMERCE_CLIENT_SECRET',
            'WK_PAYPAL_COMMERCE_PAYMENT_MODE',
            'WK_PAYPAL_COMMERCE_SANDBOX_WEBHOOK_ID',
            'WK_PAYPAL_COMMERCE_LIVE_WEBHOOK_ID',
        );

        foreach ($configKeys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function dropTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wk_paypal_commerce_order`,
            `'._DB_PREFIX_.'wk_paypal_commerce_refund`'
        );
    }

    public function getConfigFieldsValues()
    {
        return array(
            'WK_PAYPAL_COMMERCE_MERCHANT_ID' => Tools::getValue(
                'WK_PAYPAL_COMMERCE_MERCHANT_ID',
                Configuration::get('WK_PAYPAL_COMMERCE_MERCHANT_ID')
            ),
            'WK_PAYPAL_COMMERCE_EMAIL' => Tools::getValue(
                'WK_PAYPAL_COMMERCE_EMAIL',
                Configuration::get('WK_PAYPAL_COMMERCE_EMAIL')
            ),
            'WK_PAYPAL_COMMERCE_CLIENT_ID' => Tools::getValue(
                'WK_PAYPAL_COMMERCE_CLIENT_ID',
                Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID')
            ),
            'WK_PAYPAL_COMMERCE_CLIENT_SECRET' => Tools::getValue(
                'WK_PAYPAL_COMMERCE_CLIENT_SECRET',
                Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_SECRET')
            ),
            'WK_PAYPAL_COMMERCE_PAYMENT_MODE' => Tools::getValue(
                'WK_PAYPAL_COMMERCE_PAYMENT_MODE',
                Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE')
            ),
        );
    }
}
