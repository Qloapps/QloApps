<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once ('define.php');

class wkHotelFilterBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wkhotelfilterblock';
        $this->author = 'webkul';
        $this->tab = 'front_office_features';
        $this->version = '0.0.2';
        $this->context = Context::getContext();

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('layered filters and sorting block');
        $this->description = $this->l('Hotel filter and sorting block');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install() 
            || !$this->registerHook('header')
            || !$this->registerHook('displayLeftColumn'))
            return false;

        //set default config variable
        Configuration::updateValue('SHOW_RATTING_FILTER', 1);
        Configuration::updateValue('SHOW_AMENITIES_FILTER', 1);
        Configuration::updateValue('SHOW_PRICE_FILTER', 1);

        return true;
    }

    public function hookHeader()
    {
        $this->context->controller->addJQueryUI('ui.slider');
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall() || !$this->deleteConfigKeys())
            return false;

        return true;
    }

    public function hookDisplayLeftColumn()
    {
        if ($this->context->controller->php_self == 'category')
        {
            $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/wkhotelfilterblock.js');
            $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkhotelfilterblock.css');

            $all_feat = FeatureCore::getFeatures($this->context->language->id);

            $htl_id_category = Tools::getValue('id_category');
            $id_hotel = HotelBranchInformation::getHotelIdByIdCategory($htl_id_category);

            $max_adult = HotelRoomType::getMaxAdults($id_hotel);
            $max_child = HotelRoomType::getMaxChild($id_hotel);

            $category = new Category($htl_id_category);

            if (!($date_from = Tools::getValue('date_from')))
            {
                $date_from = date('Y-m-d');
                $date_to = date('Y-m-d', strtotime($date_from)+ 86400);
            } 
            if (!($date_to = Tools::getValue('date_to'))) 
                $date_to = date('Y-m-d', strtotime($date_from)+ 86400);

            $obj_rm_type = new HotelRoomType();
            $room_types = $obj_rm_type->getIdProductByHotelId($id_hotel, 0, 1, 1);

            $prod_price = array();
            if ($room_types)
                foreach ($room_types as $key => $value)
                    $prod_price[] = Product::getPriceStatic($value['id_product'], HotelBookingDetail::useTax());

            if (Configuration::get('PS_REWRITING_SETTINGS'))
                $cat_link = $this->context->link->getCategoryLink($category).'?date_from='.$date_from.'&date_to='.$date_to;
            else
                $cat_link = $this->context->link->getCategoryLink($category).'&date_from='.$date_from.'&date_to='.$date_to;

            $currency = $this->context->currency;

            $config = $this->getConfigFieldsValues();

            $obj_booking_detail = new HotelBookingDetail();
            $num_days = $obj_booking_detail->getNumberOfDays($date_from, $date_to);

            $warning_num = Configuration::get('WK_ROOM_LEFT_WARNING_NUMBER');

            $ratting_img = _MODULE_DIR_.$this->name.'/views/img/stars-sprite-image.png';
            $this->context->smarty->assign(array(
                'warning_num' => $warning_num,
                'all_feat' => $all_feat,
                'max_adult' => $max_adult,
                'max_child' => $max_child,
                'cat_link' => $cat_link,
                'ratting_img' => $ratting_img,
                'currency' => $currency,
                'date_from' => $date_from,
                'date_to' => $date_to,
                'num_days' => $num_days,
                'config' => $config,
                'min_price' => $prod_price ? min($prod_price): 0,
                'max_price' => $prod_price ? max($prod_price): 0,
            ));

            return $this->display(__FILE__, 'htlfilterblock.tpl');
        }
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit'))
            $this->_postProcess();
        else
            $this->_html .= '<br />';

        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form = array();
        $fields_form[0]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Guest Rating Filter'),
                        'name' => 'SHOW_RATTING_FILTER',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('If yes, it will display Guest Ratting Filter.')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Amenities Filter'),
                        'name' => 'SHOW_AMENITIES_FILTER',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('If yes, it will display Amenities Filter.')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Price Filter'),
                        'name' => 'SHOW_PRICE_FILTER',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('If yes, it will display Price Filter.')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->submit_action = 'btnSubmit';
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;

        //Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        //$this->fields_form = array();
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues()
    {
        $config_vars = array(
            'SHOW_RATTING_FILTER' => Tools::getValue('SHOW_RATTING_FILTER', Configuration::get('SHOW_RATTING_FILTER')),
            'SHOW_AMENITIES_FILTER' => Tools::getValue('SHOW_AMENITIES_FILTER', Configuration::get('SHOW_AMENITIES_FILTER')),
            'SHOW_PRICE_FILTER' => Tools::getValue('SHOW_PRICE_FILTER', Configuration::get('SHOW_PRICE_FILTER')),
        );
        return $config_vars;
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit'))
        {
            Configuration::updateValue('SHOW_RATTING_FILTER', Tools::getValue('SHOW_RATTING_FILTER'));
            Configuration::updateValue('SHOW_AMENITIES_FILTER', Tools::getValue('SHOW_AMENITIES_FILTER'));
            Configuration::updateValue('SHOW_PRICE_FILTER', Tools::getValue('SHOW_PRICE_FILTER'));
        }

        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
        $module_config = $this->context->link->getAdminLink('AdminModules');
        Tools::redirectAdmin($module_config.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
    }

    public function deleteConfigKeys()
    {
        $var = array('SHOW_RATTING_FILTER',
                    'SHOW_AMENITIES_FILTER', 
                    'SHOW_PRICE_FILTER');

        foreach ($var as $key)
            if (!Configuration::deleteByName($key))
                return false;
        
        return true;
    }
}