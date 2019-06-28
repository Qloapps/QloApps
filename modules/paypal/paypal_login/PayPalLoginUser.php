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

if (!defined('_PS_VERSION_')) {
    die(header('HTTP/1.0 404 Not Found'));
}

/**
 * Description of totRules
 *
 * @author 202-ecommerce
 */
class PaypalLoginUser extends ObjectModel
{

    public $id_customer;
    public $token_type;
    public $expires_in;
    public $refresh_token;
    public $id_token;
    public $access_token;
    public $account_type;
    public $user_id;
    public $verified_account;
    public $zoneinfo;
    public $age_range;

    protected $table = 'paypal_login_user';
    protected $identifier = 'id_paypal_login_user';
    protected $fieldsRequired = array(
        'id_customer',
        'token_type',
        'expires_in',
        'refresh_token',
        'id_token',
        'access_token',
        'user_id',
        'verified_account',
        'zoneinfo',
    );

    protected $fieldsValidate = array(
        'id_customer' => 'isInt',
        'token_type' => 'isString',
        'expires_in' => 'isString',
        'refresh_token' => 'isString',
        'id_token' => 'isString',
        'access_token' => 'isString',
        'account_type' => 'isString',
        'user_id' => 'isString',
        'verified_account' => 'isString',
        'zoneinfo' => 'isString',
        'age_range' => 'isString',

    );

    public function __construct($id = false, $id_lang = false)
    {
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();
        $fields = array();
        foreach (array_keys($this->fieldsValidate) as $field) {
            $fields[$field] = $this->$field;
        }

        return $fields;
    }

    public static function getPaypalLoginUsers($id_paypal_login_user = false, $id_customer = false, $refresh_token = false)
    {
        $sql = "
			SELECT `id_paypal_login_user`
			FROM `"._DB_PREFIX_."paypal_login_user`
			WHERE 1
		";

        if ($id_paypal_login_user && Validate::isInt($id_paypal_login_user)) {
            $sql .= " AND `id_paypal_login_user` = '".(int) $id_paypal_login_user."' ";
        }

        if ($id_customer && Validate::isInt($id_customer)) {
            $sql .= " AND `id_customer` = '".(int) $id_customer."' ";
        }

        if ($refresh_token) {
            $sql .= " AND `refresh_token` = '".pSQL($refresh_token)."' ";
        }

        $results = DB::getInstance()->executeS($sql);
        $logins = array();

        if ($results && count($results)) {
            foreach ($results as $result) {
                $logins[$result['id_paypal_login_user']] = new PaypalLoginUser((int) $result['id_paypal_login_user']);
            }

        }

        return $logins;
    }

    public static function getByIdCustomer($id_customer)
    {
        $login = self::getPaypalLoginUsers(false, $id_customer);

        if ($login && count($login)) {
            $login = current($login);
        } else {
            $login = false;
        }

        return $login;
    }
}
