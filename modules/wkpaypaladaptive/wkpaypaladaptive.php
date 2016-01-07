<?php
if (!defined('_PS_VERSION_'))
	exit;

require_once (_PS_MODULE_DIR_.'hotelreservationsystem/classes/HotelCustomerAdvancedPayment.php');

class WkPaypalAdaptive extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'wkpaypaladaptive';
		$this->tab = 'payments_gateways';
		$this->version = '0.0.2';
        $this->author = 'Webkul';
        $this->bootstrap = true;

		$this->dependencies = array('hotelreservationsystem');
		
		parent::__construct();

		$this->displayName = $this->l('Paypal Payment Gateway');
		$this->description = $this->l('Customer can order online using Paypal Payment Gateway');
	}

	public function getContent()
	{
		$link = new Link();
		Tools::redirectAdmin($link->getAdminLink('AdminPaypalAdaptive'));
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return;

		if (!isset($params['objOrder']) || ($params['objOrder']->module != $this->name))
            return false;

        if (isset($params['objOrder']) && Validate::isLoadedObject($params['objOrder']) && isset($params['objOrder']->valid))
        {
        	$this->smarty->assign(array(
                'id_order' => $params['objOrder']->id,
                'valid' => $params['objOrder']->valid
            ));
        }

        if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
			$this->smarty->assign('reference', $params['objOrder']->reference);

		return $this->display(__FILE__, 'payment_return.tpl');
	}

	public function hookDisplayPayment()
    {
    	// payment option will not display untill paypal settings not filled
    	if (Configuration::get('PAYPAL_EMAIL'))
    	{
    		$this->context->controller->addCSS($this->_path.'views/css/hook_payment.css');
	     	$this->smarty->assign(array(
				'this_path' => $this->_path,
				'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
			));
	      	return $this->display(__FILE__, 'payment.tpl');
    	}
    }
	
	public function install()
    {
	    if (Configuration::get('sandboxstatus') != 1)
	    	Configuration::updateValue('sandboxstatus', 1);

		if (!parent::install()
			|| !$this->callInstallTab()
			|| !$this->registerHook('displayPayment')
			|| !$this->registerHook('paymentReturn')
			|| !Configuration::updateValue('sandboxstatus', 1)
			)
	     return false;

	    return true;
	}

	public function uninstall()
	{
	   if (!parent::uninstall()
	   		|| !$this->callUninstallTab()
	   		|| !$this->deleteConfigVariable())
			return false;
	   return true;
    }

    public function deleteConfigVariable()
    {
    	$config_key = array('sandboxstatus', 
    						// 'PAYPAL_PAYMENT_TYPE',
    						'APP_ID',
    						'APP_USERNAME',
    						'APP_PASSWORD',
							'APP_SIGNATURE',
    						'PAYPAL_EMAIL');

    	foreach ($config_key as $key)
    		if (!Configuration::deleteByName($key))
    			return false;

    	return true;
    }

	public function callInstallTab() 
	{
        return $this->installTab('AdminPaypalAdaptive','Paypal Adaptive Setting','AdminHotelReservationSystemManagement');
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false) 
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;

        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $tab_name;

        if ($tab_parent_name)
            $tab->id_parent = (int)Tab::getIdFromClassName($tab_parent_name);
        else
            $tab->id_parent = 0;
        
        $tab->module = $this->name;
        return $tab->add();
    }

    public function callUninstallTab() 
    {
        $this->uninstallTab('AdminPaypalAdaptive');
        return true;
    }

    public function uninstallTab($class_name) 
    {
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        if ($id_tab)
        {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
		return false;
    }
}