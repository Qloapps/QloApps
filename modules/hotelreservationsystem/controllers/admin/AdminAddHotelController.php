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
                'optional' => true,
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

        $this->_new_list_header_design = true;

        return parent::renderList();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }
        $smartyVars = array();
        //tinymce setup
        $smartyVars['path_css'] = _THEME_CSS_DIR_;
        $smartyVars['ad'] = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $smartyVars['autoload_rte'] = true;
        $smartyVars['lang'] = true;
        $smartyVars['iso'] = $this->context->language->iso_code;
        //lang vars
        $currentLangId = $this->default_form_language ? $this->default_form_language : Configuration::get('PS_LANG_DEFAULT');
        $smartyVars['languages'] = Language::getLanguages(false);
        $smartyVars['currentLang'] = Language::getLanguage((int) $currentLangId);

        $smartyVars['defaultCurrency'] = Configuration::get('PS_CURRENCY_DEFAULT');
        $smartyVars['PS_SHORT_DESC_LIMIT'] = Configuration::get('PS_SHORT_DESC_LIMIT');
        if (!$smartyVars['PS_SHORT_DESC_LIMIT']) {
            $smartyVars['PS_SHORT_DESC_LIMIT'] = Configuration::PS_SHORT_DESC_LIMIT;
        }

        $countries = Country::getCountries($this->context->language->id, true);
        $smartyVars['country_var'] = $countries;

        $country = $this->context->country;
        $smartyVars['defaultCountry'] = $country->name[Configuration::get('PS_LANG_DEFAULT')];

        $idCountry = null;
        if ($this->object->id) {
            $idHotel = Tools::getValue('id');
            $hotelBranchInfo = new HotelBranchInformation($idHotel);
            $objCategory = new Category($hotelBranchInfo->id_category);

            $addressInfo = HotelBranchInformation::getAddress($idHotel);
            $idCountry = Tools::getValue('hotel_country', $addressInfo['id_country']);

            $smartyVars['edit'] =  1;
            $smartyVars['address_info'] = $addressInfo;
            $smartyVars['hotel_info'] = (array) $hotelBranchInfo;
            $smartyVars['link_rewrite_info'] = $objCategory->link_rewrite;
            $smartyVars['meta_title_info'] = $objCategory->meta_title;
            $smartyVars['meta_description_info'] = $objCategory->meta_description;
            $smartyVars['meta_keywords_info'] = $objCategory->meta_keywords;
            //Hotel Images
            $objHotelImage = new HotelImage();
            if ($hotelAllImages = $objHotelImage->getImagesByHotelId($idHotel)) {
                foreach ($hotelAllImages as &$image) {
                    $image['image_link'] = $this->context->link->getMediaLink($objHotelImage->getImageLink($image['id'],ImageType::getFormatedName('large')));
                    $image['image_link_small'] = $this->context->link->getMediaLink($objHotelImage->getImageLink($image['id'], ImageType::getFormatedName('small')));
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

            $smartyVars['order_restrict_date_info'] = HotelOrderRestrictDate::getDataByHotelId($idHotel);
            $objHotelFeatures = new HotelFeatures();
            $hotelFeatures = $this->object->getFeaturesOfHotelByHotelId($this->object->id);
            if ($features = $objHotelFeatures->HotelBranchSelectedFeaturesArray($hotelFeatures)) {
                foreach ($features as $idFeature => $feature) {
                    $features[$idFeature]['value'] = $idFeature;
                    $features[$idFeature]['input_name'] = 'id_feature_parents';
                    if (isset($feature['children']) && $feature['children']) {
                        $selectedChildFeatures = 0;
                        foreach ($feature['children'] as $childKey => $childFeature) {
                            $features[$idFeature]['children'][$childKey]['value'] = $childFeature['id'];
                            $features[$idFeature]['children'][$childKey]['input_name'] = 'id_features';
                            if (isset($childFeature['selected']) && $childFeature['selected']) {
                                $selectedChildFeatures++;
                            }
                        }

                        if ($selectedChildFeatures == count($feature['children'])) {
                            $features[$idFeature]['selected'] = true;
                        }
                    }
                }

                $tree = new HelperTree('hotel-features-tree', $features);
                $tree->setShowCollapseExpandButton(true)
                    ->setUseCheckBox(true)
                    ->setAutoSelectChildren(true)
                    ->setUseBulkActions(true);
                $treeContent = $tree->render();
                $smartyVars['hotel_feature_tree'] = $treeContent;
            }
        } else {
            $idCountry = Tools::getValue('hotel_country');
        }

        // manage state option
        $stateOptions = null;
        if ($idCountry) {
            $stateOptions = State::getStatesByIdCountry($idCountry);
        }

        $smartyVars['state_var'] = $stateOptions;
        $smartyVars['enabledDisplayMap'] = Configuration::get('PS_API_KEY') && Configuration::get('PS_MAP_ID') && Configuration::get('WK_GOOGLE_ACTIVE_MAP');
        $smartyVars['ps_img_dir'] = _PS_IMG_.'l/';
        $smartyVars['PS_MAX_CHECKOUT_OFFSET'] = (int) Configuration::get('PS_MAX_CHECKOUT_OFFSET');
        $smartyVars['PS_MIN_BOOKING_OFFSET'] = (int) Configuration::get('PS_MIN_BOOKING_OFFSET');
        $smartyVars['WK_ORDER_REFUND_ALLOWED'] = Configuration::get('WK_ORDER_REFUND_ALLOWED');

        $this->context->smarty->assign($smartyVars);

        Media::addJsDef(
            array(
                'img_dir_l' => _PS_IMG_.'l/',
                'PS_ALLOW_ACCENTED_CHARS_URL' => (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
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
        if (!$this->loadObject(true)) {
            return false;
        }

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
        $fax = Tools::getValue('fax');
        $activeRefund = Tools::getValue('active_refund');
        $enableUseGlobalMaxCheckoutOffset = Tools::getValue('enable_use_global_max_checkout_offset');
        $maxCheckoutOffset = trim(Tools::getValue('max_checkout_offset'));
        $enableUseGlobalMinBookingOffset = Tools::getValue('enable_use_global_min_booking_offset');
        $minBookingOffset = trim(Tools::getValue('min_booking_offset'));
        $latitude = Tools::getValue('loclatitude');
        $longitude = Tools::getValue('loclongitude');
        $map_formated_address = Tools::getValue('locformatedAddr');
        $map_input_text = Tools::getValue('googleInputField');
        $hotelFeatures = Tools::getValue('id_features', array());
        $shortDescriptionMaxChar = Configuration::get('PS_SHORT_DESC_LIMIT') ? Configuration::get('PS_SHORT_DESC_LIMIT') : Configuration::PS_SHORT_DESC_LIMIT;

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
            }
        }

        foreach ($languages as $lang) {
            if ($shortDescription = html_entity_decode(Tools::getValue('short_description_'.$lang['id_lang']))) {
                if (!Validate::isCleanHtml($shortDescription)) {
                    $this->errors[] = sprintf($this->l('Short description is not valid in %s'), $lang['name']);
                }

                $shortDescriptionLength = Tools::strlen($shortDescription);
                if ($shortDescriptionLength > $shortDescriptionMaxChar) {
                    $this->errors[] = sprintf($this->l('Short description cannot exceed %s characters in '), $shortDescriptionMaxChar).$lang['name'];
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

            if ($metaTitle = trim(Tools::getValue('meta_title_'.$lang['id_lang']))) {
                if (!Validate::isGenericName($metaTitle)) {
                    $this->errors[] = $this->l('Invalid Meta title in ').$lang['name'];
                } else if (Tools::strlen($metaTitle) > 128) {
                    $this->errors[] = $this->l('Meta title cannot exceed 128 characters in ').$lang['name'];
                }
            }

            if ($metaDescription = trim(Tools::getValue('meta_description_'.$lang['id_lang']))) {
                if (!Validate::isGenericName($metaDescription)) {
                    $this->errors[] = $this->l('Invalid Meta description in ').$lang['name'];
                } else if (Tools::strlen($metaDescription) > 255) {
                    $this->errors[] = $this->l('Meta description cannot exceed 128 characters in ').$lang['name'];
                }
            }

            if ($metaKeyWords = trim(Tools::getValue('meta_keywords_'.$lang['id_lang']))) {
                if (!Validate::isGenericName($metaKeyWords)) {
                    $this->errors[] = $this->l('Invalid Meta keywords in ').$lang['name'];
                } else if (Tools::strlen($metaKeyWords) > 255) {
                    $this->errors[] = $this->l('Meta keywords cannot exceed 128 characters in ').$lang['name'];
                }
            }
        }

        if ($activeRefund && !Configuration::get('WK_ORDER_REFUND_ALLOWED')) {
            $this->errors[] = $this->l('Enable order refunds to allow hotel-wise refunds.');
        }

        // validate Friendly URL values
        if (!trim(Tools::getValue('link_rewrite_'.$defaultLangId))) {
            $this->errors[] = $this->l('Friendly URL is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('link_rewrite_'.$lang['id_lang']))) {
                    if (!Validate::isLinkRewrite(Tools::getValue('link_rewrite_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid Friendly URL in ').$lang['name'];
                    }
                }
            }
        }

        if (!$phone = trim($phone)) {
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

        if (!$address = trim($address)) {
            $this->errors[] = $this->l('Address is required field.');
        }

        if ($fax && !Validate::isGenericName($fax)) {
            $this->errors[] = $this->l('Field fax in invalid.');
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
            $this->errors[] = $this->l('Enter a valid city name.');
        }

        //Since the address for the hotel is saved in the address table. We are validating the hotel address here manually.
        $addressValidation = Address::getValidationRules('Address');
        foreach ($addressValidation['size'] as $field => $maxSize) {
            if ('phone' == $field && Tools::strlen($phone) > $maxSize) {
                $this->errors[] = sprintf(
                    Tools::displayError('The Hotel phone number is too long (%1$d chars max).'),
                    $maxSize
                );
            } else if ('address1' == $field && Tools::strlen($address) > $maxSize) {
                $this->errors[] = sprintf(
                    Tools::displayError('The Hotel address is too long (%1$d chars max).'),
                    $maxSize
                );
            }  else if ('city' == $field && Tools::strlen($city) > $maxSize) {
                $this->errors[] = sprintf(
                    Tools::displayError('The Hotel city name is too long (%1$d chars max).'),
                    $maxSize
                );
            } else if ('postcode' == $field && Tools::strlen($zipcode) > $maxSize) {
                $this->errors[] = sprintf(
                    Tools::displayError('The Hotel zip code is too long (%1$d chars max).'),
                    $maxSize
                );
            } else if (($value = Tools::getValue($field)) && Tools::strlen($value) > $maxSize) {
                $this->errors[] = sprintf(
                    Tools::displayError('The Hotel %1$s field is too long (%2$d chars max).'),
                    $field,
                    $maxSize
                );
            }
        }

        if ($idHotel) {
            if (!$enableUseGlobalMaxCheckoutOffset) {
                if ($maxCheckoutOffset === '') {
                    $this->errors[] = $this->l('Maximum checkout offset is a required field.');
                } elseif (!$maxCheckoutOffset || !Validate::isUnsignedInt($maxCheckoutOffset)) {
                    $this->errors[] = $this->l('Maximum checkout offset is invalid.');
                }
            }

            if (!$enableUseGlobalMinBookingOffset) {
                if ($minBookingOffset === '') {
                    $this->errors[] = $this->l('Minimum booking offset is a required field.');
                } elseif (!Validate::isUnsignedInt($minBookingOffset)) {
                    $this->errors[] = $this->l('Minimum booking offset is invalid.');
                }
            }

            if (empty($this->errors)) {
                if (!$enableUseGlobalMaxCheckoutOffset && !$enableUseGlobalMinBookingOffset) {
                    if ($maxCheckoutOffset && $maxCheckoutOffset <= $minBookingOffset) {
                        $this->errors[] = $this->l('Field Maximum checkout offset cannot be be less than or equal to Minimum booking offset.');
                    }
                } else {
                    if (!$enableUseGlobalMaxCheckoutOffset && $maxCheckoutOffset <= Configuration::get('PS_MIN_BOOKING_OFFSET')) {
                        $this->errors[] = $this->l('Field Maximum checkout offset cannot be be less than or equal to global Minimum booking offset.');
                    } else if (!$enableUseGlobalMinBookingOffset && $minBookingOffset >= Configuration::get('PS_MAX_CHECKOUT_OFFSET')) {
                        $this->errors[] = $this->l('Field Minimum booking offset cannot be be greater than or equal to Global Maximum checkout offset.');
                    }
                }
            }
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
            $objHotelBranch->fax = $fax;

            // lang fields
            $hotelCatName = array();
            $linkRewriteArray = array();
            $metaTitleArray = array();
            $metaDescriptionArray = array();
            $metaKeywordsArray = array();
            foreach ($languages as $lang) {
                if (!trim(Tools::getValue('hotel_name_'.$lang['id_lang']))) {
                    $objHotelBranch->hotel_name[$lang['id_lang']] = trim(Tools::getValue(
                        'hotel_name_'.$defaultLangId
                    ));
                } else {
                    $objHotelBranch->hotel_name[$lang['id_lang']] = trim(Tools::getValue(
                        'hotel_name_'.$lang['id_lang']
                    ));
                }

                if (!trim(Tools::getValue('link_rewrite_'.$lang['id_lang']))) {
                    $linkRewriteArray[$lang['id_lang']] = Tools::getValue(
                        'link_rewrite_'.$defaultLangId
                    );
                } else {
                    $linkRewriteArray[$lang['id_lang']] = Tools::getValue(
                        'link_rewrite_'.$lang['id_lang']
                    );
                }

                $cleanShortDescription = Tools::getDescriptionClean(Tools::getValue('short_description_'.$lang['id_lang']));
                //Remove TinyMCE's Non-Breaking Spaces
                $cleanShortDescription = str_replace(chr(0xC2).chr(0xA0), "", $cleanShortDescription);
                if (!trim($cleanShortDescription)) {
                    $objHotelBranch->short_description[$lang['id_lang']] = Tools::getDescriptionClean(
                        Tools::getValue('short_description_'.$defaultLangId)
                    );
                } else {
                    $objHotelBranch->short_description[$lang['id_lang']] = Tools::getDescriptionClean(
                        Tools::getValue('short_description_'.$lang['id_lang'])
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


                if (!trim(Tools::getValue('meta_title_'.$lang['id_lang']))) {
                    $metaTitleArray[$lang['id_lang']] = Tools::getValue(
                        'meta_title_'.$defaultLangId
                    );
                } else {
                    $metaTitleArray[$lang['id_lang']] = Tools::getValue(
                        'meta_title_'.$lang['id_lang']
                    );
                }

                if (!trim(Tools::getValue('meta_description_'.$lang['id_lang']))) {
                    $metaDescriptionArray[$lang['id_lang']] = Tools::getValue(
                        'meta_description_'.$defaultLangId
                    );
                } else {
                    $metaDescriptionArray[$lang['id_lang']] = Tools::getValue(
                        'meta_description_'.$lang['id_lang']
                    );
                }

                if (!trim(Tools::getValue('meta_keywords_'.$lang['id_lang']))) {
                    $metaKeywordsArray[$lang['id_lang']] = Tools::getValue(
                        'meta_keywords_'.$defaultLangId
                    );
                } else {
                    $metaKeywordsArray[$lang['id_lang']] = Tools::getValue(
                        'meta_keywords_'.$lang['id_lang']
                    );
                }

            }
            $objHotelBranch->email = $email;
            $objHotelBranch->check_in = $check_in;
            $objHotelBranch->check_out = $check_out;
            $objHotelBranch->rating = $rating;
            $objHotelBranch->latitude = Validate::isFloat($latitude) ? Tools::ps_round($latitude, 8) : $latitude;
            $objHotelBranch->longitude = Validate::isFloat($longitude) ? Tools::ps_round($longitude, 8) : $longitude;
            $objHotelBranch->map_formated_address = $map_formated_address;
            $objHotelBranch->map_input_text = $map_input_text;
            $objHotelBranch->save();

            // hotel categories before save categories
            $categsBeforeUpd = $objHotelBranch->getAllHotelCategories();

            if ($newIdHotel = $objHotelBranch->id) {

                if ($primaryHotelId = Configuration::get('WK_PRIMARY_HOTEL')) {
                    if ($primaryHotelId == $objHotelBranch->id && !$objHotelBranch->active) {
                        $hotels = $objHotelBranch->hotelBranchesInfo(false, 1);
                        if (!empty($hotel = array_shift($hotels))) {
                            Configuration::updateValue('WK_PRIMARY_HOTEL', $objHotelBranch['id']);
                        } else {
                            $newPrimaryHotelId = Configuration::updateValue('WK_PRIMARY_HOTEL', 0);
                        }
                    }
                } else if ($objHotelBranch->active) {
                    Configuration::updateValue('WK_PRIMARY_HOTEL', $objHotelBranch->id);
                }
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
                $hotelName = $objHotelBranch->hotel_name[$defaultLangId];
                $hotelName = trim(preg_replace('/[0-9!<>,;?=+()@#"°{}_$%:]*$/u', '', $hotelName));
                $objAddress->alias = trim(substr($hotelName, 0, 32));
                $addressFirstName = $hotelName;
                $addressLastName = $hotelName;
                // If hotel name is length is greater than 32 then we split it into two
                if (Tools::strlen($hotelName) > 32) {
                    // Slicing and removing the extra spaces after slicing
                    $addressFirstName = trim(substr($hotelName, 0, 32));
                    // To remove the excess space from last name
                    if (!$addressLastName = trim(substr($hotelName, 32, 32))) {
                        // since the last name can also be an empty space we will then use first name as last name
                        $addressLastName = $addressFirstName;
                    }
                }

                $objAddress->firstname = $addressFirstName;
                $objAddress->lastname = $addressLastName;
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
                if ($catCountry = $objHotelBranch->addCategory(
                    array (
                        'name' => $countryName,
                        'group_ids' => $groupIds,
                        'parent_category' => false
                    )
                )) {
                    if ($state) {
                        $objState = new State();
                        $stateName = $objState->getNameById($state);
                    } else {
                        $stateName = $city;
                    }

                    if ($catState = $objHotelBranch->addCategory(
                        array (
                            'name' => $stateName,
                            'group_ids' => $groupIds,
                            'parent_category' => $catCountry
                        )
                    )) {
                        if ($catCity = $objHotelBranch->addCategory(
                            array (
                                'name' => $city,
                                'group_ids' => $groupIds,
                                'parent_category' => $catState
                            )
                        )) {
                            $hotelCatName = $objHotelBranch->hotel_name;
                            // add/update hotel category
                            if ($objHotelBranch->id_category) {
                                $objCategory = new Category($objHotelBranch->id_category);
                                $objCategory->name = $objHotelBranch->hotel_name;
                                $objCategory->link_rewrite = $linkRewriteArray;
                                $objCategory->meta_title = $metaTitleArray;
                                $objCategory->meta_description = $metaDescriptionArray;
                                $objCategory->meta_keywords = $metaKeywordsArray;
                                $objCategory->id_parent = $catCity;
                                $objCategory->save();
                                Category::regenerateEntireNtree();
                            } else {
                                if ($catHotel = $objHotelBranch->addCategory(
                                    array (
                                        'name' => $hotelCatName,
                                        'group_ids' => $groupIds,
                                        'parent_category' => $catCity,
                                        'is_hotel' => 1,
                                        'id_hotel' => $newIdHotel,
                                        'link_rewrite' => $linkRewriteArray,
                                        'meta_title' => $metaTitleArray,
                                        'meta_description' => $metaDescriptionArray,
                                        'meta_keywords' => $metaKeywordsArray
                                    )
                                )) {
                                    $objHotelBranch = new HotelBranchInformation($newIdHotel);
                                    $objHotelBranch->id_category = $catHotel;
                                    $objHotelBranch->save();
                                }
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
                            && $idCategory != Configuration::get('PS_LOCATIONS_CATEGORY')
                        ) {
                            $objCategory = new Category($idCategory);
                            $objCategory->delete();
                        }
                    }
                }
            }

            // update room types association after category update
            $objHotelBranch->updateRoomTypeCategories();

            if ($idHotel) {
                // save Maximum checkout offset and minimum booking offset
                $objHotelOrderRestrictDate = new HotelOrderRestrictDate();
                $restrictDateInfo = HotelOrderRestrictDate::getDataByHotelId($newIdHotel);
                if ($restrictDateInfo) {
                    $objHotelOrderRestrictDate = new HotelOrderRestrictDate($restrictDateInfo['id']);
                }

                $objHotelOrderRestrictDate->id_hotel = $newIdHotel;
                $objHotelOrderRestrictDate->use_global_max_checkout_offset = $enableUseGlobalMaxCheckoutOffset;
                $objHotelOrderRestrictDate->use_global_min_booking_offset = $enableUseGlobalMinBookingOffset;

                if (!$enableUseGlobalMaxCheckoutOffset) {
                    $objHotelOrderRestrictDate->max_checkout_offset = $maxCheckoutOffset;
                }

                if (!$enableUseGlobalMinBookingOffset) {
                    $objHotelOrderRestrictDate->min_booking_offset = $minBookingOffset;
                }

                $objHotelOrderRestrictDate->save();

                $objHotelFeatures = new HotelBranchFeatures();
                $objHotelFeatures->deleteBranchFeaturesByHotelId($idHotel);
                if (!$objHotelFeatures->assignFeaturesToHotel($idHotel, $hotelFeatures)) {
                    $this->errors[] = $this->l('Some problem occurred while assigning features to the hotel.');
                }
            }

            $conf = 3;
            if ($idHotel) {
                $conf = 4;
            }

            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                Tools::redirectAdmin(self::$currentIndex.'&id='.(int) $newIdHotel.'&update'.$this->table.'&conf='.$conf.'&token='.$this->token                );
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf='.$conf.'&token='.$this->token);
            }
        }

        if ($idHotel) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
    }

    public function processStatus()
    {
        parent::processStatus();
        if (empty($this->errors)) {
            if (Validate::isLoadedObject($objHotelBranch = new HotelBranchInformation(Tools::getValue('id')))) {
                if ($primaryHotelId = Configuration::get('WK_PRIMARY_HOTEL')) {
                    if ($primaryHotelId == $objHotelBranch->id && !$objHotelBranch->active) {
                        $hotels = $objHotelBranch->hotelBranchesInfo(false, 1);
                        if (!empty($hotel = array_shift($hotels))) {
                            Configuration::updateValue('WK_PRIMARY_HOTEL', $hotel['id']);
                        } else {
                            $newPrimaryHotelId = Configuration::updateValue('WK_PRIMARY_HOTEL', 0);
                        }
                    }
                } else {
                    $hotels = $objHotelBranch->hotelBranchesInfo(false, 1);
                    if (!empty($hotel = array_shift($hotels))) {
                        Configuration::updateValue('WK_PRIMARY_HOTEL', $hotel['id']);
                    }
                }
            }
        }
    }

    public function ajaxProcessStateByCountryId()
    {
        $response = array('status' => false, 'states' => array());
        if ($idCountry = Tools::getValue('id_country')) {
            if ($states = State::getStatesByIdCountry($idCountry)) {
                $response['status'] = true;
                $response['states'] = $states;
            }
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessUploadHotelImages()
    {
        $response = array('success' => false);
        $idHotel = Tools::getValue('id_hotel');
        if ($idHotel) {
            $invalidImg = ImageManager::validateUpload(
                $_FILES['hotel_image'],
                Tools::getMaxUploadSize()
            );
            if (!$invalidImg) {
                // Add Hotel images
                $objHotelImage = new HotelImage();
                $imageDetail = $objHotelImage->uploadHotelImages($_FILES['hotel_image'], $idHotel);
                if ($imageDetail) {
                    $response['success'] = true;
                    $imageDetail['image_link'] = $this->context->link->getMediaLink($objHotelImage->getImageLink($imageDetail['id'],ImageType::getFormatedName('large')));
                    $imageDetail['image_link_small'] = $this->context->link->getMediaLink($objHotelImage->getImageLink($imageDetail['id'], ImageType::getFormatedName('small')));
                    $response['data']['image_info'] = $imageDetail;
                    // get image row
                    $this->context->smarty->assign(array(
                        'image' => $imageDetail,
                        'hotel_info' => array('id' => $idHotel)
                    ));
                    $response['data']['image_row'] = $this->context->smarty->fetch(
                        _PS_MODULE_DIR_.$this->module->name.
                        '/views/templates/admin/add_hotel/_partials/htl-images-list-row.tpl'
                    );
                } else {
                    $response['errors'][] = $this->l('Unable to uploade image. Please try again');
                }
            } else {
                $response['errors'][] = $_FILES['hotel_image']['name'].': '.$invalidImg;
            }
        } else {
            $response['errors'][] = $this->l('Hotel info not found. Please try reloading the page');
        }
        $this->ajaxDie(json_encode($response));
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
        $this->addjQueryPlugin('tagify');

        HotelHelper::assignDataTableVariables();
        $this->context->controller->addJS(_PS_JS_DIR_.'/datatable/jquery.dataTables.min.js');
        $this->context->controller->addJS(_PS_JS_DIR_.'/datatable/dataTables.bootstrap.js');

        Media::addJsDef(
            array(
                'filesizeError' => $this->l('File exceeds maximum size.', null, true),
                'maxSizeAllowed' => Tools::getMaxUploadSize(),
                'sortRowsUrl' => $this->context->link->getAdminLink('AdminAddHotel'),
                'primaryHotelId' => Configuration::get('WK_PRIMARY_HOTEL'),
                'disableHotelMsg' => $this->l('Primary hotel for website will be updated to first available active hotel.', null, true),
                'PS_STORES_ICON' => $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_STORES_ICON')),
                'PS_MAP_ID' => ($PS_MAP_ID = Configuration::get('PS_MAP_ID'))
            )
        );
        // GOOGLE MAP
        $language = $this->context->language;
        $country = $this->context->country;
        if (($PS_API_KEY = Configuration::get('PS_API_KEY')) && $PS_MAP_ID) {
            $this->addJS(
                'https://maps.googleapis.com/maps/api/js?key='.$PS_API_KEY.'&libraries=places,marker&loading=async&language='.
                $language->iso_code.'&region='.$country->iso_code.'&callback=initGoogleMaps'
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
