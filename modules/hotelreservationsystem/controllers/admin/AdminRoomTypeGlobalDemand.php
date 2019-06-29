<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminRoomTypeGlobalDemandController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'htl_room_type_global_demand';
        $this->className = 'HotelRoomTypeGlobalDemand';
        $this->bootstrap = true;
        $this->identifier  = 'id_global_demand';
        parent::__construct();

        $this->toolbar_title = $this->l('Manage Additional Facilities');

        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_room_type_global_demand_lang` asl
        ON (a.id_global_demand = asl.id_global_demand)';
        $this->_select .= ' asl.`name`';
        $this->_where = ' AND asl.`id_lang` = '.(int) $this->context->language->id;

        $this->fields_list = array(
            'id_global_demand' => array(
                'title' => $this->l('Id'),
                'align' => 'center',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'center',
            ),
            'price' => array(
                'title' => $this->l('Price'),
                'align' => 'center',
                'type' => 'price',
                'currency' => true,
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'date',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Facility'),
            'imgclass' => 'new'
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function renderForm()
    {
        $smartyVars = array();
        $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $smartyVars['defaultcurrencySign'] = $objCurrency->sign;
        $currentLangId = Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        $smartyVars['languages'] = $languages;
        $currentLang = Language::getLanguage((int) $currentLangId);
        $smartyVars['currentLang'] = $currentLang;
        if ($this->display == 'edit') {
            $idDemand = Tools::getValue('id_global_demand');
            if (Validate::isLoadedObject(
                $objGlobalDemand = new HotelRoomTypeGlobalDemand($idDemand)
            )) {
                $smartyVars['globalDemands'] =  (array)$objGlobalDemand;
                $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption();
                $smartyVars['globalDemands']['adv_option'] = array();
                if ($advOptions = $objAdvOption->getGlobalDemandAdvanceOptions($idDemand)) {
                    $smartyVars['globalDemands']['adv_option'] = $advOptions;
                }
                $smartyVars['edit'] = 1;
            }
        }
        Media::addJsDef(
            array(
                'globalDemandLink' => $this->context->link->getAdminLink('AdminRoomTypeGlobalDemand'),
                'currentLang' => $currentLang,
                'languages' => $languages,
                'defaultcurrencySign' => $objCurrency->sign,
                'img_dir_l' => _PS_IMG_.'l/',
            )
        );
        $smartyVars['ps_img_dir'] = _PS_IMG_.'l/';
        $this->context->smarty->assign($smartyVars);
        $this->context->smarty->assign($smartyVars);

        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        return parent::renderForm();
    }

    public function processSave()
    {
        $idDemand = Tools::getValue('id_global_demand');
        $price = Tools::getValue('price');
        $activeAdvOpt = Tools::getValue('active_adv_option');
        $advOptPrices = Tools::getValue('option_price');
        $advOptIds = Tools::getValue('id_option');
        // check if field is atleast in default language. Not available in default prestashop
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);

        if (!trim(Tools::getValue('demand_name_'.$defaultLangId))) {
            $this->errors[] = $this->l('Facility name is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                // validate non required fields
                if (trim(Tools::getValue('demand_name_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('demand_name_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid facility name in ').$lang['name'];
                    }
                }
            }
        }
        if (!Validate::isPrice($price)) {
            $this->errors[] = $this->l('Please enter a valid price.');
        }
        if ($activeAdvOpt && !$advOptPrices) {
            $this->errors[] = $this->l('Please create at least one advance option for the service.');
        }

        if (!count($this->errors)) {
            if ($idDemand) {
                $objGlobalDemand = new HotelRoomTypeGlobalDemand($idDemand);
            } else {
                $objGlobalDemand = new HotelRoomTypeGlobalDemand();
            }
            // lang fields
            foreach ($languages as $lang) {
                if (!trim(Tools::getValue('demand_name_'.$lang['id_lang']))) {
                    $objGlobalDemand->name[$lang['id_lang']] = Tools::getValue(
                        'demand_name_'.$defaultLangId
                    );
                } else {
                    $objGlobalDemand->name[$lang['id_lang']] = Tools::getValue(
                        'demand_name_'.$lang['id_lang']
                    );
                }
            }
            $objGlobalDemand->price = $price;
            if ($objGlobalDemand->save()) {
                $objOption = new HotelRoomTypeGlobalDemandAdvanceOption();
                $skipIds = array();
                // if active advance payment then only add in skipIds else delete all
                if ($activeAdvOpt && $advOptIds) {
                    foreach ($advOptIds as $idOpt) {
                        if ($idOpt) {
                            $skipIds[] = $idOpt;
                        }
                    }
                }
                $objOption->deleteGlobalDemandAdvanceOptions($objGlobalDemand->id, $skipIds);
                if ($activeAdvOpt) {
                    foreach ($advOptPrices as $key => $advPrice) {
                        if (!trim(Tools::getValue('option_name_'.$defaultLangId)[$key])) {
                            $this->errors[] = $this->l('Advance option name is required at least in ').
                            $objDefaultLanguage['name'];
                        } else {
                            foreach ($languages as $lang) {
                                // validate non required fields
                                if (trim(Tools::getValue('option_name_'.$lang['id_lang'])[$key])) {
                                    if (!Validate::isGenericName(
                                        Tools::getValue('option_name_'.$lang['id_lang'])[$key]
                                    )) {
                                        $this->errors[] = $this->l('Invalid advance option name in ').$lang['name'];
                                    }
                                }
                            }
                        }
                        if (!Validate::isPrice($advPrice)) {
                            $this->errors[] = $this->l('Please enter a valid price for advance option.');
                        }

                        if (!count($this->errors)) {
                            if (isset($advOptIds[$key]) && $advOptIds[$key]) {
                                $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption($advOptIds[$key]);
                            } else {
                                $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption();
                            }
                            $objAdvOption->id_global_demand = $objGlobalDemand->id;
                            // advance options lang fields
                            foreach ($languages as $lang) {
                                if (!trim(Tools::getValue('option_name_'.$lang['id_lang'])[$key])) {
                                    $objAdvOption->name[$lang['id_lang']] = Tools::getValue(
                                        'option_name_'.$defaultLangId
                                    )[$key];
                                } else {
                                    $objAdvOption->name[$lang['id_lang']] = Tools::getValue(
                                        'option_name_'.$lang['id_lang']
                                    )[$key];
                                }
                            }
                            $objAdvOption->price = $advPrice;
                            $objAdvOption->save();
                        }
                    }
                }
            }
            if (!count($this->errors)) {
                if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                    Tools::redirectAdmin(
                        self::$currentIndex.'&id_global_demand='.(int) $objGlobalDemand->id.'&update'.$this->table.
                        '&conf=3&token='.$this->token
                    );
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                }
            } else {
                // if product is saved but some errors are occurred while saving time slots information
                $this->warnings[] = $this->l('Facility is saved successfully. But advance options with invalid data are not saved. Please correct the invalid data.');
            }
        }
        if ($idDemand) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/roomTypeGlobalDemand.js');
    }
}
