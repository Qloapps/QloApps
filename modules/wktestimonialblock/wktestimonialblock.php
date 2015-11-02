<?php
if (!defined('_PS_VERSION_'))
	exit;
include "classes/WkHotelTestimonialData.php";

class WkTestimonialBlock extends Module
{
	const INSTALL_SQL_FILE = 'install.sql';
    private $_postErrors = array();
	public function __construct()
	{
        $this->_postErrors = array();
		$this->name = 'wktestimonialblock';
		$this->tab = 'front_office_features';
		$this->version = '1.6.0';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Hotel Testimonial');
		$this->description = $this->l('Shows Hotel testimonials using this module.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function getContent()
    {
        if (Tools::isSubmit('save_testimonial_data'))
        {
            $name = Tools::getValue('name');
            $testimonial_description = Tools::getValue('testimonial_description');
            $testimonial_content = Tools::getValue('testimonial_content');

            $testimonial_id =  Tools::getValue('testimonial_id');

            foreach ($name as $key => $value)
            {
                if (!$testimonial_description[$key])
                    $this->_postErrors[] = $this->l('Testimonial description is a required field.');
                if (!$testimonial_content[$key])
                    $this->_postErrors[] = $this->l('Testimonial content is a required field.');
            }
            if ($_FILES)
                $this->validateTestimonialsImages($_FILES['testimonial_image']);                
                $testimonial_img_path = _PS_MODULE_DIR_.'wktestimonialblock/views/img/';

            if (!count($this->_postErrors))
            {
                foreach ($name as $key => $value)
                {
                    if (isset($testimonial_id[$key]) && $testimonial_id[$key])
                        $obj_testimonial_data = new WkHotelTestimonialData($testimonial_id[$key]);
                    else        
                        $obj_testimonial_data = new WkHotelTestimonialData();

                    $obj_testimonial_data->name = $value;
                    $obj_testimonial_data->testimonial_description = $testimonial_description[$key];
                    $obj_testimonial_data->testimonial_content = $testimonial_content[$key];
                    $obj_testimonial_data->save();

                    if (isset($_FILES['testimonial_image']['name'][$key]) && $_FILES['testimonial_image']['name'][$key])
                    {
                        $obj_testimonial_data_img = new WkHotelTestimonialData($obj_testimonial_data->id);

                        $image_name = $obj_testimonial_data->id.'.jpg';
                        ImageManager::resize($_FILES['testimonial_image']['tmp_name'][$key], $testimonial_img_path.$image_name);

                        $obj_testimonial_data_img->testimonial_image = $image_name;
                        $obj_testimonial_data_img->save();
                    }
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

        $obj_testimonial_data = new WkHotelTestimonialData();
        $testimonials_data = $obj_testimonial_data->getAllTestimonialsData();
        
        $this->context->smarty->assign('testimonials_data', $testimonials_data);
        $this->context->smarty->assign('module_dir', _MODULE_DIR_);

        //tinymce
        $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>'))
            $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        else
            $this->context->controller->addJS(_PS_JS_DIR_.'tinymce.inc.js');

        //tinymce setup
        $this->context->smarty->assign('path_css',_THEME_CSS_DIR_);
        $this->context->smarty->assign('ad',__PS_BASE_URI__.basename(_PS_ADMIN_DIR_));
        $this->context->smarty->assign('autoload_rte',true);
        $this->context->smarty->assign('lang',true);
        $this->context->smarty->assign('iso', $this->context->language->iso_code);
        $this->context->smarty->assign('link', new Link());

        $this->context->controller->addJS($this->_path.'views/js/hotel_testimonial_block.js');
        $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/hoteltestimonialblock.tpl');
        
        return $this->_html;
    }

    public function insertDefaultHotelTestimonialsEntries()
    {
        $designations = array(0 => 'DC Comics CEO', 1 => 'DC Comics CEO', 2 => 'DC Comics CEO');
        $images = array(0 => '1.jpg', 1 => '2.jpg', 2 => '3.jpg');
        $names = array(0 => 'Calrk Kent', 1 => 'Calrk Kent', 2 => 'Calrk Kent');
        $testimonial_description = array(
            0 => $this->l('Fap put a bird on it next level, sustainable disrupt polaroid flannel Helvetica Kickstarter quinoa bicycle rights narwhal wolf Fap put a bird on it next level. '),
            1 => $this->l('Fap put a bird on it next level, sustainable disrupt polaroid flannel Helvetica Kickstarter quinoa bicycle rights narwhal wolf Fap put a bird on it next level. '),
            2 => $this->l('Fap put a bird on it next level, sustainable disrupt polaroid flannel Helvetica Kickstarter quinoa bicycle rights narwhal wolf Fap put a bird on it next level. '));

        $testimonial_content = array(
            0 => $this->l('Hashtag typewriter YOLO try-hard, deep v Schlitz Etsy lumbersexual vegan meditation ethical pork belly ugh selvage. Flannel Schlitz put a bird on it shabby chic. Whatever Carles blog, gastropub asymmetrical Brooklyn tofu single-origin coffee. Bicycle rights raw denim Vice, blog brunch kale chips synth sustainable artisan. Helvetica mumblecore hoodie beard.'),
            1 => $this->l('Hashtag typewriter YOLO try-hard, deep v Schlitz Etsy lumbersexual vegan meditation ethical pork belly ugh selvage. Flannel Schlitz put a bird on it shabby chic. Whatever Carles blog, gastropub asymmetrical Brooklyn tofu single-origin coffee. Bicycle rights raw denim Vice, blog brunch kale chips synth sustainable artisan. Helvetica mumblecore hoodie beard.'), 
            2 => $this->l('Hashtag typewriter YOLO try-hard, deep v Schlitz Etsy lumbersexual vegan meditation ethical pork belly ugh selvage. Flannel Schlitz put a bird on it shabby chic. Whatever Carles blog, gastropub asymmetrical Brooklyn tofu single-origin coffee. Bicycle rights raw denim Vice, blog brunch kale chips synth sustainable artisan. Helvetica mumblecore hoodie beard.'));

        foreach ($images as $key => $value)
        {
            $obj_testimonial_data = new WkHotelTestimonialData();
            $obj_testimonial_data->testimonial_image = $value;
            $obj_testimonial_data->name = $names[$key];
            $obj_testimonial_data->designation = $designations[$key];
            $obj_testimonial_data->testimonial_description = $testimonial_description[$key];
            $obj_testimonial_data->testimonial_content = $testimonial_content[$key];
            $obj_testimonial_data->save();
        }

        return true;
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
            || !$this->insertDefaultHotelTestimonialsEntries())
			return false;
		return true;
	}

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_testimonials_block_data`');
    }

    public function uninstall($keep = true)
    {
        if(!parent::uninstall() 
            || !$this->deleteTables())
            return false;

        return true;
    }

	public function hookDisplayHome()
	{
        $obj_testimonial_data = new WkHotelTestimonialData();
        $testimonials_data = $obj_testimonial_data->getAllTestimonialsData();
        $this->context->smarty->assign('testimonials_data', $testimonials_data);
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/hotel_testimonial_block.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/jquery.slideview.js');
		$this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/testimonialblock.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/jquery.slideview.css');

		return $this->display(__FILE__, 'wktestimonialblock.tpl');
	}

	// validate testimonial image by webkul
	public function validateTestimonialsImages($image)
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
