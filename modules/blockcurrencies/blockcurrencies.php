<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class BlockCurrencies extends Module
{
    public function __construct()
    {
        $this->name = 'blockcurrencies';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Currency selector block');
        $this->description = $this->l('Adds a block allowing customers to select their preferred booking currency.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }
 
    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('displayNav')
            || !$this->registerHook('displayExternalNavigationHook')
        ) {
            return false;
        }
        return true;
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->addCSS($this->getPathUri().'views/css/hook/blockcurrencies.css');
        $this->context->controller->addJS($this->getPathUri().'views/js/hook/blockcurrencies.js');
    }

    protected function _prepareHook()
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }

        if (!Currency::isMultiCurrencyActivated()) {
            return false;
        }
        return true;
    }

    public function hookDisplayNav()
    {
        if ($this->_prepareHook()) {
            return $this->display(__FILE__, 'blockcurrencies.tpl');
        }
    }

    public function hookDisplayTop()
    {
        return $this->hookDisplayNav();
    }

    public function hookDisplayTopSubPrimaryBlock()
    {
        return $this->hookDisplayNav();
    }

    public function hookDisplayFooterMostLeftBlock()
    {
        return $this->hookDisplayNav();
    }

    public function hookDisplayExternalNavigationHook()
    {
        if (!$this->_prepareHook()) {
            return;
        }
        return $this->display(__FILE__, 'external-navigation-hook.tpl');
    }
}
