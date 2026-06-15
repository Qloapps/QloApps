<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@qloapps.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to https://store.webkul.com/customisation-guidelines for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

class AdminQloIcsExportConfigurationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'configuration';
        $this->className = 'Configuration';
        $this->display = 'edit';

        parent::__construct();
    }
    public function initToolbar()
    {
        parent::initToolbar();
        $this->toolbar_title = $this->l('Configuration');
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('iCal Export Configuration & Import'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Attach iCal file to order confirmation email'),
                    'name' => 'QIE_ATTACH_MAIL',
                    'is_bool' => true,
                    'desc' => $this->l('If enabled, an .ics file will be attached to the order confirmation email sent to the customer.'),
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
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        // Fetch the current value from the configuration table
        $this->fields_value = array(
            'QIE_ATTACH_MAIL' => Configuration::get('QIE_ATTACH_MAIL'),
        );
        $this->show_form_cancel_button = false;

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAddconfiguration')) {
            $attachToMail = (int) Tools::getValue('QIE_ATTACH_MAIL');
            Configuration::updateValue('QIE_ATTACH_MAIL', $attachToMail);
            Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token . '&conf=4');
        }
        return parent::postProcess();
    }
}