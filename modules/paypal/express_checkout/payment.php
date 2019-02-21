<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once dirname(__FILE__).'/../../../config/config.inc.php';
include_once _PS_ROOT_DIR_.'/init.php';

include_once _PS_MODULE_DIR_.'paypal/express_checkout/process.php';
include_once _PS_MODULE_DIR_.'paypal/express_checkout/submit.php';
include_once _PS_MODULE_DIR_.'paypal/paypal_login/PayPalLoginUser.php';

/* Normal payment process */
$id_cart = Tools::getValue('id_cart');
$id_order = Tools::getValue('id_order');
$id_module = Tools::getValue('id_module');
$paypal_key = Tools::getValue('key');

if ($id_cart && $id_order && $id_module && $paypal_key) {
    if (version_compare(_PS_VERSION_, '1.5', '<')) {
        new PayPalExpressCheckoutSubmit();
    }

    return;
}

$request_type = Tools::getValue('express_checkout');
$ppec = new PaypalExpressCheckout($request_type);

$token = Tools::getValue('token');
$payer_id = Tools::getValue('PayerID');

function setContextData($ppec)
{
    // Create new Cart to avoid any refresh or other bad manipulations
    $ppec->context->cart = new Cart();
    $ppec->context->cart->id_currency = (int) $ppec->context->currency->id;
    $ppec->context->cart->id_lang = (int) $ppec->context->language->id;

    // Customer settings
    $ppec->context->cart->id_guest = (int) $ppec->context->cookie->id_guest;
    $ppec->context->cart->id_customer = (int) $ppec->context->customer->id;

    // Secure key information
    $secure_key = isset($ppec->context->customer) ? $ppec->context->customer->secure_key : null;
    $ppec->context->cart->secure_key = $secure_key;
}

/**
 * Set customer information
 * Used to create user account with PayPal account information
 */
function setCustomerInformation($ppec, $email)
{
    $customer = new Customer();
    $customer->email = $email;
    $customer->lastname = $ppec->result['LASTNAME'];
    $customer->firstname = $ppec->result['FIRSTNAME'];
    $customer->passwd = Tools::encrypt(Tools::passwdGen());
    return $customer;
}

/**
 * Set customer address (when not logged in)
 * Used to create user address with PayPal account information
 */
function setCustomerAddress($ppec, $customer, $id = null)
{
    $address = new Address($id);
    $address->id_country = Country::getByIso($ppec->result['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE']);
    if ($id == null) {
        $address->alias = 'Paypal_Address';
    }

    $name = trim($ppec->result['PAYMENTREQUEST_0_SHIPTONAME']);
    $name = explode(' ', $name);
    if (isset($name[1])) {
        $firstname = $name[0];
        unset($name[0]);
        $lastname = implode(' ', $name);
    } else {
        $lastname = $ppec->result['LASTNAME'];
        $firstname = $ppec->result['FIRSTNAME'];
    }

    $address->lastname = $lastname;
    $address->firstname = $firstname;
    $address->address1 = $ppec->result['PAYMENTREQUEST_0_SHIPTOSTREET'];
    if (isset($ppec->result['PAYMENTREQUEST_0_SHIPTOSTREET2'])) {
        $address->address2 = $ppec->result['PAYMENTREQUEST_0_SHIPTOSTREET2'];
    }

    $address->city = $ppec->result['PAYMENTREQUEST_0_SHIPTOCITY'];
    if (Country::containsStates($address->id_country)) {
        $address->id_state = (int) State::getIdByIso($ppec->result['PAYMENTREQUEST_0_SHIPTOSTATE'], $address->id_country);
    }

    $address->postcode = $ppec->result['PAYMENTREQUEST_0_SHIPTOZIP'];
    if (isset($ppec->result['PAYMENTREQUEST_0_SHIPTOPHONENUM'])) {
        $address->phone = $ppec->result['PAYMENTREQUEST_0_SHIPTOPHONENUM'];
    }

    $address->id_customer = $customer->id;
    return $address;
}

