<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminFeaturesModuleSettingController extends ModuleAdminController
{
    protected $position_identifier = 'id_features_block_to_move';
    public function __construct()
    {
        $this->table = 'htl_features_block_data';
        $this->className = 'WkHotelFeaturesData';
        $this->_defaultOrderBy = 'position';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->identifier = 'id_features_block';
        parent::__construct();

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_features_block_data_lang` hfl
        ON (a.id_features_block = hfl.id_features_block AND hfl.`id_lang` = '.(int) $this->context->language->id.')';

        $this->_select = ' hfl.`feature_title`';

        // field options for global fields
        $this->fields_options = array(
            'global' => array(
                'title' =>    $this->l('Hotel Amenity Setting'),
                'icon' =>   'icon-cogs',
                'fields' =>    array(
                    'HOTEL_AMENITIES_BLOCK_NAV_LINK' => array(
                        'title' => $this->l('Show link at navigation'),
                        'hint' => $this->l('Enable, if you want to display a link at navigation menu for the amenities block at home page.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'required' => true
                    ),
                    'HOTEL_AMENITIES_HEADING' => array(
                        'title' => $this->l('Amenity Block Title'),
                        'type' => 'textLang',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'hint' => $this->l('Enter a title for the amenity block.')
                    ),
                    'HOTEL_AMENITIES_DESCRIPTION' => array(
                        'title' => $this->l('Amenity Block Description'),
                        'type' => 'textareaLang',
                        'rows' => '4',
                        'cols' => '2',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'hint' => $this->l('Enter a description for the amenity block.')
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );
    }

    public function getAmenityImage($echo, $row)
    {
        $image = '';
        if ($echo) {
            $imgUrl = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotels_features_img/'.$row['id_features_block'].
            '.jpg';
            if (file_exists($imgUrl)) {
                $modImgUrl = _MODULE_DIR_.$this->module->name.'/views/img/hotels_features_img/'.
                $row['id_features_block'].'.jpg';
                $image = "<img class='img-thumbnail img-responsive' style='max-width:70px' src='".$modImgUrl."'>";
            }
        }
        if ($image == '') {
            $image = "--";
        }
        return $image;
    }

    public function initContent()
    {
        parent::initContent();
        // to customize the view as per our requirements
        if ($this->display != 'add' && $this->display != 'edit') {
            $this->content .= $this->wkRenderList();
            $this->context->smarty->assign('content', $this->content);
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Hotel Amenity')
        );
    }

    public function wkRenderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_features_block' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'date_upd' => array(
                'title' => $this->l('Amenity Image'),
                'align' => 'center',
                'callback' => 'getAmenityImage',
                'search' => false,
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
                'align' => 'center',
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
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

        return parent::renderList();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $imageUrl = $imageSize = false;
        if ($this->display == 'edit') {
            $image = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotels_features_img/'.$obj->id.'.jpg';
            $imageUrl = ImageManager::thumbnail(
                $image,
                $this->table.'_'.(int)$obj->id.'.'.$this->imageType,
                350,
                $this->imageType,
                true,
                true
            );
            $imageSize = file_exists($image) ? filesize($image) / 1000 : false;
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
                    'lang' => true,
                    'hint' => $this->l('This will be displayed as amenity heading.')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Amenity Description'),
                    'name' => 'feature_description',
                    'required' => true,
                    'rows' => '4',
                    'lang' => true,
                    'hint' => $this->l('This will be displayed as amenity description.')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Amenity Image'),
                    'name' => 'feature_image',
                    'required' => true,
                    'display_image' => true,
                    'image' => $imageUrl ? $imageUrl : false,
                    'size' => $imageSize,
                    'col' => 6,
                    'hint' => sprintf(
                        $this->l('Maximum image size: %1s'),
                        Tools::formatBytes(Tools::getMaxUploadSize())
                    ),
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
        $hotelAmenityId = Tools::getValue('id_features_block');
        /*==== Validations ====*/
        // check if field is atleast in default language. Not available in default prestashop
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('feature_description_'.$defaultLangId))) {
            $this->errors[] = $this->l('feature description is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('feature_description_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('feature_description_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid feature description in ').$lang['name'];
                    }
                }
            }
        }
        if (!trim(Tools::getValue('feature_title_'.$defaultLangId))) {
            $this->errors[] = $this->l('feature title is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('feature_title_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('feature_title_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid feature title in ').$lang['name'];
                    }
                }
            }
        }
        if (!$hotelAmenityId || $file['size']) {
            if (!$file['size']) {
                $this->errors[] = $this->l('Hotel Amenity Image Required.');
            } elseif ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize())) {
                $this->errors[] = $error;
            }
        }

        /*==== Validations ====*/
        if (!count($this->errors)) {
            if ($hotelAmenityId) {
                $objFeatureData = new WkHotelFeaturesData($hotelAmenityId);
            } else {
                $objFeatureData = new WkHotelFeaturesData();
                $objFeatureData->position = $objFeatureData->getHigherPosition();
            }

            // lang fields
            foreach ($languages as $lang) {
                if (!trim(Tools::getValue('feature_title_'.$lang['id_lang']))) {
                    $objFeatureData->feature_title[$lang['id_lang']] = Tools::getValue(
                        'feature_title_'.$defaultLangId
                    );
                } else {
                    $objFeatureData->feature_title[$lang['id_lang']] = Tools::getValue(
                        'feature_title_'.$lang['id_lang']
                    );
                }
                if (!trim(Tools::getValue('feature_description_'.$lang['id_lang']))) {
                    $objFeatureData->feature_description[$lang['id_lang']] = Tools::getValue(
                        'feature_description_'.$defaultLangId
                    );
                } else {
                    $objFeatureData->feature_description[$lang['id_lang']] = Tools::getValue(
                        'feature_description_'.$lang['id_lang']
                    );
                }
            }

            $objFeatureData->active = Tools::getValue('active');
            $objFeatureData->save();
            if ($file['size']) {
                $imgPath = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotels_features_img/'.$objFeatureData->id.
                '.jpg';
                if (file_exists($imgPath)) {
                    unlink($imgPath);
                }
                ImageManager::resize($file['tmp_name'], $imgPath);
            }

            if ($hotelAmenityId) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        } else {
            if ($hotelAmenityId) {
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
            if (!trim(Tools::getValue('HOTEL_AMENITIES_HEADING_'.$defaultLangId))) {
                $this->errors[] = $this->l('Amenity block title is required at least in ').
                $objDefaultLanguage['name'];
            }
            if (!trim(Tools::getValue('HOTEL_AMENITIES_DESCRIPTION_'.$defaultLangId))) {
                $this->errors[] = $this->l('Amenity block description is required at least in ').
                $objDefaultLanguage['name'];
            }
            if (!count($this->errors)) {
                foreach ($languages as $lang) {
                    // if lang fileds are at least in default language and not available in other languages then
                    // set empty fields value to default language value
                    if (!trim(Tools::getValue('HOTEL_AMENITIES_HEADING_'.$lang['id_lang']))) {
                        $_POST['HOTEL_AMENITIES_HEADING_'.$lang['id_lang']] = Tools::getValue(
                            'HOTEL_AMENITIES_HEADING_'.$defaultLangId
                        );
                    }
                    if (!trim(Tools::getValue('HOTEL_AMENITIES_DESCRIPTION_'.$lang['id_lang']))) {
                        $_POST['HOTEL_AMENITIES_DESCRIPTION_'.$lang['id_lang']] = Tools::getValue(
                            'HOTEL_AMENITIES_DESCRIPTION_'.$defaultLangId
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
        $idFeatureBlock = (int) Tools::getValue('id');
        $positions = Tools::getValue('features_block');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idFeatureBlock) {
                if ($objFeatureBlock = new WkHotelFeaturesData((int) $pos[2])) {
                    if (isset($position)
                        && $objFeatureBlock->updatePosition($way, $position, $idFeatureBlock)
                    ) {
                        echo 'ok position '.(int) $position.' for amenity block '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update amenity block position '.
                        (int) $idFeatureBlock.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This amenity block ('.(int) $idFeatureBlock.
                    ') can t be loaded"}';
                }
                break;
            }
        }
    }
}
