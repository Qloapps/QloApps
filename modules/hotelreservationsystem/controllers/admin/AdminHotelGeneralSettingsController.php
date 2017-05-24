<?php

class AdminHotelGeneralSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'configuration';
        $this->className = 'Configuration';
        $this->bootstrap = true;
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $ps_img_url = _PS_IMG_DIR_.'hotel_header_image.jpg';
        if ($img_exist = file_exists($ps_img_url)) {
            $img_url = _PS_IMG_.'hotel_header_image.jpg';
            $image = "<img class='img-thumbnail img-responsive' style='max-width:200px' src='".$img_url."'>";
        }
        $this->fields_options = array(
            'general' => array(
                'title' => $this->l('Configuration'),
                'fields' => array(
                    'WK_HOTEL_LOCATION_ENABLE' => array(
                        'title' => $this->l('Enable Hotel Location'),
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '0',
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
                        'hint' => $this->l('whether you want to show Hotel Location field on search page.'),
                    ),
                    'WK_ROOM_LEFT_WARNING_NUMBER' => array(
                        'title' => $this->l('Display remaining Number of rooms when the rooms are lower than or equal this number'),
                        'hint' => $this->l('Mention the minimum quantity after which alert message for remaining rooms will be displayed to customers.'),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'visibility' => Shop::CONTEXT_ALL,
                    ),
                    'WK_HOTEL_GLOBAL_CONTACT_EMAIL' => array(
                        'title' => $this->l('Global Email'),
                        'hint' => $this->l('Email which you want to show a customer to email you.'),
                        'type' => 'text',
                        'validation' => 'isEmail',
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
                    'WK_HTL_CHAIN_NAME' => array(
                        'title' => $this->l('Hotel Name'),
                        'hint' => $this->l('Enter Hotel name in case of single hotel or enter your hotels chain name in case of multiple hotels'),
                        'type' => 'text',
                    ),
                    'WK_HTL_TAG_LINE' => array(
                        'title' => $this->l('Hotel Tag Line'),
                        'hint' => $this->l('This will display hotel tag line in hotel page.'),
                        'type' => 'textarea',
                    ),
                    'WK_HTL_SHORT_DESC' => array(
                        'title' => $this->l('Hotel Sort Description'),
                        'hint' => $this->l('This will display hotel short description in footer. Note: number of letters must be less then 220.'),
                        'type' => 'textarea',
                    ),
                    'WK_HTL_HEADER_IMAGE' => array(
                        'title' => $this->l('Header Background Image'),
                        'type' => 'file',
                        'image' => $img_exist ? $image : false,
                        'hint' => $this->l('This image appears as home page header background image.'),
                        'name' => 'htl_header_image',
                        'url' => _PS_IMG_,
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),
            'advancedPayment' => array(
                'title' => $this->l('Advanced Payment Global Setting'),
                'fields' => array(
                    'WK_ALLOW_ADVANCED_PAYMENT' => array(
                        'title' => $this->l('Allow Advanced Payment'),
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
                        'hint' => $this->l('If No, Advanced Payment functionality will be disabled'),
                    ),
                    'WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT' => array(
                        'title' => $this->l('Global Minimum Booking Amount'),
                        'hint' => $this->l('Enter Minimum amount to pay in percentage for booking a room.'),
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
                        'hint' => $this->l('Yes, if you want to take tax with Advanced payment otherwise No.'),
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),
            /*'orderconfirmation' => array(
                'title' => $this->l('Backorder Setting'),
                'fields' => array(
                    'WK_SHOW_MSG_ON_BO' => array(
                        'title' => $this->l('Show message on backorder'),
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
                        'hint' => $this->l('Do you want to show a message to the customer in case of backorder.'),
                    ),
                     'WK_BO_MESSAGE' => array(
                        'title' => $this->l('BackOrder Message'),
                        'hint' => $this->l('Enter Minimum amount to pay in percentage form booking a room.'),
                        'type' => 'text',
                        'class' => 'bo_msg',
                        'type' => 'text',
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),*/
            'googleMap' => array(
                'title' => $this->l('Google Map Setting'),
                'fields' => array(
                    'WK_GOOGLE_ACTIVE_MAP' => array(
                        'title' => $this->l('Display Google Map'),
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
                        'hint' => $this->l('If set to No, Google Map will not be displayed.'),
                    ),
                    'WK_GOOGLE_API_KEY' => array(
                        'title' => $this->l('Google API Key'),
                        'hint' => $this->l('Unique API key for Google map.'),
                        'type' => 'text',
                    ),
                    'WK_MAP_HOTEL_ACTIVE_ONLY' => array(
                        'title' => $this->l('Display Only Active Hotels'),
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
                        'hint' => $this->l('If yes, only active hotels will be display on map'),
                    ),
                    
                ),
                'submit' => array(
                                'title' => $this->l('Save'),
                                // 'name' => 'submitGoogleMapSetting'
                                ),
            ),
        );
        parent::__construct();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            if ($_FILES['htl_header_image']['name']) {
                $this->validateHotelHeaderImage($_FILES['htl_header_image']);
                if (!count($this->errors)) {
                    $img_path = _PS_IMG_DIR_.'hotel_header_image.jpg';

                    if (ImageManager::resize($_FILES['htl_header_image']['tmp_name'], $img_path)) {
                        Configuration::updateValue('WK_HOTEL_HEADER_IMAGE', 'hotel_header_image.jpg');
                    } else {
                        $this->errors[] = Tools::displayError('Some error occured while uoploading image.Please try again.');
                    }
                }
            }
            if (Tools::getValue('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT') <= 0) {
                $this->errors[] = Tools::displayError('Minimum partial payment percentage should be more than 0.');
            }
            if (Tools::getValue('WK_GOOGLE_ACTIVE_MAP')) {
                if (!Tools::getValue('WK_GOOGLE_API_KEY')) {
                    $this->errors[] = Tools::displayError('Please enter Google API key.');
                }
            }
        }

        parent::postProcess();
    }

    public function validateHotelHeaderImage($image)
    {
        if ($image['size'] > 0) {
            if ($image['tmp_name'] != '') {
                if (!ImageManager::isCorrectImageFileExt($image['name'])) {
                    $this->errors[] = Tools::displayError('<strong>'.$_FILES['htl_header_image']['name'].'</strong> : Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
                }
            }
        } else {
            return true;
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
    }
}
