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

class AdminCMConnectorConfigurationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->bootstrap = true;
        $this->page_header_toolbar_title = $this->l('Channel Manager API Configuration');

        parent::__construct();
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('submitQcmcConfigForm')) {
            if ($this->module->validateConfigurationValues() && $this->module->saveConfigurationValues()) {
                Tools::redirectAdmin(self::$currentIndex . '&conf=6&token=' . $this->token);
            }
        }
    }

    public function initContent()
    {
        parent::initContent();

        $this->content = $this->renderConfigurationForm();
        $this->content .= $this->module->renderCronInfo();

        $this->context->smarty->assign('content', $this->content);
    }

    protected function renderConfigurationForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this->module;
        $helper->default_form_language = $this->context->language->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitQcmcConfigForm';
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->tpl_vars = array(
            'fields_value' => $this->module->getConfigFormFieldsValue(),
            'languages' => $this->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->module->getConfigFormFields()));
    }
}
