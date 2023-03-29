<?php
/*
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class QloBlockContact extends Module
{
    public function __construct()
    {
        $this->name = 'qloblockcontact';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('QloApps Contact Block');
        $this->description = $this->l('Allows admin to add customer service information to website navigation bar.');
        $this->confirmUnsinstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install()
            || !Configuration::updateValue('QBC_PHONE', '')
            || !Configuration::updateValue('QBC_EMAIL', '')
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('displayNav')
            || !$this->registerHook('displayExternalNavigationHook')
        ) {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitModuleConfig')) {
            Configuration::updateValue('QBC_PHONE', Tools::getValue('QBC_PHONE'));
            Configuration::updateValue('QBC_EMAIL', Tools::getValue('QBC_EMAIL'));
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&conf=6'
            );
        }
        return $this->renderForm();
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->addCSS($this->getPathUri().'views/css/hook/display-nav.css');
    }

    public function hookDisplayNav()
    {
        $this->smarty->assign(array(
            'phone' => Configuration::get('QBC_PHONE'),
            'email' => Configuration::get('QBC_EMAIL'),
        ));
        return $this->display(__FILE__, 'display-nav.tpl');
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Phone'),
                        'hint' => $this->l('The phone number used for customer service.'),
                        'name' => 'QBC_PHONE',
                        'class' => 'fixed-width-xxl',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Email'),
                        'hint' => $this->l('The email used for customer service.'),
                        'name' => 'QBC_EMAIL',
                        'class' => 'fixed-width-xxl',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitModuleConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    public function hookDisplayExternalNavigationHook()
    {
        $this->smarty->assign(array(
            'phone' => Configuration::get('QBC_PHONE'),
            'email' => Configuration::get('QBC_EMAIL'),
        ));
        return $this->display(__FILE__, 'external-navigation-hook.tpl');
    }

    public function getConfigFieldsValues()
    {
        return array(
            'QBC_PHONE' => Tools::getValue('QBC_PHONE', Configuration::get('QBC_PHONE')),
            'QBC_EMAIL' => Tools::getValue('QBC_EMAIL', Configuration::get('QBC_EMAIL')),
        );
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !Configuration::deleteByName('QBC_PHONE')
            || !Configuration::deleteByName('QBC_EMAIL')
        ) {
            return false;
        }
        return true;
    }
}
