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
        $this->version = '1.0.8';
        $this->context = Context::getContext();

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Layered filters and sorting block');
        $this->description = $this->l('Property filter and sorting block');
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


        $features = array();
        $featuresList = Feature::getFeatures($this->context->language->id);
        foreach ($featuresList as $feature) {
            $features[] = array('id_group' => (int) $feature['id_feature'], 'name' => $feature['name']);
        }


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
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Features filter'),
                    'name' => 'SHOW_FEATURES_FILTER',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'features_on',
                            'value' => 1,
                        ),
                        array(
                            'id' => 'features_off',
                            'value' => 0,
                        ),
                    ),
                    'hint' => $this->l('Enable to display Room Features filter.'),
                ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Features'),
                    'name' => 'FEATURES_FILTER_BOX',
                    'form_group_class' => 'features_filter_group',
                    'groups_id_title' => $this->l('ID'),
                    'groups_title' => $this->l('Feature name'),
                    'values' => $features,
                    'hint' => $this->l('Mark the features that will be displayed in the Features filter.'),
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

        $fieldsValue = $this->getConfigFieldsValues();
        $selectedFeatures = $this->getSelectedFeatureIds();
        foreach ($features as $feature) {
            $fieldsValue['FEATURES_FILTER_BOX_'.$feature['id_group']] = in_array($feature['id_group'], $selectedFeatures);
        }

        $helper->tpl_vars = array(
            'fields_value' => $fieldsValue,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        // show the features list only when the Features filter is enabled
        $script = '<script type="text/javascript">
            $(document).ready(function() {
                function toggleFeaturesGroup() {
                    $(".features_filter_group").toggle($("input[name=\'SHOW_FEATURES_FILTER\']:checked").val() == 1);
                }
                $("input[name=\'SHOW_FEATURES_FILTER\']").on("change", toggleFeaturesGroup);
                toggleFeaturesGroup();
            });
        </script>';

        return $helper->generateForm(array($fields_form)).$script;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('btnConfigSubmit')) {
            if (Tools::getValue('SHOW_FEATURES_FILTER') && !array_filter((array) Tools::getValue('FEATURES_FILTER_BOX', array()))) {
                $this->html .= $this->displayError($this->l('Please select at least one feature to show in the Features filter.'));

                return;
            }
            Configuration::updateValue(
                'SHOW_AMENITIES_FILTER',
                Tools::getValue('SHOW_AMENITIES_FILTER')
            );
            Configuration::updateValue('SHOW_PRICE_FILTER', Tools::getValue('SHOW_PRICE_FILTER'));
            Configuration::updateValue('SHOW_FEATURES_FILTER', (int) Tools::getValue('SHOW_FEATURES_FILTER'));
            if(Tools::getValue('SHOW_FEATURES_FILTER')){
                Configuration::updateValue(
                    'FEATURES_FILTER_IDS',
                    implode(',', array_map('intval', (array) Tools::getValue('FEATURES_FILTER_BOX', array())))
                );
            }else{
                Configuration::updateValue('FEATURES_FILTER_IDS', '');
            }
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
            || !Configuration::updateValue('SHOW_FEATURES_FILTER', 0)
            || !Configuration::updateValue('FEATURES_FILTER_IDS', '')
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
                    $all_amenities = HotelAmenities::getAmenities($id_lang);

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
                    // features with their values for the features tree filter
                    $room_type_features = array();
                    if ($config['SHOW_FEATURES_FILTER'] && Feature::isFeatureActive()) {
                        $selectedFeatures = $this->getSelectedFeatureIds();
                        foreach (Feature::getFeatures($id_lang) as $feature) {
                            if (!in_array((int) $feature['id_feature'], $selectedFeatures)) {
                                continue;
                            }
                            if ($values = FeatureValue::getFeatureValuesWithLang($id_lang, (int) $feature['id_feature'])) {
                                $feature['values'] = $values;
                                $room_type_features[] = $feature;
                            }
                        }
                    }

                    $num_days = HotelHelper::getNumberOfDays($date_from, $date_to);

                    $warning_num = Configuration::get('WK_ROOM_LEFT_WARNING_NUMBER');
                    $this->context->smarty->assign(array(
                        'warning_num' => $warning_num,
                        'all_amenities' => $all_amenities,
                        'room_type_features' => $room_type_features,
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
            'SHOW_FEATURES_FILTER' => Configuration::get('SHOW_FEATURES_FILTER'),
        );
    }

    /**
     * Get the ids of the features selected in the configuration for the Features filter.
     *
     * @return int[]
     */
    public function getSelectedFeatureIds()
    {
        $ids = (string) Configuration::get('FEATURES_FILTER_IDS');

        return $ids === '' ? array() : array_map('intval', explode(',', $ids));
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !Configuration::deleteByName('SHOW_AMENITIES_FILTER')
            || !Configuration::deleteByName('SHOW_PRICE_FILTER')
            || !Configuration::deleteByName('SHOW_FEATURES_FILTER')
            || !Configuration::deleteByName('FEATURES_FILTER_IDS')
        ) {
            return false;
        }

        return true;
    }
}
