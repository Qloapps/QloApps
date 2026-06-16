<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/osl-3.0-php
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
 * @license https://opensource.org/license/osl-3.0-php Open Software License version 3.0
 */

class AdminGuestRegistrationControllerCore extends AdminController
{
    // Section IDs
    const GRC_SECTION_GUEST_INFO          = 1;
    const GRC_SECTION_TRAVEL_INFO         = 2;
    const GRC_SECTION_BOOKING_INFO        = 3;
    const GRC_SECTION_IDENTIFICATION      = 4;
    const GRC_SECTION_ADDITIONAL_GUESTS   = 5;
    const GRC_SECTION_BILLING_CORPORATE   = 6;
    const GRC_SECTION_PAYMENT_DEPOSIT     = 7;
    const GRC_SECTION_GUEST_SIGNATURE     = 8;
    const GRC_SECTION_PROPERTY_REGS       = 9;
    const GRC_SECTION_OFFICE_USE_ONLY     = 10;

    // Guest Information fields (section 1)
    const GRC_GUEST_TITLE                 = 1;
    const GRC_GUEST_FULL_NAME             = 2;
    const GRC_GUEST_PHONE                 = 3;
    const GRC_GUEST_EMAIL                 = 4;
    const GRC_GUEST_DOB                   = 5;
    const GRC_GUEST_NATIONALITY           = 6;
    const GRC_GUEST_CITY_COUNTRY          = 7;
    const GRC_GUEST_POSTAL_CODE           = 8;
    const GRC_GUEST_ADDRESS               = 9;

    // Travel Information fields (section 2)
    const GRC_TRAVEL_ARRIVED_FROM         = 1;
    const GRC_TRAVEL_NEXT_DESTINATION     = 2;
    const GRC_TRAVEL_FLIGHT_TRAIN         = 3;
    const GRC_TRAVEL_VEHICLE_REG          = 4;
    const GRC_TRAVEL_PURPOSE_OF_VISIT     = 5;

    // Booking Information fields (section 3)
    const GRC_BOOKING_REFERENCE           = 1;
    const GRC_BOOKING_RATE_PER_NIGHT      = 2;
    const GRC_BOOKING_ARRIVAL             = 3;
    const GRC_BOOKING_DEPARTURE           = 4;
    const GRC_BOOKING_ROOM_TYPE           = 5;
    const GRC_BOOKING_ROOM_NUMBER         = 6;
    const GRC_BOOKING_NUM_GUESTS          = 7;

    // Identification Document fields (section 4)
    const GRC_ID_IDENTITY_PROOF           = 1;
    const GRC_ID_NUMBER                   = 2;
    const GRC_ID_PASSPORT_NO              = 3;
    const GRC_ID_PLACE_OF_ISSUE           = 4;
    const GRC_ID_DATE_OF_ISSUE            = 5;
    const GRC_ID_DATE_OF_EXPIRY           = 6;
    const GRC_ID_VISA_NUMBER              = 7;
    const GRC_ID_VALID_UNTIL              = 8;
    const GRC_ID_ARRIVAL_DATE_IN_COUNTRY  = 9;

    // Additional Guests fields (section 5)
    const GRC_ADD_GUEST_NAME              = 1;
    const GRC_ADD_GUEST_ID_TYPE           = 2;
    const GRC_ADD_GUEST_ID_NUMBER         = 3;
    const GRC_ADD_GUEST_NATIONALITY       = 4;

    // Billing & Corporate Details fields (section 6)
    const GRC_BILLING_COMPANY             = 1;
    const GRC_BILLING_TAX_ID              = 2;

    // Payment & Deposit fields (section 7)
    const GRC_PAYMENT_METHOD              = 1;
    const GRC_PAYMENT_CARD_NUMBER         = 2;
    const GRC_PAYMENT_SECURITY_DEPOSIT    = 3;

    // Guest Signature fields (section 8)
    const GRC_SIG_SIGNATURE               = 1;
    const GRC_SIG_DATE                    = 2;

    // Property Regulations fields (section 9)
    const GRC_PROP_CHECKIN_CHECKOUT_TIME  = 1;
    const GRC_PROP_HOTEL_POLICIES         = 2;

    // For Office Use Only fields (section 10)
    const GRC_OFFICE_STAFF_NAME           = 1;
    const GRC_OFFICE_CHECKIN_TIME         = 2;
    const GRC_OFFICE_ID_VERIFIED          = 3;
    const GRC_OFFICE_REG_NO               = 4;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'configuration';
        $this->className = 'Configuration';
        $this->identifier = 'id_configuration';

