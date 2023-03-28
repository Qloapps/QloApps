<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminHotelGeneralSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'configuration';
        $this->className = 'Configuration';
        $this->bootstrap = true;
        parent::__construct();

        $psImgUrl = $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('WK_HOTEL_HEADER_IMAGE'));
        if ($imgExist = (bool)Tools::file_get_contents($psImgUrl)) {
            $image = '<img class="img-thumbnail img-responsive" style="max-width:200px" src="'.$psImgUrl.'">';
        }
        $objHotelInfo = new HotelBranchInformation();
        if (!$hotelsInfo = $objHotelInfo->hotelBranchesInfo(false, 1)) {
            $hotelsInfo = array();
        }
        foreach ($hotelsInfo as &$hotel) {
            $hotel['name'] = $hotel['hotel_name'];
        }
        $hotelNameDisable = (count($hotelsInfo) > 1 ? true : false);
        $locationDisable = ((count($hotelsInfo) < 2) && !Configuration::get('WK_HOTEL_NAME_ENABLE')) ? true : false;
        $this->fields_options = array(
            'hotelsearchpanel' => array(
                'icon' => 'icon-search',
                'title' => $this->l('Hotel Search Setting'),
                'fields' => array(
                    'WK_HOTEL_LOCATION_ENABLE' => array(
                        'title' => $this->l('Enable Hotel Location'),
                        'cast' => 'intval',
                        'type' => 'bool',
                        'disabled' => $locationDisable,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                            ),
                        ),
                        'hint' => $this->l('Either display Hotel location field on hotel search panel or hide it.'),
                    ),
                    'WK_HOTEL_NAME_ENABLE' => array(
                        'title' => $this->l('Display Hotel Name'),
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '0',
                        'disabled' => $hotelNameDisable,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                            ),
                        ),
                        'hint' => $this->l('This option can be disabled if only one active hotel in the website.
                        In case of more than one active hotel, Hotel Name will always be shown in the search panel.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
            'occupancypanel' => array(
                'icon' => 'icon-users',
                'title' => $this->l('Occupancy Setting'),
                'fields' => array(
                    // max age of child
                    'WK_GLOBAL_CHILD_MAX_AGE' => array(
                        'title' => $this->l('Consider guest as child below age'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedInt',
                        'hint' => $this->l('Enter the age of the guest,  which that guest will be considered as child.'),
                    ),
                    'WK_GLOBAL_MAX_CHILD_IN_ROOM' => array(
                        'title' => $this->l('Maximum children allowed in a room'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedInt',
                        'hint' => $this->l('Enter number of the child allowed in a room.'),
                        'desc' => $this->l('Set as 0 if you do not want to limit children in a room.')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
            'generalsetting' => array(
                'title' => $this->l('Configuration'),
                'fields' => array(
                    'WK_ROOM_LEFT_WARNING_NUMBER' => array(
                        'title' => $this->l('Display remaining Number of rooms when the rooms are lower than or equal to'),
                        'hint' => $this->l('Mention the minimum quantity of rooms after which alert message of remaining rooms will get displayed to users.'),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'visibility' => Shop::CONTEXT_ALL,
                    ),
                    'WK_HOTEL_GLOBAL_ADDRESS' => array(
                        'title' => $this->l('Global Address'),
                        'hint' => $this->l('Hotel global address which you want to show to your customers. It will be
                        shown at contact us page.'),
                        'type' => 'text',
                        'isCleanHtml' => true,
                    ),
                    'WK_HOTEL_GLOBAL_CONTACT_EMAIL' => array(
                        'title' => $this->l('Global Email'),
                        'hint' => $this->l('Email which you want to show a customer to email you.'),
                        'type' => 'text',
                    ),
                    'WK_HOTEL_GLOBAL_CONTACT_NUMBER' => array(
                        'title' => $this->l('Global Contact Number'),
                        'hint' => $this->l('Phone Number which you want to show a customer to contact you.'),
                        'type' => 'text',
                        'validation' => 'isPhoneNumber',
                    ),
                    'WK_HTL_ESTABLISHMENT_YEAR' => array(
                        'title' => $this->l('Website Launch Year'),
                        'hint' => $this->l('The year when your hotel site was launched.'),
                        'type' => 'text',
                    ),
                    'WK_PRIMARY_HOTEL' => array(
                        'title' => $this->l('Primary hotel'),
                        'hint' => $this->l('Primary hotel is used to default address for your business. The hotel address will be considered as your registered business address.'),
                        'type' => 'select',
                        'identifier' => 'id',
                        'list' => $hotelsInfo,
                    ),
                    'WK_HTL_CHAIN_NAME' => array(
                        'title' => $this->l('Hotel Name'),
                        'type' => 'textLang',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'hint' => $this->l('Enter Hotel name in case of single hotel or enter your hotels chain name in case of multiple hotels.'),
                    ),
                    'WK_HTL_TAG_LINE' => array(
                        'title' => $this->l('Hotel Tag Line'),
                        'type' => 'textareaLang',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'hint' => $this->l('This will display hotel tag line in hotel page.'),
                    ),
                    'WK_HTL_SHORT_DESC' => array(
                        'title' => $this->l('Hotel Short Description'),
                        'type' => 'textareaLang',
                        'lang' => true,
                        'required' => true,
                        'rows' => '4',
                        'cols' => '2',
                        'validation' => 'isGenericName',
                        'hint' => $this->l('This will display hotel short description in footer. Note: number of letters must be less than 220.'),
                    ),
                    'WK_HTL_HEADER_IMAGE' => array(
                        'title' => $this->l('Header Background Image'),
                        'type' => 'file',
                        'image' => $imgExist ? $image : false,
                        'hint' => $this->l('This image appears as header background image on home page.'),
                        'name' => 'htl_header_image',
                        'url' => _PS_IMG_,
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),
            'advancedPayment' => array(
                'title' => $this->l('Advance Payment Global Setting'),
                'fields' => array(
                    'WK_ALLOW_ADVANCED_PAYMENT' => array(
                        'title' => $this->l('Allow Advance Payment'),
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '1',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                            ),
                        ),
                        'hint' => $this->l('If No, Advance Payment functionality will be disabled'),
                    ),
                    'WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT' => array(
                        'title' => $this->l('Global Minimum Booking Amount'),
                        'hint' => $this->l('Enter Minimum amount to pay in percentage for booking aroom.'),
                        'type' => 'text',
                        'validation' => 'isUnsignedFloat',
                        'suffix' => $this->l('%'),
                    ),
                    'WK_ADVANCED_PAYMENT_INC_TAX' => array(
                        'title' => $this->l('Global Booking Amount Include Tax'),
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '1',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                            ),
                        ),
                        'hint' => $this->l('Yes, if you want to take tax with Advance payment otherwise No.'),
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),
            'googleMap' => array(
                'title' => $this->l('Google Maps Settings'),
                'fields' => array(
                    'PS_API_KEY' => array(
                        'title' => $this->l('Google Maps API Key'),
                        'hint' => $this->l('Unique API key for Google Maps.'),
                        'type' => 'text',
                    ),
                    'WK_GOOGLE_ACTIVE_MAP' => array(
                        'title' => $this->l('Display Google Maps For Hotel Location'),
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '1',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                            ),
                        ),
                        'hint' => $this->l('If set to No, Google Maps will not be displayed for hotel location. You need to set hotels location from edit hotel page to display location on the map at contact-us page.'),
                    ),
                    'WK_MAP_HOTEL_ACTIVE_ONLY' => array(
                        'title' => $this->l('Display Active Hotels Only'),
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '1',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                            ),
                        ),
                        'hint' => $this->l('If yes, only active hotels will be displayed on map.'),
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptions'.$this->table)) {
            // check if field is atleast in default language. Not available in default prestashop
            $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
            $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
            $languages = Language::getLanguages(false);

            // Validation for the occupancy settings
            // max age of infant after which guest will considered as child // below 18
            $globalChildMaxAge = Tools::getValue('WK_GLOBAL_CHILD_MAX_AGE');
            $globalMaxChildInRoom = Tools::getValue('WK_GLOBAL_MAX_CHILD_IN_ROOM');

            // End occupancy fields validation

            if (!trim(Tools::getValue('WK_HTL_CHAIN_NAME_'.$defaultLangId))) {
                $this->errors[] = $this->l('Hotel chain name is required at least in ').$objDefaultLanguage['name'];
            } else {
                foreach ($languages as $lang) {
                    if (trim(Tools::getValue('WK_HTL_CHAIN_NAME_'.$lang['id_lang']))) {
                        if (!Validate::isGenericName(Tools::getValue('WK_HTL_CHAIN_NAME_'.$lang['id_lang']))) {
                            $this->errors[] = $this->l('Invalid hotel chain name in ').$lang['name'];
                        }
                    }
                }
            }
            if (!trim(Tools::getValue('WK_HTL_TAG_LINE_'.$defaultLangId))) {
                $this->errors[] = $this->l('Hotel tag line is required at least in ').$objDefaultLanguage['name'];
            } else {
                foreach ($languages as $lang) {
                    if (trim(Tools::getValue('WK_HTL_TAG_LINE_'.$lang['id_lang']))) {
                        if (!Validate::isGenericName(Tools::getValue('WK_HTL_TAG_LINE_'.$lang['id_lang']))) {
                            $this->errors[] = $this->l('Invalid Hotel tag line in ').$lang['name'];
                        }
                    }
                }
            }
            if (!trim(Tools::getValue('WK_HTL_SHORT_DESC_'.$defaultLangId))) {
                $this->errors[] = $this->l('Hotel short description is required at least in ').
                $objDefaultLanguage['name'];
            } else {
                foreach ($languages as $lang) {
                    if (trim(Tools::getValue('WK_HTL_SHORT_DESC_'.$lang['id_lang']))) {
                        if (!Validate::isGenericName(Tools::getValue('WK_HTL_SHORT_DESC_'.$lang['id_lang']))) {
                            $this->errors[] = $this->l('Invalid hotel short description in ').$lang['name'];
                        }
                    }
                }
            }
            if ($_FILES['htl_header_image']['name']) {
                if ($error = ImageManager::validateUpload($_FILES['htl_header_image'], Tools::getMaxUploadSize())) {
                    $this->errors[] = $error;
                }

                if (!count($this->errors)) {
                    $file_name = 'hotel_header_image_'.time().'.jpg';
                    $img_path = _PS_IMG_DIR_.$file_name;

                    if (ImageManager::resize($_FILES['htl_header_image']['tmp_name'], $img_path)) {
                        Configuration::updateValue('WK_HOTEL_HEADER_IMAGE', $file_name);
                    } else {
                        $this->errors[] = $this->l('Some error occured while uoploading image.Please try again.');
                    }
                }
            }
            if (!Validate::isUnsignedInt(Tools::getValue('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT'))) {
                $this->errors[] = $this->l('Invalid minimum partial payment percentage.');
            } elseif (Tools::getValue('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT') <= 0) {
                $this->errors[] = $this->l('Minimum partial payment percentage should be more than 0.');
            } elseif (Tools::getValue('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT') > 100) {
                $this->errors[] = $this->l('Minimum partial payment percentage should not be more than 100.');
            }
            if (Tools::getValue('WK_GOOGLE_ACTIVE_MAP')) {
                if (!Tools::getValue('PS_API_KEY')) {
                    $this->errors[] = $this->l('Please enter Google API key.');
                }
            }
            if (!trim(Tools::getValue('WK_HOTEL_GLOBAL_ADDRESS'))) {
                $this->errors[] = $this->l('Hotel global address field is required');
            }
            if (!trim(Tools::getValue('WK_HOTEL_GLOBAL_CONTACT_NUMBER'))) {
                $this->errors[] = $this->l('Hotel global contact number field is required');
            } elseif (!Validate::isPhoneNumber(Tools::getValue('WK_HOTEL_GLOBAL_CONTACT_NUMBER'))) {
                $this->errors[] = $this->l('Hotel global contact number field is invalid');
            }
            if (!trim(Tools::getValue('WK_HOTEL_GLOBAL_CONTACT_EMAIL'))) {
                $this->errors[] = $this->l('Hotel global contact email field is required');
            } elseif (!Validate::isEmail(Tools::getValue('WK_HOTEL_GLOBAL_CONTACT_EMAIL'))) {
                $this->errors[] = $this->l('Hotel global contact email field is invalid');
            }

            if (!count($this->errors)) {
                $objHotelInfo = new HotelBranchInformation();
                if ($hotelsInfo = $objHotelInfo->hotelBranchesInfo(false, 1)) {
                    if (count($hotelsInfo) > 1) {
                        $_POST['WK_HOTEL_NAME_ENABLE'] = 1;
                    }
                }
                foreach ($languages as $lang) {
                    // if lang fileds are at least in default language and not available in other languages then
                    // set empty fields value to default language value
                    if (!trim(Tools::getValue('WK_HTL_CHAIN_NAME_'.$lang['id_lang']))) {
                        $_POST['WK_HTL_CHAIN_NAME_'.$lang['id_lang']] = trim(
                            Tools::getValue('WK_HTL_CHAIN_NAME_'.$defaultLangId)
                        );
                    }
                    if (!trim(Tools::getValue('WK_HTL_TAG_LINE_'.$lang['id_lang']))) {
                        $_POST['WK_HTL_TAG_LINE_'.$lang['id_lang']] = trim(
                            Tools::getValue('WK_HTL_TAG_LINE_'.$defaultLangId)
                        );
                    }
                    if (!trim(Tools::getValue('WK_HTL_SHORT_DESC_'.$lang['id_lang']))) {
                        $_POST['WK_HTL_SHORT_DESC_'.$lang['id_lang']] = trim(
                            Tools::getValue('WK_HTL_SHORT_DESC_'.$defaultLangId)
                        );
                    }
                }
                parent::postProcess();
                if (empty($this->errors)) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=6&token='.$this->token);
                }
            }
        } else {
            parent::postProcess();
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        Media::addJsDef(
            array(
                'filesizeError' => $this->l('File exceeds maximum size.'),
                'maxSizeAllowed' => Tools::getMaxUploadSize(),
            )
        );
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
    }
}