function checkAndModifyAddress($ppec, $customer)
{
    $context = Context::getContext();
    $customer_addresses = $customer->getAddresses($context->cookie->id_lang);
    $paypal_address = false;
    if (count($customer_addresses) == 0) {
        $paypal_address = setCustomerAddress($ppec, $customer);
    } else {
        foreach ($customer_addresses as $address) {
            if ($address['alias'] == 'Paypal_Address') {
//If a PayPal address already exists we use it to override new address from paypal
                $paypal_address = setCustomerAddress($ppec, $customer, $address['id_address']);
                break;
            } else {
//We check if an address exists with the same country / city / street

                if ($address['firstname'] == $ppec->result['FIRSTNAME'] &&
                    $address['lastname'] == $ppec->result['LASTNAME'] &&
                    $address['id_country'] == Country::getByIso($ppec->result['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE']) &&
                    $address['address1'] == $ppec->result['PAYMENTREQUEST_0_SHIPTOSTREET'] &&
                    $address['address2'] == $ppec->result['PAYMENTREQUEST_0_SHIPTOSTREET2'] &&
                    $address['city'] == $ppec->result['PAYMENTREQUEST_0_SHIPTOCITY']) {
                    $paypal_address = new Address($address['id_address']);
                    break;
                }
            }
        }
    }
    if ($paypal_address == false) {
        $paypal_address = setCustomerAddress($ppec, $customer);
    }

    $paypal_address->save();
    return $paypal_address;
}

if ($request_type && $ppec->type) {
    $id_product = (int) Tools::getValue('id_product');
    $product_quantity = (int) Tools::getValue('quantity');
    $id_product_attribute = Tools::getValue('id_p_attr');

    if (($id_product > 0) && $id_product_attribute !== false && ($product_quantity > 0)) {
        setContextData($ppec);

        if (!$ppec->context->cart->add()) {
            $ppec->logs[] = $ppec->l('Cannot create new cart');
            $display = (_PS_VERSION_ < '1.5') ? new BWDisplay() : new FrontController();

            $ppec->context->smarty->assign(
                array(
                    'logs' => $ppec->logs,
                    'message' => $ppec->l('Error occurred:'),
                    'use_mobile' => (bool) $ppec->useMobile(),
                )
            );

            $template = 'error.tpl';
        } else {
            $ppec->context->cookie->id_cart = (int) $ppec->context->cart->id;
        }

        $ppec->context->cart->updateQty((int) $product_quantity, (int) $id_product, (int) $id_product_attribute);
        $ppec->context->cart->update();
    }

    $login_user = PaypalLoginUser::getByIdCustomer((int) $ppec->context->customer->id);

    if ($login_user && $login_user->expires_in <= time()) {
        $obj = new PayPalLogin();
        $login_user = $obj->getRefreshToken();
    }

    /* Set details for a payment */
    $ppec->setExpressCheckout(($login_user ? $login_user->access_token : false));

    if (Tools::getValue('ajax') && $ppec->useInContextCheckout()) {
        $ppec->displayPaypalInContextCheckout();
    }
    if ($ppec->hasSucceedRequest() && !empty($ppec->token)) {
        $ppec->redirectToAPI();
    } else {
        // Display Error and die with this method
        $ppec->displayPayPalAPIError($ppec->l('Error during the preparation of the Express Checkout payment'), $ppec->logs);
    }

} elseif (!empty($ppec->token) && ($ppec->token == $token) && ($ppec->payer_id = $payer_id)) {


    //If a token exist with payer_id, then we are back from the PayPal API
    /* Get payment infos from paypal */
    $ppec->getExpressCheckout();

    if ($ppec->hasSucceedRequest() && !empty($ppec->token)) {
        $address = $customer = null;
        $email = $ppec->result['EMAIL'];

        /* Create Customer if not exist with address etc */
        if ($ppec->context->cookie->logged) {
            $id_customer = Paypal::getPayPalCustomerIdByEmail($email);
            if (!$id_customer) {
                PayPal::addPayPalCustomer($ppec->context->customer->id, $email);
            }

            $customer = $ppec->context->customer;
        } elseif ($id_customer = Customer::customerExists($email, true)) {
            $customer = new Customer($id_customer);
        } else {
            $customer = setCustomerInformation($ppec, $email);
            $customer->add();

            PayPal::addPayPalCustomer($customer->id, $email);
        }

        if (!$customer->id) {
            $ppec->logs[] = $ppec->l('Cannot create customer');
        }

        if (!isset($ppec->result['PAYMENTREQUEST_0_SHIPTOSTREET']) || !isset($ppec->result['PAYMENTREQUEST_0_SHIPTOCITY'])
            || !isset($ppec->result['SHIPTOZIP']) || !isset($ppec->result['COUNTRYCODE'])) {
            $ppec->redirectToCheckout($customer, ($ppec->type != 'payment_cart'));
        }

        $addresses = $customer->getAddresses($ppec->context->language->id);
        foreach ($addresses as $address) {
            if ($address['alias'] == 'Paypal_Address') {
                //If address has already been created
                $address = new Address($address['id_address']);
                break;
            }
        }

        /* Create address */
        if (is_array($address) && isset($address['id_address'])) {
            $address = new Address($address['id_address']);
        }

        if ((!$address || !$address->id) && $customer->id) {
            //If address does not exists, we create it
            $address = setCustomerAddress($ppec, $customer);
            $address->add();
        } else if ($ppec->type != 'payment_cart') {
            //We used Express Checkout Shortcut => we override address
            $address = checkAndModifyAddress($ppec, $customer);
        }

        if ($customer->id && !$address->id) {
            $ppec->logs[] = $ppec->l('Cannot create Address');
        }

        /* Create Order */
        if ($customer->id && $address->id) {
            if ($ppec->type != 'payment_cart') {
                $ppec->context->cart->id_customer = $customer->id;
                if (version_compare(_PS_VERSION_, '1.5', '<')) {
                    $ppec->context->cart->id_address_delivery = $address->id;
                    $ppec->context->cart->id_address_invoice = $address->id;
                } else {
                    $ppec->context->cart->updateAddressId($ppec->context->cart->id_address_delivery, $address->id);
                    $ppec->context->cart->updateAddressId($ppec->context->cart->id_address_invoice, $address->id);
                }

                $ppec->context->cart->id_guest = $ppec->context->cookie->id_guest;
            }

            if (!$ppec->context->cart->update()) {
                $ppec->logs[] = $ppec->l('Cannot update existing cart');
            } else {
                $payment_cart = (bool) ($ppec->type != 'payment_cart');
                $ppec->redirectToCheckout($customer, $payment_cart);
            }
        }
    }
}
/**
 * Check payment return
 */
