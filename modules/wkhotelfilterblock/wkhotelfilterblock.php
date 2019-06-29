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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'define.php';

class wkhotelfilterblock extends Module
{
    public function __construct()
    {
        $this->name = 'wkhotelfilterblock';
        $this->author = 'webkul';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->context = Context::getContext();

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('layered filters and sorting block');
        $this->description = $this->l('Hotel filter and sorting block');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('header')
            || !$this->registerHook('addOtherModuleSetting')
            || !$this->registerHook('displayLeftColumn')) {
            return false;
        }

        //set default config variable
        Configuration::updateValue('SHOW_RATTING_FILTER', 1);
        Configuration::updateValue('SHOW_AMENITIES_FILTER', 1);
        Configuration::updateValue('SHOW_PRICE_FILTER', 1);

        return true;
    }

    public function hookAddOtherModuleSetting($params)
    {
        $params['fields_options']['categoryfiltersetting'] = array(
            'title' => $this->l('Category Filter Configuration'),
            'fields' => array(
                'SHOW_RATTING_FILTER' => array(
                    'title' => $this->l('Show Guest Rating Filter'),
                    'hint' => $this->l('If yes, it will display Guest Rating Filter.'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                ),
                'SHOW_AMENITIES_FILTER' => array(
                    'title' => $this->l('Show Amenities Filter'),
                    'hint' => $this->l('If yes, it will display Amenities Filter.'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                ),
                'SHOW_PRICE_FILTER' => array(
                    'title' => $this->l('Show Price Filter'),
                    'hint' => $this->l('If yes, it will display Price Filter.'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                ),
            ),
            'submit' => array('title' => $this->l('Save')),
        );
    }

    public function hookHeader()
    {
        $this->context->controller->addJQueryUI('ui.slider');
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall() || !$this->deleteConfigKeys()) {
            return false;
        }

        return true;
    }

    public function hookDisplayLeftColumn()
    {
        if ($this->context->controller->php_self == 'category') {
            Media::addJsDef(array('noRoomAvailTxt' => $this->l('No room available')));

            $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/wkhotelfilterblock.js');
            $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkhotelfilterblock.css');
            $id_lang = $this->context->language->id;
            $all_feat = FeatureCore::getFeatures($id_lang);

            $htl_id_category = Tools::getValue('id_category');
            $id_hotel = HotelBranchInformation::getHotelIdByIdCategory($htl_id_category);

            $max_adult = HotelRoomType::getMaxAdults($id_hotel);
            $max_child = HotelRoomType::getMaxChild($id_hotel);

            $category = new Category($htl_id_category);

            if (!($date_from = Tools::getValue('date_from'))) {
                $date_from = date('Y-m-d');
                $date_to = date('Y-m-d', strtotime($date_from) + 86400);
            }
            if (!($date_to = Tools::getValue('date_to'))) {
                $date_to = date('Y-m-d', strtotime($date_from) + 86400);
            }

            $obj_rm_type = new HotelRoomType();
            $room_types = $obj_rm_type->getIdProductByHotelId($id_hotel, 0, 1, 1);

            $prod_price = array();
            if ($room_types) {
                foreach ($room_types as $key => $value) {
                    $prod_price[] = Product::getPriceStatic($value['id_product'], HotelBookingDetail::useTax());
                }
            }
            if (Configuration::get('PS_REWRITING_SETTINGS')) {
                $cat_link = $this->context->link->getCategoryLink($category, is_array($category->link_rewrite) ? $category->link_rewrite[$id_lang] : $this->link_rewrite, $id_lang).'?date_from='.$date_from.'&date_to='.$date_to;
            } else {
                $cat_link = $this->context->link->getCategoryLink($category, is_array($category->link_rewrite) ? $category->link_rewrite[$id_lang] : $this->link_rewrite, $id_lang).'&date_from='.$date_from.'&date_to='.$date_to;
            }
            $currency = $this->context->currency;

            $config = $this->getConfigFieldsValues();

            $obj_booking_detail = new HotelBookingDetail();
            $num_days = $obj_booking_detail->getNumberOfDays($date_from, $date_to);

            $warning_num = Configuration::get('WK_ROOM_LEFT_WARNING_NUMBER');
            $product_comment_installed = Module::isInstalled('productcomments');
            $ratting_img = _MODULE_DIR_.$this->name.'/views/img/stars-sprite-image.png';
            $this->context->smarty->assign(array(
                'product_comment_installed' => $product_comment_installed,
                'warning_num' => $warning_num,
                'all_feat' => $all_feat,
                'max_adult' => $max_adult,
                'max_child' => $max_child,
                'cat_link' => $cat_link,
                'ratting_img' => $ratting_img,
                'currency' => $currency,
                'date_from' => $date_from,
                'date_to' => $date_to,
                'num_days' => $num_days,
                'config' => $config,
                'min_price' => $prod_price ? min($prod_price) : 0,
                'max_price' => $prod_price ? max($prod_price) : 0,
            ));

            return $this->display(__FILE__, 'htlfilterblock.tpl');
        }
    }

    public function getConfigFieldsValues()
    {
        $config_vars = array(
            'SHOW_RATTING_FILTER' => Tools::getValue('SHOW_RATTING_FILTER', Configuration::get('SHOW_RATTING_FILTER')),
            'SHOW_AMENITIES_FILTER' => Tools::getValue('SHOW_AMENITIES_FILTER', Configuration::get('SHOW_AMENITIES_FILTER')),
            'SHOW_PRICE_FILTER' => Tools::getValue('SHOW_PRICE_FILTER', Configuration::get('SHOW_PRICE_FILTER')),
        );

        return $config_vars;
    }

    public function deleteConfigKeys()
    {
        $var = array('SHOW_RATTING_FILTER',
                    'SHOW_AMENITIES_FILTER',
                    'SHOW_PRICE_FILTER', );

        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }
}
