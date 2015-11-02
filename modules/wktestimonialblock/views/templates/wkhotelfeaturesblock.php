<?php
if (!defined('_PS_VERSION_'))
	exit;
include "classes/WkHotelFeaturesData.php";

class WkHotelFeaturesBlock extends Module
{
	private $_postErrors = array();
    const INSTALL_SQL_FILE = 'install.sql';
	public function __construct()
	{
		$this->name = 'wkhotelfeaturesblock';
		$this->tab = 'front_office_features';
		$this->version = '1.6.0';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Hotel Features');
		$this->description = $this->l('Shows Hotel Features.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

    public function getContent()
    {
        if (Tools::isSubmit('save_feature_blog'))
        {
            $feature_blog = Tools::getValue('feature_blog_title');
            $feature_blog_description = Tools::getValue('feature_blog_description');
            $feature_id = Tools::getValue('feature_id');
            
            if (!$feature_blog)
                    $this->_postErrors[] = $this->l('Feature blog title is a required field.');
            if (!$feature_blog)
                $this->_postErrors[] = $this->l('Feature blog desription is a required field.');

            if (!count($this->_postErrors))
            {
                if ($feature_id)
                    $obj_feature_data = new WkHotelFeaturesData($feature_id);
                else        
                    $obj_feature_data = new WkHotelFeaturesData();

                $obj_feature_data->blog_heading = Tools::getValue('feature_blog_title');
                $obj_feature_data->blog_description = Tools::getValue('feature_blog_description');
                $obj_feature_data->is_blog = 1;
                $obj_feature_data->save();

                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
            }
            else
            {
                foreach ($this->_postErrors as $err)
                    $this->_html .= $this->displayError($err);
            }
        }
        else if (Tools::isSubmit('save_feature_data'))
        {
            $feature_title = Tools::getValue('feature_title');
            $feature_description = Tools::getValue('feature_description');
            $feature_image =  Tools::getValue('feature_image');
            $features_id =  Tools::getValue('features_id');

            foreach ($feature_title as $key => $value)
            {
                if (!$value)
                    $this->_postErrors[] = $this->l('Feature title is a required field.');
                if (!$feature_description[$key])
                    $this->_postErrors[] = $this->l('Feature description is a required field.');
            }
            if ($_FILES)
                $this->validateFeaturesImages($_FILES['feature_image']);                
                $feature_img_path = _PS_MODULE_DIR_.'wkhotelfeaturesblock/views/img/';

            if (!count($this->_postErrors))
            {
                foreach ($feature_title as $key => $value)
                {
                    if (isset($features_id[$key]) && $features_id[$key])
                        $obj_feature_data = new WkHotelFeaturesData($features_id[$key]);
                    else        
                        $obj_feature_data = new WkHotelFeaturesData();

                    $obj_feature_data->feature_title = $value;
                    $obj_feature_data->feature_description = $feature_description[$key];
                    $obj_feature_data->is_blog = 0;
                    $obj_feature_data->save();

                    $obj_feature_data_img = new WkHotelFeaturesData($obj_feature_data->id);

                    $image_name = $obj_feature_data->id.'.jpg';

                    ImageManager::resize($_FILES['feature_image']['tmp_name'][$key], $feature_img_path.$image_name);

                    $obj_feature_data_img->feature_image = $image_name;
                    $obj_feature_data_img->save();
                }
                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
            }
            else
            {
                foreach ($this->_postErrors as $err)
                    $this->_html .= $this->displayError($err);
            }
        } 
        else
            $this->_html .= '<br />';

        $obj_features_data = new WkHotelFeaturesData();
        $features_data = $obj_features_data->getAllFeaturesData();
        $blog_data = $obj_features_data->getMainBlogData();

        $this->context->smarty->assign('features_data', $features_data);
        $this->context->smarty->assign('module_dir', _MODULE_DIR_);
        $this->context->smarty->assign('link', new Link());
        $this->context->smarty->assign('main_blog_data', $blog_data);
        $this->context->controller->addJS($this->_path.'views/js/hotel_features_block.js');
        $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/hotelfeaturesblog.tpl');

        return $this->_html;
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
			|| !$this->registerHook('displayHome'))
			return false;
		return true;
	}

	public function hookDisplayHome()
	{
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/hotel-featuresblock.css');

        $obj_features_data = new WkHotelFeaturesData();
        $features_data = $obj_features_data->getAllFeaturesData();
        $blog_data = $obj_features_data->getMainBlogData();

        $this->context->smarty->assign('features_data', $features_data);
        $this->context->smarty->assign('main_blog_data', $blog_data);
		return $this->display(__FILE__, 'hotelfeaturescontent.tpl');
	}

	// validate feature image by webkul
	public function validateFeaturesImages($image)
    {
        if (empty($image['name']))
            return;

        //if any one is invalid extension redirect
        foreach ($image['name'] as $img_name)
        {
            if ($img_name != "")
            {
                if(!ImageManager::isCorrectImageFileExt($img_name))
                    $this->_postErrors[] = $this->l('Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
            }
        }
    }
}
