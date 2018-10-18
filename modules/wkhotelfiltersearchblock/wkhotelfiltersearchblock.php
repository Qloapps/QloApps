<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class wkhotelfiltersearchblock extends Module
{
    public function __construct()
    {
        $this->name = 'wkhotelfiltersearchblock';
        $this->author = 'webkul';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->context = Context::getContext();

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Hotel filter search block');
        $this->description = $this->l('Hotel filter search block');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displayLeftColumn')) {
            return false;
        }

        return true;
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function hookDisplayLeftColumn()
    {
        if ($this->context->controller->php_self == 'category') {
            if (Tools::isSubmit('filter_search_btn')) {
                $hotel_cat_id = Tools::getValue('hotel_cat_id');
                $check_in = Tools::getValue('check_in_time');
                $check_out = Tools::getValue('check_out_time');

                $check_in = date('Y-m-d', strtotime($check_in));
                $check_out = date('Y-m-d', strtotime($check_out));

                $curr_date = date('Y-m-d');
                $max_order_date = Tools::getValue('max_order_date');
                $max_order_date = date('Y-m-d', strtotime($max_order_date));
                $error = false;

                if ($hotel_cat_id == '') {
                    $error = 1;
                } elseif ($check_in == '' || !Validate::isDate($check_in)) {
                    $error = 2;
                } elseif ($check_out == '' || !Validate::isDate($check_out)) {
                    $error = 3;
                } elseif ($check_in < $curr_date) {
                    $error = 5;
                } elseif ($check_out <= $check_in) {
                    $error = 4;
                } elseif ($max_order_date < $check_in || $max_order_date < $check_out) {
                    $error = 6;
                }

                if (!$error) {
                    if (Configuration::get('PS_REWRITING_SETTINGS')) {
                        $redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'?date_from='.$check_in.'&date_to='.$check_out;
                    } else {
                        $redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'&date_from='.$check_in.'&date_to='.$check_out;
                    }
                } else {
                    if (Configuration::get('PS_REWRITING_SETTINGS')) {
                        $redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'?error='.$error;
                    } else {
                        $redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'&error='.$error;
                    }
                }

                Tools::redirect($redirect_link);
            }

            if (Tools::getValue('error')) {
                $this->context->smarty->assign('error', Tools::getValue('error'));
            }

            $hotel_branch_obj = new HotelBranchInformation();
            $htl_id_category = Tools::getValue('id_category');
            $id_hotel = HotelBranchInformation::getHotelIdByIdCategory($htl_id_category);
            $category = new Category((int) $htl_id_category);
            $parent_dtl = $hotel_branch_obj->getCategoryDataByIdCategory((int) $category->id_parent);

            if (!($date_from = Tools::getValue('date_from'))) {
                $date_from = date('Y-m-d');
                $date_to = date('Y-m-d', strtotime($date_from) + 86400);
            }
            if (!($date_to = Tools::getValue('date_to'))) {
                $date_to = date('Y-m-d', strtotime($date_from) + 86400);
            }

            $search_data['parent_data'] = $parent_dtl;
            $search_data['date_from'] = date('d-m-Y', strtotime($date_from));
            $search_data['date_to'] = date('d-m-Y', strtotime($date_to));
            $search_data['htl_dtl'] = $hotel_branch_obj->hotelBranchesInfo(0, 1, 1, $id_hotel);
            $search_data['location'] = $search_data['htl_dtl']['city'];
            if (isset($search_data['htl_dtl']['state_name'])) {
                $search_data['location'] .= ', '.$search_data['htl_dtl']['state_name'];
            }
            $search_data['location'] .= ', '.$search_data['htl_dtl']['country_name'];

            $locationEnabled = Configuration::get('WK_HOTEL_LOCATION_ENABLE');
            if ($locationEnabled) {
                $hotel_info = $hotel_branch_obj->hotelBranchesInfo(0, 1);
                $totalActiveHotels = count($hotel_info);
                $hotel_info = $hotel_branch_obj->hotelBranchInfoByCategoryId($htl_id_category);
            } else {
                $hotel_info = $hotel_branch_obj->hotelBranchesInfo(0, 1);
                $totalActiveHotels = count($hotel_info);
            }
            foreach ($hotel_info as &$hotel) {
                $maxOrderDate = HotelOrderRestrictDate::getMaxOrderDate($hotel['id']);
                $hotel['max_order_date'] = date('Y-m-d', strtotime($maxOrderDate));
            }
            $max_order_date = HotelOrderRestrictDate::getMaxOrderDate($id_hotel);
            $this->context->smarty->assign(
                array(
                    'totalActiveHotels' => $totalActiveHotels,
                    'booking_date_to' => $date_to,
                    'search_data' => $search_data,
                    'all_hotels_info' => $hotel_info,
                    'show_only_active_htl' => Configuration::get('WK_HOTEL_NAME_ENABLE'),
                    'location_enable' => $locationEnabled,
                    'max_order_date' => date('Y-m-d', strtotime($max_order_date)),
                )
            );
            $this->context->controller->addJS(_PS_MODULE_DIR_.'hotelreservationsystem/views/js/roomSearchBlock.js');
            $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkhotelfiltersearchblock.css');
            $this->context->controller->addCSS(_PS_MODULE_DIR_.'hotelreservationsystem/views/css/datepickerCustom.css');

            return $this->display(__FILE__, 'htlfiltersearchblock.tpl');
        }
    }
}
