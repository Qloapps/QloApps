<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
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
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class BlockNewsletterSubscriptionModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->content_only = true;
    }

    public function checkAccess()
    {
        return strcmp(Tools::getValue('token', ''), $this->module->secure_key) === 0;
    }

    public function displayAjaxSubscribeNewsletter()
    {
        $this->module->newsletterRegistration();

        $this->context->smarty->assign(array(
            'message_type' => ($this->module->valid && !$this->module->error) ? 'success' : 'error',
            'message' => ($this->module->valid && !$this->module->error) ? $this->module->valid : $this->module->error,
        ));

        $response = array(
            'status' => true,
            'message_html' => $this->context->smarty->fetch(
                $this->module->getTemplatePath('subscription_execution.tpl')
            ),
        );

        $this->ajaxDie(json_encode($response));
    }
}
