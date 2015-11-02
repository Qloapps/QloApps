<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property Carrier $object
 */
class AdminCarrierWizardControllerCore extends AdminController
{
    protected $wizard_access;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        $this->table = 'carrier';
        $this->identifier = 'id_carrier';
        $this->className = 'Carrier';
        $this->lang = false;
        $this->deleted = true;
        $this->step_number = 0;
        $this->type_context = Shop::getContext();
        $this->old_context = Context::getContext();
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->context = Context::getContext();

        $this->fieldImageSettings = array(
            'name' => 'logo',
            'dir' => 's'
        );

        parent::__construct();

        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminCarriers'));
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('smartWizard');
        $this->addJqueryPlugin('typewatch');
        $this->addJs(_PS_JS_DIR_.'admin/carrier_wizard.js');
    }

    public function initWizard()
    {
        $this->wizard_steps = array(
            'name' => 'carrier_wizard',
            'steps' => array(
                array(
                    'title' => $this->l('General settings'),
                ),
                array(
                    'title' => $this->l('Shipping locations and costs'),
                ),
                array(
                    'title' => $this->l('Size, weight, and group access'),
                ),
                array(
                    'title' => $this->l('Summary'),
                ),

            ));

        if (Shop::isFeatureActive()) {
            $multistore_step = array(
                array(
                    'title' => $this->l('MultiStore'),
                )
            );
            array_splice($this->wizard_steps['steps'], 1, 0, $multistore_step);
        }
    }

    public function renderView()
    {
        $this->initWizard();

        if (Tools::getValue('id_carrier') && $this->tabAccess['edit']) {
            $carrier = $this->loadObject();
        } elseif ($this->tabAccess['add']) {
            $carrier = new Carrier();
        }

        if ((!$this->tabAccess['edit'] && Tools::getValue('id_carrier')) ||  (!$this->tabAccess['add'] && !Tools::getValue('id_carrier'))) {
            $this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
            return ;
        }

        $currency = $this->getActualCurrency();

        $this->tpl_view_vars = array(
            'currency_sign' => $currency->sign,
            'PS_WEIGHT_UNIT' => Configuration::get('PS_WEIGHT_UNIT'),
            'enableAllSteps' => Validate::isLoadedObject($carrier),
            'wizard_steps' => $this->wizard_steps,
            'validate_url' => $this->context->link->getAdminLink('AdminCarrierWizard'),
            'carrierlist_url' => $this->context->link->getAdminLink('AdminCarriers').'&conf='.((int)Validate::isLoadedObject($carrier) ? 4 : 3),
            'multistore_enable' => Shop::isFeatureActive(),
            'wizard_contents' => array(
                'contents' => array(
                    0 => $this->renderStepOne($carrier),
                    1 => $this->renderStepThree($carrier),
                    2 => $this->renderStepFour($carrier),
                    3 => $this->renderStepFive($carrier),
                )),
            'labels' => array('next' => $this->l('Next'), 'previous' => $this->l('Previous'), 'finish' => $this->l('Finish'))
        );


        if (Shop::isFeatureActive()) {
            array_splice($this->tpl_view_vars['wizard_contents']['contents'], 1, 0, array(0 => $this->renderStepTwo($carrier)));
        }

        $this->context->smarty->assign(array(
                'carrier_logo' => (Validate::isLoadedObject($carrier) && file_exists(_PS_SHIP_IMG_DIR_.$carrier->id.'.jpg') ? _THEME_SHIP_DIR_.$carrier->id.'.jpg' : false),
            ));

        $this->context->smarty->assign(array(
            'logo_content' => $this->createTemplate('logo.tpl')->fetch()
        ));

        $this->addjQueryPlugin(array('ajaxfileupload'));

        return parent::renderView();
    }

    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        if (Tools::getValue('id_carrier')) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        parent::initBreadcrumbs((int)Tab::getIdFromClassName('AdminCarriers'));

        $this->display = 'view';
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        $this->page_header_toolbar_btn['cancel'] = array(
            'href' => $this->context->link->getAdminLink('AdminCarriers'),
            'desc' => $this->l('Cancel', null, null, false)
        );
    }

    public function renderStepOne($carrier)
    {
        $this->fields_form = array(
            'form' => array(
                'id_form' => 'step_carrier_general',
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Carrier name'),
                        'name' => 'name',
                        'required' => true,
                        'hint' => array(
                            sprintf($this->l('Allowed characters: letters, spaces and "%s".'), '().-'),
                            $this->l('The carrier\'s name will be displayed during checkout.'),
                            $this->l('For in-store pickup, enter 0 to replace the carrier name with your shop name.')
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Transit time'),
                        'name' => 'delay',
                        'lang' => true,
                        'required' => true,
                        'maxlength' => 128,
                        'hint' => $this->l('The estimated delivery time will be displayed during checkout.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Speed grade'),
                        'name' => 'grade',
                        'required' => false,
                        'size' => 1,
                        'hint' => $this->l('Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.')
                    ),
                    array(
                        'type' => 'logo',
                        'label' => $this->l('Logo'),
                        'name' => 'logo'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Tracking URL'),
                        'name' => 'url',
                        'hint' => $this->l('Delivery tracking URL: Type \'@\' where the tracking number should appear. It will be automatically replaced by the tracking number.'),
                        'desc' => $this->l('For example: \'http://exampl.com/track.php?num=@\' with \'@\' where the tracking number should appear.')
                    ),
                )),
        );

        $tpl_vars = array('max_image_size' => (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE') / 1024 / 1024);
        $fields_value = $this->getStepOneFieldsValues($carrier);
        return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value, $tpl_vars);
    }

    public function renderStepTwo($carrier)
    {
        $this->fields_form = array(
            'form' => array(
                'id_form' => 'step_carrier_shops',
                'force' => true,
                'input' => array(
                    array(
                        'type' => 'shop',
                        'label' => $this->l('Shop association'),
                        'name' => 'checkBoxShopAsso',
                    ),
                ))
        );
        $fields_value = $this->getStepTwoFieldsValues($carrier);

        return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);
    }

    public function renderStepThree($carrier)
    {
        $this->fields_form = array(
            'form' => array(
                'id_form' => 'step_carrier_ranges',
                'input' => array(
                    'shipping_handling' => array(
                        'type' => 'switch',
                        'label' => $this->l('Add handling costs'),
                        'name' => 'shipping_handling',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'shipping_handling_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'shipping_handling_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('Include the handling costs (as set in Shipping > Preferences) in the final carrier price.')
                    ),
                    'is_free' => array(
                        'type' => 'switch',
                        'label' => $this->l('Free shipping'),
                        'name' => 'is_free',
                        'required' => false,
                        'class' => 't',
                        'values' => array(
                            array(
                                'id' => 'is_free_on',
                                'value' => 1,
                                'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" />'
                            ),
                            array(
                                'id' => 'is_free_off',
                                'value' => 0,
                                'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />'
                            )
                        ),
                    ),
                    'shipping_method' => array(
                        'type' => 'radio',
                        'label' => $this->l('Billing'),
                        'name' => 'shipping_method',
                        'required' => false,
                        'class' => 't',
                        'br' => true,
                        'values' => array(
                            array(
                                'id' => 'billing_price',
                                'value' => Carrier::SHIPPING_METHOD_PRICE,
                                'label' => $this->l('According to total price.')
                            ),
                            array(
                                'id' => 'billing_weight',
                                'value' => Carrier::SHIPPING_METHOD_WEIGHT,
                                'label' => $this->l('According to total weight.')
                            )
                        )
                    ),
                    'id_tax_rules_group' => array(
                        'type' => 'select',
                        'label' => $this->l('Tax'),
                        'name' => 'id_tax_rules_group',
                        'options' => array(
                            'query' => TaxRulesGroup::getTaxRulesGroups(true),
                            'id' => 'id_tax_rules_group',
                            'name' => 'name',
                            'default' => array(
                                'label' => $this->l('No tax'),
                                'value' => 0
                            )
                        )
                    ),
                    'range_behavior' => array(
                        'type' => 'select',
                        'label' => $this->l('Out-of-range behavior'),
                        'name' => 'range_behavior',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 0,
                                    'name' => $this->l('Apply the cost of the highest defined range')
                                ),
                                array(
                                    'id' => 1,
                                    'name' => $this->l('Disable carrier')
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                        'hint' => $this->l('Out-of-range behavior occurs when no defined range matches the customer\'s cart (e.g. when the weight of the cart is greater than the highest weight limit defined by the weight ranges).')
                    ),
                    'zones' => array(
                        'type' => 'zone',
                        'name' => 'zones'
                    )
                ),

            ));

        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            unset($this->fields_form['form']['input']['id_tax_rules_group']);
        }

        $tpl_vars = array();
        $tpl_vars['PS_WEIGHT_UNIT'] = Configuration::get('PS_WEIGHT_UNIT');

        $currency = $this->getActualCurrency();

        $tpl_vars['currency_sign'] = $currency->sign;

        $fields_value = $this->getStepThreeFieldsValues($carrier);

        $this->getTplRangesVarsAndValues($carrier, $tpl_vars, $fields_value);
        return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value, $tpl_vars);
    }

    /**
     * @param Carrier $carrier
     *
     * @return string
     */
    public function renderStepFour($carrier)
    {
        $this->fields_form = array(
            'form' => array(
                'id_form' => 'step_carrier_conf',
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => sprintf($this->l('Maximum package width (%s)'), Configuration::get('PS_DIMENSION_UNIT')),
                        'name' => 'max_width',
                        'required' => false,
                        'hint' => $this->l('Maximum width managed by this carrier. Set the value to "0", or leave this field blank to ignore.').' '.$this->l('The value must be an integer.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => sprintf($this->l('Maximum package height (%s)'), Configuration::get('PS_DIMENSION_UNIT')),
                        'name' => 'max_height',
                        'required' => false,
                        'hint' => $this->l('Maximum height managed by this carrier. Set the value to "0", or leave this field blank to ignore.').' '.$this->l('The value must be an integer.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => sprintf($this->l('Maximum package depth (%s)'), Configuration::get('PS_DIMENSION_UNIT')),
                        'name' => 'max_depth',
                        'required' => false,
                        'hint' => $this->l('Maximum depth managed by this carrier. Set the value to "0", or leave this field blank to ignore.').' '.$this->l('The value must be an integer.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => sprintf($this->l('Maximum package weight (%s)'), Configuration::get('PS_WEIGHT_UNIT')),
                        'name' => 'max_weight',
                        'required' => false,
                        'hint' => $this->l('Maximum weight managed by this carrier. Set the value to "0", or leave this field blank to ignore.')
                    ),
                    array(
                        'type' => 'group',
                        'label' => $this->l('Group access'),
                        'name' => 'groupBox',
                        'values' => Group::getGroups(Context::getContext()->language->id),
                        'hint' => $this->l('Mark the groups that are allowed access to this carrier.')
                    )
                )
            ));

        $fields_value = $this->getStepFourFieldsValues($carrier);

        // Added values of object Group
        $carrier_groups = $carrier->getGroups();
        $carrier_groups_ids = array();
        if (is_array($carrier_groups)) {
            foreach ($carrier_groups as $carrier_group) {
                $carrier_groups_ids[] = $carrier_group['id_group'];
            }
        }

        $groups = Group::getGroups($this->context->language->id);

        foreach ($groups as $group) {
            $fields_value['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], (in_array($group['id_group'], $carrier_groups_ids) || empty($carrier_groups_ids) && !$carrier->id));
        }

        return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);
    }

    public function renderStepFive($carrier)
    {
        $this->fields_form = array(
            'form' => array(
                'id_form' => 'step_carrier_summary',
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enabled'),
                        'name' => 'active',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0
                            )
                        ),
                        'hint' => $this->l('Enable the carrier in the front office.')
                    )
                )
            ));

        $template = $this->createTemplate('controllers/carrier_wizard/summary.tpl');

        $fields_value = $this->getStepFiveFieldsValues($carrier);

        $active_form = $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);

        $active_form =  str_replace(array('<fieldset id="fieldset_form">', '</fieldset>'), '', $active_form);

        $template->assign('active_form', $active_form);

        return $template->fetch('controllers/carrier_wizard/summary.tpl');
    }

    /**
     * @param Carrier $carrier
     * @param array   $tpl_vars
     * @param array   $fields_value
     */
    protected function getTplRangesVarsAndValues($carrier, &$tpl_vars, &$fields_value)
    {
        $tpl_vars['zones'] = Zone::getZones(false);
        $carrier_zones = $carrier->getZones();
        $carrier_zones_ids = array();
        if (is_array($carrier_zones)) {
            foreach ($carrier_zones as $carrier_zone) {
                $carrier_zones_ids[] = $carrier_zone['id_zone'];
            }
        }

        $range_table = $carrier->getRangeTable();
        $shipping_method = $carrier->getShippingMethod();

        $zones = Zone::getZones(false);
        foreach ($zones as $zone) {
            $fields_value['zones'][$zone['id_zone']] = Tools::getValue('zone_'.$zone['id_zone'], (in_array($zone['id_zone'], $carrier_zones_ids)));
        }

        if ($shipping_method == Carrier::SHIPPING_METHOD_FREE) {
            $range_obj = $carrier->getRangeObject($carrier->shipping_method);
            $price_by_range = array();
        } else {
            $range_obj = $carrier->getRangeObject();
            $price_by_range = Carrier::getDeliveryPriceByRanges($range_table, (int)$carrier->id);
        }

        foreach ($price_by_range as $price) {
            $tpl_vars['price_by_range'][$price['id_'.$range_table]][$price['id_zone']] = $price['price'];
        }

        $tmp_range = $range_obj->getRanges((int)$carrier->id);
        $tpl_vars['ranges'] = array();
        if ($shipping_method != Carrier::SHIPPING_METHOD_FREE) {
            foreach ($tmp_range as $id => $range) {
                $tpl_vars['ranges'][$range['id_'.$range_table]] = $range;
                $tpl_vars['ranges'][$range['id_'.$range_table]]['id_range'] = $range['id_'.$range_table];
            }
        }

        // init blank range
        if (!count($tpl_vars['ranges'])) {
            $tpl_vars['ranges'][] = array('id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0);
        }
    }

    public function renderGenericForm($fields_form, $fields_value, $tpl_vars = array())
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->tpl_vars = array_merge(array(
                'fields_value' => $fields_value,
                'languages' => $this->getLanguages(),
                'id_language' => $this->context->language->id
            ), $tpl_vars);
        $helper->override_folder = 'carrier_wizard/';

        return $helper->generateForm($fields_form);
    }

    public function getStepOneFieldsValues($carrier)
    {
        return array(
            'id_carrier' => $this->getFieldValue($carrier, 'id_carrier'),
            'name' => $this->getFieldValue($carrier, 'name'),
            'delay' => $this->getFieldValue($carrier, 'delay'),
            'grade' => $this->getFieldValue($carrier, 'grade'),
            'url' => $this->getFieldValue($carrier, 'url'),
        );
    }

    public function getStepTwoFieldsValues($carrier)
    {
        return array('shop' => $this->getFieldValue($carrier, 'shop'));
    }

    public function getStepThreeFieldsValues($carrier)
    {
        $id_tax_rules_group = (is_object($this->object) && !$this->object->id) ? Carrier::getIdTaxRulesGroupMostUsed() : $this->getFieldValue($carrier, 'id_tax_rules_group');

        $shipping_handling = (is_object($this->object) && !$this->object->id) ? 0 : $this->getFieldValue($carrier, 'shipping_handling');

        return array(
            'is_free' => $this->getFieldValue($carrier, 'is_free'),
            'id_tax_rules_group' => (int)$id_tax_rules_group,
            'shipping_handling' => $shipping_handling,
            'shipping_method' => $this->getFieldValue($carrier, 'shipping_method'),
            'range_behavior' =>  $this->getFieldValue($carrier, 'range_behavior'),
            'zones' =>  $this->getFieldValue($carrier, 'zones'),
        );
    }

    public function getStepFourFieldsValues($carrier)
    {
        return array(
            'range_behavior' => $this->getFieldValue($carrier, 'range_behavior'),
            'max_height' => $this->getFieldValue($carrier, 'max_height'),
            'max_width' => $this->getFieldValue($carrier, 'max_width'),
            'max_depth' => $this->getFieldValue($carrier, 'max_depth'),
            'max_weight' => $this->getFieldValue($carrier, 'max_weight'),
            'group' => $this->getFieldValue($carrier, 'group'),
        );
    }

    public function getStepFiveFieldsValues($carrier)
    {
        return array('active' => $this->getFieldValue($carrier, 'active'));
    }

    public function ajaxProcessChangeRanges()
    {
        if ((Validate::isLoadedObject($this->object) && !$this->tabAccess['edit']) || !$this->tabAccess['add']) {
            $this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
            return;
        }
        if ((!(int)$shipping_method = Tools::getValue('shipping_method')) || !in_array($shipping_method, array(Carrier::SHIPPING_METHOD_PRICE, Carrier::SHIPPING_METHOD_WEIGHT))) {
            return ;
        }

        $carrier = $this->loadObject(true);
        $carrier->shipping_method = $shipping_method;

        $tpl_vars = array();
        $fields_value = $this->getStepThreeFieldsValues($carrier);
        $this->getTplRangesVarsAndValues($carrier, $tpl_vars, $fields_value);
        $template = $this->createTemplate('controllers/carrier_wizard/helpers/form/form_ranges.tpl');
        $template->assign($tpl_vars);
        $template->assign('change_ranges', 1);

        $template->assign('fields_value', $fields_value);
        $template->assign('input', array('type' => 'zone', 'name' => 'zones' ));

        $currency = $this->getActualCurrency();

        $template->assign('currency_sign', $currency->sign);
        $template->assign('PS_WEIGHT_UNIT', Configuration::get('PS_WEIGHT_UNIT'));

        die($template->fetch());
    }

    protected function validateForm($die = true)
    {
        $step_number = (int)Tools::getValue('step_number');
        $return = array('has_error' => false);

        if (!$this->tabAccess['edit']) {
            $this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
        } else {
            if (Shop::isFeatureActive() && $step_number == 2) {
                if (!Tools::getValue('checkBoxShopAsso_carrier')) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->l('You must choose at least one shop or group shop.');
                }
            } else {
                $this->validateRules();
            }
        }

        if (count($this->errors)) {
            $return['has_error'] = true;
            $return['errors'] = $this->errors;
        }
        if (count($this->errors) || $die) {
            die(Tools::jsonEncode($return));
        }
    }


    public function ajaxProcessValidateStep()
    {
        $this->validateForm(true);
    }

    public function processRanges($id_carrier)
    {
        if (!$this->tabAccess['edit'] || !$this->tabAccess['add']) {
            $this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
            return;
        }

        $carrier = new Carrier((int)$id_carrier);
        if (!Validate::isLoadedObject($carrier)) {
            return false;
        }

        $range_inf = Tools::getValue('range_inf');
        $range_sup = Tools::getValue('range_sup');
        $range_type = Tools::getValue('shipping_method');

        $fees = Tools::getValue('fees');

        $carrier->deleteDeliveryPrice($carrier->getRangeTable());
        if ($range_type != Carrier::SHIPPING_METHOD_FREE) {
            foreach ($range_inf as $key => $delimiter1) {
                if (!isset($range_sup[$key])) {
                    continue;
                }
                $add_range = true;
                if ($range_type == Carrier::SHIPPING_METHOD_WEIGHT) {
                    if (!RangeWeight::rangeExist((int)$carrier->id, (float)$delimiter1, (float)$range_sup[$key])) {
                        $range = new RangeWeight();
                    } else {
                        $range = new RangeWeight((int)$key);
                        $add_range = false;
                    }
                }

                if ($range_type == Carrier::SHIPPING_METHOD_PRICE) {
                    if (!RangePrice::rangeExist((int)$carrier->id, (float)$delimiter1, (float)$range_sup[$key])) {
                        $range = new RangePrice();
                    } else {
                        $range = new RangePrice((int)$key);
                        $add_range = false;
                    }
                }
                if ($add_range) {
                    $range->id_carrier = (int)$carrier->id;
                    $range->delimiter1 = (float)$delimiter1;
                    $range->delimiter2 = (float)$range_sup[$key];
                    $range->save();
                }

                if (!Validate::isLoadedObject($range)) {
                    return false;
                }
                $price_list = array();
                if (is_array($fees) && count($fees)) {
                    foreach ($fees as $id_zone => $fee) {
                        $price_list[] = array(
                            'id_range_price' => ($range_type == Carrier::SHIPPING_METHOD_PRICE ? (int)$range->id : null),
                            'id_range_weight' => ($range_type == Carrier::SHIPPING_METHOD_WEIGHT ? (int)$range->id : null),
                            'id_carrier' => (int)$carrier->id,
                            'id_zone' => (int)$id_zone,
                            'price' => isset($fee[$key]) ? (float)$fee[$key] : 0,
                        );
                    }
                }

                if (count($price_list) && !$carrier->addDeliveryPrice($price_list, true)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function ajaxProcessUploadLogo()
    {
        if (!$this->tabAccess['edit']) {
            die('<return result="error" message="'.Tools::displayError('You do not have permission to use this wizard.').'" />');
        }

        $allowedExtensions = array('jpeg', 'gif', 'png', 'jpg');

        $logo = (isset($_FILES['carrier_logo_input']) ? $_FILES['carrier_logo_input'] : false);
        if ($logo && !empty($logo['tmp_name']) && $logo['tmp_name'] != 'none'
            && (!isset($logo['error']) || !$logo['error'])
            && preg_match('/\.(jpe?g|gif|png)$/', $logo['name'])
            && is_uploaded_file($logo['tmp_name'])
            && ImageManager::isRealImage($logo['tmp_name'], $logo['type'])) {
            $file = $logo['tmp_name'];
            do {
                $tmp_name = uniqid().'.jpg';
            } while (file_exists(_PS_TMP_IMG_DIR_.$tmp_name));
            if (!ImageManager::resize($file, _PS_TMP_IMG_DIR_.$tmp_name)) {
                die('<return result="error" message="Impossible to resize the image into '.Tools::safeOutput(_PS_TMP_IMG_DIR_).'" />');
            }
            @unlink($file);
            die('<return result="success" message="'.Tools::safeOutput(_PS_TMP_IMG_.$tmp_name).'" />');
        } else {
            die('<return result="error" message="Cannot upload file" />');
        }
    }

    public function ajaxProcessFinishStep()
    {
        $return = array('has_error' => false);
        if (!$this->tabAccess['edit']) {
            $return = array(
                'has_error' =>  true,
                $return['errors'][] = Tools::displayError('You do not have permission to use this wizard.')
            );
        } else {
            $this->validateForm(false);
            if ($id_carrier = Tools::getValue('id_carrier')) {
                $current_carrier = new Carrier((int)$id_carrier);

                // if update we duplicate current Carrier
                /** @var Carrier $new_carrier */
                $new_carrier = $current_carrier->duplicateObject();

                if (Validate::isLoadedObject($new_carrier)) {
                    // Set flag deteled to true for historization
                    $current_carrier->deleted = true;
                    $current_carrier->update();

                    // Fill the new carrier object
                    $this->copyFromPost($new_carrier, $this->table);
                    $new_carrier->position = $current_carrier->position;
                    $new_carrier->update();

                    $this->updateAssoShop((int)$new_carrier->id);
                    $this->duplicateLogo((int)$new_carrier->id, (int)$current_carrier->id);
                    $this->changeGroups((int)$new_carrier->id);

                    //Copy default carrier
                    if (Configuration::get('PS_CARRIER_DEFAULT') == $current_carrier->id) {
                        Configuration::updateValue('PS_CARRIER_DEFAULT', (int)$new_carrier->id);
                    }

                    // Call of hooks
                    Hook::exec('actionCarrierUpdate', array(
                            'id_carrier' => (int)$current_carrier->id,
                            'carrier' => $new_carrier
                        ));
                    $this->postImage($new_carrier->id);
                    $this->changeZones($new_carrier->id);
                    $new_carrier->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group'));
                    $carrier = $new_carrier;
                }
            } else {
                $carrier = new Carrier();
                $this->copyFromPost($carrier, $this->table);
                if (!$carrier->add()) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->l('An error occurred while saving this carrier.');
                }
            }

            if ($carrier->is_free) {
                //if carrier is free delete shipping cost
                $carrier->deleteDeliveryPrice('range_weight');
                $carrier->deleteDeliveryPrice('range_price');
            }

            if (Validate::isLoadedObject($carrier)) {
                if (!$this->changeGroups((int)$carrier->id)) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->l('An error occurred while saving carrier groups.');
                }

                if (!$this->changeZones((int)$carrier->id)) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->l('An error occurred while saving carrier zones.');
                }

                if (!$carrier->is_free) {
                    if (!$this->processRanges((int)$carrier->id)) {
                        $return['has_error'] = true;
                        $return['errors'][] = $this->l('An error occurred while saving carrier ranges.');
                    }
                }

                if (Shop::isFeatureActive() && !$this->updateAssoShop((int)$carrier->id)) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->l('An error occurred while saving associations of shops.');
                }

                if (!$carrier->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group'))) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->l('An error occurred while saving the tax rules group.');
                }

                if (Tools::getValue('logo')) {
                    if (Tools::getValue('logo') == 'null' && file_exists(_PS_SHIP_IMG_DIR_.$carrier->id.'.jpg')) {
                        unlink(_PS_SHIP_IMG_DIR_.$carrier->id.'.jpg');
                    } else {
                        $logo = basename(Tools::getValue('logo'));
                        if (!file_exists(_PS_TMP_IMG_DIR_.$logo) || !copy(_PS_TMP_IMG_DIR_.$logo, _PS_SHIP_IMG_DIR_.$carrier->id.'.jpg')) {
                            $return['has_error'] = true;
                            $return['errors'][] = $this->l('An error occurred while saving carrier logo.');
                        }
                    }
                }
                $return['id_carrier'] = $carrier->id;
            }
        }
        die(Tools::jsonEncode($return));
    }

    protected function changeGroups($id_carrier, $delete = true)
    {
        $carrier = new Carrier((int)$id_carrier);
        if (!Validate::isLoadedObject($carrier)) {
            return false;
        }

        return $carrier->setGroups(Tools::getValue('groupBox'));
    }

    public function changeZones($id)
    {
        $return = true;
        $carrier = new Carrier($id);
        if (!Validate::isLoadedObject($carrier)) {
            die(Tools::displayError('The object cannot be loaded.'));
        }
        $zones = Zone::getZones(false);
        foreach ($zones as $zone) {
            if (count($carrier->getZone($zone['id_zone']))) {
                if (!isset($_POST['zone_'.$zone['id_zone']]) || !$_POST['zone_'.$zone['id_zone']]) {
                    $return &= $carrier->deleteZone((int)$zone['id_zone']);
                }
            } elseif (isset($_POST['zone_'.$zone['id_zone']]) && $_POST['zone_'.$zone['id_zone']]) {
                $return &= $carrier->addZone((int)$zone['id_zone']);
            }
        }

        return $return;
    }

    public function getValidationRules()
    {
        $step_number = (int)Tools::getValue('step_number');
        if (!$step_number) {
            return;
        }

        if ($step_number == 4 && !Shop::isFeatureActive() || $step_number == 5 && Shop::isFeatureActive()) {
            return array('fields' => array());
        }

        $step_fields = array(
            1 => array('name', 'delay', 'grade', 'url'),
            2 => array('is_free', 'id_tax_rules_group', 'shipping_handling', 'shipping_method', 'range_behavior'),
            3 => array('range_behavior', 'max_height', 'max_width', 'max_depth', 'max_weight'),
            4 => array(),
        );
        if (Shop::isFeatureActive()) {
            $tmp = $step_fields;
            $step_fields = array_slice($tmp, 0, 1, true) + array(2 => array('shop'));
            $step_fields[3] = $tmp[2];
            $step_fields[4] = $tmp[3];
        }

        $definition = ObjectModel::getDefinition('Carrier');
        foreach ($definition['fields'] as $field => $def) {
            if (is_array($step_fields[$step_number]) && !in_array($field, $step_fields[$step_number])) {
                unset($definition['fields'][$field]);
            }
        }
        return $definition;
    }

    public static function displayFieldName($field)
    {
        return $field;
    }

    public function duplicateLogo($new_id, $old_id)
    {
        $old_logo = _PS_SHIP_IMG_DIR_.'/'.(int)$old_id.'.jpg';
        if (file_exists($old_logo)) {
            copy($old_logo, _PS_SHIP_IMG_DIR_.'/'.(int)$new_id.'.jpg');
        }

        $old_tmp_logo = _PS_TMP_IMG_DIR_.'/carrier_mini_'.(int)$old_id.'.jpg';
        if (file_exists($old_tmp_logo)) {
            if (!isset($_FILES['logo'])) {
                copy($old_tmp_logo, _PS_TMP_IMG_DIR_.'/carrier_mini_'.$new_id.'.jpg');
            }
            unlink($old_tmp_logo);
        }
    }

    public function getActualCurrency()
    {
        if ($this->type_context == Shop::CONTEXT_SHOP) {
            Shop::setContext($this->type_context, $this->old_context->shop->id);
        } elseif ($this->type_context == Shop::CONTEXT_GROUP) {
            Shop::setContext($this->type_context, $this->old_context->shop->id_shop_group);
        }

        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        Shop::setContext(Shop::CONTEXT_ALL);

        return $currency;
    }
}
