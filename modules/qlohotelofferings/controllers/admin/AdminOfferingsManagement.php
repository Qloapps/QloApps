<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class AdminOfferingsManagementController extends ModuleAdminController
{
    protected $position_identifier = 'id_offering_block_to_move';
    public function __construct()
    {
        $this->table = 'qho_offering';
        $this->className = 'QhoHotelOffering';
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'position';
        $this->context = Context::getContext();
        $this->identifier  = 'id_offering';
        $this->lang = true;

        parent::__construct();

        $this->_select = 'a.`id_offering` AS id_offering_img';
        $this->fields_options = array(
            'modulesetting' => array(
                'title' => $this->l('Offerings block Setting'),
                'fields' => array(
                    'OFFERING_DISPLAY_POSITION' => array(
                        'title' => $this->l('Display page'),
                        'hint' => $this->l('Select display page for the offerings block'),
                        'validation' => 'isInt',
                        'type' => 'select',
                        'list' => array(
                            array('id' => QhoHotelOffering::QHO_DISPLAY_PAGE_BOTH, 'name' => $this->l('Both pages')),
                            array('id' => QhoHotelOffering::QHO_DISPLAY_PAGE_HOTEL, 'name' => $this->l('Hotel page only')),
                            array('id' => QhoHotelOffering::QHO_DISPLAY_PAGE_HOME, 'name' => $this->l('Home Page only')),
                        ),
                        'identifier' => 'id',
                        'cast' => 'intval'
                    ),
                    'OFFERING_BLOCK_HEADING' => array(
                        'title' => $this->l('Offerings block title'),
                        'type' => 'textLang',
                        'lang' => true,
                        'hint' => $this->l('Offerings block title.'),
                        'required' => true,
                        'validation' => 'isGenericName'
                    ),
                    'OFFERING_BLOCK_CONTENT' => array(
                        'title' => $this->l('Offerings block description'),
                        'type' => 'textareaLang',
                        'rows' => '4',
                        'cols' => '2',
                        'hint' => $this->l('Offerings block description.'),
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
            'id_offering' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'id_offering_img' => array(
                'title' => $this->l('Offering Image'),
                'align' => 'center',
                'callback' => 'getOfferingImage',
                'search' => false,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'center',
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
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
                'class' => 'fixed-width-xs'
            ),
        );

    }

    public function getOfferingImage($echo, $row)
    {
        $imgUrl = $this->context->link->getMediaLink($this->module->getPathUri().'views/img/offering_img/'.$row['id_offering'].'.jpg');
        $image = "<img class='img-thumbnail img-responsive' style='max-width:70px' src='".$imgUrl."'>";
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
        $imgUrl = $this->context->link->getMediaLink($this->module->getPathUri().'views/img/offering_img/'.$obj->id.'.jpg');
        $image = "<img class='img-thumbnail img-responsive' style='max-width:100px' src='".$imgUrl."'>";

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->object->id ? $this->l('Edit') : $this->l('Add new'),
                'icon' => $this->object->id ? 'icon-pencil  ' : 'icon-plus',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Offering Name'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Offering name')
                ),
                array(
                    'type' => 'textarea',
                    'rows' => '4',
                    'label' => $this->l('Offering Description'),
                    'name' => 'description',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Testimonial content')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Offering image'),
                    'name' => 'offering_image',
                    'display_image' => true,
                    'required' => true,
                    'image' => $this->object->id ? $image : false,
                    'hint' => $this->l('Upload an image for the offering.'),
                    'desc' => $this->l('Recommended resolution: 720 x 540 pixels.'),
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
            'desc' => $this->l('Add new Offering')
        );
    }

    public function processSave()
    {
        $idTestimonial = Tools::getValue('id_offering');
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
        if (!trim(Tools::getValue('description_'.$defaultLangId))) {
            $this->errors[] = $this->l('testimonial content is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('description_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('description_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid testimonial content in ').$lang['name'];
                    }
                }
            }
        }

        if (isset($_FILES['offering_image']) && $_FILES['offering_image']['tmp_name']) {
            if ($error = ImageManager::validateUpload($_FILES['offering_image'], Tools::getMaxUploadSize())) {
                $this->errors[] = $error;
            }
        }

        if (!count($this->errors)) {
            if ($idTestimonial) {
                $objTestimonialData = new QhoHotelOffering($idTestimonial);
            } else {
                $objTestimonialData = new QhoHotelOffering();
                $objTestimonialData->position = $objTestimonialData->getHigherPosition();
            }
            $objTestimonialData->name = $personName;
            $objTestimonialData->designation = $personDesignation;
            // lang fields
            foreach ($languages as $lang) {
                if (!trim(Tools::getValue('description_'.$lang['id_lang']))) {
                    $objTestimonialData->description[$lang['id_lang']] = Tools::getValue(
                        'description_'.$defaultLangId
                    );
                } else {
                    $objTestimonialData->description[$lang['id_lang']] = Tools::getValue(
                        'description_'.$lang['id_lang']
                    );
                }
            }
            $objTestimonialData->active = Tools::getValue('active');
            if ($objTestimonialData->save()) {
                if ($_FILES['offering_image']['size']) {
                    $testimonial_img_path = $this->module->getLocalPath().'views/img/offering_img/'.
                    $objTestimonialData->id.'.jpg';
                    $imageSize = ImageType::getByName(ImageType::getFormatedName('small'));
                    ImageManager::resize(
                        $_FILES['offering_image']['tmp_name'],
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

    public function beforeUpdateOptions()
    {
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('OFFERING_BLOCK_HEADING_'.$defaultLangId))) {
            $this->errors[] = $this->l('Offerings block title is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('OFFERING_BLOCK_HEADING_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('OFFERING_BLOCK_HEADING_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid Offerings block title in ').$lang['name'];
                    }
                }
            }
        }
        if (!trim(Tools::getValue('OFFERING_BLOCK_CONTENT_'.$defaultLangId))) {
            $this->errors[] = $this->l('Offerings block description is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('OFFERING_BLOCK_CONTENT_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('OFFERING_BLOCK_CONTENT_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid Offerings block description in ').$lang['name'];
                    }
                }
            }
        }
        if (!count($this->errors)) {
            foreach ($languages as $lang) {
                // if lang fileds are at least in default language and not available in other languages then
                // set empty fields value to default language value
                if (!trim(Tools::getValue('OFFERING_BLOCK_HEADING_'.$lang['id_lang']))) {
                    $_POST['OFFERING_BLOCK_HEADING_'.$lang['id_lang']] = Tools::getValue(
                        'OFFERING_BLOCK_HEADING_'.$defaultLangId
                    );
                }
                if (!trim(Tools::getValue('OFFERING_BLOCK_CONTENT_'.$lang['id_lang']))) {
                    $_POST['OFFERING_BLOCK_CONTENT_'.$lang['id_lang']] = Tools::getValue(
                        'OFFERING_BLOCK_CONTENT_'.$defaultLangId
                    );
                }
            }
            // if no custom errors the send to parent::postProcess() for further process
        }
    }

    // update positions
    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $idTestimonialBlock = (int) Tools::getValue('id');
        $positions = Tools::getValue('offering');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idTestimonialBlock) {
                if ($objHotelOffering = new QhoHotelOffering((int) $pos[2])) {
                    if (isset($position) && $objHotelOffering->updatePosition($way, $position)) {
                        echo 'ok position '.(int) $position.' for Offerings block '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update Offerings block position '.
                        (int) $idTestimonialBlock.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This Offerings block ('.(int) $idTestimonialBlock.
                    ') can t be loaded"}';
                }
                break;
            }
        }
    }
}