function validateOrder($customer, $cart, $ppec)
{
    $amount_match = $ppec->rightPaymentProcess();
    // Payment succeed
    if ($ppec->hasSucceedRequest() && !empty($ppec->token) && $amount_match) {
        if ((bool) Configuration::get('PAYPAL_CAPTURE')) {
            $payment_type = (int) Configuration::get('PS_OS_PAYPAL');
            $payment_status = 'Pending_capture';
            $message = $ppec->l('Pending payment capture.').'<br />';
        } else {
            if (isset($ppec->result['PAYMENTINFO_0_PAYMENTSTATUS'])) {
                $payment_status = $ppec->result['PAYMENTINFO_0_PAYMENTSTATUS'];
            } else {
                $payment_status = 'Error';
            }

            if ((strcasecmp($payment_status, 'Completed') === 0) || (strcasecmp($payment_status, 'Completed_Funds_Held') === 0)) {
                $payment_type = (int) Configuration::get('PS_OS_PAYMENT');
                $message = $ppec->l('Payment accepted.').'<br />';
            } elseif (Tools::getValue('banktxnpendingurl') && Tools::getValue('banktxnpendingurl') == 'true') {
                $payment_type = (int) Configuration::get('PS_OS_PAYPAL');
                $message = $ppec->l('eCheck').'<br />';
            } elseif (strcasecmp($payment_status, 'Pending') === 0) {
                $payment_type = (int) Configuration::get('PS_OS_PAYPAL');
                $message = $ppec->l('Pending payment confirmation.').'<br />';
            }
        }
    } else {
        // Payment error
        //Check if error is 10486, if it is redirect user to paypal
        if ($ppec->result['L_ERRORCODE0'] == 10486) {
            $ppec->redirectToAPI();
        }

        $payment_status = isset($ppec->result['PAYMENTINFO_0_PAYMENTSTATUS']) ? $ppec->result['PAYMENTINFO_0_PAYMENTSTATUS'] : false;
        $payment_type = (int) Configuration::get('PS_OS_ERROR');

        if ($amount_match) {
            $message = implode('<br />', $ppec->logs).'<br />';
        } else {
            $message = $ppec->l('Price paid on paypal is not the same that on PrestaShop.').'<br />';
        }

    }

    $transaction = PayPalOrder::getTransactionDetails($ppec, $payment_status);
    $ppec->context->cookie->id_cart = $cart->id;

    $ppec->validateOrder(
        (int) $cart->id,
        $payment_type,
        $transaction['total_paid'],
        $ppec->displayName,
        $message,
        $transaction,
        (int) $cart->id_currency,
        false,
        $customer->secure_key,
        $ppec->context->shop
    );
}
/* If Previous steps succeed, ready (means 'ready to pay') will be set to true */
if ($ppec->ready && !empty($ppec->token) && (Tools::isSubmit('confirmation') || $ppec->type == 'payment_cart')) {
    /* Check modification on the product cart / quantity */
    if ($ppec->isProductsListStillRight()) {
        $cart = $ppec->context->cart;
        $customer = new Customer((int) $cart->id_customer);

        // When all information are checked before, we can validate the payment to paypal
        // and create the prestashop order
        $ppec->doExpressCheckout();

        if (isset($ppec->result['RedirectRequired']) && $ppec->result['RedirectRequired'] == 'true') {
            $ppec->redirectToAPI();
        }

        validateOrder($customer, $cart, $ppec);

        unset($ppec->context->cookie->{PaypalExpressCheckout::$cookie_name});

        if (!$ppec->currentOrder) {
            $ppec->logs[] = $ppec->l('Cannot create order');
        } else {
            $id_order = (int) $ppec->currentOrder;
            $order = new Order($id_order);
        }

        /* Check payment details to display the appropriate content */
        if (isset($order) && ($ppec->result['ACK'] != 'Failure')) {
            $values = array(
                'key' => $customer->secure_key,
                'id_module' => (int) $ppec->id,
                'id_cart' => (int) $cart->id,
                'id_order' => (int) $ppec->currentOrder,
            );

            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                $query = http_build_query($values, '', '&');
                Tools::redirectLink(_MODULE_DIR_.$ppec->name.'/express_checkout/payment.php?'.$query);
            } else {
                $link = $ppec->context->link->getModuleLink('paypal', 'submit', $values);
                Tools::redirect($link);
            }
        } elseif ($ppec->result['ACK'] != 'Failure') {
            $ppec->context->smarty->assign(array(
                'logs' => $ppec->logs,
                'message' => $ppec->l('Error occurred:'),
            ));

            $template = 'error.tpl';
        }
    } else {
        /* If Cart changed, no need to keep the paypal data */
        unset($ppec->context->cookie->{PaypalExpressCheckout::$cookie_name});
        $ppec->logs[] = $ppec->l('Cart changed since the last checkout express, please make a new Paypal checkout payment');
    }
}

