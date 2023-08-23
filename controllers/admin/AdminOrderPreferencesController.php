<?php
/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property Configuration $object
 */
class AdminOrderPreferencesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';

        parent::__construct();

        // List of CMS tabs
        $cms_tab = array(0 => array(
            'id' => 0,
            'name' => $this->l('None')
        ));
        foreach (CMS::listCms($this->context->language->id) as $cms_file) {
            $cms_tab[] = array('id' => $cms_file['id_cms'], 'name' => $cms_file['meta_title']);
        }

        // List of order process types
        $order_process_type = array(
            array(
                'value' => PS_ORDER_PROCESS_STANDARD,
                'name' => $this->l('Standard (Five steps)')
            ),
            array(
                'value' => PS_ORDER_PROCESS_OPC,
                'name' => $this->l('One-page checkout')
            )
        );

        $this->fields_options = array(
            'order_restrict' => array(
                'title' => $this->l('Order Restrict'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'MAX_GLOBAL_BOOKING_DATE' => array(
                        'title' => $this->l('Maximum Global Check-out Date to book a room'),
                        'hint' => $this->l('Maximum date of check-out by which rooms of your hotels can be booked.'),
                        'type' => 'text',
                        'id' => 'max_global_book_date',
                        'class' => 'fixed-width-xxl readonly',
                    ),
                    'GLOBAL_PREPARATION_TIME' => array(
                        'title' => $this->l('Preparation time'),
                        'hint' => $this->l('Number of days required to prepare a room for a guest\'s stay. For example, if you set this value to 3 and someone is booking on 2nd of March he can only book rooms for dates from and after 3 days, i.e, 5th of March.'),
                        'desc' => $this->l('Set to 0 to disable this feature.'),
                        'type' => 'text',
                        'class' => 'fixed-width-xl',
                        'suffix' => $this->l('day(s)'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
            'general' => array(
                'title' =>    $this->l('General'),
                'icon' =>    'icon-cogs',
                'fields' =>    array(
                   /* 'PS_ORDER_PROCESS_TYPE' => array(
                        'title' => $this->l('Order process type'),
                        'hint' => $this->l('Please choose either the five-step or one-page checkout process.'),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'hidden',
                        'list' => $order_process_type,
                        'value' => PS_ORDER_PROCESS_OPC,
                        'identifier' => 'value',
                    ),*/
                    'PS_GUEST_CHECKOUT_ENABLED' => array(
                        'title' => $this->l('Enable guest checkout'),
                        'hint' => $this->l('Allow guest visitors to place an order without registration.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    /*'PS_DISALLOW_HISTORY_REORDERING' => array(
                        'title' => $this->l('Disable Reordering Option'),
                        'hint' => $this->l('Disable the option to allow customers to reorder in one click from the order history page (required in some European countries).'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),*/
                    'PS_PURCHASE_MINIMUM' => array(
                        'title' => $this->l('Minimum purchase total required in order to validate the order'),
                        'hint' => $this->l('Set to 0 to disable this feature.'),
                        'validation' => 'isFloat',
                        'cast' => 'floatval',
                        'type' => 'price'
                    ),
                   /* 'PS_ALLOW_MULTISHIPPING' => array(
                        'title' => $this->l('Allow multishipping'),
                        'hint' => $this->l('Allow the customer to ship orders to multiple addresses. This option will convert the customer\'s cart into one or more orders.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),*/
                    /*'PS_SHIP_WHEN_AVAILABLE' => array(
                        'title' => $this->l('Delayed shipping'),
                        'hint' => $this->l('Allows you to delay shipping at your customers\' request. '),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),*/
                    'PS_CONDITIONS' => array(
                        'title' => $this->l('Terms of service'),
                        'hint' => $this->l('Require customers to accept or decline terms of service before processing an order.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'js' => array(
                            'on' => 'onchange="changeCMSActivationAuthorization()"',
                            'off' => 'onchange="changeCMSActivationAuthorization()"'
                        )
                    ),
                    'PS_CONDITIONS_CMS_ID' => array(
                        'title' => $this->l('CMS page for Terms and Conditions'),
                        'hint' => $this->l('Choose the CMS page which contains your website\'s Terms and Conditions.'),
                        'validation' => 'isInt',
                        'type' => 'select',
                        'list' => $cms_tab,
                        'identifier' => 'id',
                        'cast' => 'intval'
                    ),
                    'PS_ROOM_PRICE_AUTO_ADD_BREAKDOWN' => array(
                        'title' => $this->l('Show room price breakdown'),
                        'hint' => $this->l('Show price breakdown for rooms with auto added services in booking cart rooms list.'),
                        'desc' => $this->l('This room price breakdown will always be displayed for rooms having cart rules applied to them.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'email' => array(
                'title' =>    $this->l('Email'),
                'icon' =>    'icon-cogs',
                'fields' =>    array(
                    'PS_ORDER_CONF_MAIL_TO_CUSTOMER' => array(
                        'title' => $this->l('Order confirmation mail to customer'),
                        'hint' => $this->l('Enable, if you want to send order confirmation email to the customer.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ORDER_CONF_MAIL_TO_SUPERADMIN' => array(
                        'title' => $this->l('Order confirmation mail to super admin'),
                        'hint' => $this->l('Enable, if you want to send order confirmation email to the super admin.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ORDER_CONF_MAIL_TO_HOTEL_MANAGER' => array(
                        'title' => $this->l('Order confirmation mail to hotelier'),
                        'hint' => $this->l('Enable, if you want to send order confirmation email to the hotelier. Mail will be sent to the email saved while creating the hotel.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ORDER_CONF_MAIL_TO_EMPLOYEE' => array(
                        'title' => $this->l('Order confirmation mail to employees'),
                        'hint' => $this->l('Enable, if you want to send order confirmation email to employees having permission to manage the hotel.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        if (!Configuration::get('PS_ALLOW_MULTISHIPPING')) {
            unset($this->fields_options['general']['fields']['PS_ALLOW_MULTISHIPPING']);
        }

        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            unset($this->fields_options['gift']['fields']['PS_GIFT_WRAPPING_TAX_RULES_GROUP']);
        }
    }

    /**
     * This method is called before we start to update options configuration
     */
    public function beforeUpdateOptions()
    {
        $sql = 'SELECT `id_cms` FROM `'._DB_PREFIX_.'cms`
        WHERE id_cms = '.(int)Tools::getValue('PS_CONDITIONS_CMS_ID');
        if (Tools::getValue('PS_CONDITIONS') && (Tools::getValue('PS_CONDITIONS_CMS_ID') == 0 || !Db::getInstance()->getValue($sql))) {
            $this->errors[] = Tools::displayError('Please assign a valid CMS page for Terms and Conditions.');
        }

        $maxGlobalBookingDate = Tools::getValue('MAX_GLOBAL_BOOKING_DATE');
        $globalPreparationTime = Tools::getValue('GLOBAL_PREPARATION_TIME');
        $maxGlobalBookingDateFormatted = date('Y-m-d', strtotime($maxGlobalBookingDate));

        if ($maxGlobalBookingDate == '') {
            $this->errors[] = Tools::displayError('Field \'Maximum Global Check-out Date to book a room\' can not be empty.');
        } elseif (!Validate::isDate($maxGlobalBookingDateFormatted)) {
            $this->errors[] = Tools::displayError('Field \'Maximum Global Check-out Date to book a room\' is invalid.');
        } elseif (strtotime($maxGlobalBookingDateFormatted) < strtotime(date('Y-m-d'))) {
            $this->errors[] = Tools::displayError('Field \'Maximum Global Check-out Date to book a room\' can not be a past date. Please use a future date.');
        }

        if ($globalPreparationTime === '') {
            $this->errors[] = Tools::displayError('Field \'Preparation time\' can not be empty.');
        } elseif ($globalPreparationTime !== '0' && !Validate::isUnsignedInt($globalPreparationTime)) {
            $this->errors[] = Tools::displayError('Field \'Preparation time\' is invalid.');
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
        $this->addJS(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
    }
}
