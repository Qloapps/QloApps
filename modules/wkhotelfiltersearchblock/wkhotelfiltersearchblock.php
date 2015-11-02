<?php

if (!defined('_PS_VERSION_'))
    exit;

class wkHotelFilterSearchBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wkhotelfiltersearchblock';
        $this->author = 'webkul';
        $this->tab = 'front_office_features';
        $this->version = '1.6.1.1';
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
            || !$this->registerHook('displayLeftColumn'))
            return false;

        return true;
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall())
            return false;

        return true;
    }

    public function hookDisplayLeftColumn()
    {
        if ($this->context->controller->php_self == 'category')
        {
            if (Tools::isSubmit('filter_search_btn'))
            {
                $hotel_cat_id = Tools::getValue('hotel_cat_id');
                $check_in = Tools::getValue('check_in_time');
                $check_out = Tools::getValue('check_out_time');

                $error = false;

                if ($hotel_cat_id == '')
                    $error = 1;
                elseif ($check_in == '' || !Validate::isDate($check_in))
                    $error = 1;
                elseif ($check_out == '' || !Validate::isDate($check_out))
                    $error = 1;
                elseif ($check_out <= $check_in)
                    $error = 1;

                if (!$error)
                {
                    if (Configuration::get('PS_REWRITING_SETTINGS'))
                        $redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'?date_from='.$check_in.'&date_to='.$check_out;
                    else
                        $redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'&date_from='.$check_in.'&date_to='.$check_out;
                }
                else
                {
                    if (Configuration::get('PS_REWRITING_SETTINGS'))
                        $redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'?error='.$error;
                    else
                        $redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'&error='.$error;
                }

                Tools::redirect($redirect_link);
            }

            if (Tools::getValue('error')) 
                $this->context->smarty->assign('error', Tools::getValue('error'));

            $location_enable = Configuration::get('WK_HOTEL_LOCATION_ENABLE');
            
            $hotel_branch_obj = new HotelBranchInformation();
            
            $htl_id_category = Tools::getValue('id_category');
            $category = new Category((int)$htl_id_category);
            $parent_dtl = $hotel_branch_obj->getCategoryDataByIdCategory((int)$category->id_parent);

            if (!($date_from = Tools::getValue('date_from')))
            {
                $date_from = date('Y-m-d');
                $date_to = date('Y-m-d', strtotime($date_from)+ 86400);
            } 
            if (!($date_to = Tools::getValue('date_to'))) 
                $date_to = date('Y-m-d', strtotime($date_from)+ 86400);

            $search_data['parent_data'] = $parent_dtl;
            $search_data['date_from'] = $date_from;
            $search_data['date_to'] = $date_to;
            $search_data['htl_dtl'] = $hotel_branch_obj->hotelBranchInfoById(HotelBranchInformation::getHotelIdByIdCategory($htl_id_category));

            $hotel_info = $hotel_branch_obj->getActiveHotelBranchesInfo();
            $this->context->smarty->assign(
                array(
                    'search_data'=>$search_data,
                    'all_hotels_info'=>$hotel_info,
                    'location_enable'=>$location_enable,
                    ));

        	$this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkhotelfiltersearchblock.css');

    		return $this->display(__FILE__, 'htlfiltersearchblock.tpl');
        }
    }
}