$display = (_PS_VERSION_ < '1.5') ? new BWDisplay() : new FrontController();
$payment_confirmation = Tools::getValue('get_confirmation');

if ($ppec->ready && $payment_confirmation && (_PS_VERSION_ < '1.5')) {
    $shop_domain = PayPal::getShopDomainSsl(true, true);
    $form_action = $shop_domain._MODULE_DIR_.$ppec->name.'/express_checkout/payment.php';

    $ppec->assignCartSummary();
    $ppec->context->smarty->assign(array(
        'form_action' => $form_action,
    ));

    $template = 'order-summary.tpl';
} else {
    /* Display result if error occurred */
    if (!$ppec->context->cart->id) {
        $ppec->context->cart->delete();
        $ppec->logs[] = $ppec->l('Your cart is empty.');
    }
    $ppec->context->smarty->assign(array(
        'logs' => $ppec->logs,
        'message' => $ppec->l('Error occurred:'),
    ));

    $template = 'error.tpl';
}

/**
 * Detect if we are using mobile or not
 * Check the 'ps_mobile_site' parameter.
 */
$ppec->context->smarty->assign('use_mobile', (bool) $ppec->useMobile());

$display->setTemplate(_PS_MODULE_DIR_.'paypal/views/templates/front/'.$template);
$display->run();
