<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property Currency $object
 */
class AdminCurrenciesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'currency';
        $this->className = 'Currency';
        $this->lang = false;

        $this->fields_list = array(
            'id_currency' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'name' => array('title' => $this->l('Currency')),
            'iso_code' => array('title' => $this->l('ISO code'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'iso_code_num' => array('title' => $this->l('ISO code number'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'sign' => array('title' => $this->l('Symbol'), 'width' => 20, 'align' => 'center', 'orderby' => false, 'search' => false, 'class' => 'fixed-width-xs'),
            'conversion_rate' => array('title' => $this->l('Exchange rate'), 'type' => 'float', 'align' => 'center', 'width' => 130, 'search' => false, 'filter_key' => 'currency_shop!conversion_rate'),
            'active' => array('title' => $this->l('Enabled'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'class' => 'fixed-width-sm'),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_options = array(
            'change' => array(
                'title' =>    $this->l('Currency rates'),
                'image' => '../img/admin/exchangesrate.gif',
                'description' => $this->l('Use PrestaShop\'s webservice to update your currency\'s exchange rates. However, please use caution: rates are provided as-is.'),
                'submit' => array(
                    'title' => $this->l('Update currency rates'),
                    'name' => 'SubmitExchangesRates'
                )
            ),
            'cron' => array(
                'title' =>    $this->l('Automatically update currency rates'),
                'image' => '../img/admin/tab-tools.gif',
                'info' => '<div class="alert alert-block"><p>'.$this->l('Use PrestaShop\'s webservice to update your currency exchange rates. However, please use caution: rates are provided as-is.').'<br/>'.$this->l('You can place the following URL in your crontab file, or you can click it yourself regularly:').'</p>
					<p><strong><a href="'.Tools::getShopDomain(true, true).__PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/cron_currency_rates.php?secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')).'" onclick="return !window.open($(this).attr(\'href\'));">'.Tools::getShopDomain(true, true).__PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/cron_currency_rates.php?secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')).'</a></strong></p></div>',
            )
        );

        parent::__construct();

        $this->_select .= 'currency_shop.conversion_rate conversion_rate';
        $this->_join .= Shop::addSqlAssociation('currency', 'a');
        $this->_group .= 'GROUP BY a.id_currency';
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_where = 'AND a.`deleted` = 0';

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Currencies'),
                'icon' => 'icon-money'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Currency name'),
                    'name' => 'name',
                    'size' => 30,
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->l('Only letters and the minus character are allowed.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('ISO code'),
                    'name' => 'iso_code',
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->l('ISO code (e.g. USD for Dollars, EUR for Euros, etc.).')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Numeric ISO code'),
                    'name' => 'iso_code_num',
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->l('Numeric ISO code (e.g. 840 for Dollars, 978 for Euros, etc.).')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Symbol'),
                    'name' => 'sign',
                    'maxlength' => 8,
                    'required' => true,
                    'hint' => $this->l('Will appear in front office (e.g. $, &euro;, etc.)')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Exchange rate'),
                    'name' => 'conversion_rate',
                    'maxlength' => 11,
                    'required' => true,
                    'hint' => $this->l('Exchange rates are calculated from one unit of your shop\'s default currency. For example, if the default currency is euros and your chosen currency is dollars, type "1.20" (1&euro; = $1.20).')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Currency format'),
                    'name' => 'format',
                    'maxlength' => 11,
                    'required' => true,
                    'hint' =>$this->l('Applies to all prices (e.g. $1,240.15).'),
                    'options' => array(
                        'query' => array(
                            array('key' => 1, 'name' => 'X0,000.00 ('.$this->l('Such as with Dollars').')'),
                            array('key' => 2, 'name' => '0 000,00X ('.$this->l('Such as with Euros').')'),
                            array('key' => 3, 'name' => 'X0.000,00'),
                            array('key' => 4, 'name' => '0,000.00X'),
                            array('key' => 5, 'name' => '0\'000.00X') // Added for the switzerland currency
                        ),
                        'name' => 'name',
                        'id' => 'key'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Decimals'),
                    'name' => 'decimals',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->l('Display decimals in prices.'),
                    'values' => array(
                        array(
                            'id' => 'decimals_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'decimals_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Spacing'),
                    'name' => 'blank',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->l('Include a space between symbol and price (e.g. $1,240.15 -> $ 1,240.15).'),
                    'values' => array(
                        array(
                            'id' => 'blank_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'blank_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable'),
                    'name' => 'active',
                    'required' => false,
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
                    )
                )
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        return parent::renderForm();
    }

    protected function checkDeletion($object)
    {
        if (Validate::isLoadedObject($object)) {
            if ($object->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
                $this->errors[] = $this->l('You cannot delete the default currency');
            } else {
                return true;
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while deleting the object.').'
				<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }

        return false;
    }

    protected function checkDisableStatus($object)
    {
        if (Validate::isLoadedObject($object)) {
            if ($object->active && $object->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
                $this->errors[] = $this->l('You cannot disable the default currency');
            } else {
                return true;
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').'
				<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }

        return false;
    }

    /**
     * @see AdminController::processDelete()
     */
    public function processDelete()
    {
        $object = $this->loadObject();
        if (!$this->checkDeletion($object)) {
            return false;
        }
        return parent::processDelete();
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id_currency) {
                $object = new Currency((int)$id_currency);
                if (!$this->checkDeletion($object)) {
                    return false;
                }
            }
        }

        return parent::processBulkDelete();
    }

    /**
     * @see AdminController::processStatus()
     */
    public function processStatus()
    {
        $object = $this->loadObject();
        if (!$this->checkDisableStatus($object)) {
            return false;
        }

        return parent::processStatus();
    }

    protected function processBulkDisableSelection()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id_currency) {
                $object = new Currency((int)$id_currency);
                if (!$this->checkDisableStatus($object)) {
                    return false;
                }
            }
        }
        return parent::processBulkDisableSelection();
    }

    /**
     * Update currency exchange rates
     */
    public function processExchangeRates()
    {
        if (!$this->errors = Currency::refreshCurrencies()) {
            Tools::redirectAdmin(self::$currentIndex.'&conf=6&token='.$this->token);
        }
    }

    /**
     * @see AdminController::initProcess()
     */
    public function initProcess()
    {
        if (Tools::isSubmit('SubmitExchangesRates')) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'exchangeRates';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        if (Tools::isSubmit('submitAddcurrency') && !Tools::getValue('id_currency') && Currency::exists(Tools::getValue('iso_code'), Tools::getValue('iso_code_num'))) {
            $this->errors[] = Tools::displayError('This currency already exists.');
        }
        if (Tools::isSubmit('submitAddcurrency') && (float)Tools::getValue('conversion_rate') <= 0) {
            $this->errors[] = Tools::displayError('The currency conversion rate can not be equal to 0.');
        }
        parent::initProcess();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_currency'] = array(
                'href' => self::$currentIndex.'&addcurrency&token='.$this->token,
                'desc' => $this->l('Add new currency', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }
}
