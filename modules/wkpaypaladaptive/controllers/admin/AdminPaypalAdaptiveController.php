<?php
class AdminPaypalAdaptiveController extends ModuleAdminController
{
	public function __construct()
    {
    	$this->bootstrap = true;
        $this->className = '';
        $this->table = 'configuration';

        $this->fields_options = array(
        	'marketplace_paypal' => array(
        		'title' =>	$this->l('Paypal Settings'),
        		'fields' => array(
        			'sandboxstatus' => array(
						'title' => $this->l('Sandbox Mode'),
						'hint' => $this->l('Paypal setting mode'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'required' => true
					),
					// 'PAYPAL_PAYMENT_TYPE' => array(
					// 	'title' => $this->l('Payment Method'),
					// 	'hint' => $this->l('If Chained, amount will go to admin then distribute, buyer can not see the distribution'),
					// 	'cast' => 'intval',
					// 	'show' => true,
					// 	'type' => 'radio',
					// 	'required' => true,
					// 	'choices' => array(
					// 		1 => $this->l('Parallel'),
					// 		2 => $this->l('Chained')
					// 	)
					// ),
					'APP_ID' => array(
						'title' => $this->l('API APP ID'),
						'type' => 'text',
						'required' => true
					),
					'APP_USERNAME' => array(
						'title' => $this->l('API USERNAME'),
						'required' => true,
						'type' => 'text',
					),
					'APP_PASSWORD' => array(
						'title' => $this->l('API PASSWORD'),
						'type' => 'text',
						'required' => true
					),
					'APP_SIGNATURE' => array(
						'title' => $this->l('API SIGNATURE'),
						'type' => 'text',
						'required' => true
					),
					'PAYPAL_EMAIL' => array(
						'title' => $this->l('PAYPAL EMAIL ID'),
						'validation' => 'isEmail',
						'type' => 'text',
						'required' => true
					)
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
        	)
		);
		parent::__construct();
    }
}
?>