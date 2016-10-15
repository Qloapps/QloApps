<?php
class AdminOtherHotelModulesSettingController extends ModuleAdminController 
{
	public function __construct() 
	{
		$this->table = 'configuration';
		$this->className = 'Configuration';
		$this->bootstrap = true;
		$this->fields_options = array();
		Hook::exec('addOtherModuleSetting', array('fields_options' => &$this->fields_options));
		parent::__construct();
	}

}