<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class CustomerGuestDetailCore extends ObjectModel
{
    public $id_customer_guest_detail;
    public $id_customer;
    public $id_gender;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'customer_guest_detail',
        'primary' => 'id_customer_guest_detail',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_gender' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );


    /**
     * Get the customer guest id by id cart.
     *
     * @param int id_cart
     * @return int|false id_customer_guest_detail if exists
     */
    public static function getCustomerGuestIdByIdCart($id_cart)
    {
        return Db::getInstance()->getValue('
            SELECT `id_customer_guest_detail`
            FROM `'._DB_PREFIX_.'cart_customer_guest`
            WHERE `id_cart` = '.(int) $id_cart
        );
    }

    /**
     * set the customer guest id for the id cart.
     *
     * @param int id_cart
     * @param int id_customer_guest_detail
     * @return bool True on success, false on failure
     */
    public function saveCustomerGuestInCart($idCart, $idCustomerGuestDetail)
    {
        return Db::getInstance()->insert(
            'cart_customer_guest',
            array(
                'id_cart' => (int) $idCart,
                'id_customer_guest_detail' => (int) $idCustomerGuestDetail
            )
        );
    }

    /**
     * removes the customer guest id for the id cart.
     *
     * @param int id_cart
     * @return bool True on success, false on failure
     */
    public static function deleteCustomerGuestInCart($idCart)
    {
        return Db::getInstance()->delete(
            'cart_customer_guest',
            ' `id_cart`='.(int) $idCart
        );
    }

    /**
     * Removes this guest's ID from all related cart entries.
     *
     * @return bool True on success, false on failure
     */
    public function deleteCustomerGuestFromCart()
    {
        return Db::getInstance()->delete(
            'cart_customer_guest',
            '`id_customer_guest_detail` = ' . (int) $this->id
        );
    }

    public function delete()
    {
        $this->deleteCustomerGuestFromCart();

        return parent::delete();
    }

    /**
     * Deletes the guest profiles created by a customer.
     *
     * @param int $idCustomer Customer ID
     * @param int $offset     Number of guests to skip
     * @return bool True on success, false on failure
     */
    public function deleteCustomerGuestByIdCustomer($idCustomer, $offset = 0)
    {
        $guests = $this->getCustomerGuestsByIdCustomer((int)$idCustomer);
        if (empty($guests)) {
            return true;
        }

        $guestsToDelete = array_slice($guests, $offset);
        if (empty($guestsToDelete)) {
            return true;
        }

        $res = true;
        foreach ($guestsToDelete as $guest) {
            $customerGuestDetail = new CustomerGuestDetail($guest['id_customer_guest_detail']);
            $res &= $customerGuestDetail->delete();
        }

        return $res;
    }

    /**
     * Retrieves guest profiles created by a customer using the "booking for someone else" feature.
     *
     * @param int $idCustomer The customer ID
     * @param string $firstname Optional first name filter
     * @param string $lastname Optional last name filter
     * @param string $email Optional email filter
     * @return array List of matching guest profiles
     */
    public function getCustomerGuestsByIdCustomer($idCustomer, $firstname = '', $lastname = '', $email = '')
    {
        $sql = 'SELECT cgd.`id_customer_guest_detail`, cgd.`email`, cgd.`firstname`, cgd.`lastname`, cgd.`phone`
            FROM `' . _DB_PREFIX_ . 'customer_guest_detail` cgd
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_customer_guest` ccg
                ON ccg.`id_customer_guest_detail` = cgd.`id_customer_guest_detail`
            WHERE cgd.`id_customer` = '.(int) $idCustomer;

        if ($firstname !== '') {
            $sql .= ' AND cgd.`firstname` LIKE "%'.pSQL($firstname).'%"';
        }

        if ($lastname !== '') {
            $sql .= ' AND cgd.`lastname` LIKE "%'.pSQL($lastname).'%"';
        }

        if ($email !== '') {
            $sql .= ' AND cgd.`email` LIKE "%'.pSQL($email).'%"';
        }

        $sql .= ' GROUP BY cgd.`id_customer_guest_detail` ORDER BY cgd.`date_add` DESC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Retrieves guest information using the Id.
     *
     * @param int id_customer_guest_detail
     * @return array customer guest details.
     */
    public static function getCustomerGuestDetailById($idCustomerGuestDetail)
    {
        return Db::getInstance()->getRow('
            SELECT `id_gender`, `firstname`, `lastname`, `email`, `phone`
            FROM `'._DB_PREFIX_.'customer_guest_detail`
            WHERE `id_customer_guest_detail` = '.(int) $idCustomerGuestDetail
        );
    }

    /**
     * Retrieves guest information using the email.
     *
     * @param int email
     * @return int|false id customer guest details.
     */
    public static function getCustomerGuestByEmail($email, $idCustomer = null, $idCart = 0)
    {
        return Db::getInstance()->getValue(
            'SELECT cgd.`id_customer_guest_detail` FROM `'._DB_PREFIX_.'customer_guest_detail` as cgd
            LEFT JOIN `'._DB_PREFIX_.'cart_customer_guest` ccg
            ON ccg.`id_customer_guest_detail` = cgd.`id_customer_guest_detail`
            WHERE cgd.`email` = "'.pSQL($email).'"'.
            (!is_null($idCart) ? ' AND (ccg.`id_cart` = '.(int) $idCart.' '. (($idCart) ? ')'  : ' OR ISNULL(ccg.`id_cart`)) ') : ' ').
            (!is_null($idCustomer) ? ' AND cgd.`id_customer` = '.(int) $idCustomer : ' ')
        );
    }

    /**
     * Validates the Guest profile details.
     *
     * @return bool return true or false after validating the guest profile.
     */
    public function validateGuestInfo()
    {
        $isValid = true;
        if (!trim($this->firstname) || !Validate::isName($this->firstname)) {
            $isValid = false;
        }
        if (!trim($this->lastname) || !Validate::isName($this->lastname)) {
            $isValid = false;
        }
        if (!trim($this->email) || !Validate::isEmail($this->email)) {
            $isValid = false;
        }
        if (!trim($this->phone) || !Validate::isPhoneNumber($this->phone)) {
            $isValid = false;
        }

        $className = 'CustomerGuestDetail';
        $rules = call_user_func(array($className, 'getValidationRules'), $className);

        if (isset($rules['size']['firstname'])) {
            if (Tools::strlen(trim($this->firstname)) > $rules['size']['firstname']) {
                $isValid = false;
            }
        }
        if (isset($rules['size']['lastname'])) {
            if (Tools::strlen(trim($this->lastname)) > $rules['size']['lastname']) {
                $isValid = false;
            }
        }
        if (isset($rules['size']['email'])) {
            if (Tools::strlen(trim($this->email)) > $rules['size']['email']) {
                $isValid = false;
            }
        }
        if (isset($rules['size']['phone'])) {
            if (Tools::strlen(trim($this->phone)) > $rules['size']['phone']) {
                $isValid = false;
            }
        }

        return $isValid;
    }
}
