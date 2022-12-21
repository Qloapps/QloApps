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

class AdminAddHotelController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_branch_info';
        $this->className = 'HotelBranchInformation';
        $this->identifier = 'id';
        $this->context = Context::getContext();

        // START send access query information to the admin controller
        $this->access_select = ' SELECT a.`id` FROM '._DB_PREFIX_.'htl_branch_info a';
        if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1)) {
            $this->access_where = ' WHERE a.id IN ('.implode(',', $acsHtls).')';
        }

        parent::__construct();

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
        ON (a.id = hbl.id AND hbl.`id_lang` = '.(int) $this->context->language->id.')';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'address` aa ON (aa.`id_hotel` = a.`id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = aa.`id_state`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl
        ON (cl.`id_country` = aa.`id_country` AND cl.`id_lang` = '.(int) $this->context->language->id.')';

        $this->_select = ' hbl.`hotel_name`, aa.`city`, s.`name` as `state_name`, cl.`name` as country_name';

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'hotel_name' => array(
                'title' => $this->l('Hotel Name'),
                'align' => 'center',
            ),
            'city' => array(
                'title' => $this->l('City'),
                'align' => 'center',
            ),
            'state_name' => array(
                'title' => $this->l('State'),
                'align' => 'center',
                'filter_key' => 's!name',
            ),
            'country_name' => array(
                'title' => $this->l('Country'),
                'align' => 'center',
                'filter_key' => 'cl!name',
            ),
            'active' => array(
                'align' => 'center',
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new Hotel'),
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $smartyVars = array();
        //tinymce setup
        $smartyVars['path_css'] = _THEME_CSS_DIR_;
        $smartyVars['ad'] = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $smartyVars['autoload_rte'] = true;
        $smartyVars['lang'] = true;
        $smartyVars['iso'] = $this->context->language->iso_code;
        //lang vars
        $currentLangId = Configuration::get('PS_LANG_DEFAULT');
        $smartyVars['languages'] = Language::getLanguages(false);
        $smartyVars['currentLang'] = Language::getLanguage((int) $currentLangId);

        $smartyVars['defaultCurrency'] = Configuration::get('PS_CURRENCY_DEFAULT');

        $countries = Country::getCountries($this->context->language->id, true);
        $smartyVars['country_var'] = $countries;

        $country = $this->context->country;
        $smartyVars['defaultCountry'] = $country->name[Configuration::get('PS_LANG_DEFAULT')];

        if ($this->display == 'edit') {
            $idHotel = Tools::getValue('id');
            $hotelBranchInfo = new HotelBranchInformation($idHotel);

            $addressInfo = $hotelBranchInfo->getAddress($idHotel);
            $statesbycountry = State::getStatesByIdCountry($addressInfo['id_country']);

            $states = array();
            if ($statesbycountry) {
                foreach ($statesbycountry as $key => $value) {
                    $states[$key]['id'] = $value['id_state'];
                    $states[$key]['name'] = $value['name'];
                }
            }
            $smartyVars['edit'] =  1;
            $smartyVars['state_var'] = $states;
            $smartyVars['address_info'] = $addressInfo;
            $smartyVars['hotel_info'] = (array) $hotelBranchInfo;
            //Hotel Images
            $objHotelImage = new HotelImage();
            if ($hotelAllImages = $objHotelImage->getImagesByHotelId($idHotel)) {
                foreach ($hotelAllImages as &$image) {
                    $image['image_link'] = $this->context->link->getMediaLink(_MODULE_DIR_.$this->module->name.'/views/img/hotel_img/'.$image['hotel_image_id'].'.jpg');
                }
                $smartyVars['hotelImages'] =  $hotelAllImages;
            }

            $objRefundRules = new HotelOrderRefundRules();
            if ($allRefundRules = $objRefundRules->getAllOrderRefundRules(0, $idHotel)) {
                $smartyVars['allRefundRules'] =  $allRefundRules;
                $smartyVars['WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE;
                $smartyVars['WK_REFUND_RULE_PAYMENT_TYPE_FIXED'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_FIXED;
                // send hotel refund rules
                $objBranchRefundRules = new HotelBranchRefundRules();
                if ($hotelRefundRules = $objBranchRefundRules->getHotelRefundRules($idHotel)) {
                    $smartyVars['hotelRefundRules'] =  array_column($hotelRefundRules, 'id_refund_rule');
                }
            }
        }

        $smartyVars['enabledDisplayMap'] =  Configuration::get('WK_GOOGLE_ACTIVE_MAP');
        $smartyVars['ps_img_dir'] = _PS_IMG_.'l/';

        $this->context->smarty->assign($smartyVars);

        Media::addJsDef(
            array(
                'img_dir_l' => _PS_IMG_.'l/',
            )
        );
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        return parent::renderForm();
    }

    public function processSave()
    {
        $idHotel = Tools::getValue('id');
        $phone = Tools::getValue('phone');
        $email = Tools::getValue('email');
        $check_in = Tools::getValue('check_in');
        $check_out = Tools::getValue('check_out');
        $rating = Tools::getValue('hotel_rating');
        $city = Tools::getValue('hotel_city');
        $state = Tools::getValue('hotel_state');
        $country = Tools::getValue('hotel_country');
        $zipcode = Tools::getValue('hotel_postal_code');
        $address = Tools::getValue('address');
        $active = Tools::getValue('ENABLE_HOTEL');
        $activeRefund = Tools::getValue('active_refund');
        $latitude = Tools::getValue('loclatitude');
        $longitude = Tools::getValue('loclongitude');
        $map_formated_address = Tools::getValue('locformatedAddr');
        $map_input_text = Tools::getValue('googleInputField');
        // check if field is atleast in default language. Not available in default prestashop
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('hotel_name_'.$defaultLangId))) {
            $this->errors[] = $this->l('Hotel name is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                // validate non required fields
                if (trim(Tools::getValue('hotel_name_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('hotel_name_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid Hotel name in ').$lang['name'];
                    }
                }
                if ($shortDescription = html_entity_decode(Tools::getValue('short_description_'.$lang['id_lang']))) {
                    if (!Validate::isCleanHtml($shortDescription)) {
                        $this->errors[] = sprintf($this->l('Short description is not valid in %s'), $lang['name']);
                    }
                }
                if ($description = html_entity_decode(Tools::getValue('description_'.$lang['id_lang']))) {
                    if (!Validate::isCleanHtml($description)) {
                        $this->errors[] = sprintf($this->l('Description is not valid in %s'), $lang['name']);
                    }
                }
                if ($policies = html_entity_decode(Tools::getValue('policies_'.$lang['id_lang']))) {
                    if (!Validate::isCleanHtml($policies)) {
                        $this->errors[] = sprintf($this->l('policies are not valid in %s'), $lang['name']);
                    }
                }
            }
        }
        if (!$phone) {
            $this->errors[] = $this->l('Phone number is required field.');
        } elseif (!Validate::isPhoneNumber($phone)) {
            $this->errors[] = $this->l('Please enter a valid phone number.');
        }

        if ($email == '') {
            $this->errors[] = $this->l('Email is required field.');
        } elseif (!Validate::isEmail($email)) {
            $this->errors[] = $this->l('Please enter a valid email.');
        }

        if ($check_in == '') {
            $this->errors[] = $this->l('Check In time is required field.');
        }
        if ($check_out == '') {
            $this->errors[] = $this->l('Check Out Time is required field.');
        }
        if ($check_in && $check_out && strtotime($check_out) > strtotime($check_in)) {
            $this->errors[] = $this->l('Check Out time must be before Check In time.');
        }

        if (!$rating) {
            $this->errors[] = $this->l('Rating is required field.');
        }

        if ($address == '') {
            $this->errors[] = $this->l('Address is required field.');
        }

        if (!$country) {
            $this->errors[] = $this->l('Country is required field.');
        } else {
            $statesbycountry = State::getStatesByIdCountry($country);
            /*If selected country has states only the validate state field*/

            if (!$state) {
                if ($statesbycountry) {
                    $this->errors[] = $this->l('State is required field.');
                }
            }
            /* Check zip code format */
            $objCountry = new Country($country);
            if ($objCountry->zip_code_format && !$objCountry->checkZipCode($zipcode)) {
                $this->errors[] = sprintf($this->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $objCountry->iso_code, str_replace('N', '0', str_replace('L', 'A', $objCountry->zip_code_format))));
            } elseif (empty($zipcode) && $objCountry->need_zip_code) {
                $this->errors[] = $this->l('A Zip / Postal code is required.');
            } elseif ($zipcode && !Validate::isPostCode($zipcode)) {
                $this->errors[] = $this->l('The Zip / Postal code is invalid.');
            }
        }

        if ($city == '') {
            $this->errors[] = $this->l('City is required field.');
        } elseif (!Validate::isCityName($city)) {
            $this->errors[] = $this->l('Enter a Valid City Name.');
        }

        if (!count($this->errors)) {
            if ($idHotel) {
                $objHotelBranch = new HotelBranchInformation($idHotel);
            } else {
                $objHotelBranch = new HotelBranchInformation();
            }
            if ($objHotelBranch->id) {
                if (!$active) {
                    $objHtlRoomInfo = new HotelRoomType();
                    $idsProduct = $objHtlRoomInfo->getIdProductByHotelId($objHotelBranch->id);
                    if (isset($idsProduct) && $idsProduct) {
                        foreach ($idsProduct as $product) {
                            $objProduct = new Product($product['id_product']);
                            if ($objProduct->active) {
                                $objProduct->toggleStatus();
                            }
                        }
                    }
                }
            }
            $objHotelBranch->active = $active;
            $objHotelBranch->active_refund = $activeRefund;

            // lang fields
            $hotelCatName = array();
            foreach ($languages as $lang) {
                if (!trim(Tools::getValue('hotel_name_'.$lang['id_lang']))) {
                    $objHotelBranch->hotel_name[$lang['id_lang']] = Tools::getValue(
                        'hotel_name_'.$defaultLangId
                    );
                } else {
                    $objHotelBranch->hotel_name[$lang['id_lang']] = Tools::getValue(
                        'hotel_name_'.$lang['id_lang']
                    );
                }

                $cleanShortDescription = Tools::getDescriptionClean(
                    Tools::getValue('short_description_'.$lang['id_lang'])
                );
                //Remove TinyMCE's Non-Breaking Spaces
                $cleanShortDescription = str_replace(chr(0xC2).chr(0xA0), "", $cleanShortDescription);
                if (!trim($cleanShortDescription)) {
                    $objHotelBranch->short_description[$lang['id_lang']] = Tools::getValue(
                        'short_description_'.$defaultLangId
                    );
                } else {
                    $objHotelBranch->short_description[$lang['id_lang']] = Tools::getValue(
                        'short_description_'.$lang['id_lang']
                    );
                }
                $cleanDescription = Tools::getDescriptionClean(
                    Tools::getValue('description_'.$lang['id_lang'])
                );
                //Remove TinyMCE's Non-Breaking Spaces
                $cleanDescription = str_replace(chr(0xC2).chr(0xA0), "", $cleanDescription);
                if (!trim($cleanDescription)) {
                    $objHotelBranch->description[$lang['id_lang']] = Tools::getValue(
                        'description_'.$defaultLangId
                    );
                } else {
                    $objHotelBranch->description[$lang['id_lang']] = Tools::getValue(
                        'description_'.$lang['id_lang']
                    );
                }
                $cleanPolicies = Tools::getDescriptionClean(
                    Tools::getValue('policies_'.$lang['id_lang'])
                );
                //Remove TinyMCE's Non-Breaking Spaces
                $cleanPolicies = str_replace(chr(0xC2).chr(0xA0), "", $cleanPolicies);
                if (!trim($cleanPolicies)) {
                    $objHotelBranch->policies[$lang['id_lang']] = Tools::getValue(
                        'policies_'.$defaultLangId
                    );
                } else {
                    $objHotelBranch->policies[$lang['id_lang']] = Tools::getValue(
                        'policies_'.$lang['id_lang']
                    );
                }
            }
            $objHotelBranch->email = $email;
            $objHotelBranch->check_in = $check_in;
            $objHotelBranch->check_out = $check_out;
            $objHotelBranch->rating = $rating;
            $objHotelBranch->latitude = $latitude;
            $objHotelBranch->longitude = $longitude;
            $objHotelBranch->map_formated_address = $map_formated_address;
            $objHotelBranch->map_input_text = $map_input_text;
            $objHotelBranch->save();

            // hotel categories before save categories
            $categsBeforeUpd = $objHotelBranch->getAllHotelCategories();

            if ($newIdHotel = $objHotelBranch->id) {

                // getHotel address
                if ($idHotelAddress = $objHotelBranch->getHotelIdAddress()) {
                $objAddress = new Address($idHotelAddress);
                } else {
                    $objAddress = new Address();
                }
                $objAddress->id_hotel = $newIdHotel;
                $objAddress->id_country = $country;
                $objAddress->id_state = $state;
                $objAddress->city = $city;
                $objAddress->postcode = $zipcode;
                $objAddress->alias = Tools::getValue('hotel_name_'.$defaultLangId);
                $objAddress->lastname = Tools::getValue('hotel_name_'.$defaultLangId);
                $objAddress->firstname = Tools::getValue('hotel_name_'.$defaultLangId);
                $objAddress->address1 = $address;
                $objAddress->phone = $phone;

                $objAddress->save();

                // Save refund rules of the hotels
                if ($hotelRefundRules = Tools::getValue('htl_refund_rules')) {
                    foreach ($hotelRefundRules as $key => $idRefundRule) {
                        $objBranchRefundRules = new HotelBranchRefundRules();
                        if (!$objBranchRefundRules->getHotelRefundRules(
                            $newIdHotel,
                            $idRefundRule
                        )) {
                            $objBranchRefundRules->id_hotel = $newIdHotel;
                            $objBranchRefundRules->id_refund_rule = $idRefundRule;
                            $objBranchRefundRules->position = $key + 1;
                            $objBranchRefundRules->save();
                        }
                    }
                }
                // delete unselected (but previously selected refund values)
                $objBranchRefundRules = new HotelBranchRefundRules();
                $objBranchRefundRules->deleteHotelRefundRules(
                    $newIdHotel,
                    0,
                    $hotelRefundRules
                );

                $groupIds = array();
                if ($dataGroupIds = Group::getGroups($this->context->language->id)) {
                    foreach ($dataGroupIds as $key => $value) {
                        $groupIds[] = $value['id_group'];
                    }
                }
                $objCountry = new Country();
                $countryName = $objCountry->getNameById(Configuration::get('PS_LANG_DEFAULT'), $country);
                if ($catCountry = $objHotelBranch->addCategory($countryName, false, $groupIds)) {
                    if ($state) {
                        $objState = new State();
                        $stateName = $objState->getNameById($state);
                        $catState = $objHotelBranch->addCategory($stateName, $catCountry, $groupIds);
                    } else {
                        $catState = $objHotelBranch->addCategory($city, $catCountry, $groupIds);
                    }
                    if ($catState) {
                        if ($catCity = $objHotelBranch->addCategory($city, $catState, $groupIds)) {
                            $hotelCatName = $objHotelBranch->hotel_name;
                            if ($catHotel = $objHotelBranch->addCategory(
                                $hotelCatName, $catCity, $groupIds, 1, $newIdHotel
                            )) {
                                $objHotelBranch = new HotelBranchInformation($newIdHotel);
                                $objHotelBranch->id_category = $catHotel;
                                $objHotelBranch->save();
                            }
                        }
                    }
                }
            }
            // hotel categories after save categories
            $categsAfterUpd = $objHotelBranch->getAllHotelCategories();

            // delete categories which not in hotel categories and also unused
            if ($unusedCategs = array_diff($categsBeforeUpd, $categsAfterUpd)) {
                if ($hotelCategories = $objHotelBranch->getAllHotelCategories()) {
                    foreach ($unusedCategs as $idCategory) {
                        if (!in_array($idCategory, $hotelCategories)
                            && $idCategory != Configuration::get('PS_HOME_CATEGORY')
                        ) {
                            $objCategory = new Category($idCategory);
                            $objCategory->delete();
                        }
                    }
                }
            }

            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                if ($idHotel) {
                    Tools::redirectAdmin(
                        self::$currentIndex.'&id='.(int) $newIdHotel.'&update'.$this->table.'&conf=4&token='.
                        $this->token
                    );
                } else {
                    Tools::redirectAdmin(
                        self::$currentIndex.'&id='.(int) $newIdHotel.'&update'.$this->table.'&conf=3&token='.
                        $this->token
                    );
                }
            } else {
                if ($idHotel) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                }
            }
        }
        if ($idHotel) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
    }

    public function ajaxProcessStateByCountryId()
    {
        $states = array();
        if ($idCountry = Tools::getValue('id_country')) {
            if ($statesbycountry = State::getStatesByIdCountry($idCountry)) {
                foreach ($statesbycountry as $key => $value) {
                    $states[$key]['id'] = $value['id_state'];
                    $states[$key]['name'] = $value['name'];
                }
            }
        }
        die(json_encode($states));
    }

    public function ajaxProcessUploadHotelImages()
    {
        $idHotel = Tools::getValue('id_hotel');
        if ($idHotel) {
            $invalidImg = ImageManager::validateUpload(
                $_FILES['hotel_image'],
                Tools::getMaxUploadSize()
            );
            if (!$invalidImg) {
                // Add Hotel images
                $kwargs = [
                    'id_hotel' => $idHotel,
                    'hotel_image' => $_FILES['hotel_image'],
                ];
                $objHotelImage = new HotelImage();
                $hotelImgPath = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/';
                $imageDetail = $objHotelImage->uploadHotelImages($_FILES['hotel_image'], $idHotel, $hotelImgPath);
                if ($imageDetail) {
                    die(json_encode($imageDetail));
                } else {
                    die(json_encode(array('hasError' => true)));
                }
            } else {
                die(
                    json_encode(
                        array('hasError' => true, 'message' => $_FILES['hotel_image']['name'].': '.$invalidImg)
                    )
                );
            }
        } else {
            die(json_encode(array('hasError' => true)));
        }
    }

    public function ajaxProcessChangeCoverImage()
    {
        $idImage = Tools::getValue('id_image');
        if ($idImage) {
            $idHotel = Tools::getValue('id_hotel');
            if ($coverImg = HotelImage::getCover($idHotel)) {
                $objHtlImage = new HotelImage((int) $coverImg['id']);
                $objHtlImage->cover = 0;
                $objHtlImage->save();
            }

            $objHtlImage = new HotelImage((int) $idImage);
            $objHtlImage->cover = 1;
            if ($objHtlImage->update()) {
                die(true);
            } else {
                die(false);
            }
        } else {
            die(false);
        }
    }

    public function ajaxProcessDeleteHotelImage()
    {
        if ($idImage = Tools::getValue('id_image')) {
            if ($idHotel = Tools::getValue('id_hotel')) {
                if (Validate::isLoadedObject($objHtlImage = new HotelImage((int) $idImage))) {
                    if ($objHtlImage->delete()) {
                        if (!HotelImage::getCover($idHotel)) {
                            $images = $objHtlImage->getImagesByHotelId($idHotel);
                            if ($images) {
                                $objHtlImage = new HotelImage($images[0]['id']);
                                $objHtlImage->cover = 1;
                                $objHtlImage->save();
                            }
                        }
                        die(true);
                    }
                }
            }
        }
        die(false);
    }

    public function ajaxProcessUpdateSlidesPosition()
    {
        if (($slideIds = Tools::getValue('slides'))
            && ($idHotel = Tools::getValue('id_hotel'))
        ) {
            $position = 1;
            $objBranchRefundRule = new HotelBranchRefundRules();
             foreach ($slideIds as $idRefundRule) {
                if ($hotelRefundRule = $objBranchRefundRule->getHotelRefundRules($idHotel, $idRefundRule)) {
                    $hotelRefundRule = reset($hotelRefundRule);
                    $objBranchRefundRule = new HotelBranchRefundRules($hotelRefundRule['id_hotel_refund_rule']);
                    $objBranchRefundRule->position = $position;
                    $objBranchRefundRule->save();
                    $position += 1;
                }
            }
            die(1);
        }
        die(0);
    }

    public function setMedia()
    {
        parent::setMedia();

        HotelHelper::assignDataTableVariables();
        $this->context->controller->addJS(_PS_JS_DIR_.'/datatable/jquery.dataTables.min.js');
        $this->context->controller->addJS(_PS_JS_DIR_.'/datatable/dataTables.bootstrap.js');

        Media::addJsDef(
            array(
                'filesizeError' => $this->l('File exceeds maximum size.'),
                'maxSizeAllowed' => Tools::getMaxUploadSize(),
                'sortRowsUrl' => $this->context->link->getAdminLink('AdminAddHotel'),
            )
        );
        // GOOGLE MAP
        $language = $this->context->language;
        $country = $this->context->country;
        if ($PS_API_KEY = Configuration::get('PS_API_KEY')) {
            $this->addJS(
                'https://maps.googleapis.com/maps/api/js?key='.$PS_API_KEY.'&libraries=places&language='.
                $language->iso_code.'&region='.$country->iso_code
            );
        }
        //tinymce
        $this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_.'tinymce.inc.js');
        }

        $this->addJqueryUI('ui.sortable');

        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/hotelImage.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/HotelReservationAdmin.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/HotelReservationAdmin.css');
    }
}
