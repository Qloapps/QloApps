<?php

class AdminAddHotelController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_branch_info';
        $this->className = 'HotelBranchInformation';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`state_id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = a.`country_id` AND cl.`id_lang` = '.Configuration::get('PS_LANG_DEFAULT').')';
        $this->_select = 's.`name` as `state_name`, cl.`name` as country_name';
        $this->context = Context::getContext();
        $this->fields_list = array();
        //$hotel_country_ids = $this->getHotelsCountryNameIdArray();
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
        $this->identifier = 'id';
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
                                                        'icon' => 'icon-trash',
                                                        'confirm' => $this->l('Delete selected items?'), ),
                                    );
        parent::__construct();
    }

    // public function getHotelsCountryNameIdArray()
    // {
    //     $all_htl_countries_ids = Db::getInstance()->executeS('SELECT `country_id` FROM `'._DB_PREFIX_.'htl_branch_info`');
    //     $countries_array = array();
    //     if ($all_htl_countries_ids) {
    //         foreach ($all_htl_countries_ids as $row) {
    //             $country = new Country($row['country_id'], Configuration::get('PS_LANG_DEFAULT'));
    //             $countries_array[$row['country_id']] = $country->name;
    //         }
    //         return $countries_array;
    //     } else {
    //         return false;
    //     }
    // }

    public function getCountryName($echo, $row)
    {
        if ($echo) {
            $country = new Country($echo, Configuration::get('PS_LANG_DEFAULT'));
            $return = $country->name;
        } else {
            $return = $this->l('Country Id missing');
        }

        return $return;
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
        //tinymce setup
        $this->context->smarty->assign('path_css', _THEME_CSS_DIR_);
        $this->context->smarty->assign('ad', __PS_BASE_URI__.basename(_PS_ADMIN_DIR_));
        $this->context->smarty->assign('autoload_rte', true);
        $this->context->smarty->assign('lang', true);
        $this->context->smarty->assign('iso', $this->context->language->iso_code);

        $obj_countries = new Country();
        $countries_var = Country::getCountries($this->context->language->id);

        $country = $this->context->country;
        $this->context->smarty->assign('defaultCountry', $country->name[Configuration::get('PS_LANG_DEFAULT')]);

        if ($this->display == 'add') {
            $this->context->smarty->assign('country_var', $countries_var);
        } elseif ($this->display == 'edit') {
            $hotel_id = Tools::getValue('id');
            $hotel_branch_info_obj = new HotelBranchInformation();
            $hotel_branch_info = $hotel_branch_info_obj->hotelBranchInfoById($hotel_id);

            $country_id = $hotel_branch_info['country_id'];
            $statesbycountry = State::getStatesByIdCountry($country_id);

            $states = array();
            if ($statesbycountry) {
                foreach ($statesbycountry as $key => $value) {
                    $states[$key]['id'] = $value['id_state'];
                    $states[$key]['name'] = $value['name'];
                }
            }
            $this->context->smarty->assign('edit', 1);
            $this->context->smarty->assign('country_var', $countries_var);
            $this->context->smarty->assign('state_var', $states);
            $this->context->smarty->assign('hotel_info', $hotel_branch_info);
            //Hotel Images
            $objHotelImage = new HotelImage();
            $hotelAllImages = $objHotelImage->getAllImagesByHotelId($hotel_id);
            if ($hotelAllImages) {
                foreach ($hotelAllImages as &$image) {
                    $image['path'] = _MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/'.$image['hotel_image_id'].'.jpg';
                }
                $this->context->smarty->assign('hotelImages', $hotelAllImages);
            }
        }
        $this->context->smarty->assign('enabledDisplayMap', Configuration::get('WK_GOOGLE_ACTIVE_MAP'));
        

        $this->fields_form = array(
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            );

        return parent::renderForm();
    }

    public function processSave()
    {
        $hotel_id = Tools::getValue('id');
        $hotel_name = Tools::getValue('hotel_name');
        $phone = Tools::getValue('phone');
        $email = Tools::getValue('email');
        $check_in = Tools::getValue('check_in');
        $check_out = Tools::getValue('check_out');
        $short_description = Tools::getValue('short_description');
        $description = Tools::getValue('description');
        $rating = Tools::getValue('hotel_rating');
        $city = Tools::getValue('hotel_city');
        $state = Tools::getValue('hotel_state');
        $country = Tools::getValue('hotel_country');
        $policies = Tools::getValue('hotel_policies');
        $zipcode = Tools::getValue('hotel_postal_code');
        $address = Tools::getValue('address');
        $active = Tools::getValue('ENABLE_HOTEL');
        $latitude = Tools::getValue('loclatitude');
        $longitude = Tools::getValue('loclongitude');
        $map_formated_address = Tools::getValue('locformatedAddr');
        $map_input_text = Tools::getValue('googleInputField');

        if ($hotel_name == '') {
            $this->errors[] = Tools::displayError('Hotel name is required field.');
        } elseif (!Validate::isGenericName($hotel_name)) {
            $this->errors[] = Tools::displayError($this->l('Hotel name must not have Invalid characters <>;=#{}'));
        }

        if (!$phone) {
            $this->errors[] = Tools::displayError('Phone number is required field.');
        } elseif (!Validate::isPhoneNumber($phone)) {
            $this->errors[] = Tools::displayError('Please enter a valid phone number.');
        }

        if ($email == '') {
            $this->errors[] = Tools::displayError('Email is required field.');
        } elseif (!Validate::isEmail($email)) {
            $this->errors[] = Tools::displayError('Please enter a valid email.');
        }

        if ($check_in == '') {
            $this->errors[] = Tools::displayError('Check In time is required field.');
        }

        if ($check_out == '') {
            $this->errors[] = Tools::displayError('Check Out Time is required field.');
        }

        if ($zipcode == '') {
            $this->errors[] = Tools::displayError('Postal Code is required field.');
        } elseif (!Validate::isPostCode($zipcode)) {
            $this->errors[] = Tools::displayError('Enter a Valid Postal Code.');
        }

        if (!$rating) {
            $this->errors[] = Tools::displayError('Rating is required field.');
        }

        if ($address == '') {
            $this->errors[] = Tools::displayError('Address is required field.');
        }

        if (!$country) {
            $this->errors[] = Tools::displayError('Country is required field.');
        } else {
            $statesbycountry = State::getStatesByIdCountry($country);
            /*If selected country has states only the validate state field*/

            if (!$state) {
                if ($statesbycountry) {
                    $this->errors[] = Tools::displayError('State is required field.');
                }
            }
        }

        if ($city == '') {
            $this->errors[] = Tools::displayError('City is required field.');
        } elseif (!Validate::isCityName($city)) {
            $this->errors[] = Tools::displayError('Enter a Valid City Name.');
        }

        //validate Hotel's other images
        if (isset($_FILES['hotel_images']) && $_FILES['hotel_images']) {
            $imgErr = 1;
            $htlImages = $_FILES['hotel_images'];
            if (count($htlImages['name'])) {
                $objHotelHelper = new HotelHelper();
                foreach ($htlImages['name'] as $imageName) {
                    if ($imageName) {
                        if (!$objHotelHelper->validImageExt($imageName)) {
                            $this->errors[] = Tools::displayError('<strong>'.$imageName.'</strong> : Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
                        }
                    }
                }
            }
        }
        if (!count($this->errors)) {
            if ($hotel_id) {
                $obj_hotel_info = new HotelBranchInformation($hotel_id);
            } else {
                $obj_hotel_info = new HotelBranchInformation();
            }

            if ($obj_hotel_info->id) {
                if (!$active) {
                    $obj_htl_rm_info = new HotelRoomType();
                    $ids_product = $obj_htl_rm_info->getIdProductByHotelId($obj_hotel_info->id);
                    if (isset($ids_product) && $ids_product) {
                        foreach ($ids_product as $key_prod => $value_prod) {
                            $obj_product = new Product($value_prod['id_product']);
                            if ($obj_product->active) {
                                $obj_product->toggleStatus();
                            }
                        }
                    }
                }
            }

            $obj_hotel_info->active = $active;
            $obj_hotel_info->hotel_name = $hotel_name;
            $obj_hotel_info->phone = $phone;
            $obj_hotel_info->email = $email;
            $obj_hotel_info->check_in = $check_in;
            $obj_hotel_info->check_out = $check_out;
            $obj_hotel_info->short_description = $short_description;
            $obj_hotel_info->description = $description;
            $obj_hotel_info->rating = $rating;
            $obj_hotel_info->city = $city;
            $obj_hotel_info->state_id = $state;
            $obj_hotel_info->country_id = $country;
            $obj_hotel_info->zipcode = $zipcode;
            $obj_hotel_info->policies = $policies;
            $obj_hotel_info->address = $address;
            $obj_hotel_info->latitude = $latitude;
            $obj_hotel_info->longitude = $longitude;
            $obj_hotel_info->map_formated_address = $map_formated_address;
            $obj_hotel_info->map_input_text = $map_input_text;
            $obj_hotel_info->save();

            $new_hotel_id = $obj_hotel_info->id;
            $hotel_img_path = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/';

            //upload hotel's other images
            if (isset($_FILES['hotel_images']) && $_FILES['hotel_images']) {
                $objHotelImage = new HotelImage();
                $hotelImgPath = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/';
                $objHotelImage->uploadHotelImages($_FILES['hotel_images'], $new_hotel_id, $hotelImgPath);
            }

            if ($new_hotel_id) {
                $grp_ids = array();
                $data_grp_ids = Group::getGroups($this->context->language->id);

                foreach ($data_grp_ids as $key => $value) {
                    $grp_ids[] = $value['id_group'];
                }
                //test
                $country_name = (new Country())->getNameById($this->context->language->id, $country);
                $cat_country = $obj_hotel_info->addCategory($country_name, false, $grp_ids);

                if ($cat_country) {
                    if ($state) {
                        $state_name = (new State())->getNameById($state);
                        $cat_state = $obj_hotel_info->addCategory($state_name, $cat_country, $grp_ids);
                    } else {
                        $cat_state = $obj_hotel_info->addCategory($city, $cat_country, $grp_ids);
                    }
                }
                if ($cat_state) {
                    $cat_city = $obj_hotel_info->addCategory($city, $cat_state, $grp_ids);
                }

                if ($cat_city) {
                    $cat_hotel = $obj_hotel_info->addCategory($hotel_name, $cat_city, $grp_ids, 1, $new_hotel_id);
                }

                if ($cat_hotel) {
                    $obj_hotel_info = new HotelBranchInformation($new_hotel_id);
                    $obj_hotel_info->id_category = $cat_hotel;
                    $obj_hotel_info->save();
                }
            }

            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                if ($hotel_id) {
                    Tools::redirectAdmin(self::$currentIndex.'&id='.(int) $new_hotel_id.'&update'.$this->table.'&conf=4&token='.$this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&id='.(int) $new_hotel_id.'&update'.$this->table.'&conf=3&token='.$this->token);
                }
            } else {
                if ($hotel_id) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                }
            }
        } else {
            if ($hotel_id) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function ajaxProcessDeleteHotelImage()
    {
        $id_htl_img = Tools::getValue('id_htl_img');
        $objHotelImage = new HotelImage($id_htl_img);
        if ($objHotelImage->id) {
            if ($objHotelImage->delete()) {
                die('1');
            }
        }
        die('0');
    }

    public function ajaxProcessStateByCountryId()
    {
        $country_id = Tools::getValue('id_country');
        $states = array();
        $statesbycountry = State::getStatesByIdCountry($country_id);
        if ($statesbycountry) {
            $states = array();
            foreach ($statesbycountry as $key => $value) {
                $states[$key]['id'] = $value['id_state'];
                $states[$key]['name'] = $value['name'];
            }
            if (isset($states)) {
                die(Tools::jsonEncode($states));
            } else {
                die(Tools::jsonEncode($states));
            }
        } else {
            die(Tools::jsonEncode($states));
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        // GOOGLE MAP
        $language = $this->context->language;
        $country = $this->context->country;
        $WK_GOOGLE_API_KEY = Configuration::get('WK_GOOGLE_API_KEY');
        $this->addJs("https://maps.googleapis.com/maps/api/js?key=$WK_GOOGLE_API_KEY&libraries=places&language=$language->iso_code&region=$country->iso_code");

        //tinymce
        $this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');

        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_.'tinymce.inc.js');
        }

        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
    }
}
