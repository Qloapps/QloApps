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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'define.php';

class wkhotelfilterblock extends Module
{
    public function __construct()
    {
        $this->name = 'wkhotelfilterblock';
        $this->author = 'Webkul';
        $this->tab = 'front_office_features';
        $this->version = '1.0.7';
        $this->context = Context::getContext();

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Layered filters and sorting block');
        $this->description = $this->l('Hotel filter and sorting block');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function getContent()
    {
        $this->html = '';
        if (Tools::isSubmit('btnConfigSubmit')) {
            $this->postProcess();
        } else {
            $this->html .= '<br />';
        }

        $this->html .= $this->renderForm();

        return $this->html;
    }

    public function renderForm()
    {
        $fields_form = array();
        $fields_form['form'] = array(
            'legend' => array(
                'icon' => 'icon-cog',
                'title' => $this->l('Search Results Page Filters'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Amenities filter'),
                    'name' => 'SHOW_AMENITIES_FILTER',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                        ),
                    ),
                    'hint' => $this->l('Enable to display Amenities filter.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Price filter'),
                    'name' => 'SHOW_PRICE_FILTER',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                        ),
                    ),
                    'hint' => $this->l('Enable to display Price filter.'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'submit_conf_filter',
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnConfigSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).
        '&configure='.$this->name.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function postProcess()
    {
        if (Tools::isSubmit('btnConfigSubmit')) {
            Configuration::updateValue(
                'SHOW_AMENITIES_FILTER',
                Tools::getValue('SHOW_AMENITIES_FILTER')
            );
            Configuration::updateValue('SHOW_PRICE_FILTER', Tools::getValue('SHOW_PRICE_FILTER'));

            // redirect after saving the configuration
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.
                '&module_name='.$this->name.'&conf=4'
            );
        }
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('header')
            || !$this->registerHook('addOtherModuleSetting')
            || !$this->registerHook('displayLeftColumn')
            || !Configuration::updateValue('SHOW_AMENITIES_FILTER', 1)
            || !Configuration::updateValue('SHOW_PRICE_FILTER', 1)
        ) {
            return false;
        }

        return true;
    }

    public function hookHeader()
    {
        $this->context->controller->addJQueryUI('ui.slider');
    }

    public function hookDisplayLeftColumn()
    {
        if ($this->context->controller->php_self == 'category') {
            $htl_id_category = Tools::getValue('id_category');
            if (Validate::isLoadedObject($objCategory = new Category((int) $htl_id_category))
                && ($id_hotel = HotelBranchInformation::getHotelIdByIdCategory($htl_id_category))
            ) {
                if ($objCategory->hasParent(Configuration::get('PS_LOCATIONS_CATEGORY'))) {
                    Media::addJsDef(array('noRoomAvailTxt' => $this->l('No room available', false, true)));

                    $this->context->controller->addJS($this->_path.'/views/js/wkhotelfilterblock.js');
                    $this->context->controller->addCSS($this->_path.'/views/css/wkhotelfilterblock.css');
                    $id_lang = $this->context->language->id;
                    $all_feat = FeatureCore::getFeatures($id_lang);

                    $max_adult = HotelRoomType::getMaxAdults($id_hotel);
                    $max_child = HotelRoomType::getMaxChild($id_hotel);

                    $urlData = array ();
                    if (!($date_from = Tools::getValue('date_from'))) {
                        $date_from = date('Y-m-d H:i:s');
                        $date_to = date('Y-m-d H:i:s', strtotime($date_from) + 86400);
                    } else {
                        $urlData['date_from'] = $date_from;
                    }

                    if (!($date_to = Tools::getValue('date_to'))) {
                        $date_to = date('Y-m-d H:i:s', strtotime($date_from) + 86400);
                    } else {
                        $urlData['date_to'] = $date_to;
                    }

                    if ($occupancy = Tools::getValue('occupancy')) {
                        $urlData['occupancy'] = $occupancy;
                    }

                    $obj_rm_type = new HotelRoomType();
                    $room_types = $obj_rm_type->getIdProductByHotelId($id_hotel, 0, 1, 1);
                    $occupancy = Tools::getValue('occupancy');

                    $prod_price = array();
                    if ($room_types) {
                        foreach ($room_types as $key => $value) {
                            $prod_price[] = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay($value['id_product'], $date_from, $date_to, HotelBookingDetail::useTax(), 0, 0, 0, 0, 1, 1, $occupancy);
                        }
                    }

                    if (Configuration::get('PS_REWRITING_SETTINGS')) {
                        $categoryUrl = $this->context->link->getCategoryLink(
                            new Category($htl_id_category, $this->context->language->id),
                            null,
                            $this->context->language->id
                        ).'?'.http_build_query($urlData);
                    } else {
                        $categoryUrl = $this->context->link->getCategoryLink(
                            new Category($htl_id_category, $this->context->language->id),
                            null,
                            $this->context->language->id
                        ).'&'.http_build_query($urlData);
                    }
                    $currency = $this->context->currency;

                    $config = $this->getConfigFieldsValues();

                    $num_days = HotelHelper::getNumberOfDays($date_from, $date_to);

                    $warning_num = Configuration::get('WK_ROOM_LEFT_WARNING_NUMBER');
                    $this->context->smarty->assign(array(
                        'warning_num' => $warning_num,
                        'all_feat' => $all_feat,
                        'max_adult' => $max_adult,
                        'max_child' => $max_child,
                        'cat_link' => $categoryUrl,
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
        }
    }

    public function getConfigFieldsValues()
    {
        return array(
            'SHOW_AMENITIES_FILTER' => Configuration::get('SHOW_AMENITIES_FILTER'),
            'SHOW_PRICE_FILTER' => Configuration::get('SHOW_PRICE_FILTER'),
        );
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !Configuration::deleteByName('SHOW_AMENITIES_FILTER')
            || !Configuration::deleteByName('SHOW_PRICE_FILTER')
        ) {
            return false;
        }

        return true;
    }
}
