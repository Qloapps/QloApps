<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

include_once dirname(__FILE__).'/classes/wkpaypalhelper.php';
include_once dirname(__FILE__).'/classes/wkpaypaltransaction.php';
include_once dirname(__FILE__).'/classes/wkpaypalrefund.php';
include_once _PS_MODULE_DIR_.'hotelreservationsystem/classes/HotelCustomerAdvancedPayment.php';

class WkPayPalAdaptive extends PaymentModule
{
    const INSTALL_SQL_FILE = 'install.sql';
    private $_html = '';

    public function __construct()
    {
        $this->name = 'wkpaypaladaptive';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.1';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Paypal Payment');
        $this->description = $this->l('Manage paypal payment');
    }

    public function hookAddPaymentSetting($params)
    {
        $params['fields_options']['wkpaypaladaptive'] = array(
            'title' =>  $this->l('Paypal Payment Configuration'),
            'fields' => array(
                'WK_PAYPAL_SANDBOX' => array(
                    'title' => $this->l('Sandbox Mode'),
                    'hint' => $this->l('Paypal setting mode'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'required' => true
                ),
                'APP_ID' => array(
                    'title' => $this->l('API APP ID'),
                    'type' => 'text',
                    'required' => true
                ),
                'APP_USERNAME' => array(
                    'title' => $this->l('API USERNAME'),
                    'required' => true,
                    'type' => 'text',
                ),
                'APP_PASSWORD' => array(
                    'title' => $this->l('API PASSWORD'),
                    'type' => 'text',
                    'required' => true
                ),
                'APP_SIGNATURE' => array(
                    'title' => $this->l('API SIGNATURE'),
                    'type' => 'text',
                    'required' => true
                ),
                'PAYPAL_EMAIL' => array(
                    'title' => $this->l('PAYPAL EMAIL ID'),
                    'validation' => 'isEmail',
                    'type' => 'text',
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );
    }

    public function getConfigFieldsValues()
    {
        return array(
            'WK_PAYPAL_SANDBOX' => Tools::getValue('WK_PAYPAL_SANDBOX', Configuration::get('WK_PAYPAL_SANDBOX')),
            'APP_ID' => Tools::getValue('APP_ID', Configuration::get('APP_ID')),
            'APP_USERNAME' => Tools::getValue('APP_USERNAME', Configuration::get('APP_USERNAME')),
            'APP_PASSWORD' => Tools::getValue('APP_PASSWORD', Configuration::get('APP_PASSWORD')),
            'APP_SIGNATURE' => Tools::getValue('APP_SIGNATURE', Configuration::get('APP_SIGNATURE')),
            'PAYPAL_EMAIL' => Tools::getValue('PAYPAL_EMAIL', Configuration::get('PAYPAL_EMAIL')),
        );
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }
        if (!isset($params['objOrder']) || ($params['objOrder']->module != $this->name)) {
            return false;
        }

        if (isset($params['objOrder'])
            && Validate::isLoadedObject($params['objOrder'])
            && isset($params['objOrder']->valid)
        ) {
            $this->smarty->assign(
                array(
                    'id_order' => $params['objOrder']->id,
                    'valid' => $params['objOrder']->valid,
                )
            );
        }

        if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference)) {
            $this->smarty->assign('reference', $params['objOrder']->reference);
        }

        return $this->display(__FILE__, 'payment_return.tpl');
    }

    public function hookDisplayPayment()
    {
        // payment option will not display untill paypal settings not filled
        if (Configuration::get('PAYPAL_EMAIL')) {
            $this->context->controller->addCSS($this->_path.'views/css/hook_payment.css');
            $this->smarty->assign(
                array(
                    'this_path' => $this->_path,
                    'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
                )
            );

            return $this->display(__FILE__, 'payment.tpl');
        }
    }

    public function hookAdminOrder($params)
    {
        $id_cart = Cart::getCartIdByOrderId($params['id_order']);
        $paypal_transaction = WkPaypalTransaction::getTransactionByIdCart($id_cart);
        if ($paypal_transaction) {
            $paypal_transaction['payment_info'] = Tools::jsonDecode($paypal_transaction['payment_info'])->paymentInfo;
            $this->context->smarty->assign('paypal_transaction', $paypal_transaction);
            $refundDetails = WkPaypalRefund::getRefundHistoryByTransactionId($paypal_transaction['id']);
            if ($refundDetails) {
                foreach ($refundDetails as &$refunfInfo) {
                    $refunfInfo['refund_details'] = Tools::jsonDecode($refunfInfo['refund_details']);
                }
                // p($refundDetails);die;
                $this->context->smarty->assign('refundDetails', $refundDetails);
            }
            return $this->display(__FILE__, 'admin-order.tpl');
        }
    }

    public function install()
    {
        /* The cURL PHP extension must be enabled to use this module */
        if (!function_exists('curl_version')) {
            $this->_errors[] = $this->l('Sorry, this module requires the cURL PHP Extension (http://www.php.net/curl), which is not enabled on your server. Please ask your hosting provider for assistance.');

            return false;
        }

        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        }
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }
        /* General Configuration options */
        Configuration::updateValue('WK_PAYPAL_SANDBOX', 1);
        if (Configuration::get('WK_PAYPAL_SANDBOX') != 1) {
            Configuration::updateValue('WK_PAYPAL_SANDBOX', 1);
        }

        if (!parent::install()
            || !$this->registerModuleHooks()
        ) {
            return false;
        }

        return true;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'displayPayment',
                'displayAdminSellerInfoJoin',
                'paymentReturn',
                'adminOrder',
                'addPaymentSetting',
            )
        );
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->dropTable()
            || !$this->deleteConfigVariable()
        ) {
            return false;
        }
        return true;
    }

    public function deleteConfigVariable()
    {
        $configKeys = array(
            'WK_PAYPAL_SANDBOX',
            'APP_ID',
            'APP_USERNAME',
            'APP_PASSWORD',
            'APP_SIGNATURE',
            'PAYPAL_EMAIL'
        );
        foreach ($configKeys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function dropTable()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wkpaypal_transaction`,
            `'._DB_PREFIX_.'wkpaypal_refund`'
        );
    }
}
