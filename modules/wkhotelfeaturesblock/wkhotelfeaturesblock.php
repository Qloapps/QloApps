<?php
if (!defined('_PS_VERSION_'))
	exit;

require_once (_PS_MODULE_DIR_.'hotelreservationsystem/define.php');
require_once dirname(__FILE__).'/../wkhotelfeaturesblock/classes/WkHotelFeaturesData.php';

class WkHotelFeaturesBlock extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
	private $_postErrors = array();
	public function __construct()
	{
		$this->name = 'wkhotelfeaturesblock';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'webkul';
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Hotel Features');
		$this->description = $this->l('Show Hotel Amenities on the home page using this module.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

    public function hookDisplayHome()
    {
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkHotelFeaturesBlockFront.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/wkHotelFeaturesBlockFront.js');

        $obj_features_data = new WkHotelFeaturesData();
        $hotelAmenities = $obj_features_data->getHotelAmenities();

        $HOTEL_AMENITIES_HEADING = Configuration::get('HOTEL_AMENITIES_HEADING');
        $HOTEL_AMENITIES_DESCRIPTION = Configuration::get('HOTEL_AMENITIES_DESCRIPTION');

        $this->context->smarty->assign(array('HOTEL_AMENITIES_HEADING' => $HOTEL_AMENITIES_HEADING, 
        									 'HOTEL_AMENITIES_DESCRIPTION' => $HOTEL_AMENITIES_DESCRIPTION, 
        									 'hotelAmenities' => $hotelAmenities, 
        									));

        return $this->display(__FILE__, 'hotelfeaturescontent.tpl');
    }

    public function hookDisplayAddModuleSettingLink()
    {
        $href_features_conf = $this->context->link->getAdminLink('AdminFeaturesModuleSetting');
        $this->context->smarty->assign('features_setting_link', $href_features_conf);
        return $this->display(__FILE__, 'hotelFeatureSettingLink.tpl');
    }

    public function hookDisplayFooterExploreSectionHook()
    {
        return $this->display(__FILE__, 'hotelFeatureFooterExploreLink.tpl');
    }

    public function hookDisplayDefaultNavigationHook() 
    {
        return $this->display(__FILE__, 'hotelFeatureNaviagtionMenu.tpl');
    }

    public function insertDefaultHotelFeaturesEntries()
    {
        $HOTEL_AMENITIES_HEADING = $this->l('Amenities');
        $HOTEL_AMENITIES_DESCRIPTION = $this->l('Families travelling with kids will find Amboseli national park a safari destination matched to no other, with less tourist traffic, breathtaking open space.');

        Configuration::updateValue('HOTEL_AMENITIES_HEADING', $HOTEL_AMENITIES_HEADING);
        Configuration::updateValue('HOTEL_AMENITIES_DESCRIPTION', $HOTEL_AMENITIES_DESCRIPTION);

        $amenityTitle = array('luxurious Rooms', 'World class cheffs', 'Restaurants', 'Gym & Spa');
        $feature_description  = $this->l('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s');

        for ($i = 1; $i < 5; $i++) {
            $obj_feature_data = new WkHotelFeaturesData();
            $obj_feature_data->feature_image = 0;
            $obj_feature_data->feature_title = $amenityTitle[$i-1];
            $obj_feature_data->feature_description = $feature_description;
            $obj_feature_data->active = 1;
            $obj_feature_data->add();

            $img_name = $obj_feature_data->id.'.jpg';
            $img_path = _PS_MODULE_DIR_.$this->name.'/views/img/hotels_features_img/'.$img_name;
            if (file_exists($img_path)) {
                unlink($img_path);
            }
            ImageManager::resize(_PS_MODULE_DIR_.$this->name.'/views/img/dummy_img/'.$i.'.jpg', $img_path);
            
            $obj_feature_data->feature_image = $img_name;
            $obj_feature_data->save();
        }

        return true;
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those ontrollers...
        $this->installTab('AdminFeaturesModuleSetting', 'Hotel Amenities Configurations');
        return true;
    }

    public function installTab($class_name,$tab_name,$tab_parent_name=false) 
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $tab_name;

        if($tab_parent_name)
            $tab->id_parent = (int)Tab::getIdFromClassName($tab_parent_name);
        else
            $tab->id_parent = -1;
        
        $tab->module = $this->name;
        $res = $tab->add();
        //Set position of the Hotel reservation System Tab to the position wherewe want...
        return $res;
    }

    public function install()
	{
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;

        $sql = str_replace(array('PREFIX_',  'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query)
            if ($query)
                if (!Db::getInstance()->execute(trim($query)))
                    return false;
		if (!parent::install()
			|| !$this->registerHook('displayHome')
            || !$this->registerHook('displayFooterExploreSectionHook')
            || !$this->registerHook('displayAddModuleSettingLink')
            || !$this->registerHook('displayDefaultNavigationHook')
            || !$this->callInstallTab()
            || !$this->insertDefaultHotelFeaturesEntries()
            )
			return false;
		return true;
	}

	public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }
        return true;
    }

    public function uninstall($keep = true)
    {
        if(!parent::uninstall() 
            || !$this->callUninstallTab()
        	|| ($keep && !$this->deleteTables())
            || ($keep && !$this->deleteConfigKeys())
            || ($keep && !$this->deleteHotelAmenityImg()))
            return false;

        return true;
    }

    public function deleteHotelAmenityImg()
    {
        $uploadedImg = glob(_PS_MODULE_DIR_.$this->name.'/views/img/hotels_features_img/*.jpg');
        if ($uploadedImg) {
            foreach ($uploadedImg as $amenityImg) {
                unlink($amenityImg);
            }
        }
        return true;
    }

    public function deleteConfigKeys()
    {
        $var = array('HOTEL_AMENITIES_HEADING',
        			 'HOTEL_AMENITIES_DESCRIPTION');

        foreach ($var as $key)
            if (!Configuration::deleteByName($key))
                return false;
        
        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_features_block_data`');
    }

    public function callUninstallTab()
    {
        $this->uninstallTab('AdminFeaturesModuleSetting');
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
        else
            return false;
    }
}
