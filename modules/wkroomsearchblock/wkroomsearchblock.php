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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
include_once dirname(__FILE__).'/classes/WkRoomSearchHelper.php';

class WkRoomSearchBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wkroomsearchblock';
        $this->tab = 'front_office_features';
        $this->version = '1.1.1';
        $this->author = 'webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('QloApps Room Search Panels');
        $this->description = $this->l('Room search blocks on different pages to search rooms as per user travel parameters.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookActionFrontControllerSetMedia()
    {
        $controller = Tools::getValue('controller');
        // apply assets globally for all pages of search panel
        if ('category' == $controller
            || 'index' == $controller
            || 'product' == $controller
        ) {
            $this->context->controller->addCSS(_PS_MODULE_DIR_.'hotelreservationsystem/views/css/datepickerCustom.css');
            $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wk-global-search.css');
            $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/wk-room-search-block.js');

            Media::addJsDef(
                array (
                    'autocomplete_search_url' => $this->context->link->getModuleLink(
                        'wkroomsearchblock',
                        'autocompletesearch'
                    ),
                    'no_results_found_cond' => $this->l('No results found for this search'),
                    'hotel_name_cond' => $this->l('Please select a hotel name'),
                    'check_in_time_cond' => $this->l('Please enter Check In time'),
                    'check_out_time_cond' => $this->l('Please enter Check Out time'),
                    'less_checkin_date' => $this->l('Check In date can not be before current date.'),
                    'more_checkout_date' => $this->l('Check Out date must be greater than Check In date.'),
                    'select_htl_txt' => $this->l('Select Hotel'),
                )
            );
        }

        // apply assets as per pages
        if ('category' == $controller) {
            $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wk-category-search.css');
        }
        if ('index' == $controller) {
            $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wk-landing-page-search.css');
        }
        if ('product' == $controller) {
            $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wk-roomtype-search.css');
            $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/wk-roomtype-search.js');
        }
    }


    // search panel block on the landing page
    public function hookDisplayAfterHookTop($params)
    {
        if ('index' == Tools::getValue('controller')) {
            $objSearchHelper = new WkRoomSearchHelper();
            $objSearchHelper->assignSearchPanelVariables();
            return $this->display(__FILE__, 'landingPageSearch.tpl');
        } elseif ('product' == Tools::getValue('controller')) {
            $objSearchHelper = new WkRoomSearchHelper();
            $objSearchHelper->assignSearchPanelVariables();
            return $this->display(__FILE__, 'roomTypePageSearch.tpl');
        }
    }

    // In the xs sceen the booking button on the landing page
    public function hookDisplayAfterHeaderHotelDesc()
    {
        return $this->display(__FILE__, 'landingPageXsBtn.tpl');
    }

    // search panel block on the category page on left block
    public function hookDisplayLeftColumn()
    {
        if ('category' == Tools::getValue('controller')) {
            $objSearchHelper = new WkRoomSearchHelper();
            $objSearchHelper->assignSearchPanelVariables();
            return $this->display(__FILE__, 'categoryPageSearch.tpl');
        }
    }

    public function hookDisplayHeader()
    {
        // handle room search submit from all pages search panels
        if (Tools::isSubmit('search_room_submit')) {
            $this->roomSearchProcess();
        }
    }

    public function roomSearchProcess()
    {
        $hotelCategoryId = Tools::getValue('hotel_cat_id');
        $checkIn = Tools::getValue('check_in_time');
        $checkOut = Tools::getValue('check_out_time');

        // change dates format to acceptable format
        $checkIn = date('Y-m-d', strtotime($checkIn));
        $checkOut = date('Y-m-d', strtotime($checkOut));

        $currentDate = date('Y-m-d');
        $maxOrderDate = Tools::getValue('max_order_date');
        $maxOrderDate = date('Y-m-d', strtotime($maxOrderDate));

        $objSearchHelper = new WkRoomSearchHelper();
        $this->context->controller->errors = array_merge(
            $this->context->controller->errors,
            $objSearchHelper->validateSearchFields()
        );
        // id there is no validation error the proceed to redirect on search result page
        if (!count($this->context->controller->errors)) {
            $urlData = array (
                'date_from' => $checkIn,
                'date_to' => $checkOut,
            );
            if (Configuration::get('PS_REWRITING_SETTINGS')) {
                $redirectLink = $this->context->link->getCategoryLink(
                    new Category($hotelCategoryId, $this->context->language->id),
                    null,
                    $this->context->language->id
                ).'?'.http_build_query($urlData);
            } else {
                $redirectLink = $this->context->link->getCategoryLink(
                    new Category($hotelCategoryId, $this->context->language->id),
                    null,
                    $this->context->language->id
                ).'&'.http_build_query($urlData);
            }
            Tools::redirect($redirectLink);
        }
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerModuleHooks()
        ) {
            return false;
        }
        return true;
    }

    private function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'displayHeader',
                'displayLeftColumn',
                'actionFrontControllerSetMedia',
                'displayAfterHookTop',
                'displayAfterHeaderHotelDesc',
                'displayAddModuleSettingLink',
            )
        );
    }
}
