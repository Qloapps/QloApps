<?php
/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminHotelFeaturePricesSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'htl_room_type_feature_pricing';
        $this->className = 'HotelRoomTypeFeaturePricing';

        $this->_select .= ' IF(a.impact_type=1 , CONCAT(round(a.impact_value, 2), " ",  "%"), a.impact_value) AS impact_value';
        $this->_select .= ' ,IF(a.impact_type=1 , \''.$this->l('Percentage').'\', \''.$this->l('Fixed Amount').'\') AS impact_type';
        $this->_select .= ' ,IF(a.impact_way=1 , \''.$this->l('Decrease').'\', \''.$this->l('Increase').'\') AS impact_way';

        $this->bootstrap = true;
        $this->identifier  = 'id';
        parent::__construct();

        $impactWays = array(1 => 'decrease', 2 => 'increase');
        $impactTypes = array(1 => 'Percentage', 2 => 'Fixed Price');

        $priorities = Configuration::get('HTL_FEATURE_PRICING_PRIORITY');
        $this->context->smarty->assign('featurePricePriority', explode(';', $priorities));
        $this->fields_options = array(
                    'feature_price_priority' => array(
                    ));
        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'id_product' => array(
                'title' => $this->l('Id Product'),
                'align' => 'center',
            ),
            'feature_price_name' => array(
                'title' => $this->l('Feature Name'),
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
            'date_from' => array(
                'title' => $this->l('Date From'),
                'align' => 'center',
                'type' => 'date',
            ),
            'date_to' => array(
                'title' => $this->l('Date To'),
                'align' => 'center',
                'type' => 'date',
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
    }

    /**
     * [setOrderCurrency description] - A callback function for setting currency sign with amount
     * @param [type] $echo [description]
     * @param [type] $tr   [description]
     */
    public static function setOrderCurrency($echo, $tr)
    {
        $currency_default = Configuration::get('PS_CURRENCY_DEFAULT');
        return Tools::displayPrice($echo, (int)$currency_default);
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add Feature Price'),
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
        $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $currencySign = $objCurrency->sign;
        $dateFrom = date('d-m-Y');
        $dateTo = date('d-m-Y', strtotime($dateFrom) + 86400);
        if ($this->display == 'edit') {
            $featurePriceId = Tools::getValue('id');
            $featurePriceInfo = new HotelRoomTypeFeaturePricing($featurePriceId);
            if ($featurePriceInfo->id_product) {
                $product = new Product($featurePriceInfo->id_product, false, Configuration::get('PS_LANG_DEFAULT'));
                $productName = $product->name;
                $this->context->smarty->assign('productName', $productName);
            }
            $dateTo = date('d-m-Y', strtotime($featurePriceInfo->date_to));
            $this->context->smarty->assign(array(
                'edit' => 1,
                'featurePriceInfo' => $featurePriceInfo,
                'special_days' => Tools::jsonDecode($featurePriceInfo->special_days),
            ));
        }
        $this->tpl_form_vars = array(
            'defaultcurrency_sign' => $currencySign,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        );

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
                    $this->errors[] = $this->l('Some error occurred while updating feature price priorities.');
                }
            } else {
                $this->errors[] = $this->l('Duplicate values selected for feature price priorities.');
            }
        } else {
            parent::postProcess();
        }
    }

    public function processSave()
    {
        $id = Tools::getValue('id');
        if (!isset($id) || !$id) {
            $id = 0;
        }
        $enableFeaturePrice = Tools::getValue('enable_feature_price');
        $roomTypeId = Tools::getValue('room_type_id');
        $featurePriceName = Tools::getValue('feature_price_name');
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');
        $isSpecialDaysExists = Tools::getValue('is_special_days_exists');
        $specialDays = Tools::getValue('special_days');
        $priceImpactWay = Tools::getValue('price_impact_way');
        $priceImpactType = Tools::getValue('price_impact_type');
        $impactValue = Tools::getValue('impact_value');
        $dateSelectionType = Tools::getValue('date_selection_type');
        $specificDate = date('Y-m-d', strtotime(Tools::getValue('specific_date')));
        $jsonSpecialDays = Tools::jsonEncode($specialDays);
        $roomTypeFeaturePricing = new HotelRoomTypeFeaturePricing();
        if (!$roomTypeId) {
            $this->errors[] = $this->l('Product is not selected. Please try again.');
        }

        if ($featurePriceName == '') {
            $this->errors[] = $this->l('Feature Price Name is required field.');
        } elseif (!Validate::isGenericName($featurePriceName)) {
            $this->errors[] = $this->l($this->l('Feature Price Name must not have Invalid characters <>;=#{}'));
        }

        if ($dateSelectionType == 1) {
            if ($dateFrom == '') {
                $this->errors[] = $this->l('Please choose Date from for the feature price.');
            }
            if ($dateTo == '') {
                $this->errors[] = $this->l('Please choose Date to for the feature price.');
            }
            $dateFrom = date('Y-m-d', strtotime(Tools::getValue('date_from')));
            $dateTo = date('Y-m-d', strtotime(Tools::getValue('date_to')));
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
                $this->errors[] = $this->l('Please choose Date from for the feature price.');
            } elseif (!Validate::isDate($specificDate)) {
                $this->errors[] = $this->l('Invalid Date From.');
            }
            $specificDate = date('Y-m-d', strtotime(Tools::getValue('specific_date')));
        }

        if (!$impactValue) {
            $this->errors[] = $this->l('Please enter a valid imapct value.');
        } else {
            if (!Validate::isPrice($impactValue)) {
                $this->errors[] = $this->l('Invalid value of impact value.');
            }
            if ($priceImpactType == 1) {
                if ($impactValue > 100) {
                    $this->errors[] = $this->l('Invalid value of impact percentage.');
                }
            }
        }
        if (!count($this->errors)) {
            if ($dateSelectionType == 2) {
                $isPlanTypeExists = $roomTypeFeaturePricing->checkRoomTypeFeaturePriceExistance($roomTypeId, $specificDate, date('Y-m-d', strtotime($specificDate) + 86400), 'specific_date', false, $id);
            } else if (isset($isSpecialDaysExists) && $isSpecialDaysExists) {
                $isPlanTypeExists = $roomTypeFeaturePricing->checkRoomTypeFeaturePriceExistance($roomTypeId, $dateFrom, $dateTo, 'special_day', $jsonSpecialDays, $id);
            } else {
                $isPlanTypeExists = $roomTypeFeaturePricing->checkRoomTypeFeaturePriceExistance($roomTypeId, $dateFrom, $dateTo, 'date_range', false, $id);
            }
            if ($isPlanTypeExists) {
                $this->errors[] = $this->l('A feature price plan already exists in which some dates are common with this plan. Please select a different date range.');
            } else {
                if ($id) {
                    $roomTypeFeaturePricing = new HotelRoomTypeFeaturePricing($id);
                } else {
                    $roomTypeFeaturePricing = new HotelRoomTypeFeaturePricing();
                }
                $roomTypeFeaturePricing->id_product = $roomTypeId;
                $roomTypeFeaturePricing->feature_price_name = $featurePriceName;
                $roomTypeFeaturePricing->date_selection_type = $dateSelectionType;
                if ($dateSelectionType == 1) {
                    $roomTypeFeaturePricing->date_from = $dateFrom;
                    $roomTypeFeaturePricing->date_to = $dateTo;
                } else {
                    $roomTypeFeaturePricing->date_from = $specificDate;
                    $roomTypeFeaturePricing->date_to = date('Y-m-d', strtotime($specificDate) + 86400);
                }
                $roomTypeFeaturePricing->impact_way = $priceImpactWay;
                $roomTypeFeaturePricing->is_special_days_exists = $isSpecialDaysExists;
                $roomTypeFeaturePricing->special_days = $jsonSpecialDays;
                $roomTypeFeaturePricing->impact_type = $priceImpactType;
                $roomTypeFeaturePricing->impact_value = $impactValue;
                $roomTypeFeaturePricing->active = $enableFeaturePrice;
                if ($roomTypeFeaturePricing->save()) {
                    if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                        if ($id) {
                            Tools::redirectAdmin(self::$currentIndex.'&id='.(int) $roomTypeFeaturePricing->id.'&update'.$this->table.'&conf=4&token='.$this->token);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&id='.(int) $roomTypeFeaturePricing->id.'&update'.$this->table.'&conf=3&token='.$this->token);
                        }
                    } else {
                        if ($id) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                        }
                    }
                } else {
                    $this->errors[] = $this->l('Some error occured while saving feature price plan.');
                }
            }
        }
        if (isset($id) && $id) {
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
            if ($productsByName) {
                foreach ($productsByName as &$product) {
                    $hotelRoomType = new HotelRoomType();
                    $roomInfoByIdProduct = $hotelRoomType->getRoomTypeInfoByIdProduct($product['id_product']);
                    $idHotel = $roomInfoByIdProduct['id_hotel'];
                    if (isset($idHotel) && $idHotel) {
                        $hotelBranchInformation = new HotelBranchInformation($idHotel);
                        $product['name'].= ' / '.$hotelBranchInformation->hotel_name;
                    }
                }
                echo Tools::jsonEncode($productsByName, true); die;
            } else {
                die(Tools::jsonEncode(array('status' => 'failed', 'msg' => $this->l('No match found for entered room type name.'))));    
            }
        } else {
            die(Tools::jsonEncode(array('status' => 'failed', 'msg' => $this->l('No match found for entered room type name.'))));
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/datepickerCustom.css');
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
    }
}
