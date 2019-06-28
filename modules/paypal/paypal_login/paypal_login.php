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

class PayPalLogin
{
    private $_logs = array();
    private $enable_log = false;

    private $paypal_connect = null;

    public function __construct()
    {
        $this->paypal_connect = new PayPalConnect();
    }

    public function getIdentityAPIURL()
    {
        if (Configuration::get('PAYPAL_SANDBOX')) {
            //return 'www.sandbox.paypal.com';
            return 'api.sandbox.paypal.com';
        } else {
            return 'api.paypal.com';
        }

    }

    public function getTokenServiceEndpoint()
    {
        if (Configuration::get('PAYPAL_SANDBOX')) {
            // return '/webapps/auth/protocol/openidconnect/v1/tokenservice';
            return '/v1/identity/openidconnect/tokenservice';
        } else {
            return '/v1/identity/openidconnect/tokenservice';
        }

    }

    public function getUserInfoEndpoint()
    {
        return '/v1/identity/openidconnect/userinfo';
    }

    public static function getReturnLink()
    {
        // return 'http://requestb.in/1jlaizq1';
        if (method_exists(Context::getContext()->shop, 'getBaseUrl')) {
            return Context::getContext()->shop->getBaseUrl().'modules/paypal/paypal_login/paypal_login_token.php';
        } else {
            return 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/modules/paypal/paypal_login/paypal_login_token.php';
        }

    }

    public function getAuthorizationCode()
    {
        unset($this->_logs);

        $context = Context::getContext();
        $is_logged = (method_exists($context->customer, 'isLogged') ? $context->customer->isLogged() : $context->cookie->isLogged());

        if ($is_logged) {
            return $this->getRefreshToken();
        }

        $params = array(
            'grant_type' => 'authorization_code',
            'code' => Tools::getValue('code'),
            'redirect_url' => PayPalLogin::getReturnLink(),
        );

        $request = http_build_query($params, '', '&');
        $result = $this->paypal_connect->makeConnection($this->getIdentityAPIURL(), $this->getTokenServiceEndpoint(), $request, false, false, true);

        /*
        if ($this->enable_log === true) {
            $handle = fopen(dirname(__FILE__).'/Results.txt', 'a+');
            fwrite($handle, "Request => ".print_r($request, true)."\r\n");
            fwrite($handle, "Result => ".print_r($result, true)."\r\n");
            fwrite($handle, "Journal => ".print_r($this->_logs, true."\r\n"));
            fclose($handle);
        }*/

        $result = Tools::jsonDecode($result);

        if ($result) {

            $login = new PayPalLoginUser();

            $customer = $this->getUserInformations($result->access_token, $login);

            if (!$customer) {
                return false;
            }

            $temp = PaypalLoginUser::getByIdCustomer((int) $context->customer->id);

            if ($temp) {
                $login = $temp;
            }

            $login->id_customer = $customer->id;
            $login->token_type = $result->token_type;
            $login->expires_in = (string) (time() + (int) $result->expires_in);
            $login->refresh_token = $result->refresh_token;
            $login->id_token = $result->id_token;
            $login->access_token = $result->access_token;

            $login->save();

            return $login;
        }
    }

    public function getRefreshToken()
    {
        unset($this->_logs);
        $login = PaypalLoginUser::getByIdCustomer((int) Context::getContext()->customer->id);

        if (!is_object($login)) {
            return false;
        }

        $params = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $login->refresh_token,
        );

        $request = http_build_query($params, '', '&');
        $result = $this->paypal_connect->makeConnection($this->getIdentityAPIURL(), $this->getTokenServiceEndpoint(), $request, false, false, true);

        /*
        if ($this->enable_log === true) {
            $handle = fopen(dirname(__FILE__).'/Results.txt', 'a+');
            fwrite($handle, "Request => ".print_r($request, true)."\r\n");
            fwrite($handle, "Result => ".print_r($result, true)."\r\n");
            fwrite($handle, "Journal => ".print_r($this->_logs, true."\r\n"));
            fclose($handle);
        }
        */

        $result = Tools::jsonDecode($result);

        if ($result) {
            $login->access_token = $result->access_token;
            $login->expires_in = (string) (time() + $result->expires_in);
            $login->save();
            return $login;
        }

        return false;
    }

    private function getUserInformations($access_token, &$login)
    {
        unset($this->_logs);
        $headers = array(
            // 'Content-Type:application/json',
            'Authorization: Bearer '.$access_token,
        );

        $params = array(
            'schema' => 'openid',
        );

        $request = http_build_query($params, '', '&');
        $result = $this->paypal_connect->makeConnection($this->getIdentityAPIURL(), $this->getUserInfoEndpoint(), $request, false, $headers, true);

        /*
        if ($this->enable_log === true) {
            $handle = fopen(dirname(__FILE__).'/Results.txt', 'a+');
            fwrite($handle, "Request => ".print_r($request, true)."\r\n");
            fwrite($handle, "Result => ".print_r($result, true)."\r\n");
            fwrite($handle, "Headers => ".print_r($headers, true)."\r\n");
            fwrite($handle, "Journal => ".print_r($this->_logs, true."\r\n"));
            fclose($handle);
        }
        */

        $result = Tools::jsonDecode($result);

        if ($result) {
            $customer = new Customer();
            $customer = $customer->getByEmail($result->email);

            if (!$customer) {
                $customer = $this->setCustomer($result);
            }

            $login->account_type = $result->account_type;
            $login->user_id = $result->user_id;
            $login->verified_account = $result->verified_account;
            $login->zoneinfo = $result->zoneinfo;
            $login->age_range = $result->age_range;

            return $customer;
        }

        return false;
    }

    private function setCustomer($result)
    {
        $customer = new Customer();
        $customer->firstname = $result->given_name;
        $customer->lastname = $result->family_name;
        if (version_compare(_PS_VERSION_, '1.5.3.1', '>')) {
            $customer->id_lang = Language::getIdByIso(strstr($result->language, '_', true));
        }

        $customer->birthday = $result->birthday;
        $customer->email = $result->email;
        $customer->passwd = Tools::encrypt(Tools::passwdGen());
        $customer->save();

        $result_address = $result->address;

        $address = new Address();
        $address->id_customer = $customer->id;
        $address->id_country = Country::getByIso($result_address->country);
        $address->alias = 'My address';
        $address->lastname = $customer->lastname;
        $address->firstname = $customer->firstname;
        $address->address1 = $result_address->street_address;
        $address->postcode = $result_address->postal_code;
        $address->city = $result_address->locality;
        $address->phone = $result->phone_number;

        $address->save();

        return $customer;
    }
}
