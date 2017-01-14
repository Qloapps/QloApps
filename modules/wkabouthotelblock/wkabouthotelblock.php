<?php
if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__).'/define.php';

class WkAboutHotelBlock extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'wkabouthotelblock';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('About Hotel Block');
        $this->description = $this->l('Now show Block about your hotel using this module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookDisplayDefaultNavigationHook() 
    {
        return $this->display(__FILE__, 'hotelInteriorNaviagtionMenu.tpl');
    }

    public function hookDisplayHome()
    {
        // owl.carousel Plug-in files
        $this->context->controller->addCSS(_PS_MODULE_DIR_.'hotelreservationsystem/libs/owl.carousel/assets/owl.carousel.min.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_.'hotelreservationsystem/libs/owl.carousel/assets/owl.theme.default.min.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.'hotelreservationsystem/libs/owl.carousel/owl.carousel.min.js');

        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/WkAboutHotelBlockFront.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/WkAboutHotelBlockFront.js');
        
        $HOTEL_INTERIOR_HEADING = Configuration::get('HOTEL_INTERIOR_HEADING');
        $HOTEL_INTERIOR_DESCRIPTION = Configuration::get('HOTEL_INTERIOR_DESCRIPTION');

        $obj_inter_img = new WkHotelInteriorImage();
        $InteriorImg = $obj_inter_img->getHotelInteriorImg();

        $this->context->smarty->assign(array('HOTEL_INTERIOR_HEADING' => $HOTEL_INTERIOR_HEADING,
                                             'HOTEL_INTERIOR_DESCRIPTION' => $HOTEL_INTERIOR_DESCRIPTION,
                                             'InteriorImg' => $InteriorImg,
                                            ));

        return $this->display(__FILE__, 'hotelInteriorBlock.tpl');
    }

    public function hookDisplayAddModuleSettingLink()
    {
        return $this->display(__FILE__, 'aboutHotelBlockModuleSetting.tpl');
    }

    public function hookDisplayFooterExploreSectionHook()
    {
        return $this->display(__FILE__, 'hotelInteriorFooterExploreLink.tpl');
    }

    public function insertDefaultHotelEntries()
    {
        $HOTEL_INTERIOR_HEADING = $this->l('Interior');
        $HOTEL_INTERIOR_DESCRIPTION = $this->l('Families travelling with kids will find Amboseli national park a safari destination matched to no other, with less tourist traffic, breathtaking open space.');

        Configuration::updateValue('HOTEL_INTERIOR_HEADING', $HOTEL_INTERIOR_HEADING);
        Configuration::updateValue('HOTEL_INTERIOR_DESCRIPTION', $HOTEL_INTERIOR_DESCRIPTION);

        // Database Entry
        for ($i = 1; $i <= 12 ; $i++) {
            do {
                $tmp_name = uniqid().'.jpg';
            } while (file_exists(_PS_MODULE_DIR_.$this->name.'/views/img/hotel_interior/'.$tmp_name));
            ImageManager::resize(_PS_MODULE_DIR_.$this->name.'/views/img/dummy_img/'.$i.'.jpg', _PS_MODULE_DIR_.$this->name.'/views/img/hotel_interior/'.$tmp_name);
            $obj_inter_img = new WkHotelInteriorImage();
            $obj_inter_img->name = $tmp_name;
            $obj_inter_img->display_name = 'Dummy Image '.$i;
            $obj_inter_img->active = 1;
            $obj_inter_img->add();
        }

        return true;
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        }

        $sql = str_replace(array('PREFIX_',  'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        if (!parent::install()
            || !$this->registerHook('displayHome')
            || !$this->registerHook('displayFooterExploreSectionHook')
            || !$this->registerHook('displayAddModuleSettingLink')
            || !$this->registerHook('displayDefaultNavigationHook')
            || !$this->callInstallTab()
            || !$this->insertDefaultHotelEntries()
            )
            return false;
        return true;
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those controllers...
        $this->installTab('AdminAboutHotelBlockSetting', 'Hotel Description Configuration');
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
            || ($keep && !$this->deleteTables())
            || ($keep && !$this->deleteConfigKeys())
            || ($keep && !$this->deleteHotelInterierImg())
            || !$this->callUninstallTab())
            return false;

        return true;
    }

    public function deleteHotelInterierImg()
    {
        $uploadedImg = glob(_PS_MODULE_DIR_.$this->name.'/views/img/hotel_interior/*.jpg');
        if ($uploadedImg) {
            foreach ($uploadedImg as $interiorImg) {
                unlink($interiorImg);
            }
        }
        return true;
    }

    public function deleteConfigKeys()
    {
        $var = array('HOTEL_INTERIOR_HEADING', 'HOTEL_INTERIOR_DESCRIPTION');

        foreach ($var as $key)
            if (!Configuration::deleteByName($key))
                return false;
        
        return true;
    }

    public function callUninstallTab()
    {
        $this->uninstallTab('AdminAboutHotelBlockSetting');
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

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_interior_image`');
    }
}
