<?php
/**
* 2010-2024 Webkul.
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
* @copyright 2010-2024 Webkul IN
* @license LICENSE.txt
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
