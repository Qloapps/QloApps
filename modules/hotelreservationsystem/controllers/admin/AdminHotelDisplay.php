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

class AdminHotelDisplayController extends ModuleAdminController
{
    protected $position_identifier = 'id_hotel_block';
    public function __construct()
    {
        $this->table = 'htl_block_data';
        $this->className = 'HotelDisplayBlock';
        $this->_defaultOrderBy = 'position';
        $this->identifier = 'position';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->identifier = 'id_hotel_block';

        $this->access_select = ' SELECT a.`id` FROM '._DB_PREFIX_.'htl_branch_info a';
        if ($this->hotelList = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1)) {
            $acsHtls = array_column($this->hotelList, 'id_hotel');
            $this->access_where = ' WHERE a.id IN ('.implode(',', $acsHtls).')';
        }

        parent::__construct();

        $this->_select = 'hbil.`hotel_name` ';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
            ON (a.`id_hotel` = hbil.`id` AND hbil.`id_lang` =  '.(int) $this->context->language->id.')';

        $this->fields_options = array(
            'global' => array(
                'title' => $this->l('Hotel Display Block Setting'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'HOTEL_BLOCK_NAV_TYPE' => array(
                        'title' => $this->l('Slider navigation type'),
                        'hint' => $this->l('Select slider navigation type for the hotel blocks slider in the front office'),
                        'validation' => 'isInt',
                        'type' => 'select',
                        'list' => HotelHelper::getSliderNavigationTypes(),
                        'identifier' => 'id',
                        'cast' => 'intval'
                    ),
                    'HOTEL_BLOCK_DISPLAY_HEADING' => array(
                        'title' => $this->l('Hotel Block Title'),
                        'type' => 'textLang',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'hint' => $this->l('Enter a title for the hotel display block.'),
                    ),
                    'HOTEL_BLOCK_DISPLAY_DESCRIPTION' => array(
                        'title' => $this->l('Hotel Block Description'),
                        'type' => 'textareaLang',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'rows' => '4',
                        'cols' => '2',
                        'hint' => $this->l('Enter a description for the hotel display block.'),
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_hotel_block' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'id_hotel' => array(
                'title' => $this->l('Hotel Image'),
                'align' => 'center',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'getHotelImage',
            ),
            'hotel_name' => array(
                'title' => $this->l('Hotel'),
                'align' => 'center',
                'orderby' => false,
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'filter_key' => 'a!date_add',
                'align' => 'center',
                'type' => 'datetime',
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            )
        );

    }

    public function getHotelImage($idHotel)
    {
        if ($coverImage = HotelImage::getCover($idHotel)) {
            $objHotelImage = new HotelImage((int) $coverImage['id']);
            $imagePath = $objHotelImage->image_dir.$objHotelImage->id.'.'.$objHotelImage->image_format;
            $imageFormat = $objHotelImage->image_format;
        } else {
            $imageFormat = 'jpg';
            $type = ImageType::getFormatedName('small');
            $imageId = $this->context->language->iso_code."-default";
            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$imageId.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
            $imagePath = _PS_ROOT_DIR_.'/img/p/'.$this->context->language->iso_code."-default".($type ? '-'.$type : '').$theme.'.jpg';
        }

        return ImageManager::thumbnail($imagePath, $this->table.'_mini_'.$idHotel.'_'.$this->context->shop->id.'.'.$imageFormat, 45, $imageFormat);
    }

    public function initContent()
    {
        parent::initContent();
        // to customize the view as per our requirements
        if ($this->display != 'add' && $this->display != 'edit') {
            $this->content = $this->renderOptions();
            // this is added in the renderoptions and cause incorrect actions in the render list.
            $this->toolbar_btn = array();
            $this->content .= $this->renderList();
            $this->context->smarty->assign('content', $this->content);
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        if (empty($this->display) || $this->display == 'list') {
            $this->page_header_toolbar_btn['new'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add New Hotel Display Block')
            );
        }
    }

    public function renderForm()
    {
        if (!($this->loadObject(true))) {
            return;
        } else if (!$this->object->id && empty($this->hotelList)) {
            $this->warnings[] = $this->l('No hotel found to create new hotel room block.');
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Rooms Configuration'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Select hotel'),
                    'name' => 'id_hotel',
                    'required' => true,
                    'class' => 'chosen',
                    'options' => array(
                        'query' => $this->hotelList,
                        'id' => 'id_hotel',
                        'name' => 'hotel_name'
                    ),
                    'hint' => $this->l('Select the hotel to search for the room types.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => true,
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
        if (!$this->loadObject(true)) {
            return;
        }
        $idHotel = Tools::getValue('id_hotel');
        $conf = 3;
        $idHotelBlock = $this->object->id;
        $acsHtls = array_column($this->hotelList, 'id_hotel');

        // Validations
        if (!in_array($idHotel, $acsHtls)) {
            $this->errors[] = $this->l('Hotel Not Found!!');
        }

        if (!count($this->errors)) {
            if (!$this->object->id) {
                $this->object = new HotelDisplayBlock((int) $this->object->getByIdHotel($idHotel));
            }

            if ($this->object->id) {
                $conf = 4;
            } else {
                $this->object->position = $this->object->getHigherPosition();
            }

            $this->object->id_hotel = $idHotel;
            $this->object->active = Tools::getValue('active');
            if ($this->object->save()) {
                Tools::redirectAdmin(self::$currentIndex.'&conf='.$conf.'&token='.$this->token);
            }
        } else {
            if ($idHotelBlock) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function beforeUpdateOptions()
    {
        // check if field is atleast in default language. Not available in default prestashop
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('HOTEL_BLOCK_DISPLAY_HEADING_'.$defaultLangId))) {
            $this->errors[] = $this->l('Hotels block title is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('HOTEL_BLOCK_DISPLAY_HEADING_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('HOTEL_BLOCK_DISPLAY_HEADING_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid hotels block title in ').$lang['name'];
                    }
                }
            }
        }
        if (!trim(Tools::getValue('HOTEL_BLOCK_DISPLAY_DESCRIPTION_'.$defaultLangId))) {
            $this->errors[] = $this->l('Hotels block description is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('HOTEL_BLOCK_DISPLAY_DESCRIPTION_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('HOTEL_BLOCK_DISPLAY_DESCRIPTION_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid hotels block description in ').$lang['name'];
                    }
                }
            }
        }
        if (!count($this->errors)) {
            foreach ($languages as $lang) {
                // if lang fileds are at least in default language and not available in other languages then
                // set empty fields value to default language value
                if (!trim(Tools::getValue('HOTEL_BLOCK_DISPLAY_HEADING_'.$lang['id_lang']))) {
                    $_POST['HOTEL_BLOCK_DISPLAY_HEADING_'.$lang['id_lang']] = Tools::getValue(
                        'HOTEL_BLOCK_DISPLAY_HEADING_'.$defaultLangId
                    );
                }
                if (!trim(Tools::getValue('HOTEL_BLOCK_DISPLAY_DESCRIPTION_'.$lang['id_lang']))) {
                    $_POST['HOTEL_BLOCK_DISPLAY_DESCRIPTION_'.$lang['id_lang']] = Tools::getValue(
                        'HOTEL_BLOCK_DISPLAY_DESCRIPTION_'.$defaultLangId
                    );
                }
            }
        }
    }

    public function processUpdateOptions()
    {
        $this->beforeUpdateOptions();
        if (empty($this->errors)) {
            parent::processUpdateOptions();
        }
    }

    public function processStatus()
    {
        if (Validate::isLoadedObject($this->loadObject())) {
            if (!$this->object->active) {
                if (Validate::isLoadedObject($objHotelBranchInfo = new HotelBranchInformation($this->object->id_hotel))) {
                    if (!$objHotelBranchInfo->active) {
                        $this->errors[] = $this->l('Hotel block can not be set to active because related hotel is not active');
                    }
                } else {
                    $this->errors[] = $this->l('Hotel not found!');
                }
            }
            if (!count($this->errors)) {
                parent::processStatus();
            }
        } else {
            $this->errors[] = $this->l('An error occurred while updating the status for an object.').
            ' <b>'.$this->table.'</b> '.$this->l('(cannot load object)');
        }
    }

    protected function processBulkStatusSelection($status)
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                if (Validate::isLoadedObject($object = new HotelDisplayBlock((int) $id))) {
                    if (!$object->active) {
                        if (Validate::isLoadedObject($objHotelBranchInfo = new HotelBranchInformation($this->object->id_hotel))) {
                            if (!$objHotelBranchInfo->active) {
                                $this->errors[] = $this->l('Because selected Hotel is not active so hotel block can not be active for Id = ').$id;
                            }
                        } else {
                            $this->errors[] = $this->l('Hotel not found for Id = ').$id;
                        }
                    }
                } else {
                    $this->errors[] = $this->l('Cannot load object for Id = ').$id;
                }
            }
        }
        if (!count($this->errors)) {
            parent::processBulkStatusSelection($status);
        }
    }

    // update positions
    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $idObject = (int) Tools::getValue('id');
        $positions = Tools::getValue('hotel_block');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idObject) {
                if ($objHotelDisplayBlock = new HotelDisplayBlock((int) $pos[2])) {
                    if (isset($position)
                        && $objHotelDisplayBlock->updatePosition($way, $position)
                    ) {
                        echo 'ok position '.(int) $position.' for hotel block '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update hotel block position '.
                        (int) $idObject.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This hotel block ('.(int) $idObject.
                    ') cant be loaded"}';
                }
                break;
            }
        }
    }
}
