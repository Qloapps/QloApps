<?php
/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminTestimonialsModuleSettingController extends ModuleAdminController
{
    protected $position_identifier = 'id_testimonial_block_to_move';
    public function __construct()
    {
        $this->table = 'htl_testimonials_block_data';
        $this->className = 'WkHotelTestimonialData';
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'position';
        $this->context = Context::getContext();
        $this->identifier  = 'id_testimonial_block';

        parent::__construct();

        $this->fields_options = array(
            'modulesetting' => array(
                'title' =>    $this->l('Hotel Testimonials Setting'),
                'fields' =>    array(
                    'HOTEL_TESIMONIAL_BLOCK_HEADING' => array(
                        'title' => $this->l('Testimonial block title'),
                        'type' => 'textLang',
                        'hint' => $this->l('Testimonial block title. ex. guest testimonials.'),
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName'
                    ),
                    'HOTEL_TESIMONIAL_BLOCK_CONTENT' => array(
                        'title' => $this->l('Testimonial block description'),
                        'type' => 'textareaLang',
                        'rows' => '4',
                        'cols' => '2',
                        'hint' => $this->l('Testimonial block description.'),
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_testimonial_block' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'date_upd' => array(
                'title' => $this->l('Person Image'),
                'align' => 'center',
                'callback' => 'getTestimonialImage',
                'search' => false,
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'align' => 'center',
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
                'class' => 'fixed-width-xs'
            ),
        );

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
    }

    public function getTestimonialImage($echo, $row)
    {
        $image = '';
        if ($echo) {
            $imgUrl = $this->context->link->getMediaLink(_MODULE_DIR_.$this->module->name.'/views/img/hotels_testimonials_img/'.$row['id_testimonial_block'].'.jpg');
            if ((bool)Tools::file_get_contents($imgUrl)) {
                $image = "<img class='img-thumbnail img-responsive' style='max-width:70px' src='".$imgUrl."'>";
            }
        }
        if ($image == '') {
            $imgUrl = $this->context->link->getMediaLink(_MODULE_DIR_.$this->module->name.'/views/img/default-user.jpg');
            $image = "<img class='img-thumbnail img-responsive' style='max-width:70px' src='".$imgUrl."'>";
        }
        return $image;
    }

    public function initContent()
    {
        parent::initContent();
        // to customize the view as per our requirements
        if ($this->display != 'add' && $this->display != 'edit') {
            $this->content = $this->renderOptions();
            $this->content .= $this->renderList();
            $this->context->smarty->assign('content', $this->content);
        }
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $imgUrl = $this->context->link->getMediaLink(_MODULE_DIR_.$this->module->name.'/views/img/hotels_testimonials_img/'.$obj->id.'.jpg');
        if ($imgExist = (bool)Tools::file_get_contents($imgUrl)) {
            $image = "<img class='img-thumbnail img-responsive' style='max-width:100px' src='".$imgUrl."'>";
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Hotel Testimonial Configuration'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Person Name'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => $this->l('Testimonial person name')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Person\'s Designation'),
                    'name' => 'designation',
                    'required' => true,
                    'hint' => $this->l('Testimonial person designation')
                ),
                array(
                    'type' => 'textarea',
                    'rows' => '4',
                    'label' => $this->l('Testimonial Description'),
                    'name' => 'testimonial_content',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Testimonial content')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Person image'),
                    'name' => 'testimonial_image',
                    'display_image' => true,
                    'image' => $imgExist ? $image : false,
                    'hint' => $this->l('Upload an image of the person to whom this testimonial belongs.'),
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
            )
        );
        return parent::renderForm();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new Testimonial')
        );
    }

    public function processSave()
    {
        $idTestimonial = Tools::getValue('id_testimonial_block');
        $personName = Tools::getValue('name');
        $personDesignation = Tools::getValue('designation');
        if (!$personName) {
            $this->errors[] = $this->l('Person\'s Name is a required field.');
        } elseif (!Validate::isName($personName)) {
            $this->errors[] = $this->l('Invalid Person\'s Name.');
        }
        if (!$personDesignation) {
            $this->errors[] = $this->l('Person\'s Designation is a required field.');
        } elseif (!Validate::isGenericName($personDesignation)) {
            $this->errors[] = $this->l('Invalid Person\'s Designation.');
        }

        // check if field is atleast in default language. Not available in default prestashop
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('testimonial_content_'.$defaultLangId))) {
            $this->errors[] = $this->l('testimonial content is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('testimonial_content_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('testimonial_content_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid testimonial content in ').$lang['name'];
                    }
                }
            }
        }

        if (isset($_FILES['testimonial_image']) && $_FILES['testimonial_image']['tmp_name']) {
            if ($error = ImageManager::validateUpload($_FILES['testimonial_image'], Tools::getMaxUploadSize())) {
                $this->errors[] = $error;
            }
        }

        if (!count($this->errors)) {
            if ($idTestimonial) {
                $objTestimonialData = new WkHotelTestimonialData($idTestimonial);
            } else {
                $objTestimonialData = new WkHotelTestimonialData();
                $objTestimonialData->position = $objTestimonialData->getHigherPosition();
            }
            $objTestimonialData->name = $personName;
            $objTestimonialData->designation = $personDesignation;
            // lang fields
            foreach ($languages as $lang) {
                if (!trim(Tools::getValue('testimonial_content_'.$lang['id_lang']))) {
                    $objTestimonialData->testimonial_content[$lang['id_lang']] = Tools::getValue(
                        'testimonial_content_'.$defaultLangId
                    );
                } else {
                    $objTestimonialData->testimonial_content[$lang['id_lang']] = Tools::getValue(
                        'testimonial_content_'.$lang['id_lang']
                    );
                }
            }
            $objTestimonialData->active = Tools::getValue('active');
            if ($objTestimonialData->save()) {
                if ($_FILES['testimonial_image']['size']) {
                    $testimonial_img_path = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotels_testimonials_img/'.
                    $objTestimonialData->id.'.jpg';
                    $imageSize = ImageType::getByName(ImageType::getFormatedName('small'));
                    ImageManager::resize(
                        $_FILES['testimonial_image']['tmp_name'],
                        $testimonial_img_path,
                        $imageSize['width'],
                        $imageSize['height']
                    );
                }
            }
            if (Tools::getValue("id")) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        } else {
            if (Tools::getValue("id")) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptions'.$this->table)) {
            // check if field is atleast in default language. Not available in default prestashop
            $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
            $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
            $languages = Language::getLanguages(false);
            if (!trim(Tools::getValue('HOTEL_TESIMONIAL_BLOCK_HEADING_'.$defaultLangId))) {
                $this->errors[] = $this->l('Testimonial block title is required at least in ').
                $objDefaultLanguage['name'];
            } else {
                foreach ($languages as $lang) {
                    if (trim(Tools::getValue('HOTEL_TESIMONIAL_BLOCK_HEADING_'.$lang['id_lang']))) {
                        if (!Validate::isGenericName(Tools::getValue('HOTEL_TESIMONIAL_BLOCK_HEADING_'.$lang['id_lang']))) {
                            $this->errors[] = $this->l('Invalid testimonial block title in ').$lang['name'];
                        }
                    }
                }
            }
            if (!trim(Tools::getValue('HOTEL_TESIMONIAL_BLOCK_CONTENT_'.$defaultLangId))) {
                $this->errors[] = $this->l('Testimonial block description is required at least in ').
                $objDefaultLanguage['name'];
            } else {
                foreach ($languages as $lang) {
                    if (trim(Tools::getValue('HOTEL_TESIMONIAL_BLOCK_CONTENT_'.$lang['id_lang']))) {
                        if (!Validate::isGenericName(Tools::getValue('HOTEL_TESIMONIAL_BLOCK_CONTENT_'.$lang['id_lang']))) {
                            $this->errors[] = $this->l('Invalid testimonial block description in ').$lang['name'];
                        }
                    }
                }
            }
            if (!count($this->errors)) {
                foreach ($languages as $lang) {
                    // if lang fileds are at least in default language and not available in other languages then
                    // set empty fields value to default language value
                    if (!trim(Tools::getValue('HOTEL_TESIMONIAL_BLOCK_HEADING_'.$lang['id_lang']))) {
                        $_POST['HOTEL_TESIMONIAL_BLOCK_HEADING_'.$lang['id_lang']] = Tools::getValue(
                            'HOTEL_TESIMONIAL_BLOCK_HEADING_'.$defaultLangId
                        );
                    }
                    if (!trim(Tools::getValue('HOTEL_TESIMONIAL_BLOCK_CONTENT_'.$lang['id_lang']))) {
                        $_POST['HOTEL_TESIMONIAL_BLOCK_CONTENT_'.$lang['id_lang']] = Tools::getValue(
                            'HOTEL_TESIMONIAL_BLOCK_CONTENT_'.$defaultLangId
                        );
                    }
                }
                // if no custom errors the send to parent::postProcess() for further process
                parent::postProcess();
            }
        } else {
            parent::postProcess();
        }
    }

    // update positions
    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $idTestimonialBlock = (int) Tools::getValue('id');
        $positions = Tools::getValue('testimonial_block');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idTestimonialBlock) {
                if ($objTestimonialBlock = new WkHotelTestimonialData((int) $pos[2])) {
                    if (isset($position)
                        && $objTestimonialBlock->updatePosition($way, $position, $idTestimonialBlock)
                    ) {
                        echo 'ok position '.(int) $position.' for testimonial block '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update testimonial block position '.
                        (int) $idTestimonialBlock.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This testimonial block ('.(int) $idTestimonialBlock.
                    ') can t be loaded"}';
                }
                break;
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        Media::addJsDef(
            array(
                'filesizeError' => $this->l('File exceeds maximum size.', null, true),
                'maxSizeAllowed' => Tools::getMaxUploadSize(),
            )
        );
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/WkTestimonialBlockAdmin.js');
    }
}
