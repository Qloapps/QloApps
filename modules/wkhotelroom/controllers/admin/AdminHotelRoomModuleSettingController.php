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

class AdminHotelRoomModuleSettingController extends ModuleAdminController
{
    protected $position_identifier = 'id_room_block_to_move';
    public function __construct()
    {
        $this->table = 'htl_room_block_data';
        $this->className = 'WkHotelRoomDisplay';
        $this->_defaultOrderBy = 'position';
        $this->bootstrap = true;
        $this->context = Context::getContext();

        $this->_select = ' pl.`name`, hbil.`hotel_name` ';

        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
            ON (pl.`id_product` = a.`id_product` AND pl.id_lang = '.(int) $this->context->language->id.')';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_room_type` hrt
            ON (hrt.`id_product` = a.`id_product`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
            ON (hrt.`id_hotel` = hbil.`id` AND hbil.`id_lang` = pl.`id_lang`)';

        $this->fields_options = array(
            'global' => array(
                'title' => $this->l('Hotel Room Display Setting'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'HOTEL_ROOM_DISPLAY_HEADING' => array(
                        'title' => $this->l('Hotel Room Block Title'),
                        'type' => 'textLang',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'hint' => $this->l('Enter a title for the hotel rooms block.'),
                    ),
                    'HOTEL_ROOM_DISPLAY_DESCRIPTION' => array(
                        'title' => $this->l('Hotel room block description'),
                        'type' => 'textareaLang',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'rows' => '4',
                        'cols' => '2',
                        'hint' => $this->l('Enter a description for the hotel rooms block.'),
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_room_block' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'id_product' => array(
                'title' => $this->l('Room Image'),
                'align' => 'center',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'getProductImage',
            ),
            'name' => array(
                'title' => $this->l('Room Type'),
                'align' => 'center',
                'orderby' => false,
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
                'align' => 'center',
                'type' => 'datetime',
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
        $this->identifier = 'id_room_block';

        parent::__construct();
        if ($this->hotelList = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1)) {
            $objHotelRoomType = new HotelRoomType();
            if ($this->loadObject(true)
                && $this->object->id
                && ($roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct((int) $this->object->id_product))
            ) {
                $idsHotel = array_column($this->hotelList, 'id_hotel');
                if (!in_array($roomTypeInfo['id_hotel'], $idsHotel)) {
                    $this->tabAccess['edit'] = 0;
                    $this->tabAccess['view'] = 0;
                }
            }
        } else {
            if ($this->loadObject(true) && $this->object->id) {
                $this->tabAccess['view'] =0;
                $this->tabAccess['edit'] =0;
            }
        }
    }

    public function getProductImage($idProduct)
    {
        $objProduct = new Product($idProduct, false, Configuration::get('PS_LANG_DEFAULT'));
        if ($coverImageId = Product::getCover($objProduct->id)) {
            $prodImg = $this->context->link->getImageLink(
                $objProduct->link_rewrite,
                $objProduct->id.'-'.$coverImageId['id_image'],
                ImageType::getFormatedName('home')
            );
        } else {
            $prodImg = $this->context->link->getImageLink(
                $objProduct->link_rewrite,
                $this->context->language->iso_code."-default",
                ImageType::getFormatedName('home')
            );
        }
        return '<img src="'.$prodImg.'" class="img-thumbnail htlRoomImg">';
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

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Hotel Room Block')
        );
    }

    public function renderForm()
    {
        if (!($this->loadObject(true))) {
            return;
        } else if (!$this->object->id && empty($this->hotelList)) {
            $this->warnings[] = $this->l('No hotel found to create new hotel room block.');
            return;
        }

        $productName = false;
        if ($idProduct = $this->object->id_product) {
            $product = new Product($idProduct, false, Configuration::get('PS_LANG_DEFAULT'));
            $productName = $product->name;
            $objHotelRoomType = new HotelRoomType();
            if ($roomInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct((int) $product->id)) {
                $this->fields_value['id_hotel'] = $roomInfo['id_hotel'];
            }
        }

        $this->context->smarty->assign(
            array(
                'productName' => $productName,
                'idProduct' => $idProduct
            )
        );
        $html = $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->module->name.
            '/views/templates/admin/hotel_room/product_search_block.tpl'
        );

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
                    'label' => $this->l('Search Room Type'),
                    'type' => 'html',
                    'name' => 'product_search',
                    'html_content' => $html,
                    'required' => true,
                    'hint' => $this->l('Select a room type to display in home page.')
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
        $idRoomBlock = Tools::getValue('id_room_block');
        $idProduct = Tools::getValue('id_product');
        $active = Tools::getValue('active');
        $objRoomBlock = new WkHotelRoomDisplay();

        /*==== Validations ====*/
        if (!Tools::getValue('id_product')) {
            $this->errors[] = $this->l('Please select Room Type.');
        } elseif (!Validate::isUnsignedId(Tools::getValue('id_product'))) {
            $this->errors[] = $this->l('Please enter valid room type.');
        } else {
            if ($objRoomBlock->checkRoomTypeAlreadySelected($idProduct, $idRoomBlock)) {
                $this->errors[] = $this->l('This Room Type is already selected.');
            }
            if (Validate::isLoadedObject($objProduct = new Product($idProduct))) {
                if ($active && !$objProduct->active) {
                    $this->errors[] = $this->l(
                        'Hotel room block can not be active because selected room type is not active'
                    );
                }
            } else {
                $this->errors[] = $this->l('Product not found');
            }
        }

        if (!count($this->errors)) {
            if ($idRoomBlock) {
                $objRoomBlock = new WkHotelRoomDisplay($idRoomBlock);
            } else {
                $objRoomBlock->position = $objRoomBlock->getHigherPosition();
            }
            $objRoomBlock->id_product = $idProduct;
            $objRoomBlock->active = $active;
            $objRoomBlock->save();
            $idRoomBlock = $objRoomBlock->id;

            if ($idRoomBlock) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        } else {
            if ($idRoomBlock) {
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
            if (!trim(Tools::getValue('HOTEL_ROOM_DISPLAY_HEADING_'.$defaultLangId))) {
                $this->errors[] = $this->l('Hotel rooms block title is required at least in ').
                $objDefaultLanguage['name'];
            } else {
                foreach ($languages as $lang) {
                    if (trim(Tools::getValue('HOTEL_ROOM_DISPLAY_HEADING_'.$lang['id_lang']))) {
                        if (!Validate::isGenericName(Tools::getValue('HOTEL_ROOM_DISPLAY_HEADING_'.$lang['id_lang']))) {
                            $this->errors[] = $this->l('Invalid hotel rooms block title in ').$lang['name'];
                        }
                    }
                }
            }
            if (!trim(Tools::getValue('HOTEL_ROOM_DISPLAY_DESCRIPTION_'.$defaultLangId))) {
                $this->errors[] = $this->l('Hotel rooms block description is required at least in ').
                $objDefaultLanguage['name'];
            } else {
                foreach ($languages as $lang) {
                    if (trim(Tools::getValue('HOTEL_ROOM_DISPLAY_DESCRIPTION_'.$lang['id_lang']))) {
                        if (!Validate::isGenericName(Tools::getValue('HOTEL_ROOM_DISPLAY_DESCRIPTION_'.$lang['id_lang']))) {
                            $this->errors[] = $this->l('Invalid hotel rooms block description in ').$lang['name'];
                        }
                    }
                }
            }
            if (!count($this->errors)) {
                foreach ($languages as $lang) {
                    // if lang fileds are at least in default language and not available in other languages then
                    // set empty fields value to default language value
                    if (!trim(Tools::getValue('HOTEL_ROOM_DISPLAY_HEADING_'.$lang['id_lang']))) {
                        $_POST['HOTEL_ROOM_DISPLAY_HEADING_'.$lang['id_lang']] = Tools::getValue(
                            'HOTEL_ROOM_DISPLAY_HEADING_'.$defaultLangId
                        );
                    }
                    if (!trim(Tools::getValue('HOTEL_ROOM_DISPLAY_DESCRIPTION_'.$lang['id_lang']))) {
                        $_POST['HOTEL_ROOM_DISPLAY_DESCRIPTION_'.$lang['id_lang']] = Tools::getValue(
                            'HOTEL_ROOM_DISPLAY_DESCRIPTION_'.$defaultLangId
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

    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if (!$object->active) {
                if (Validate::isLoadedObject($objProduct = new Product($object->id_product))) {
                    if (!$objProduct->active) {
                        $this->errors[] = $this->l(
                            'Hotel room block can not be active because selected room type is not active'
                        );
                    }
                } else {
                    $this->errors[] = $this->l('Product not found');
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
                if (Validate::isLoadedObject($object = new WkHotelRoomDisplay($id))) {
                    if (!$object->active) {
                        if (Validate::isLoadedObject($objProduct = new Product($object->id_product))) {
                            if (!$objProduct->active) {
                                $this->errors[] = $this->l('Because selected room type is not active so hotel room
                                block can not be active for Id = ').$id;
                            }
                        } else {
                            $this->errors[] = $this->l('Product not found for Id = ').$id;
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
        $idRoomBlock = (int) Tools::getValue('id');
        $positions = Tools::getValue('room_block');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idRoomBlock) {
                if ($objRoomBlock = new WkHotelRoomDisplay((int) $pos[2])) {
                    if (isset($position)
                        && $objRoomBlock->updatePosition($way, $position)
                    ) {
                        echo 'ok position '.(int) $position.' for hotel room block '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update hotel room block position '.
                        (int) $idRoomBlock.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This hotel room block ('.(int) $idRoomBlock.
                    ') cant be loaded"}';
                }
                break;
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_MODULE_DIR_.'wkhotelroom/views/js/WkHotelRoomBlockAdmin.js');
        $this->addCSS(_MODULE_DIR_.'wkhotelroom/views/css/WkHotelRoomBlockAdmin.css');
    }
}
