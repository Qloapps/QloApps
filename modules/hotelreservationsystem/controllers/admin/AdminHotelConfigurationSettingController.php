<?php
class AdminHotelConfigurationSettingController extends ModuleAdminController 
{
	public function __construct() 
	{
		$this->table = 'configuration';
		$this->className = 'Configuration';
		$this->bootstrap = true;

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Configuration'),
				'fields' =>	array(
					'WK_HOTEL_LOCATION_ENABLE' => array(
						'title' => $this->l('Enable Hotal Location'),
						'cast' => 'intval',
						'type' => 'bool',
						'default' => '0',
						'values' => array(
	                        array(
	                            'id' => 'active_on',
	                            'value' => 1
	                        ),
	                        array(
	                            'id' => 'active_off',
	                            'value' => 0
	                        )
	                    ),
						'hint' => $this->l('whether you want to show Hotel Location field on search page.'),
					),
					'WK_ROOM_LEFT_WARNING_NUMBER' => array(
                        'title' => $this->l('Number of rooms left'),
                        'hint' => $this->l('Mention the minimum quantity after which alert message for remaining rooms will be displayed to customers.'),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'WK_HOTEL_GLOBAL_CONTACT_EMAIL' => array(
                        'title' => $this->l('Global Email'),
                        'hint' => $this->l('Email which you want to show a customer to email you.'),
                        'type' => 'text',
                        'validation' => 'isEmail',
                    ),
                    'WK_HOTEL_GLOBAL_CONTACT_NUMBER' => array(
                        'title' => $this->l('Global Contact Number'),
                        'hint' => $this->l('Phone Number which you want to show a customer to contact you.'),
						'type' => 'text',
						'validation' => 'isPhoneNumber',
                    ),
                    'WK_HTL_ESTABLISHMENT_YEAR' => array(
                        'title' => $this->l('Hotel Establishmment Year'),
                        'hint' => $this->l('Year when your has established.'),
						'type' => 'text',
                    ),
                    'WK_HTL_CHAIN_NAME' => array(
                        'title' => $this->l('Hotel Chain Name'),
                        'hint' => $this->l('Name of chain of of your hotes. Enter Hotel name in case of single hotel'),
						'type' => 'text',
                    ),
                    'WK_TITLE_HEADER_BLOCK' => array(
                        'title' => $this->l('Home Page Header Block Title'),
                        'hint' => $this->l('Title Text you want to show on Home Page Header Block.'),
						'type' => 'text',
                    ),
                    'WK_CONTENT_HEADER_BLOCK' => array(
                        'title' => $this->l('Home Page Header Block Content'),
                        'hint' => $this->l('Content Text you want to show on Home Page Header Block.'),
						'type' => 'textarea',
                    ),
                    'WK_HTL_HEADER_IMAGE' => array(
                        'title' => $this->l('Header Background Image'),
						'type' => 'file',
						'hint' => $this->l('Will appear on Home Page Header Background Image'),
						'name' => 'htl_header_image',
						'url' => _PS_IMG_,
                    ),
				),
				'submit' => array('title' => $this->l('Save'))
			)
		);
		parent::__construct();
	}

	public function postProcess()
	{	
		if (Tools::isSubmit('submitOptionsconfiguration'))
		{
			if ($_FILES['htl_header_image']['name'])
			{
				$this->validateHotelHeaderImage($_FILES['htl_header_image']);
				if (!count($this->errors))
				{
					$img_path = _PS_IMG_DIR_.'hotel_header_image.png';

			       	if (ImageManager::resize($_FILES['htl_header_image']['tmp_name'], $img_path))
			        	Configuration::updateValue('WK_HOTEL_HEADER_IMAGE', 'hotel_header_image.png');
			        else
			      	 	$this->errors[] = Tools::displayError('Some error occured while uoploading image.Please try again.');
			    }
		    }

		}
		parent::postProcess();
	}

	public function validateHotelHeaderImage($image)
	{
		if ($image['size'] > 0)
		{			
			if ($image['tmp_name'] != "")
			{
				if(!ImageManager::isCorrectImageFileExt($image['name']))
				  	$this->errors[] = Tools::displayError('<strong>'.$_FILES['header_image']['name'].'</strong> : Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
			}
		}
		else
			return true;
	}

	public function initContent()
	{
		$this->show_toolbar = false;
		parent::initContent();
	}
}	