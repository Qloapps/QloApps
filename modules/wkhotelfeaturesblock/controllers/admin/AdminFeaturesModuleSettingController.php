<?php
class AdminFeaturesModuleSettingController extends ModuleAdminController 
{
	public function __construct() 
	{
		$this->table = 'htl_features_block_data';
		$this->className = 'WkHotelFeaturesData';
		$this->bootstrap = true;
		$this->context = Context::getContext();

		$this->fields_options = array(
			'global' => array(
				'title' =>	$this->l('Hotel Features Setting'),
				'icon' =>   'icon-cogs',
				'fields' =>	array(
					'HOTEL_AMENITIES_HEADING' => array(
						'title' => $this->l('Feature Block Title'),
						'type' => 'text',
						'required' => 'true',
						'validation' => 'isCatalogName',
						'id' => 'HOTEL_AMENITIES_HEADING',
						'hint' => $this->l('Block Heading. Ex: Amenities.'),
					),
					'HOTEL_AMENITIES_DESCRIPTION' => array(
						'title' => $this->l('Feature Block Description'),
						'type' => 'textarea',
						'required' => 'true',
						'id' => 'HOTEL_AMENITIES_DESCRIPTION',
						'validation' => 'isCatalogName',
						'rows' => '4',
						'cols' => '2',
						'hint' => $this->l('Block description.'),
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
		);
		
		$this->fields_list = array(
			'id' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
			),
			'image' => array(
	            'title' => $this->l('Logo'),
	            'align' => 'center',
	            'image' => 'store_logo',
	            'orderby' => false,
	            'search' => false
	        ),
	        'feature_title' => array(
                'title' => $this->l('Amenity Title'),
                'align' => 'text-center',
            ),
	        'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ),
			'date_add' => array(
				'title' => $this->l('Date Add'),
				'align' => 'center',
			),
		);

		$this->identifier = 'id';

		$this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
            'enableSelection' => array(
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ),
        );
		parent::__construct();
	}

	public function renderList() 
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$ps_ftr_img_dir = _PS_MODULE_DIR_.'wkhotelfeaturesblock/views/img/hotels_features_img';
		$this->context->smarty->assign('ps_ftr_img_dir', $ps_ftr_img_dir);
		
		$features_img_dir = _MODULE_DIR_.'wkhotelfeaturesblock/views/img/hotels_features_img';
		$this->context->smarty->assign('features_img_dir', $features_img_dir);

		$this->page_header_toolbar_btn['new'] = array(
			'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
			'desc' => $this->l('Add New Hotel Amenity')
		);

		return parent::renderList();
	}

	public function renderForm() 
	{
		if (!($obj = $this->loadObject(true))) {
            return;
        }

        $image_url = $image_size = false;

        if ($this->display == 'edit')
        {
            $image = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotels_features_img/'.$obj->id.'.jpg';
	        $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350,
	            $this->imageType, true, true);
	        $image_size = file_exists($image) ? filesize($image) / 1000 : false;
        }
        
		$this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Amenities Configuration'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Amenity Title'),
                    'name' => 'feature_title',
                    'required' => true,
                    'hint' => $this->l('This will be displayed as amenity heading.')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Amenity Description'),
                    'name' => 'feature_description',
                    'required' => true,
                    'rows' => '4',
                    'hint' => $this->l('This will be displayed as amenity description.')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Amenity Image'),
                    'name' => 'feature_image',
                    'required' => true,
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'col' => 6,
                    'hint' => sprintf($this->l('Maximum image size: %1s'), Tools::formatBytes(Tools::getMaxUploadSize())),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
            ),
            'submit' => array(
				'title' => $this->l('Save')
			));
		
		return parent::renderForm();
	}

	public function processSave()
	{
		$file = $_FILES['feature_image'];

		/*==== Validations ====*/
        if (!Tools::getValue('feature_title')) {
            $this->errors[] = Tools::displayError($this->l('Please enter amenity title.'));
        } elseif (!Validate::isCatalogName(Tools::getValue('feature_title'))) {
            $this->errors[] = Tools::displayError($this->l('Please enter valid title.'));
        }

        if (!Tools::getValue('feature_description')) {
        	$this->errors[] = Tools::displayError($this->l('Please enter amenity description.'));
        } elseif (!Validate::isCatalogName(Tools::getValue('feature_description'))) {
            $this->errors[] = Tools::displayError($this->l('Please enter valid description.'));
        }

        if (!(Tools::getValue("id") && !$file['size'])) {
	        if (!$file['size']) {
	            $this->errors[] = Tools::displayError($this->l('Hotel Amenity Image Required.'));
	        } elseif ($file['error']) {
	            $this->errors[] = Tools::displayError($this->l('Cannot upload file.'));
	        } elseif (!(preg_match('/\.(jpe?g|gif|png)$/', $file['name']) && ImageManager::isRealImage($file['tmp_name'], $file['type']))) {
	            $this->errors[] = Tools::displayError($this->l('Please upload image file.'));
	        }
        }
        /*==== Validations ====*/

		
		if (!count($this->errors))
		{
			$hotelAmenityId = Tools::getValue('id');
			$amenityTitle = Tools::getValue('feature_title');
	        $amenityDescription = Tools::getValue('feature_description');

			if ($hotelAmenityId)
                $obj_feature_data = new WkHotelFeaturesData($hotelAmenityId);
            else        
                $obj_feature_data = new WkHotelFeaturesData();

            if ($file['size']) {
            	$obj_feature_data->feature_image = 0;
            }
			$obj_feature_data->feature_title = $amenityTitle;
            $obj_feature_data->feature_description = $amenityDescription;
            $obj_feature_data->active = Tools::getValue('active');
            $obj_feature_data->save();
            $hotelAmenityId = $obj_feature_data->id; 

            if ($file['size']) {
            	$img_name = $hotelAmenityId.'.jpg';
            	$img_path = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotels_features_img/'.$img_name;
            	if (file_exists($img_path)) {
            		unlink($img_path);
            	}
	            ImageManager::resize($file['tmp_name'], $img_path);
            	
                $obj_feature_data->feature_image = $img_name;
                $obj_feature_data->save();
        	}

			if (Tools::getValue("id")) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
		} else {
            if (Tools::getValue("id"))
                $this->display = 'edit';
            else
                $this->display = 'add';
        }
	}

	public function postProcess()
    {
        parent::postProcess();
        $this->addjQueryPlugin(array(
                'tablednd',
            ));
        // $this->addJS(_MODULE_DIR_.'wkabouthotelblock/views/js/WkAboutHotelBlockAdmin.js');
    }
}