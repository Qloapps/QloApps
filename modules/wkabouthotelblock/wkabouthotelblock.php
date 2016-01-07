<?php
if (!defined('_PS_VERSION_'))
    exit;

class WkAboutHotelBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wkabouthotelblock';
        $this->tab = 'front_office_features';
        $this->version = '0.0.2';
        $this->author = 'webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('About Hotel Block');
        $this->description = $this->l('Now show Block about your hotel using this module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    private function _postValidation()
    {
        /*if (Tools::isSubmit('btnSubmit'))
        {
            return 1;
        }*/
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit'))
        {
            Configuration::updateValue('WHY_THIS_HOTEL_HEADING', Tools::getValue('WHY_THIS_HOTEL_HEADING'));
            Configuration::updateValue('WHY_THIS_HOTEL_CONTENT', Tools::getValue('WHY_THIS_HOTEL_CONTENT'));
            Configuration::updateValue('ABOUT_HOTEL_HEADING', Tools::getValue('ABOUT_HOTEL_HEADING'));
            Configuration::updateValue('ABOUT_HOTEL_CONTENT', Tools::getValue('ABOUT_HOTEL_CONTENT'));
        }

        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
        $module_config = $this->context->link->getAdminLink('AdminModules');
        Tools::redirectAdmin($module_config.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit'))
        {
            $this->_postValidation();
            if (!count($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors as $err)
                    $this->_html .= $this->displayError($err);
        }
        else
            $this->_html .= '<br />';

        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form[0]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Heading1'),
                        'name' => 'WHY_THIS_HOTEL_HEADING',
                        'hint' => $this->l('Heading to describe why to choose your hotel.')
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Heading1 Content'),
                        'name' => 'WHY_THIS_HOTEL_CONTENT',
                        'rows' => '6',
                        'hint' => $this->l('Content for heading1.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Heading2'),
                        'name' => 'ABOUT_HOTEL_HEADING',
                        'hint' => $this->l('Heading to Describe about your hotel.')
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Heading2 Content'),
                        'name' => 'ABOUT_HOTEL_CONTENT',
                        'rows' => '6',
                        'hint' => $this->l('Content for heading2.')
                    )
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

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm($fields_form);
    }

    
    public function hookDisplayHome()
    {
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/hotel-aboutblock.css');

        $why_hotel_head = Configuration::get('WHY_THIS_HOTEL_HEADING');
        $about_hotel_content = Configuration::get('ABOUT_HOTEL_CONTENT');
        $about_hotel_head = Configuration::get('ABOUT_HOTEL_HEADING');
        $why_hotel_content = Configuration::get('WHY_THIS_HOTEL_CONTENT');

        $this->context->smarty->assign('why_hotel_head', $why_hotel_head);
        $this->context->smarty->assign('about_hotel_content', $about_hotel_content);
        $this->context->smarty->assign('about_hotel_head', $about_hotel_head);
        $this->context->smarty->assign('why_hotel_content', $why_hotel_content);

        return $this->display(__FILE__, 'homeabouthotelcontent.tpl');
    }

    public function getConfigFieldsValues()
    {
        return array(
            'WHY_THIS_HOTEL_HEADING' => Tools::getValue('WHY_THIS_HOTEL_HEADING', Configuration::get('WHY_THIS_HOTEL_HEADING')),
            'ABOUT_HOTEL_CONTENT' => Tools::getValue('ABOUT_HOTEL_CONTENT', Configuration::get('ABOUT_HOTEL_CONTENT')),
            'ABOUT_HOTEL_HEADING' => Tools::getValue('ABOUT_HOTEL_HEADING', Configuration::get('ABOUT_HOTEL_HEADING')),
            'WHY_THIS_HOTEL_CONTENT' => Tools::getValue('WHY_THIS_HOTEL_CONTENT', Configuration::get('WHY_THIS_HOTEL_CONTENT')),
        );
    }

    public function deleteConfigKeys()
    {
        $var = array('WHY_THIS_HOTEL_HEADING',
                    'ABOUT_HOTEL_CONTENT', 'ABOUT_HOTEL_HEADING',
                    'WHY_THIS_HOTEL_CONTENT');

        foreach ($var as $key)
            if (!Configuration::deleteByName($key))
                return false;
        
        return true;
    }

    public function insertDefaultHotelEntries()
    {
        //from about hotel module
        $about_htl_head = $this->l('About Gresham Palace');
        $about_htl_content = $this->l('A la carte, des produits fins, régionaux et originaux. Le bar quand à lui sera satisfaire les amateurs de whisky, spiritueux et grandes maisons de champagne. A la carte, des produits fins, régionaux et originaux. Le bar quand à lui sera satisfaire les amateurs de whisky, spiritueux et grandes maisons de champagne.');
        $ehy_this_htl_head = $this->l('Why Hotel Gresham Palace');
        $why_this_htl_content = $this->l('A la carte, des produits fins, régionaux et originaux. Le bar quand à lui sera satisfaire les amateurs de whisky, spiritueux et grandes maisons de champagne.');

        Configuration::updateValue('ABOUT_HOTEL_HEADING', $about_htl_head);
        Configuration::updateValue('ABOUT_HOTEL_CONTENT', $about_htl_content);
        Configuration::updateValue('WHY_THIS_HOTEL_HEADING', $ehy_this_htl_head);
        Configuration::updateValue('WHY_THIS_HOTEL_CONTENT', $why_this_htl_content);
        return true;
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displayHome')
            || !$this->insertDefaultHotelEntries()
            )
            return false;
        return true;
    }

    public function uninstall($keep = true)
    {
        if(!parent::uninstall() 
            || !$this->deleteConfigKeys())
            return false;

        return true;
    }
}
