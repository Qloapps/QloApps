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

class AdminHotelFeaturePricesSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'htl_room_type_feature_pricing';
        $this->className = 'HotelRoomTypeFeaturePricing';
        $this->bootstrap = true;
        $this->identifier  = 'id_feature_price';
        $this->context = Context::getContext();

        // START send access query information to the admin controller
        $this->access_select = ' SELECT a.`id_feature_price` FROM '._DB_PREFIX_.'htl_room_type_feature_pricing a';
        $this->access_join = ' INNER JOIN '._DB_PREFIX_.'htl_room_type hrt ON (hrt.id_product = a.id_product)';
        if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1)) {
            $this->access_where = ' WHERE hrt.id_hotel IN ('.implode(',', $acsHtls).')';
        }

        parent::__construct();

        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = a.`id_product` AND pl.`id_lang`='.(int) $this->context->language->id.')';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_lang` fpl ON (a.id_feature_price = fpl.id_feature_price AND fpl.`id_lang` = '.(int) $this->context->language->id.')';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_room_type` hrt ON (hrt.`id_product` = a.`id_product`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl ON (hbl.`id` = hrt.`id_hotel` AND hbl.`id_lang`='.(int) $this->context->language->id.')';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_group` hrtfpg ON (hrtfpg.`id_feature_price` = a.`id_feature_price`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (gl.`id_group` = hrtfpg.`id_group` AND gl.`id_lang` = '.(int) $this->context->language->id.')';

        $this->_select .= ' fpl.`feature_price_name` as ftr_price_name, CONCAT(pl.`name`, " (#", a.`id_product`, ")") as product_name, hbl.`hotel_name`, IF(a.impact_type='.(int) HotelRoomTypeFeaturePricing::IMPACT_TYPE_PERCENTAGE.', CONCAT(round(a.impact_value, 2), " ", "%"), a.impact_value) AS impact_value';
        $this->_select .= ' ,IF(a.impact_type='.(int) HotelRoomTypeFeaturePricing::IMPACT_TYPE_PERCENTAGE.', \''.$this->l('Percentage').'\', \''.$this->l('Fixed Amount').'\')
        AS impact_type, count(hrtfpg.`id_feature_price`) AS group_access_count, group_concat(gl.`name` separator ", ") as group_names';
        $this->_select .= ', CASE
            WHEN a.`impact_way` = '.(int) HotelRoomTypeFeaturePricing::IMPACT_WAY_DECREASE.' THEN \''.$this->l('Decrease').'\'
            WHEN a.`impact_way` = '.(int) HotelRoomTypeFeaturePricing::IMPACT_WAY_INCREASE.' THEN \''.$this->l('Increase').'\'
            WHEN a.`impact_way` = '.(int) HotelRoomTypeFeaturePricing::IMPACT_WAY_FIX_PRICE.' THEN \''.$this->l('Fix').'\'
        END AS `impact_way`';

        $this->_group = 'GROUP BY a.`id_feature_price`';

        $this->_where = ' AND a.`id_cart` = 0 AND a.`id_guest` = 0 AND a.`id_room` = 0';

        $impactWays = array(1 => $this->l('Decrease'), 2 => $this->l('Increase'), 3 => $this->l('Fix'));
        $impactTypes = array(1 => $this->l('Percentage'), 2 => $this->l('Fixed Price'));

        $priorities = Configuration::get('HTL_FEATURE_PRICING_PRIORITY');
        $this->context->smarty->assign('featurePricePriority', explode(';', $priorities));
        $this->fields_options = array('feature_price_priority' => array());
        $this->fields_list = array(
            'id_feature_price' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'ftr_price_name' => array(
                'title' => $this->l('Rule Name'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'product_name' => array(
                'title' => $this->l('Room Type'),
                'align' => 'center',
                'havingFilter' => true,
                'callback' => 'getRoomTypeLink',
            ),
            'hotel_name' => array(
                'title' => $this->l('Hotel'),
                'align' => 'center',
            ),
            'impact_way' => array(
                'title' => $this->l('Impact Way'),
                'align' => 'center',
                'type' => 'select',
                'list' => $impactWays,
                'filter_key' => 'a!impact_way',
            ),
            'impact_type' => array(
                'title' => $this->l('Impact Type'),
                'align' => 'center',
                'type' => 'select',
                'list' => $impactTypes,
                'filter_key' => 'a!impact_type',
            ),
            'impact_value' => array(
                'title' => $this->l('Impact Value'),
                'align' => 'center',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
            ),
            'group_access_count' => array(
                'align' => 'center',
                'title' => $this->l('Group Access'),
                'type' => 'bool',
                'callback' => 'setGroupAccessLabel',
                'search' => false,
            ),
            'date_from' => array(
                'title' => $this->l('Date From'),
                'align' => 'center',
                'type' => 'date',
            ),
            'date_to' => array(
                'title' => $this->l('Date To'),
                'align' => 'center',
                'type' => 'date',
                'callback' => 'getDateToValue',
            ),
            'active' => array(
                'align' => 'center',
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );

        $this->list_no_link = true;
    }

    public function getDateToValue($dateTo, $row)
    {
        if ($row['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE) {
            return date($this->context->language->date_format_lite, strtotime($dateTo));
        } else {
            return '<span class="badge badge-success">'.$this->l('Specific date').'</span>';
        }
    }

    public function getRoomTypeLink($productName, $row)
    {
        $displayData = '';
        if ($row['id_product']) {
            $displayData .= '<a target="_blank" href="'.$this->context->link->getAdminLink('AdminProducts').
                '&id_product='.$row['id_product'].'&updateproduct">'.$productName.'</a>';
        }
        return $displayData;
    }

    //A callback function for setting currency sign with amount
    public static function setOrderCurrency($echo, $row)
    {
        $currency_default = Configuration::get('PS_CURRENCY_DEFAULT');
        return Tools::displayPrice($echo, (int)$currency_default);
    }

    public function setGroupAccessLabel($echo, $row)
    {
        $this->context->smarty->assign('row' ,$row);
        $tpl_path = 'hotelreservationsystem/views/templates/admin/hotel_feature_prices_settings/group_access_label.tpl';
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$tpl_path);
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new rule'),
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
        $currencySign = $objCurrency->sign;
        $dateFrom = date('d-m-Y');
        $dateTo = date('d-m-Y', strtotime($dateFrom) + 86400);
        $currentLangId = $this->default_form_language ? $this->default_form_language : Configuration::get('PS_LANG_DEFAULT');

        $smartyVars['languages'] = Language::getLanguages(false);
        $smartyVars['currentLang'] = Language::getLanguage((int) $currentLangId);

        if ($this->display == 'edit') {
            $idFeaturePrice = Tools::getValue('id_feature_price');
            if (Validate::isLoadedObject(
                $objFeaturePrice = new HotelRoomTypeFeaturePricing($idFeaturePrice)
            )) {
                if ($objFeaturePrice->id_product) {
                    $product = new Product($objFeaturePrice->id_product, false, Configuration::get('PS_LANG_DEFAULT'));
                    $smartyVars['productName'] =  $product->name;
                }
            }
            if ($objFeaturePrice->special_days) {
                $smartyVars['special_days'] =  (array)json_decode($objFeaturePrice->special_days);
            }
            $smartyVars['objFeaturePrice'] = $objFeaturePrice;
            $smartyVars['edit'] = 1;

            $smartyVars['feature_price_groups'] = $objFeaturePrice->getGroups($idFeaturePrice);
        }
        $smartyVars['defaultcurrency_sign'] = $currencySign;
        $smartyVars['date_from'] = $dateFrom;
        $smartyVars['date_to'] = $dateTo;
        $smartyVars['groups'] = Group::getGroups($this->context->language->id);
        $this->context->smarty->assign($smartyVars);
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAddFeaturePricePriority')) {
            $priority = Tools::getValue('featurePricePriority');
            $uniquePriorities = array_unique($priority);
            if (count($priority) == count($uniquePriorities)) {
                $priorityConfig = implode(';', $priority);
                if (Configuration::updateValue('HTL_FEATURE_PRICING_PRIORITY', $priorityConfig)) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                } else {
                    $this->errors[] = $this->l('Some error occurred while updating advanced price rule priorities.');
                }
            } else {
                $this->errors[] = $this->l('Duplicate values selected for advanced price rule priorities.');
            }
        } else {
            parent::postProcess();
        }
    }

    public function processSave()
    {
        $idFeaturePrice = Tools::getValue('id_feature_price');
        if (!isset($idFeaturePrice) || !$idFeaturePrice) {
            $idFeaturePrice = 0;
        }
        $enableFeaturePrice = Tools::getValue('enable_feature_price');
        $roomTypeId = Tools::getValue('room_type_id');
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');
        $isSpecialDaysExists = Tools::getValue('is_special_days_exists');
        $specialDays = Tools::getValue('special_days');
        $priceImpactWay = Tools::getValue('price_impact_way');
        $priceImpactType = Tools::getValue('price_impact_type');
        $impactValue = Tools::getValue('impact_value');
        $dateSelectionType = Tools::getValue('date_selection_type');
        $specificDate = date('Y-m-d', strtotime(Tools::getValue('specific_date')));
        $jsonSpecialDays = json_encode($specialDays);
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');

        $objFeaturePricing = new HotelRoomTypeFeaturePricing();

        if ($priceImpactWay == HotelRoomTypeFeaturePricing::IMPACT_WAY_FIX_PRICE) {
            $priceImpactType = HotelRoomTypeFeaturePricing::IMPACT_TYPE_FIXED_PRICE;
        }

        $languages = Language::getLanguages(false);
        $objDefaultLang = new language($defaultLangId);
        $isPlanTypeExists = 0;
        if ($dateSelectionType == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_SPECIFIC) {
            $isPlanTypeExists = $objFeaturePricing->checkRoomTypeFeaturePriceExistance(
                $roomTypeId,
                $specificDate,
                date('Y-m-d', strtotime("+1 day", strtotime($specificDate))),
                'specific_date',
                false,
                $idFeaturePrice
            );
        } elseif (isset($isSpecialDaysExists) && $isSpecialDaysExists == 'on') {
            if ($jsonSpecialDays != "false") {
                $isPlanTypeExists = $objFeaturePricing->checkRoomTypeFeaturePriceExistance(
                    $roomTypeId,
                    $dateFrom,
                    $dateTo,
                    'special_day',
                    $jsonSpecialDays,
                    $idFeaturePrice
                );
            } else {
                $this->errors[] = $this->l('Please select at least one day for the special day selection.');
            }
        } else {
            $isPlanTypeExists = $objFeaturePricing->checkRoomTypeFeaturePriceExistance(
                $roomTypeId,
                $dateFrom,
                $dateTo,
                'date_range',
                false,
                $idFeaturePrice
            );
        }

        if ($isPlanTypeExists) {
            $this->errors[] = $this->l('An advanced price rule already exists in which some dates are common with this
            plan. Please select a different date range.');
        } else {
            if (!$roomTypeId) {
                $this->errors[] = $this->l('Room is not selected. Please try again.');
            }
            if (!Tools::getValue('feature_price_name_'.$defaultLangId)) {
                $this->errors[] = sprintf(
                    $this->l('Advanced price rule name is required at least in %s'),
                    $objDefaultLang->name
                );
            }
            $validateRules = call_user_func(
                array('HotelRoomTypeFeaturePricing', 'getValidationRules'),
                'HotelRoomTypeFeaturePricing'
            );
            foreach ($languages as $language) {
                if (!Validate::isCatalogName(Tools::getValue('feature_price_name_'.$language['id_lang']))) {
                    $this->errors[] = $this->l('Advanced price rule name is invalid in ').$language['name'];
                } elseif (Tools::strlen(Tools::getValue('feature_price_name_'.$language['id_lang'])) > $validateRules['sizeLang']['feature_price_name']) {
                    sprintf(
                        $this->l('Advanced price rule Name field is too long (%2$d chars max).'),
                        $validateRules['sizeLang']['feature_price_name']
                    );
                }
            }

            if ($dateSelectionType == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE) {
                if ($dateFrom == '') {
                    $this->errors[] = $this->l('Please choose Date from for the advanced price rule.');
                }
                if ($dateTo == '') {
                    $this->errors[] = $this->l('Please choose Date to for the advanced price rule.');
                }
                $dateFrom = date('Y-m-d', strtotime($dateFrom));
                $dateTo = date('Y-m-d', strtotime($dateTo));
                if (!Validate::isDate($dateFrom)) {
                    $this->errors[] = $this->l('Invalid Date From.');
                }
                if (!Validate::isDate($dateTo)) {
                    $this->errors[] = $this->l('Invalid Date To.');
                }
                if ($dateTo < $dateFrom) {
                    $this->errors[] = $this->l('Date To must be a date after Date From.');
                }
                if (isset($isSpecialDaysExists) && $isSpecialDaysExists == 'on') {
                    $isSpecialDaysExists = 1;
                    if (!isset($specialDays) || !$specialDays) {
                        $isSpecialDaysExists = 0;
                        $this->errors[] = $this->l('Please select at least one day for the special day selection.');
                    }
                } else {
                    $isSpecialDaysExists = 0;
                }
            } else {
                if ($specificDate == '') {
                    $this->errors[] = $this->l('Please choose Date from for the advanced price rule.');
                }
                $specificDate = date('Y-m-d', strtotime($specificDate));
                if (!Validate::isDate($specificDate)) {
                    $this->errors[] = $this->l('Invalid Date From.');
                }
            }

            if (!$impactValue) {
                $this->errors[] = $this->l('Please enter a valid impact value.');
            } else {
                if (!Validate::isPrice($impactValue)) {
                    $this->errors[] = $this->l('Invalid value of impact value.');
                }
            }

            if (!(bool)Tools::getValue('groupBox')) {
                $this->errors[] = $this->l('Please select at least one group for the group access');
            }

            if (!count($this->errors)) {
                if ($idFeaturePrice) {
                    $objFeaturePricing = new HotelRoomTypeFeaturePricing($idFeaturePrice);
                }
                $objFeaturePricing->id_product = $roomTypeId;
                // lang fields
                foreach ($languages as $language) {
                    if (Tools::getValue('feature_price_name_'.$language['id_lang'])) {
                        $objFeaturePricing->feature_price_name[$language['id_lang']] = Tools::getValue(
                            'feature_price_name_'.$language['id_lang']
                        );
                    } else {
                        $objFeaturePricing->feature_price_name[$language['id_lang']] = Tools::getValue(
                            'feature_price_name_'.$defaultLangId
                        );
                    }
                }
                $objFeaturePricing->date_selection_type = $dateSelectionType;

                if ($dateSelectionType == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE) {
                    $objFeaturePricing->date_from = $dateFrom;
                    $objFeaturePricing->date_to = $dateTo;
                } else {
                    $objFeaturePricing->date_from = $specificDate;
                    $objFeaturePricing->date_to = date('Y-m-d', strtotime($specificDate) + 86400);
                }
                $objFeaturePricing->impact_way = $priceImpactWay;
                $objFeaturePricing->is_special_days_exists = $isSpecialDaysExists;
                $objFeaturePricing->special_days = $jsonSpecialDays;
                $objFeaturePricing->impact_type = $priceImpactType;
                $objFeaturePricing->impact_value = $impactValue;
                $objFeaturePricing->active = $enableFeaturePrice;

                // set the values of the groups for this feature price
                $objFeaturePricing->groupBox = Tools::getValue('groupBox');

                if ($objFeaturePricing->save()) {
                    if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&id_feature_price='.(int) $objFeaturePricing->id.
                            '&update'.$this->table.'&conf=4&token='.$this->token
                        );
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                    }
                } else {
                    $this->errors[] = $this->l('Some error occured while saving advanced price rule.');
                }
            }
        }
        if (isset($idFeaturePrice) && $idFeaturePrice) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
    }

    public function ajaxProcessSearchProductByName()
    {
        $productName = Tools::getValue('room_type_name');
        if ($productName) {
            $productsByName = Product::searchByName($this->context->language->id, $productName);
            // filter room types as per accessed hotels
            $productsByName = HotelBranchInformation::filterDataByHotelAccess(
                $productsByName,
                $this->context->employee->id_profile
            );
            if ($productsByName) {
                foreach ($productsByName as &$product) {
                    $hotelRoomType = new HotelRoomType();
                    $roomInfoByIdProduct = $hotelRoomType->getRoomTypeInfoByIdProduct($product['id_product']);
                    $idHotel = $roomInfoByIdProduct['id_hotel'];
                    if (isset($idHotel) && $idHotel) {
                        $onjBranchInfo = new HotelBranchInformation($idHotel, $this->context->language->id);
                        $product['name'].= ' / '.$onjBranchInfo->hotel_name;
                    }
                }
                echo json_encode($productsByName, true);
                die;
            } else {
                die(
                    json_encode(
                        array('status' => 'failed', 'msg' => $this->l('No match found for entered room type name.'))
                    )
                );
            }
        } else {
            die(
                json_encode(
                    array('status' => 'failed', 'msg' => $this->l('No match found for entered room type name.'))
                )
            );
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        Media::addJsDef(array(
            'date_selection_types' => array(
                'range' => HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE,
                'specific' => HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_SPECIFIC,
            ),
            'impact_ways' => array(
                'decrease' => HotelRoomTypeFeaturePricing::IMPACT_WAY_DECREASE,
                'increase' => HotelRoomTypeFeaturePricing::IMPACT_WAY_INCREASE,
                'fix' => HotelRoomTypeFeaturePricing::IMPACT_WAY_FIX_PRICE,
            ),
            'impact_types' => array(
                'percentage' => HotelRoomTypeFeaturePricing::IMPACT_TYPE_PERCENTAGE,
                'fixed' => HotelRoomTypeFeaturePricing::IMPACT_TYPE_FIXED_PRICE,
            ),
        ));

        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
    }
}
