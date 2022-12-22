<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class StatsData extends Module
{
    public function __construct()
    {
        $this->name = 'statsdata';
        $this->tab = 'analytics_stats';
        $this->version = '1.6.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Data mining for statistics');
        $this->description = $this->l('This module must be enabled if you want to use statistics.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
    }

    public function install()
    {
        return (parent::install()
            && $this->registerHook('displayFooter')
            && $this->registerHook('authentication')
            && $this->registerHook('createAccount'));
    }

    public function getContent()
    {
        $html = '';
        if (Tools::isSubmit('submitStatsData')) {
            Configuration::updateValue('PS_STATSDATA_CUSTOMER_PAGESVIEWS', (int)Tools::getValue('PS_STATSDATA_CUSTOMER_PAGESVIEWS'));
            Configuration::updateValue('PS_STATSDATA_PAGESVIEWS', (int)Tools::getValue('PS_STATSDATA_PAGESVIEWS'));
            Configuration::updateValue('PS_STATSDATA_PLUGINS', (int)Tools::getValue('PS_STATSDATA_PLUGINS'));
            $html .= $this->displayConfirmation($this->l('Configuration updated'));
        }

        $html .= $this->renderForm();

        return $html;
    }

    public function hookDisplayFooter($params)
    {
        return $this->getScriptCustomerPagesViews($params);
    }

    private function getScriptCustomerPagesViews($params)
    {
        // Record the guest path then increment the visit counter of the page
        $token_array = Connection::setPageConnection($params['cookie']);
        ConnectionsSource::logHttpReferer();
        if (Configuration::get('PS_STATSDATA_PAGESVIEWS')) {
            Page::setPageViewed($token_array['id_page']);
        }

        if (Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS')) {
            // Ajax request sending the time spend on the page
            $token = sha1($token_array['id_connections'].$token_array['id_page'].$token_array['time_start']._COOKIE_KEY_);
            return '<script type="text/javascript">
				var time_start;
				$(window).load(
					function() {
						time_start = new Date();
					}
				);
				$(window).unload(
					function() {
						var time_end = new Date();
						var pagetime = new Object;
						pagetime.type = "pagetime";
						pagetime.id_connections = "'.(int)$token_array['id_connections'].'";
						pagetime.id_page = "'.(int)$token_array['id_page'].'";
						pagetime.time_start = "'.$token_array['time_start'].'";
						pagetime.token = "'.$token.'";
						pagetime.time = time_end-time_start;
						$.post("'.Context::getContext()->link->getPageLink('statistics', (bool)(Tools::getShopProtocol() == 'https://')).'", pagetime);
					}
				);
			</script>';
        }

        return '';
    }

    public function hookCreateAccount($params)
    {
        return $this->hookAuthentication($params);
    }

    public function hookAuthentication($params)
    {
        // Update or merge the guest with the customer id (login and account creation)
        $guest = new Guest($params['cookie']->id_guest);
        $result = Db::getInstance()->getRow('
		SELECT `id_guest`
		FROM `'._DB_PREFIX_.'guest`
		WHERE `id_customer` = '.(int)$params['cookie']->id_customer);

        if ((int)$result['id_guest']) {
            // The new guest is merged with the old one when it's connecting to an account
            $guest->mergeWithCustomer($result['id_guest'], $params['cookie']->id_customer);
            $params['cookie']->id_guest = $guest->id;
        } else {
            // The guest is duplicated if it has multiple customer accounts
            $method = ($guest->id_customer) ? 'add' : 'update';
            $guest->id_customer = $params['cookie']->id_customer;
            $guest->{$method}();
        }
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Save page views for each customer'),
                        'name' => 'PS_STATSDATA_CUSTOMER_PAGESVIEWS',
                        'desc' => $this->l('Storing customer page views uses a lot of CPU resources and database space. Only enable if your server can handle it.'),
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
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Save global page views'),
                        'name' => 'PS_STATSDATA_PAGESVIEWS',
                        'desc' => $this->l('Global page views uses fewer resources than customer\'s, but it uses resources nonetheless.'),
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
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Plugins detection'),
                        'name' => 'PS_STATSDATA_PLUGINS',
                        'desc' => $this->l('Plugins detection loads an extra 20 kb JavaScript file once for new visitors.'),
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
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitStatsData';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'PS_STATSDATA_CUSTOMER_PAGESVIEWS' => Tools::getValue('PS_STATSDATA_CUSTOMER_PAGESVIEWS', Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS')),
            'PS_STATSDATA_PAGESVIEWS' => Tools::getValue('PS_STATSDATA_PAGESVIEWS', Configuration::get('PS_STATSDATA_PAGESVIEWS')),
            'PS_STATSDATA_PLUGINS' => Tools::getValue('PS_STATSDATA_PLUGINS', Configuration::get('PS_STATSDATA_PLUGINS')),
        );
    }
}
