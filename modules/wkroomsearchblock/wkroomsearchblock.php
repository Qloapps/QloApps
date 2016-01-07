<?php
if (!defined('_PS_VERSION_'))
	exit;

require_once (_PS_MODULE_DIR_.'hotelreservationsystem/classes/HotelBranchInformation.php');

class WkRoomSearchBlock extends Module
{
	public function __construct()
	{
		$this->name = 'wkroomsearchblock';
		$this->tab = 'front_office_features';
		$this->version = '0.0.2';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Rooms Search Block');
		$this->description = $this->l('Search rooms by search block using this module.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function hookDisplayHeader()
	{
		$is_hotel_room_search = Tools::getValue('is_hotel_rooms_search');
		if (isset($is_hotel_room_search) && $is_hotel_room_search)
		{
			$hotel_cat_id = Tools::getValue('hotel_cat_id');
			$check_in = Tools::getValue('check_in_time');
			$check_out = Tools::getValue('check_out_time');

			$check_in = date("Y-m-d", strtotime($check_in));
			$check_out = date("Y-m-d", strtotime($check_out));

			$curr_date = date("Y-m-d");

			$error = false;

			if ($hotel_cat_id == '')
				$error = 1;
			elseif ($check_in == '' || !Validate::isDate($check_in))
				$error = 2;
			elseif ($check_out == '' || !Validate::isDate($check_out))
				$error = 3;
			elseif ($check_in < $curr_date)
				$error = 5;
			elseif ($check_out <= $check_in)
				$error = 4;

			if (!$error)
			{
				if (Configuration::get('PS_REWRITING_SETTINGS'))
					$redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'?date_from='.$check_in.'&date_to='.$check_out;
                else
                	$redirect_link = $this->context->link->getCategoryLink(new Category($hotel_cat_id, $this->context->language->id), null, $this->context->language->id).'&date_from='.$check_in.'&date_to='.$check_out;

				Tools::redirect($redirect_link);
			}
			else
			{
	            if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) 
	            	Tools::redirect($protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__."index.php?error=".$error);
	            else
	            	Tools::redirect(_PS_BASE_URL_.__PS_BASE_URI__."index.php?error=".$error);
            
			}
		}

		$location_enable = Configuration::get('WK_HOTEL_LOCATION_ENABLE');

		$hotel_branch_obj = new HotelBranchInformation();
		$hotel_info = $hotel_branch_obj->getActiveHotelBranchesInfo();

		$this->context->smarty->assign('hotel_name',$hotel_info);
		$this->context->smarty->assign('header_block_title', Configuration::get('WK_TITLE_HEADER_BLOCK'));
		$this->context->smarty->assign('header_block_content', Configuration::get('WK_CONTENT_HEADER_BLOCK'));
		$this->context->smarty->assign('location_enable',$location_enable);
		
		$this->context->controller->addJqueryUI(array('ui.datepicker'));
		$this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/searchblock.css');
		$this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/wksearchblock.js');
	}

	public function hookDisplaySearchHotelPanel($params)
	{
		if (Tools::getValue('controller') == 'index')
			$this->context->smarty->assign('is_index_page',1);

		$this->context->smarty->assign('wk_id_cart',$this->context->cart->id);

		if (Tools::getValue('error')) 
		{
			$this->context->smarty->assign('error', Tools::getValue('error'));
		}

		return $this->display(__FILE__, 'roomseachblock.tpl');
	}

	public function install()
	{
		if (!parent::install()
			|| !$this->registerHook('displayHeader')
			|| !$this->registerHook('displaySearchHotelPanel'))
			return false;
		return true;
	}
}