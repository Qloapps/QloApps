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

        // actions when overbooking will be created
        $overbookingAction = array(
            array(
                'value' => Order::OVERBOOKING_ORDER_CANCEL_ACTION,
                'name' => $this->l('Cancel the booking')
            ),
            array(
                'value' => Order::OVERBOOKING_ORDER_NO_ACTION,
                'name' => $this->l('Take the overbooking')
            )
        );

        // Options to display currency in order list
        $displayCurrencyOptions = array(
            array(
                'value' => Order::ORDER_LIST_PRICE_DISPLAY_IN_PAYMENT_CURRENCY,
                'name' => $this->l('Order currency')
            ),
            array(
                'value' => Order::ORDER_LIST_PRICE_DISPLAY_IN_DEFAULT_CURRENCY,
                'name' => $this->l('Default currency')
            )
        );

        $this->fields_options = array(
            'order_restrict' => array(
                'title' => $this->l('Order Restrict'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'PS_MAX_CHECKOUT_OFFSET' => array(
                        'title' => $this->l('Maximum checkout offset'),
                        'hint' => $this->l('The maximum checkout offset defines how many days from today checkout is allowed. For example, if this value is set to 10 and someone is booking on March 1st, they can only select a checkout date up to March 11th.'),
                        'type' => 'text',
                        'class' => 'fixed-width-xl',
                        'suffix' => $this->l('day(s)'),
                    ),
                    'PS_MIN_BOOKING_OFFSET' => array(
                        'title' => $this->l('Minimum booking offset'),
                        'hint' => $this->l('The minimum booking offset is the minimum number of days before the check-in date that a guest must book a room. For example, if you set this value to 3 and someone is booking on 2nd of March he can only book rooms for dates from and after 3 days, i.e, 5th of March.'),
                        'desc' => $this->l('Set to 0 to disable this feature.'),
                        'type' => 'text',
                        'class' => 'fixed-width-xl',
                        'suffix' => $this->l('day(s)'),
                    ),
                    'PS_BACKDATE_ORDER_SUPERADMIN' => array(
                        'title' => $this->l('Allow backdate order from Back-office for super-admin'),
                        'hint' => $this->l('Allow superadmin to create bookings for backdate'),
                        'type' => 'bool',
                    ),
                    'PS_BACKDATE_ORDER_EMPLOYEES' => array(
                        'title' => $this->l('Allow backdate order from Back-office for employees'),
                        'hint' => $this->l('Allow employees to create bookings for backdate'),
                        'type' => 'bool',
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
                        'hint' => $this->l('Show price breakdown for rooms with auto added services on checkout page.'),
                        'desc' => $this->l('Displays a price breakdown for rooms that have auto-added services during checkout.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ORDER_LIST_PRICE_DISPLAY_CURRENCY' => array(
                        'title' => $this->l('Display order list prices in Back Office'),
                        'hint' => $this->l('Choose the currency in which you want the prices in the order list to be displayed.'),
                        'desc' => $this->l('\'Order currency\' is the currency in which customer created the order and \'Default currency\' is the currency configured in localization.'),
                        'validation' => 'isInt',
                        'type' => 'select',
                        'cast' => 'intval',
                        'list' => $displayCurrencyOptions,
                        'identifier' => 'value',
                    ),
                    'PS_ORDER_KPI_AVG_ORDER_VALUE_NB_DAYS' => array(
                        'title' => $this->l('Days for Average Order Value KPI'),
                        'hint' => $this->l('Set for last how many days, the \'Average order value\' in the KPI to be calculated.'),
                        'type' => 'text',
                        'validation' => 'isUnsignedInt',
                        'class' => 'fixed-width-xl',
                        'suffix' => $this->l('day(s)'),
                    ),
                    'PS_ORDER_KPI_PER_VISITOR_PROFIT_NB_DAYS' => array(
                        'title' => $this->l('Days for Net Profit Per Visitor KPI'),
                        'hint' => $this->l('Set for last how many days, the \'Net Profit per Visitor\' in the KPI to be calculated.'),
                        'type' => 'text',
                        'validation' => 'isUnsignedInt',
                        'class' => 'fixed-width-xl',
                        'suffix' => $this->l('day(s)'),
                    ),
                    'PS_ALLOW_ADD_ALL_SERVICES_IN_BOOKING' => array(
                        'title' => $this->l('Allow to add all services in a booking'),
                        'hint' => $this->l('Enable to allow all services created to be added in a booking while adding or editing a booking. Disable to allow only attached services with room type.'),
                        'desc' => $this->l('If disabled, only attached services with room type will be available to be added to a booking.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ALLOW_CREATE_CUSTOM_SERVICES_IN_BOOKING' => array(
                        'title' => $this->l('Allow to create custom service for a booking'),
                        'hint' => $this->l('Enable to allow to create custom service while adding or editing a booking.'),
                        'desc' => $this->l('If disabled, a custom service can not be created while adding or editing a booking.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'email' => array(
                'title' =>    $this->l('Order Confirmation / Overbooking Email'),
                'icon' =>    'icon-cogs',
                'fields' =>    array(
                    'PS_ORDER_CONF_MAIL_TO_CUSTOMER' => array(
                        'title' => $this->l('Send email to customer'),
                        'hint' => $this->l('Enable, if you want to send order confirmation or overbooking or overbooking email to the customer.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ORDER_CONF_MAIL_TO_SUPERADMIN' => array(
                        'title' => $this->l('Send email to super admin'),
                        'hint' => $this->l('Enable, if you want to send order confirmation or overbooking email to the super admin.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ORDER_CONF_MAIL_TO_HOTEL_MANAGER' => array(
                        'title' => $this->l('Send email to hotelier'),
                        'hint' => $this->l('Enable, if you want to send order confirmation or overbooking email to the hotelier. Mail will be sent to the email saved while creating the hotel.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ORDER_CONF_MAIL_TO_EMPLOYEE' => array(
                        'title' => $this->l('Send email to employees'),
                        'hint' => $this->l('Enable, if you want to send order confirmation or overbooking email to employees having permission to manage the hotel.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'overbooking' => array(
                'title' => $this->l('Overbooking'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'PS_OVERBOOKING_ORDER_ACTION' => array(
                        'title' => $this->l('Overbooking order action'),
                        'hint' => $this->l('Please choose the action to take when overbooking is created in any order.'),
                        'validation' => 'isInt',
                        'type' => 'select',
                        'cast' => 'intval',
                        'validation' => 'isInt',
                        'list' => $overbookingAction,
                        'identifier' => 'value',
                        'js' => "changeOverbookingOrderAction()"
                    ),
                    'PS_MAX_OVERBOOKING_PER_HOTEL_PER_DAY' => array(
                        'title' => $this->l('Maximun hotel overbookings per date'),
                        'hint' => $this->l('Enter the maximum number of the overbookings on any date can be created for a hotel.(Set 0 for no limit)'),
                        'desc' => $this->l('When maximun number of the overbookings on any date in the booking duration is reached, then the new overbooking order will be cancelled.(Set 0 for no limit)'),
                        'validation' => 'isInt',
                        'type' => 'text',
                        'class' => 'fixed-width-xxl',
                        'id' => 'PS_MAX_OVERBOOKING_PER_HOTEL_PER_DAY',
                    ),
                    'OVERBOOKING_ORDER_CANCEL_ACTION' => array(
                        'title' => $this->l('Overbooking order cancel action value'),
                        'type' => 'hidden',
                        'value' => Order::OVERBOOKING_ORDER_CANCEL_ACTION,
                        'auto_value' => false,
                    ),
                    'OVERBOOKING_ORDER_NO_ACTION' => array(
                        'title' => $this->l('Overbooking order no action value'),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'hidden',
                        'value' => Order::OVERBOOKING_ORDER_NO_ACTION,
                        'auto_value' => false
                    ),
                    'PS_OVERBOOKING_AUTO_RESOLVE' => array(
                        'title' => $this->l('Resolve overbooking automatically'),
                        'hint' => $this->l('Enable, if you want to resolve overbooking automatically when rooms are available to be replaced with overbooked rooms.'),
                        'desc' => $this->l('If enabled, the overbookings in the order will be resolved automatically when rooms are available to be replaced with all overbooked rooms in the order.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        if (Hook::exec('actionEnableStandartProductConfig')) {
            $addressPrefrenceTypes = array(
                array(
                    'value' => Product::STANDARD_PRODUCT_ADDRESS_PREFERENCE_CUSTOMER,
                    'name' => $this->l('Customer Address')
                ),
                array(
                    'value' => Product::STANDARD_PRODUCT_ADDRESS_PREFERENCE_HOTEL,
                    'name' => $this->l('Hotel Address')
                ),
                array(
                    'value' => Product::STANDARD_PRODUCT_ADDRESS_PREFERENCE_CUSTOM,
                    'name' => $this->l('Custom Address')
                )
            );

            $customAddressDetails = array();
            $objHotelBranch = new HotelBranchInformation();
            if ((($idCustomAddress = Configuration::get('PS_STANDARD_PRODUCT_ORDER_ADDRESS_ID'))
                    && Validate::isLoadedObject($objAddress = new Address($idCustomAddress))
                ) || (($primaryHotelId = Configuration::get('WK_PRIMARY_HOTEL'))
                    && ($idHotelAddresss = HotelBranchInformation::getAddress((int) $primaryHotelId))
                    && Validate::isLoadedObject($objAddress = new Address($idHotelAddresss['id_address']))
                ) || (($hotels = $objHotelBranch->hotelBranchesInfo(false, 1))
                    && ($idHotelAddresss = HotelBranchInformation::getAddress((int) $hotels[0]['id']))
                    && Validate::isLoadedObject($objAddress = new Address($idHotelAddresss))
            )) {
                $customAddressDetails = array(
                    'id_country' => $objAddress->id_country,
                    'id_state' => $objAddress->id_state,
                    'city' => $objAddress->city,
                    'postcode' => $objAddress->postcode,
                );

                $this->context->smarty->assign('custom_address_details', $customAddressDetails);
            }

            $countries = Country::getCountries($this->context->language->id);
            $this->context->smarty->assign('countries', $countries);
            $this->fields_options['standard_product'] = array(
                'title' => $this->l('Standard Product Address configuration'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'PS_STANDARD_PRODUCT_ORDER_ADDRESS_PREFRENCE' => array(
                        'title' => $this->l('Address for standard product tax calculation'),
                        'validation' => 'isInt',
                        'type' => 'select',
                        'cast' => 'intval',
                        'list' => $addressPrefrenceTypes,
                        'identifier' => 'value',
                        'js' => "changeStandardProductAddressType()",
                        'hint' => $this->l('Select address for standard product tax calculation, this will be used to calculate the tax for the standard product price.'),
                        'desc' => $this->l('The selected address will be used to calculate tax for standard products. To change the hotel address, update the "Primary Hotel" in the').
                        '<a href="'.$this->context->link->getAdminLink('AdminHotelGeneralSettings').'"> '.$this->l('Hotel General Configuration.').'</a>'
                    ),
                    'PS_STANDARD_PRODUCT_ORDER_ADDRESS' => array(
                        'title' => $this->l('Custom address for tax calculation'),
                        'hint' => $this->l('Set the custom address details for tax calculation'),
                        'type' => 'html',
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            );
        }
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

        $maxCheckoutOffset = Tools::getValue('PS_MAX_CHECKOUT_OFFSET');
        $minBookingOffset = Tools::getValue('PS_MIN_BOOKING_OFFSET');
        if ($maxCheckoutOffset === '') {
            $this->errors[] = Tools::displayError('Field \'Maximum checkout offset\' can not be empty.');
        } elseif (!$maxCheckoutOffset || !Validate::isUnsignedInt($maxCheckoutOffset)) {
            $this->errors[] = Tools::displayError('Field \'Maximum checkout offset\' is invalid.');
        }

        $avgOrderValueField = $this->fields_options['general']['fields']['PS_ORDER_KPI_AVG_ORDER_VALUE_NB_DAYS'];
        if (!Tools::getValue('PS_ORDER_KPI_AVG_ORDER_VALUE_NB_DAYS')) {
            $this->errors[] = sprintf(Tools::displayError('field %s must be greater than 0.'), $avgOrderValueField['title']);;
        }

        $netProfitField = $this->fields_options['general']['fields']['PS_ORDER_KPI_PER_VISITOR_PROFIT_NB_DAYS'];
        if (!Tools::getValue('PS_ORDER_KPI_PER_VISITOR_PROFIT_NB_DAYS')) {
            $this->errors[] = sprintf(Tools::displayError('field %s must be greater than 0.'), $netProfitField['title']);;
        }

        if ($minBookingOffset === '') {
            $this->errors[] = Tools::displayError('Field \'Minimum booking offset\' can not be empty.');
        } elseif ($minBookingOffset != 0 && !Validate::isUnsignedInt($minBookingOffset)) {
            $this->errors[] = Tools::displayError('Field \'Minimum booking offset\' is invalid.');
        }

        if ($maxCheckoutOffset && $maxCheckoutOffset <= $minBookingOffset) {
            $this->errors[] = Tools::displayError('Field \'Maximum checkout offset\' cannot be be less than or equal to \'Minimum booking offset\'.');
        }

        $idCountry = Tools::getValue('service_id_country');
        $idSate = Tools::getValue('service_id_state');
        $city = trim(Tools::getValue('service_city'));
        $postcode = trim(Tools::getValue('service_postcode'));
        $serviceAddressPrefrenceType = Tools::getValue('PS_STANDARD_PRODUCT_ORDER_ADDRESS_PREFRENCE');
        if ($serviceAddressPrefrenceType == Product::STANDARD_PRODUCT_ADDRESS_PREFERENCE_CUSTOM) {
            $addressValidation = Address::getValidationRules('Address')['size'];
            if (!$idCountry) {
                $this->errors[] = Tools::displayError('Field country is required');
            } elseif (!Validate::isLoadedObject($objCountry = new Country($idCountry))) {
                $this->errors[] = Tools::displayError('Invaid country');
            } elseif ($objCountry->zip_code_format && !$objCountry->checkZipCode($postcode)) {
                $this->errors[] = sprintf($this->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $objCountry->iso_code, str_replace('N', '0', str_replace('L', 'A', $objCountry->zip_code_format))));
            } elseif (!$postcode && $objCountry->need_zip_code) {
                $this->errors[] = $this->l('Zip / Postal code is required.');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = $this->l('The Zip / Postal code is invalid.');
            } elseif (Tools::strlen($postcode) > $addressValidation['postcode']) {
                $this->errors[] = sprintf(Tools::displayError('The zip code is too long (%1$d chars max).'), $addressValidation['postcode']);
            } elseif ($objCountry->contains_states && !$idSate) {
                $this->errors[] = Tools::displayError('Field State is required');
            } elseif (!Validate::isLoadedObject(new State($idSate))) {
                $this->errors[] = Tools::displayError('Invaid state');
            }

            if (!$city || $city == '') {
                $this->errors[] = $this->l('City is required field.');
            } elseif (!Validate::isCityName($city)) {
                $this->errors[] = $this->l('Enter a valid city name.');
            } elseif (Tools::strlen($city) > $addressValidation['city']) {
                $this->errors[] = sprintf(Tools::displayError('The city name is too long (%1$d chars max).'), $addressValidation['city']);
            }
        }
    }

    public function processUpdateOptions()
    {
        parent::processUpdateOptions();
        if (empty($this->errors)) {
            $serviceAddressPrefrenceType = Tools::getValue('PS_STANDARD_PRODUCT_ORDER_ADDRESS_PREFRENCE');
            if ($serviceAddressPrefrenceType == Product::STANDARD_PRODUCT_ADDRESS_PREFERENCE_CUSTOM) {
                $idCountry = Tools::getValue('service_id_country');
                if ($idCustomAddress = Configuration::get('PS_STANDARD_PRODUCT_ORDER_ADDRESS_ID')) {
                    $objAddress = new Address($idCustomAddress);
                } else {
                    $objAddress = new Address();
                }

                $objCountry = new Country($idCountry, $this->context->language->id);
                $objAddress->alias = 'Standard Product -'.$objCountry->name;
                $objAddress->address1 = 'Standard Product -'.$objCountry->name;
                $objAddress->firstname = 'Stand Alone';
                $objAddress->lastname = 'Standard Product';
                $objAddress->id_country = $idCountry;
                $objAddress->id_customer = 0;
                $objAddress->id_hotel = 0;
                $objAddress->id_state = Tools::getValue('service_id_state');

                $objAddress->city = Tools::getValue('service_city');
                $objAddress->postcode = Tools::getValue('service_postcode');
                if ($objAddress->save()) {
                    Configuration::updateValue('PS_STANDARD_PRODUCT_ORDER_ADDRESS_ID', $objAddress->id);
                }
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        Media::addJsDef(array(
            'STANDARD_PRODUCT_ADDRESS_PREFERENCE_CUSTOM' => Product::STANDARD_PRODUCT_ADDRESS_PREFERENCE_CUSTOM
        ));
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
        $this->addJS(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
    }
}
