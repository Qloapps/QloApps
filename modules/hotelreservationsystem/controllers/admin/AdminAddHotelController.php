<?php
class AdminAddHotelController extends ModuleAdminController 
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'htl_branch_info';
		$this->className = 'HotelBranchInformation';
		$this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`state_id`)';
		$this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = a.`country_id` AND cl.`id_lang` = '.Configuration::get('PS_LANG_DEFAULT').')';
		$this->_select = 's.`name` as `state_name`, cl.`name`';
		$this->context = Context::getContext();
		$this->fields_list = array();
		$this->fields_list = array(
			'id' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
			),

			'hotel_name' => array(
				'title' => $this->l('Hotel Name'),
				'align' => 'center',
			),

			'city' => array(
				'title' => $this->l('City'),
				'align' => 'center',
			),

			'state_name' => array(
				'title' => $this->l('State'),
				'align' => 'center',
			),

			'active' => array(
				'align' => 'center',
				'title' => $this->l('Status'),
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false
			),

			'name' => array(
				'title' => $this->l('Country'),
				'align' => 'center'
			));
		$this->identifier  = 'id';
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
											  'icon' => 'icon-trash',
											  'confirm' => $this->l('Delete selected items?'))
									);
		parent::__construct();
	}

	public function initToolbar() 
	{
		parent::initToolbar();
		$this->page_header_toolbar_btn['new'] = array(
			'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
			'desc' => $this->l('Add new Hotel')
		);
	}

	public function renderList() 
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		return parent::renderList();
	}

	public function renderForm() 
	{
		//tinymce setup
		$this->context->smarty->assign('path_css',_THEME_CSS_DIR_);
		$this->context->smarty->assign('ad',__PS_BASE_URI__.basename(_PS_ADMIN_DIR_));
		$this->context->smarty->assign('autoload_rte',true);
        $this->context->smarty->assign('lang',true);
        $this->context->smarty->assign('iso', $this->context->language->iso_code);
        
        $obj_countries = new Country();
		$countries_var = Country::getCountries($this->context->language->id);
		
		if ($this->display == 'add')
		{
			$this->context->smarty->assign('country_var',$countries_var);
		}
		elseif ($this->display == 'edit') 
		{
			$this->context->smarty->assign('edit',1);
			$hotel_id = Tools::getValue('id');
			$hotel_branch_info_obj = new HotelBranchInformation();
			$hotel_branch_info = $hotel_branch_info_obj->hotelBranchInfoById($hotel_id);

			$country_id = $hotel_branch_info['country_id'];
			$statesbycountry = State::getStatesByIdCountry($country_id);

			if ($statesbycountry)
			{
				$states = array();
				foreach($statesbycountry as $key=>$value)
				{
					$states[$key]['id'] = $value['id_state'];
					$states[$key]['name'] = $value['name'];
				}
			}
			$this->context->smarty->assign('country_var',$countries_var);
			$this->context->smarty->assign('state_var',$states);
			$this->context->smarty->assign('hotel_info',$hotel_branch_info);
		}

		$this->fields_form = array(
				'submit' => array(
					'title' => $this->l('Save')
				)
			);
		return parent::renderForm();
	}

	public function processSave()
	{
		$hotel_id = Tools::getValue('hotel_id');
		$hotel_name = Tools::getValue('hotel_name');
		$phone = Tools::getValue('phone');
		$email = Tools::getValue('email');
		$check_in = Tools::getValue('check_in');
		$check_out = Tools::getValue('check_out');
		$short_description = Tools::getValue('short_description');
		$description = Tools::getValue('description');
		$rating = Tools::getValue('hotel_rating');
		$city = Tools::getValue('hotel_city');
		$state = Tools::getValue('hotel_state');
		$country = Tools::getValue('hotel_country');
		$policies = Tools::getValue('hotel_policies');
		$zipcode = Tools::getValue('hotel_postal_code');
		$address = Tools::getValue('address');
		$active = Tools::getValue('ENABLE_HOTEL');
		if ($hotel_name == '')
			$this->errors[] = Tools::displayError('Hotel name is required field.');
		else if (!Validate::isGenericName($hotel_name))
			$this->errors[] = Tools::displayError($this->l('Hotel name must not have Invalid characters <>;=#{}'));
		if (!$phone)
			$this->errors[] = Tools::displayError('Phone number is required field.');
		else if (!Validate::isPhoneNumber($phone))
			$this->errors[] = Tools::displayError('Please enter a valid phone number.');
		if ($email == '')
			$this->errors[] = Tools::displayError('Email is required field.');
		else if (!Validate::isEmail($email))
			$this->errors[] = Tools::displayError('Please enter a valid email.');

		if ($check_in == '')
			$this->errors[] = Tools::displayError('Check In time is required field.');
		if ($check_out == '')
			$this->errors[] = Tools::displayError('Check Out Time is required field.');
		if ($zipcode == '')
			$this->errors[] = Tools::displayError('Postal Code is required field.');
		else if (!Validate::isPostCode($zipcode))
			$this->errors[] = Tools::displayError('Enter a Valid Postal Code.');

		if (!$rating)
			$this->errors[] = Tools::displayError('Rating is required field.');
		if ($address == '')
			$this->errors[] = Tools::displayError('Address is required field.');
		
		if (!$country)
			$this->errors[] = Tools::displayError('Country is required field.');
		if (!$state)
			$this->errors[] = Tools::displayError('State is required field.');

		if ($city == '')
			$this->errors[] = Tools::displayError('City is required field.');
		else if (!Validate::isCityName($city))
			$this->errors[] = Tools::displayError('Enter a Valid City Name.');

		//validate hotel main image
		if(isset($_FILES['hotel_image']) && $_FILES['hotel_image']['name'])
		{
			$obj_htl_img = new HotelImage();
			$error = $obj_htl_img->validAddHotelMainImage($_FILES['hotel_image']);
			if ($error)
				$this->errors[] = Tools::displayError('<strong>'.$_FILES['hotel_image']['name'].'</strong> : Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
		}
		//validate Hotel's other images
		if (isset($_FILES['images']) && $_FILES['images'])
		{
			$obj_htl_img = new HotelImage();
			$error = $obj_htl_img->validAddHotelOtherImage($_FILES['images']);
			if ($error)
				$this->errors[] = Tools::displayError('<strong>'.$_FILES['hotel_image']['name'].'</strong> : Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
		}
		if (!count($this->errors))
		{
			if ($hotel_id)
				$obj_hotel_info = new HotelBranchInformation($hotel_id);
			else
				$obj_hotel_info = new HotelBranchInformation();

			if ($obj_hotel_info)
			{
				if (!$active)
				{
					$obj_htl_rm_info = new HotelRoomType();
					$ids_product = $obj_htl_rm_info->getIdProductByHotelId($obj_hotel_info->id);
					if (isset($ids_product) && $ids_product)
					{
						foreach ($ids_product as $key_prod => $value_prod)
						{
							$obj_product = new Product($value_prod['id_product']);
							if ($obj_product->active)
								$obj_product->toggleStatus();
			    		}
			    	}
				}
			}

			$obj_hotel_info->active = $active;
			$obj_hotel_info->hotel_name = $hotel_name;
			$obj_hotel_info->phone = $phone;
			$obj_hotel_info->email = $email;
			$obj_hotel_info->check_in = $check_in;
			$obj_hotel_info->check_out = $check_out;
			$obj_hotel_info->short_description = $short_description;
			$obj_hotel_info->description = $description;
			$obj_hotel_info->rating = $rating;
			$obj_hotel_info->city = $city;
			$obj_hotel_info->state_id = $state;
			$obj_hotel_info->country_id = $country;
			$obj_hotel_info->zipcode = $zipcode;
			$obj_hotel_info->policies = $policies;
			$obj_hotel_info->address = $address;
			$obj_hotel_info->save();

			$new_hotel_id = $obj_hotel_info->id;
			$hotel_img_path = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/';

			//upload hotel's image
			if(isset($_FILES['hotel_image']) && $_FILES['hotel_image'])
			{
				$obj_htl_img = new HotelImage();
				$obj_htl_img->uploadMainImage($_FILES['hotel_image'], $new_hotel_id, $hotel_img_path);
			}
			
			//upload hotel's other images
			if (isset($_FILES['images']) && $_FILES['images'])
			{
				$obj_htl_img = new HotelImage();
				$obj_htl_img->uploadOtherImages($_FILES['images'], $new_hotel_id, $hotel_img_path);
			}

			if ($new_hotel_id)
			{
				$grp_ids = array();
				$obj_grp = new Group();
				$data_grp_ids = $obj_grp->getGroups(1, $id_shop = false);

				foreach ($data_grp_ids as $key => $value)
				{
					$grp_ids[] = $value['id_group'];
				}
				$country_name = (new Country())->getNameById(Configuration::get('PS_LANG_DEFAULT'),$country);
				$cat_country = $this->addCategory($country_name, false, $grp_ids);

				if ($cat_country)
				{
					$state_name = (new State())->getNameById($state);
					$cat_state = $this->addCategory($state_name, $cat_country, $grp_ids);
				}
				if ($cat_state)
					$cat_city = $this->addCategory($city, $cat_state, $grp_ids);
				if ($cat_city)
					$cat_hotel = $this->addCategory($hotel_name, $cat_city, $grp_ids, 1, $new_hotel_id);
				if ($cat_hotel)
				{
					$obj_hotel_info = new HotelBranchInformation($new_hotel_id);
					$obj_hotel_info->id_category = $cat_hotel;
					$obj_hotel_info->save();
				}
			}

			if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
			{
				if ($hotel_id)
				{
					Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$new_hotel_id.'&update'.$this->table.'&conf=4&token='.$this->token);
				}
				else
					Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$new_hotel_id.'&update'.$this->table.'&conf=3&token='.$this->token);
			}
			else
			{
				if ($hotel_id)
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				else
					Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
			}
		}
		else
		{
			if ($hotel_id)
				$this->display = 'edit';
			else
				$this->display = 'add';
		}
	}

	public function processDelete()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			$object = $this->loadObject();
			if ($object->id)
			{
				$obj_branch_features = new HotelBranchFeatures();

				$obj_htl_cart_data = new HotelCartBookingData();
				
				$obj_htl_img = new HotelImage();
				
				$obj_htl_rm_info = new HotelRoomInformation();
				
				$obj_htl_rm_type = new HotelRoomType();
				$ids_product = $obj_htl_rm_type->getIdProductByHotelId($object->id);
				
				if (isset($ids_product) && $ids_product)
				{
					foreach ($ids_product as $key_prod => $value_prod)
					{
						$delete_cart_data = $obj_htl_cart_data->deleteBookingCartDataNotOrderedByProductId($value_prod['id_product']);
						
						$delete_room_info = $obj_htl_rm_info->deleteByProductId($value_prod['id_product']);
				
						$delete_room_type = $obj_htl_rm_type->deleteByProductId($value_prod['id_product']);
				
						$obj_product = new Product($value_prod['id_product']);
						$delete_product = $obj_product->delete();
					}
				}
				$delete_branch_features = $obj_branch_features->deleteBranchFeaturesByHotelId($object->id);
				$htl_all_images = $obj_htl_img->getAllImagesByHotelId($object->id);
				if ($htl_all_images)
				{
					foreach ($htl_all_images as $key_img => $value_img)
					{
						$path_img = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/'.$value_img['hotel_image_id'].'.jpg';
						@unlink($path_img);
					}
				}

				$delete_htl_img = $obj_htl_img->deleteByHotelId($object->id);
			}
		}
		else
		{
            $this->errors[] = Tools::displayError('An error occurred while deleting the object.').
                ' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
		parent::processDelete();
	}

	protected function processBulkDelete()
    {
    	if (is_array($this->boxes) && !empty($this->boxes))
    	{
    		foreach ($this->boxes as $key => $value)
    		{
    			$obj_branch_features = new HotelBranchFeatures();

				$obj_htl_cart_data = new HotelCartBookingData();
				
				$obj_htl_img = new HotelImage();
				
				$obj_htl_rm_info = new HotelRoomInformation();

    			$obj_htl_rm_type = new HotelRoomType();
				$ids_product = $obj_htl_rm_type->getIdProductByHotelId($value);
				if (isset($ids_product) && $ids_product)
				{
					foreach ($ids_product as $key_prod => $value_prod)
					{
						$delete_cart_data = $obj_htl_cart_data->deleteBookingCartDataNotOrderedByProductId($value_prod['id_product']);
				
						$delete_room_info = $obj_htl_rm_info->deleteByProductId($value_prod['id_product']);

						$delete_room_type = $obj_htl_rm_type->deleteByProductId($value_prod['id_product']);
				
						$obj_product = new Product($value_prod['id_product']);
						$delete_product = $obj_product->delete();
		    		}
		    	}
		    	$delete_branch_features = $obj_branch_features->deleteBranchFeaturesByHotelId($object->id);
				$htl_all_images = $obj_htl_img->getAllImagesByHotelId($object->id);
				
				foreach ($htl_all_images as $key_img => $value_img)
				{
					$path_img = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/'.$value_img['hotel_image_id'].'.jpg';
					@unlink($path_img);
				}
				$delete_htl_img = $obj_htl_img->deleteByHotelId($object->id);
	    	}
	    	parent::processBulkDelete();
    	}
    	else
            $this->errors[] = Tools::displayError('You must select at least one element to delete.');
    }

    public function processStatus()
    {
    	if (Validate::isLoadedObject($object = $this->loadObject()))
    	{
    		if ($object->id && $object->active)
			{
				$obj_htl_rm_info = new HotelRoomType();
				$ids_product = $obj_htl_rm_info->getIdProductByHotelId($object->id);
				if (isset($ids_product) && $ids_product)
				{
					foreach ($ids_product as $key_prod => $value_prod)
					{
						$obj_product = new Product($value_prod['id_product']);
					
						if ($obj_product->active)
							$obj_product->toggleStatus();
		    		}
		    	}
			}
        }
        else
        {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
        parent::processStatus();
    }

	public function addCategory($name, $parent_cat=false, $group_ids, $ishotel=false, $hotel_id=false)
	{
		if (!$parent_cat)
			$parent_cat = Category::getRootCategory()->id;

		if ($ishotel && $hotel_id)
		{
			$cat_id_hotel = Db::getInstance()->getValue('SELECT `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id='.$hotel_id);
			if ($cat_id_hotel)
			{
				$obj_cat = new Category($cat_id_hotel);
				$obj_cat->name = array();
				$obj_cat->description = array();
				$obj_cat->link_rewrite = array();

				foreach (Language::getLanguages(true) as $lang)
				{
					$obj_cat->name[$lang['id_lang']] = $name;
					$obj_cat->description[$lang['id_lang']] = $this->l('this category are for hotels only');
					$obj_cat->link_rewrite[$lang['id_lang']] = $this->l(Tools::link_rewrite($name));
				}
				$obj_cat->id_parent = $parent_cat;
				$obj_cat->groupBox = $group_ids;
				$obj_cat->save();
				$cat_id = $obj_cat->id;
				return $cat_id;
			}
		}
		$check_category_exists = Category::searchByNameAndParentCategoryId($this->context->language->id, $name, $parent_cat);

		if ($check_category_exists)
			return $check_category_exists['id_category'];
		else
		{
			$obj = new Category();
			$obj->name = array();
			$obj->description = array();
			$obj->link_rewrite = array();

			foreach (Language::getLanguages(true) as $lang)
			{
				$obj->name[$lang['id_lang']] = $name;
				$obj->description[$lang['id_lang']] = $this->l('this category are for hotels only');
				$obj->link_rewrite[$lang['id_lang']] = $this->l(Tools::link_rewrite($name));
			}
			$obj->id_parent = $parent_cat;
			$obj->groupBox = $group_ids;
			$obj->add();
			$cat_id = $obj->id;
			return $cat_id;
		}
	}

	public function ajaxProcessStateByCountryId()
	{
		$country_id = Tools::getValue('id_country');
		$states = array();
		$statesbycountry = State::getStatesByIdCountry($country_id);
		if ($statesbycountry)
		{
			$states = array();
			foreach($statesbycountry as $key=>$value)
			{
				$states[$key]['id'] = $value['id_state'];
				$states[$key]['name'] = $value['name'];
			}
			if (isset($states))
				die(Tools::jsonEncode($states));
			else
				die(Tools::jsonEncode($states));
		}
		else
			die(Tools::jsonEncode($states));
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
		$this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
		
		//tinymce
		$this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
		
		if (version_compare(_PS_VERSION_, '1.6.0.11', '>'))
			$this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
		else
			$this->addJS(_PS_JS_DIR_.'tinymce.inc.js');
	}
}	