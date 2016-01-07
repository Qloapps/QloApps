<?php

class  WkPaypalAdaptivePaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;

        parent::initContent();

        if ($this->context->customer->id) 
        {
            if ($this->module->active) 
            {
                // paypal api start
                $sandbox = '';
                $API_AppID = Configuration::get('APP_ID');
                $sandboxstatus = Configuration::get('sandboxstatus');

                if ($sandboxstatus == 1) {
                    $sandbox = 'sandbox.';
                    $API_AppID = Configuration::get('APP_ID');
                }

                $id_currency = $this->context->cart->id_currency;
                $obj_currency = new Currency($id_currency);
                $currency_iso_code = $obj_currency->iso_code;

                $url = trim('https://svcs.'.$sandbox.'paypal.com/AdaptivePayments/Pay');

                $API_UserName = Configuration::get('APP_USERNAME');
                $API_Password = Configuration::get('APP_PASSWORD');
                $API_Signature = Configuration::get('APP_SIGNATURE');
                $API_RequestFormat = 'NV';
                $API_ResponseFormat = 'NV';

                $success_page = $this->context->link->getModuleLink('wkpaypaladaptive', 'success');

                $admin_paypal_email = Configuration::get('PAYPAL_EMAIL');

                if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT')) 
				{
					$obj_customer_adv = new HotelCustomerAdvancedPayment();
					$order_total = $obj_customer_adv->getOrdertTotal($this->context->cart->id, $this->context->cart->id_guest);
				}
				else
					$order_total = $cart->getOrderTotal(true, Cart::BOTH);

                $bodyparams = array(
                    'requestEnvelope.errorLanguage' => 'en_US',
                    'actionType' => 'PAY',
                    'currencyCode' => $currency_iso_code,
                    'cancelUrl' => $this->context->link->getModuleLink('wkpaypaladaptive', 'cancelurl'),
                    'returnUrl' => $success_page,
                    'senderOptions.referrerCode' => 'Webkul_SP',
                    'ipnNotificationUrl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/wkpaypaladaptive/notify.php',
                );

		        
                $temp = array("receiverList.receiver(0).email" => $admin_paypal_email,
                              "receiverList.receiver(0).amount" => $order_total, 
                            );

                $bodyparams += $temp;

                $body_data = http_build_query($bodyparams, '', chr(38));
                try 
                {
                    $params = array('http' => array(
                            'method' => 'POST',
                            'content' => $body_data,
                            'header' => 'X-PAYPAL-SECURITY-USERID: '.$API_UserName."\r\n".
                                        'X-PAYPAL-SECURITY-SIGNATURE: '.$API_Signature."\r\n".'X-PAYPAL-SECURITY-PASSWORD: '.$API_Password."\r\n".'X-PAYPAL-APPLICATION-ID: '.$API_AppID."\r\n".
                                        'X-PAYPAL-REQUEST-DATA-FORMAT: '.$API_RequestFormat."\r\n".
                                        'X-PAYPAL-RESPONSE-DATA-FORMAT: '.$API_ResponseFormat."\r\n".
                                        "\r\n",
                            ));

                    $ctx = stream_context_create($params);
                    $fp = @fopen($url, 'r', false, $ctx);
                    $response = stream_get_contents($fp);

                    if ($response === false) {
                        throw new Exception('php error message');
                    }

                    fclose($fp);

                    $keyArray = explode('&', $response);
                    $kArray = array();
                    foreach ($keyArray as $rVal) {
                        list($qKey, $qVal) = explode('=', $rVal);
                        $kArray[$qKey] = $qVal;
                    }

                    if (!empty($kArray)) 
                    {
                        if ($kArray['responseEnvelope.ack'] == 'Success') 
                        {
                            $payPalURL = 'https://www.'.$sandbox.'paypal.com/webscr?cmd=_ap-payment&paykey='.$kArray['payKey'];
                            $this->context->smarty->assign('payPalURL', $payPalURL);
                            $this->setTemplate('redirect_pay.tpl');
                        } 
                        else 
                        {
                            $this->context->smarty->assign('error_code', $kArray['error(0).errorId']);
                            $this->context->smarty->assign('error_msg', urldecode($kArray['error(0).message']));
                            $this->setTemplate('paypal_error.tpl');
                        }
                    }
                } 
                catch (Exception $e) 
                {
                    echo 'Message: ||'.$e->getMessage().'||';
                }
            }
        }
        else 
        {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }
}