        parent::__construct();
    }

    public function initContent()
    {
        $this->display = 'options';
        $this->initToolbar();
        parent::initContent();
    }

    public function renderOptions()
    {
        $grcInfoJson = Configuration::get('QLO_GUEST_REGISTRATION_CARD_INFO');
        $savedSections = array();
        if ($grcInfoJson !== false && $grcInfoJson !== '') {
            $grcData = Tools::jsonDecode($grcInfoJson, true);
            if (is_array($grcData)) {
                $savedSections = $grcData;
            }
        }

        $allSelected = empty($savedSections);
        $nodes = array();
        foreach ($this->getRegistrationCardInfo() as $sectionId => $section) {
            $sectionFields = isset($savedSections[$sectionId]) ? $savedSections[$sectionId] : array();
            $fieldNodes = array();
            foreach ($section['fields'] as $fieldId => $fieldName) {
                $fieldSelected = $allSelected || in_array($fieldId, $sectionFields);
                $fieldNodes[] = array(
                    'value'      => $fieldId,
                    'name'       => $fieldName,
                    'input_name' => 'grc_field_'.$sectionId,
                    'selected'   => $fieldSelected,
                );
            }
            $fieldsSelected = $allSelected || !empty(array_filter($fieldNodes, function ($n) { return $n['selected']; }));
            $nodes[] = array(
                'value'      => $sectionId,
                'name'       => $section['name'],
                'input_name' => 'grc_section',
                'selected'   => $fieldsSelected,
                'children'   => $fieldNodes,
            );
        }

        $tree = new HelperTree('grc-card-info-tree');
        $tree->setData($nodes)
            ->setUseCheckBox(true)
            ->setAutoSelectChildren(true)
            ->setUseBulkActions(true);

        $this->context->smarty->assign(array(
            'guest_reg_card_info_tree'        => $tree->render(),
            'guest_reg_card_form_action'           => self::$currentIndex.'&token='.$this->token,
            'guest_reg_card_preview_img_url' => $this->context->link->getBaseLink().'img/admin/guest_registration_preview.jpg',
            'guest_reg_card_preview_title'   => $this->l('Guest Registration Card Preview'),
        ));

        return $this->createTemplate('card_info_form.tpl')->fetch();
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array($this->l('Guest Registration Card'));
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitGrcCardInfo')) {
            $grcInfo = array();
            foreach (array_keys($this->getRegistrationCardInfo()) as $sectionId) {
                $fields = array_values(array_filter(array_map('intval', (array)Tools::getValue('grc_field_'.$sectionId, array()))));
                if (!empty($fields)) {
                    $grcInfo[$sectionId] = $fields;
                }
            }
            if (empty($grcInfo)) {
                $this->errors[] = $this->l('Please select at least one field for the Guest Registration Card.');
                return;
            }
            Configuration::updateValue('QLO_GUEST_REGISTRATION_CARD_INFO', Tools::jsonEncode($grcInfo));
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
            return;
        }

        parent::postProcess();
    }


    /**
     * Returns all Guest Registration Card sections and their fields.
     * Modules may add, remove, or reorder sections/fields via the hook.
     *
     * @return array  sectionId => ['name' => string, 'fields' => [fieldId => string, ...]]
     */
    public function getRegistrationCardInfo()
    {
        $guestRegCardInfo = array(
            self::GRC_SECTION_GUEST_INFO => array(
                'name'   => $this->l('Guest Information', null, false, false),
                'fields' => array(
                    self::GRC_GUEST_TITLE       => $this->l('Title', null, false, false),
                    self::GRC_GUEST_FULL_NAME   => $this->l('Full Name', null, false, false),
                    self::GRC_GUEST_PHONE       => $this->l('Phone / Mobile', null, false, false),
                    self::GRC_GUEST_EMAIL       => $this->l('Email', null, false, false),
                    self::GRC_GUEST_DOB         => $this->l('Date of Birth', null, false, false),
                    self::GRC_GUEST_NATIONALITY => $this->l('Nationality', null, false, false),
                    self::GRC_GUEST_CITY_COUNTRY => $this->l('City / Country', null, false, false),
                    self::GRC_GUEST_POSTAL_CODE => $this->l('Postal Code', null, false, false),
                    self::GRC_GUEST_ADDRESS     => $this->l('Address', null, false, false),
                ),
            ),
            self::GRC_SECTION_TRAVEL_INFO => array(
                'name'   => $this->l('Travel Information', null, false, false),
                'fields' => array(
                    self::GRC_TRAVEL_ARRIVED_FROM     => $this->l('Arrived From', null, false, false),
                    self::GRC_TRAVEL_NEXT_DESTINATION => $this->l('Next Destination', null, false, false),
                    self::GRC_TRAVEL_FLIGHT_TRAIN     => $this->l('Flight / Train Number', null, false, false),
                    self::GRC_TRAVEL_VEHICLE_REG      => $this->l('Vehicle Reg. No.', null, false, false),
                    self::GRC_TRAVEL_PURPOSE_OF_VISIT => $this->l('Purpose of Visit', null, false, false),
                ),
            ),
            self::GRC_SECTION_BOOKING_INFO => array(
                'name'   => $this->l('Booking Information', null, false, false),
                'fields' => array(
                    self::GRC_BOOKING_REFERENCE      => $this->l('Booking Reference No.', null, false, false),
                    self::GRC_BOOKING_RATE_PER_NIGHT => $this->l('Rate per Night', null, false, false),
                    self::GRC_BOOKING_ARRIVAL        => $this->l('Arrival Date & Time', null, false, false),
                    self::GRC_BOOKING_DEPARTURE      => $this->l('Departure Date & Time', null, false, false),
                    self::GRC_BOOKING_ROOM_TYPE      => $this->l('Room Type', null, false, false),
                    self::GRC_BOOKING_ROOM_NUMBER    => $this->l('Room Number', null, false, false),
                    self::GRC_BOOKING_NUM_GUESTS     => $this->l('Number of Guests', null, false, false),
                ),
            ),
            self::GRC_SECTION_IDENTIFICATION => array(
                'name'   => $this->l('Identification Document', null, false, false),
                'fields' => array(
                    self::GRC_ID_IDENTITY_PROOF          => $this->l('Identity Proof', null, false, false),
                    self::GRC_ID_NUMBER                  => $this->l('ID Number', null, false, false),
                    self::GRC_ID_PASSPORT_NO             => $this->l('Passport No.', null, false, false),
                    self::GRC_ID_PLACE_OF_ISSUE          => $this->l('Place of Issue', null, false, false),
                    self::GRC_ID_DATE_OF_ISSUE           => $this->l('Date of Issue', null, false, false),
                    self::GRC_ID_DATE_OF_EXPIRY          => $this->l('Date of Expiry', null, false, false),
                    self::GRC_ID_VISA_NUMBER             => $this->l('Visa Number', null, false, false),
                    self::GRC_ID_VALID_UNTIL             => $this->l('Valid Until', null, false, false),
                    self::GRC_ID_ARRIVAL_DATE_IN_COUNTRY => $this->l('Arrival Date in Country', null, false, false),
                ),
            ),
            self::GRC_SECTION_ADDITIONAL_GUESTS => array(
                'name'   => $this->l('Additional Guests', null, false, false),
                'fields' => array(
                    self::GRC_ADD_GUEST_NAME        => $this->l('Guest Name', null, false, false),
                    self::GRC_ADD_GUEST_ID_TYPE     => $this->l('ID Type', null, false, false),
                    self::GRC_ADD_GUEST_ID_NUMBER   => $this->l('ID Number', null, false, false),
                    self::GRC_ADD_GUEST_NATIONALITY => $this->l('Nationality', null, false, false),
                ),
            ),
            self::GRC_SECTION_BILLING_CORPORATE => array(
                'name'   => $this->l('Billing & Corporate Details', null, false, false),
                'fields' => array(
                    self::GRC_BILLING_COMPANY => $this->l('Company / Agent', null, false, false),
                    self::GRC_BILLING_TAX_ID  => $this->l('Tax ID / VAT No.', null, false, false),
                ),
            ),
            self::GRC_SECTION_PAYMENT_DEPOSIT => array(
                'name'   => $this->l('Payment & Deposit', null, false, false),
                'fields' => array(
                    self::GRC_PAYMENT_METHOD           => $this->l('Payment Method', null, false, false),
                    self::GRC_PAYMENT_CARD_NUMBER      => $this->l('Credit Card Number', null, false, false),
                    self::GRC_PAYMENT_SECURITY_DEPOSIT => $this->l('Security Deposit', null, false, false),
                ),
            ),
            self::GRC_SECTION_GUEST_SIGNATURE => array(
                'name'   => $this->l('Guest Signature', null, false, false),
                'fields' => array(
                    self::GRC_SIG_SIGNATURE => $this->l('Signature', null, false, false),
                    self::GRC_SIG_DATE      => $this->l('Date', null, false, false),
                ),
            ),
            self::GRC_SECTION_PROPERTY_REGS => array(
                'name'   => $this->l('Property Regulations', null, false, false),
                'fields' => array(
                    self::GRC_PROP_CHECKIN_CHECKOUT_TIME => $this->l('Check-in / Check-out Time', null, false, false),
                    self::GRC_PROP_HOTEL_POLICIES        => $this->l('Hotel Policies', null, false, false),
                ),
            ),
            self::GRC_SECTION_OFFICE_USE_ONLY => array(
                'name'   => $this->l('For Office Use Only', null, false, false),
                'fields' => array(
                    self::GRC_OFFICE_STAFF_NAME   => $this->l('Staff Name', null, false, false),
                    self::GRC_OFFICE_CHECKIN_TIME => $this->l('Check-in Time', null, false, false),
                    self::GRC_OFFICE_ID_VERIFIED  => $this->l('ID Verified', null, false, false),
                    self::GRC_OFFICE_REG_NO       => $this->l('Registration No.', null, false, false),
                ),
            ),
        );

        Hook::exec('action'.$this->controller_name.'GuestRegCardInfoModifier', array(
            'guest_registration_info' => &$guestRegCardInfo,
        ));

        return $guestRegCardInfo;
    }
}
