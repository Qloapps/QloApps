<?php
class AdminPaymentsSettingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = '';
        $this->table = 'configuration';
        $this->context = Context::getContext();
        $this->fields_options = array();
        Hook::exec('addPaymentSetting', array('fields_options' => &$this->fields_options));
        parent::__construct();
    }
}
