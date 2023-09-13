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
        $this->version = '1.0.0';
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
            || !Configuration::updateValue('QBC_PHONE', Configuration::get('WK_HOTEL_GLOBAL_CONTACT_NUMBER'))
            || !Configuration::updateValue('QBC_EMAIL', Configuration::get('WK_HOTEL_GLOBAL_CONTACT_EMAIL'))
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('displayNav')
            || !$this->registerHook('displayExternalNavigationHook')
            || !$this->registerHook('actionAdminHotelGeneralSettingsOptionsModifier')
            || !$this->registerHook('actionAdminHotelGeneralSettingsControllerUpdate_optionsBefore')
        ) {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminHotelGeneralSettings').'#configuration_fieldset_contactdetail'
        );
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

    public function hookDisplayExternalNavigationHook()
    {
        $this->smarty->assign(array(
            'phone' => Configuration::get('QBC_PHONE'),
            'email' => Configuration::get('QBC_EMAIL'),
        ));
        return $this->display(__FILE__, 'external-navigation-hook.tpl');
    }

    public function hookActionAdminHotelGeneralSettingsOptionsModifier($params)
    {
        $params['options']['contactdetail']['fields'] = array_merge(
            $params['options']['contactdetail']['fields'],
            array(
                'QBC_PHONE' => array(
                    'title' => $this->l('Support Contact Number'),
                    'type' => 'text',
                    'hint' => $this->l('The phone number used for customer service.'),
                    'class' => 'fixed-width-xxl',
                ),
                'QBC_EMAIL' => array(
                    'title' => $this->l('Support Email'),
                    'type' => 'text',
                    'hint' => $this->l('The email used for customer service.'),
                    'class' => 'fixed-width-xxl',
                ),
            )
        );
    }

    public function hookActionAdminHotelGeneralSettingsControllerUpdate_optionsBefore()
    {
        $phone = Tools::getValue('QBC_PHONE');
        $email = Tools::getValue('QBC_EMAIL');

        if ($phone != '' && !Validate::isPhoneNumber($phone)) {
            $this->context->controller->errors[] = $this->l('Support Contact Number is invalid.');
        }

        if ($email != '' && !Validate::isEmail($email)) {
            $this->context->controller->errors[] = $this->l('Support Email is invalid.');
        }

        if (!count($this->context->controller->errors)) {
            Configuration::updateValue('QBC_PHONE', $phone);
            Configuration::updateValue('QBC_EMAIL', $email);
        }
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
