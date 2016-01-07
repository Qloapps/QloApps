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
		$this->version = '0.0.2';
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
        if (Tools::isSubmit('save_parent_testimonial_data'))
        {
            $testi_head = Tools::getValue('testimonial_heading');
            $testi_description = Tools::getValue('testimonial_description');
            $testimonial_id = Tools::getValue('parent_testimonial_id');
            
            if (!$testi_head)
                    $this->_postErrors[] = $this->l('Testimonial Heading is a required field.');
            if (!$testi_description)
                    $this->_postErrors[] = $this->l('Testimonial description is a required field.');

            if (!count($this->_postErrors))
            {
                if ($testimonial_id)
                    $obj_feature_data = new WkHotelFeaturesData($testimonial_id);
                else        
                    $obj_feature_data = new WkHotelFeaturesData();

                $obj_feature_data->blog_heading = $testi_head;
                $obj_feature_data->blog_description = $testi_description;
                $obj_feature_data->parent_data = 1;
                $obj_feature_data->save();

                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
            }
            else
            {
                foreach ($this->_postErrors as $err)
                    $this->_html .= $this->displayError($err);
            }
        }
        else if (Tools::isSubmit('save_testimonial_data'))
        {
            $name = Tools::getValue('name');
            $testimonial_content = Tools::getValue('testimonial_content');

            $testimonial_id =  Tools::getValue('testimonial_id');
            foreach ($name as $key => $value)
            {
                if (!$name[$key])
                    $this->_postErrors[] = $this->l('Name is a required field.');
                if (!$testimonial_content[$key])
                    $this->_postErrors[] = $this->l('Testimonial content is a required field.');
            }
            if ($_FILES)
                $this->validateTestimonialsImages($_FILES['testimonial_image']);                
                $testimonial_img_path = _PS_MODULE_DIR_.'wktestimonialblock/views/img/';

            if (!count($this->_postErrors))
            {
                foreach ($name as $key_testi => $value_testi)
                {
                    if (isset($testimonial_id[$key_testi]) && $testimonial_id[$key_testi])
                        $obj_testimonial_data = new WkHotelTestimonialData($testimonial_id[$key_testi]);
                    else        
                        $obj_testimonial_data = new WkHotelTestimonialData();

                    $obj_testimonial_data->name = $value;
                    $obj_testimonial_data->testimonial_description = $testimonial_description[$key_testi];
                    $obj_testimonial_data->testimonial_content = $testimonial_content[$key_testi];
                    $obj_feature_data->parent_data = 0;
                    $obj_testimonial_data->save();

                    if (isset($_FILES['testimonial_image']['name'][$key_testi]) && $_FILES['testimonial_image']['name'][$key_testi])
                    {
                        $obj_testimonial_data_img = new WkHotelTestimonialData($obj_testimonial_data->id);

                        $image_name = $obj_testimonial_data->id.'.png';
                        ImageManager::resize($_FILES['testimonial_image']['tmp_name'][$key_testi], $testimonial_img_path.$image_name);

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
        $testimonials_parent_data = $obj_testimonial_data->getParentTestimonialsData();
        
        $this->context->smarty->assign('testimonials_data', $testimonials_data);
        $this->context->smarty->assign('parent_testi_data', $testimonials_parent_data);
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
        $testimonial_heading = $this->l('Guest Testimonials');
        $testimonial_description = $this->l('Fap put a bird on it next level, sustainable disrupt polaroid flannel Helvetica Kickstarter quinoa bicycle rights narwhal wolf Fap put a bird on it next level.');
        $designations = array(0 => 'Eon Comics CEO', 1 => 'Ken Comics Kal', 2 => 'Jan Comics Joe');
        $images = array(0 => '1.png', 1 => '2.png', 2 => '3.png');
        $names = array(0 => 'Calrk Kent', 1 => 'Calrk Kent', 2 => 'Calrk Kent');

        $testimonial_content = array(
            0 => $this->l('Hashtag typewriter YOLO try-hard, deep v Schlitz Etsy lumbersexual vegan meditation ethical pork belly ugh selvage. Flannel Schlitz put a bird on it shabby chic. Whatever Carles blog, gastropub asymmetrical Brooklyn tofu single-origin coffee. Bicycle rights raw denim Vice, blog brunch kale chips synth sustainable artisan. Helvetica mumblecore hoodie beard.'),
            1 => $this->l('Hashtag typewriter YOLO try-hard, deep v Schlitz Etsy lumbersexual vegan meditation ethical pork belly ugh selvage. Flannel Schlitz put a bird on it shabby chic. Whatever Carles blog, gastropub asymmetrical Brooklyn tofu single-origin coffee. Bicycle rights raw denim Vice, blog brunch kale chips synth sustainable artisan. Helvetica mumblecore hoodie beard.'), 
            2 => $this->l('Hashtag typewriter YOLO try-hard, deep v Schlitz Etsy lumbersexual vegan meditation ethical pork belly ugh selvage. Flannel Schlitz put a bird on it shabby chic. Whatever Carles blog, gastropub asymmetrical Brooklyn tofu single-origin coffee. Bicycle rights raw denim Vice, blog brunch kale chips synth sustainable artisan. Helvetica mumblecore hoodie beard.'));

        $obj_testimonial_data = new WkHotelTestimonialData();
        $obj_testimonial_data->testimonial_heading = $testimonial_heading;
        $obj_testimonial_data->testimonial_description = $testimonial_description;
        $obj_testimonial_data->parent_data = 1;
        $obj_testimonial_data->save();

        foreach ($images as $key => $value)
        {
            $obj_testimonial_data = new WkHotelTestimonialData();
            $obj_testimonial_data->testimonial_image = $value;
            $obj_testimonial_data->name = $names[$key];
            $obj_testimonial_data->designation = $designations[$key];
            $obj_testimonial_data->testimonial_content = $testimonial_content[$key];
            $obj_testimonial_data->parent_data = 0;
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
        /*--- owl-carousel files ---*/
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/owl-carousel/owl.carousel.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/owl-carousel/owl.theme.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/owl-carousel/owl.transitions.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/css/owl-carousel/owl.carousel.js');

        /*---- Module Files ----*/
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/hotel_testimonial_block.js');
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/testimonialblock.css');

        $obj_testimonial_data = new WkHotelTestimonialData();
        $testimonials_data = $obj_testimonial_data->getAllTestimonialsData();

        $parent_testimonial_data = $obj_testimonial_data->getParentTestimonialsData();

        $this->context->smarty->assign('testimonials_data', $testimonials_data);
        $this->context->smarty->assign('parent_testimonial_data', $parent_testimonial_data);

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
