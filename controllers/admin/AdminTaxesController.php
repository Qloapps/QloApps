<?php
/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property Tax $object
 */
class AdminTaxesControllerCore extends AdminController
{
    protected $invalidCoreFields = array();
    protected $invalidTierFields = array();
    protected $invalidChildFields = array();
    protected $invalidValidityFields = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'tax';
        $this->className = 'Tax';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_tax' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'name' => array('title' => $this->l('Name'), 'width' => 'auto'),
            'is_tourism_tax' => array(
                'title' => $this->l('Tourism Tax'),
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'callback' => 'displayTourismTaxBadge',
                'orderby' => false,
                'type' => 'bool',
            ),
            'rate' => array(
                'title' => $this->l('Rate'),
                'align' => 'center',
                'class' => 'fixed-width-md',
                'callback' => 'displayRate',
                'orderby' => false,
            ),
            'active' => array('title' => $this->l('Enabled'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'class' => 'fixed-width-sm', 'remove_onclick' => true)
            );

        // Code For eco-tax working
        // $ecotax_desc = '';
        // if (Configuration::get('PS_USE_ECOTAX')) {
        //     $ecotax_desc = $this->l('If you disable the ecotax, the ecotax for all your products will be set to 0.');
        // }

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->l('Tax options'),
                'fields' =>    array(
                    'PS_TAX' => array(
                        'title' => $this->l('Enable tax'),
                        'desc' => $this->l('Select whether or not to include tax on purchases.'),
                        'cast' => 'intval', 'type' => 'bool'),
                    'PS_TAX_DISPLAY' => array(
                        'title' => $this->l('Display tax in the booking cart'),
                        'desc' => $this->l('Select whether or not to display tax on a distinct line in the cart.'),
                        'cast' => 'intval',
                        'type' => 'bool'),
                    // 'PS_TAX_ADDRESS_TYPE' => array(
                    //     'title' => $this->l('Based on'),
                    //     'cast' => 'pSQL',
                    //     'type' => 'select',
                    //     'list' => array(
                    //         array(
                    //             'name' => $this->l('Invoice address'),
                    //             'id' => 'id_address_invoice'
                    //         ),
                    //         array(
                    //             'name' => $this->l('Delivery address'),
                    //             'id' => 'id_address_delivery')
                    //         ),
                    //     ),
                    //     'identifier' => 'id'
                    // ),
                    // Code For eco-tax working
                    // 'PS_USE_ECOTAX' => array(
                    //     'title' => $this->l('Use ecotax'),
                    //     'desc' => $ecotax_desc,
                    //     'validation' => 'isBool',
                    //     'cast' => 'intval',
                    //     'type' => 'bool'
                    //     ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        // Code For eco-tax working
        // if (Configuration::get('PS_USE_ECOTAX') || Tools::getValue('PS_USE_ECOTAX')) {
        //     $this->fields_options['general']['fields']['PS_ECOTAX_TAX_RULES_GROUP_ID'] = array(
        //         'title' => $this->l('Ecotax'),
        //         'hint' => $this->l('Define the ecotax (e.g. French ecotax: 19.6%).'),
        //         'cast' => 'intval',
        //         'type' => 'select',
        //         'identifier' => 'id_tax_rules_group',
        //         'list' => TaxRulesGroup::getTaxRulesGroupsForOptions()
        //         );
        // }

        $this->fields_options['general']['fields']['QLO_USE_TOURISM_TAX'] = array(
            'title' => $this->l('Use tourism tax'),
            'hint' => $this->l('Enable tourism tax calculation for hotel room bookings.'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
        );

        if (Configuration::get('QLO_USE_TOURISM_TAX') || Tools::getValue('QLO_USE_TOURISM_TAX')) {
            $this->fields_options['general']['fields']['QLO_TOURISM_TAX_GROSSED_UP'] = array(
                'title' => $this->l('Include tourism tax in displayed prices'),
                'desc' => $this->l('When enabled, tourism tax is included in displayed prices. The applicable tourism tax is always shown as a separate line if applicable.'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
            );
        }

        parent::__construct();

        $this->_where .= ' AND a.deleted = 0';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'tax_configuration` tourism_tax ON tourism_tax.`id_tax` = a.`id_tax`';
        $extraSelect = 'IFNULL(tourism_tax.`calculation_type`, 0) AS `tourism_tax_calc_type`, IFNULL(tourism_tax.`tax_value`, 0) AS `tourism_tax_value`';
        $this->_select = ($this->_select ? $this->_select . ', ' : '') . $extraSelect;
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display) || $this->display == 'list') {
            $this->page_header_toolbar_btn['new_tax'] = array(
                'href' => self::$currentIndex.'&addtax&token='.$this->token,
                'desc' => $this->l('Add new tax', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Display delete action link
     *
     * @param string|null $token
     * @param int         $id
     *
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function displayDeleteLink($token, $id)
    {
        if (!array_key_exists('Delete', self::$cache_lang)) {
            self::$cache_lang['Delete'] = $this->l('Delete');
        }

        if (!array_key_exists('DeleteItem', self::$cache_lang)) {
            self::$cache_lang['DeleteItem'] = $this->l('Delete item #', __CLASS__, true, false);
        }

        if (TaxRule::isTaxInUse($id)) {
            $confirm = $this->l('This tax is currently in use as a tax rule. Are you sure you\'d like to continue?', null, true, false);
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token != null ? $token : $this->token),
            'confirm' => (isset($confirm) ? '\r'.$confirm : self::$cache_lang['DeleteItem'].$id.' ? '),
            'action' => self::$cache_lang['Delete'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_delete.tpl');
    }

    /**
     * Fetch the template for action enable
     *
     * @param string $token
     * @param int $id
     * @param int $value state enabled or not
     * @param string $active status
     * @param int $id_category
     * @param int $id_product
     */
    public function displayEnableLink($token, $id, $value, $active, $id_category = null, $id_product = null)
    {
        if ($value && TaxRule::isTaxInUse($id)) {
            $confirm = $this->l('This tax is currently in use as a tax rule. If you continue, this tax will be removed from the tax rule. Are you sure you\'d like to continue?', null, true, false);
        }
        $tpl_enable = $this->context->smarty->createTemplate('helpers/list/list_action_enable.tpl');
        $tpl_enable->assign(array(
            'enabled' => (bool)$value,
            'url_enable' => self::$currentIndex.'&'.$this->identifier.'='.(int)$id.'&'.$active.$this->table.
                ((int)$id_category && (int)$id_product ? '&id_category='.(int)$id_category : '').'&token='.($token != null ? $token : $this->token),
            'confirm' => isset($confirm) ? $confirm : null,
        ));

        return $tpl_enable->fetch();
    }

    public function renderList()
    {
        $this->_new_list_header_design = true;

        return parent::renderList();
    }

    public function renderForm()
    {
        $idTax = (int) Tools::getValue('id_' . $this->table);
        $subtype = null;
        $tiers = array();
        $childRanges = array();
        $validityRanges = array();
        $contextCurrency = $this->context->currency;
        $currencySign = Tools::safeOutput(trim($contextCurrency->prefix . $contextCurrency->suffix));

        $isAnySubmit = Tools::isSubmit('submitAdd' . $this->table) || Tools::isSubmit('submitAdd' . $this->table . 'AndStay');

        if ($idTax && !$isAnySubmit) {
            $subtype = TaxConfiguration::getByTaxId($idTax);
            if ($subtype) {
                $_POST['tax_calc_type'] = $subtype->calculation_type;
                $_POST['is_per_night'] = $subtype->per_night;
                $_POST['is_per_person'] = $subtype->per_person;
                $_POST['tax_value'] = $subtype->tax_value;
                $_POST['is_tiered'] = $subtype->has_tiered_pricing;
                $_POST['has_child_rate'] = $subtype->apply_on_child;
                $_POST['has_child_age_range'] = $subtype->has_child_age_range;
                $_POST['child_calc_type'] = $subtype->child_calculation_type;
                $_POST['has_multiple_valid_ranges'] = $subtype->has_multiple_valid_ranges;

                $tiers = TaxPriceTier::getByTaxId($idTax);
                $childRanges = TaxChildRange::getByTaxId($idTax);
                $validityRanges = TaxValidityRange::getByTaxId($idTax);
            }
        } elseif ($isAnySubmit) {
            $postTierIds = (array) Tools::getValue('tier_id', array());
            $postTierMins = (array) Tools::getValue('tier_min', array());
            $postTierMaxs = (array) Tools::getValue('tier_max', array());
            $postTierValues = (array) Tools::getValue('tier_value', array());
            foreach ($postTierIds as $tierRowIndex => $tierId) {
                $tiers[] = array(
                    'id_tier' => (int) $tierId,
                    'min_amount' => isset($postTierMins[$tierRowIndex]) ? $postTierMins[$tierRowIndex] : '',
                    'max_amount' => isset($postTierMaxs[$tierRowIndex]) ? $postTierMaxs[$tierRowIndex] : '',
                    'tax_value' => isset($postTierValues[$tierRowIndex]) ? $postTierValues[$tierRowIndex] : '',
                );
            }

            $postChildIds = (array) Tools::getValue('child_id', array());
            $postChildMins = (array) Tools::getValue('child_min', array());
            $postChildMaxs = (array) Tools::getValue('child_max', array());
            $postChildValues = (array) Tools::getValue('child_value', array());
            foreach ($postChildIds as $childRangeRowIndex => $childId) {
                $childRanges[] = array(
                    'id_child_range' => (int) $childId,
                    'min_age' => isset($postChildMins[$childRangeRowIndex]) ? $postChildMins[$childRangeRowIndex] : '',
                    'max_age' => isset($postChildMaxs[$childRangeRowIndex]) ? $postChildMaxs[$childRangeRowIndex] : '',
                    'tax_value' => isset($postChildValues[$childRangeRowIndex]) ? $postChildValues[$childRangeRowIndex] : '',
                );
            }

            $postValidityIds = (array) Tools::getValue('validity_id', array());
            $postValidityFroms = (array) Tools::getValue('validity_from', array());
            $postValidityTos = (array) Tools::getValue('validity_to', array());
            foreach ($postValidityIds as $validityRangeRowIndex => $rangeId) {
                $validityRanges[] = array(
                    'id_validity_range' => (int) $rangeId,
                    'valid_from' => isset($postValidityFroms[$validityRangeRowIndex]) ? $postValidityFroms[$validityRangeRowIndex] : '',
                    'valid_to' => isset($postValidityTos[$validityRangeRowIndex]) ? $postValidityTos[$validityRangeRowIndex] : '',
                );
            }
        }

        $taxType = (int) Tools::getValue('tax_calc_type', 0);
        $baseTaxValue = Tools::getValue('tax_value', $subtype ? $subtype->tax_value : 0);
        $allDayKeys = TaxConfiguration::DAY_KEYS;
        if ($subtype && $subtype->special_days) {
            $defaultSpecialDays = json_decode($subtype->special_days, true) ?: $allDayKeys;
        } else {
            $defaultSpecialDays = $allDayKeys;
        }
        $specialDaysChecked = Tools::getValue('valid_days', $defaultSpecialDays);
        if (!is_array($specialDaysChecked)) {
            $specialDaysChecked = $allDayKeys;
        }

        $initialTypeSign = ($taxType == TaxConfiguration::CALCULATION_TYPE_PERCENTAGE) ? '%' : $currencySign;
        $taxValueInputHtml = '
            <div class="input-group col-lg-5">
                <span class="input-group-addon" id="tourism-tax-type-sign" data-currency="' . htmlspecialchars($currencySign) . '">' . $initialTypeSign . '</span>
                <input type="text" name="tax_value" id="tax_value"
                    class="form-control' . (!empty($this->invalidCoreFields['tax_value']) ? ' error-border' : '') . '" value="' . htmlspecialchars($baseTaxValue) . '" />
            </div>';
        $rateCurrentValue = (float) Tools::getValue('rate', (isset($this->object) && $this->object) ? $this->object->rate : 0);
        $rateInputHtml = '
            <div class="input-group" style="max-width:220px">
                <span class="input-group-addon">%</span>
                <input type="text" name="rate" id="rate" class="form-control"
                    value="' . $rateCurrentValue . '" maxlength="6" />
            </div>';
        $priceBracketsHtml = '
            <table class="table" id="tourism-tax-tiers-table">
                <thead><tr class="nodrag nodrop">
                    <th class="col-sm-4 center">
                        <label class="control-label">' . $this->l('Min price (from)') . '</label>
                    </th>
                    <th class="col-sm-4 center">
                        <label class="control-label">' . $this->l('Max price (up to)') . '</label>
                    </th>
                    <th class="col-sm-3 center">
                        <label class="control-label required">' . $this->l('Tax value') . '</label>
                    </th>
                    <th class="col-sm-1 center">--</th>
                </tr></thead>
                <tbody id="tourism-tax-tiers-body">';
        foreach ($tiers as $tierRowIndex => $tier) {
            $tierMinRaw = isset($tier['min_amount']) ? $tier['min_amount'] : '';
            $tierMin = (is_numeric($tierMinRaw) && (float) $tierMinRaw == 0) ? '' : $tierMinRaw;
            $tierMaxRaw = isset($tier['max_amount']) ? $tier['max_amount'] : '';
            $tierMax = (is_numeric($tierMaxRaw) && (float) $tierMaxRaw == 0) ? '' : $tierMaxRaw;
            $tierVal = isset($tier['tax_value']) ? $tier['tax_value'] : '';
            $tierId = isset($tier['id_tier']) ? (int) $tier['id_tier'] : 0;
            $tierRowErrors = isset($this->invalidTierFields[$tierRowIndex]) ? $this->invalidTierFields[$tierRowIndex] : array();

            $priceBracketsHtml .= '
                <tr class="tourism-tax-tier-row nodrag nodrop">
                    <td class="col-sm-4 center">
                        <input type="text" name="tier_min[' . $tierRowIndex . ']"
                            value="' . htmlspecialchars($tierMin) . '" class="form-control' . (!empty($tierRowErrors['min']) ? ' error-border' : '') . '" placeholder="0" />
                    </td>
                    <td class="col-sm-4 center">
                        <input type="text" name="tier_max[' . $tierRowIndex . ']"
                            value="' . htmlspecialchars($tierMax) . '" class="form-control' . (!empty($tierRowErrors['max']) ? ' error-border' : '') . '" placeholder="' . $this->l('No cap') . '" />
                    </td>
                    <td class="col-sm-3 center">
                        <div class="input-group">
                            <span class="input-group-addon tourism-tax-row-type-sign"></span>
                            <input type="text" name="tier_value[' . $tierRowIndex . ']"
                                value="' . htmlspecialchars($tierVal) . '" class="form-control' . (!empty($tierRowErrors['value']) ? ' error-border' : '') . '" />
                        </div>
                    </td>
                    <td class="col-sm-1 center">
                        <input type="hidden" name="tier_id[' . $tierRowIndex . ']" value="' . $tierId . '" />
                        <a href="#" class="btn btn-default tourism-tax-remove-row">
                            <i class="icon-trash"></i>
                        </a>
                    </td>
                </tr>';
        }
        $priceBracketsHtml .= '
                </tbody>
            </table>
            <div class="form-group tourism-tax-add-row-btn">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-default" id="tourism-tax-add-tier">
                        <i class="icon-plus-circle"></i> ' . $this->l('Add Range') . '
                    </button>
                </div>
            </div>
            <script type="text/x-template" id="tourism-tax-tier-row-tpl">
                <tr class="tourism-tax-tier-row nodrag nodrop">
                    <td class="col-sm-4 center">
                        <input type="text" name="tier_min[__IDX__]" value="" class="form-control" placeholder="0" />
                    </td>
                    <td class="col-sm-4 center">
                        <input type="text" name="tier_max[__IDX__]" value="" class="form-control" placeholder="' . $this->l('No cap') . '" />
                    </td>
                    <td class="col-sm-3 center">
                        <div class="input-group">
                            <span class="input-group-addon tourism-tax-row-type-sign"></span>
                            <input type="text" name="tier_value[__IDX__]" value="" class="form-control" />
                        </div>
                    </td>
                    <td class="col-sm-1 center">
                        <input type="hidden" name="tier_id[__IDX__]" value="0" />
                        <a href="#" class="btn btn-default tourism-tax-remove-row">
                            <i class="icon-trash"></i>
                        </a>
                    </td>
                </tr>
            </script>';

        $infantMaxAge = (int) Configuration::get('QLO_GLOBAL_MAX_INFANT_AGE');
        $childMaxAge = (int) Configuration::get('WK_GLOBAL_CHILD_MAX_AGE');

        $ageBandsHtml = '
            <table class="table" id="tourism-tax-child-table">
                <thead><tr class="nodrag nodrop">
                    <th class="col-sm-4 center">
                        <label class="control-label">' . $this->l('Min age') . '</label>
                    </th>
                    <th class="col-sm-4 center">
                        <label class="control-label">' . $this->l('Max age') . '</label>
                    </th>
                    <th class="col-sm-3 center">
                        <label class="control-label required">' . $this->l('Tax value') . '</label>
                    </th>
                    <th class="col-sm-1 center">--</th>
                </tr></thead>
                <tbody id="tourism-tax-child-body">';
        foreach ($childRanges as $rangeRowIndex => $range) {
            $rangeMinRaw = isset($range['min_age']) ? $range['min_age'] : '';
            $rangeMin = (is_numeric($rangeMinRaw) && (int) $rangeMinRaw == 0) ? '' : $rangeMinRaw;
            $rangeMaxRaw = isset($range['max_age']) ? $range['max_age'] : '';
            $rangeMax = (is_numeric($rangeMaxRaw) && (int) $rangeMaxRaw == 0) ? '' : $rangeMaxRaw;
            $rangeVal = isset($range['tax_value']) ? $range['tax_value'] : '';
            $rangeId = isset($range['id_child_range']) ? (int) $range['id_child_range'] : 0;
            $rangeRowErrors = isset($this->invalidChildFields[$rangeRowIndex]) ? $this->invalidChildFields[$rangeRowIndex] : array();

            $ageBandsHtml .= '
                <tr class="tourism-tax-child-row nodrag nodrop">
                    <td class="col-sm-4 center">
                        <input type="text" name="child_min[' . $rangeRowIndex . ']"
                            value="' . htmlspecialchars($rangeMin) . '" class="form-control' . (!empty($rangeRowErrors['min']) ? ' error-border' : '') . '" placeholder="0" />
                    </td>
                    <td class="col-sm-4 center">
                        <input type="text" name="child_max[' . $rangeRowIndex . ']"
                            value="' . htmlspecialchars($rangeMax) . '" class="form-control' . (!empty($rangeRowErrors['max']) ? ' error-border' : '') . '" placeholder="' . $this->l('No cap') . '" />
                    </td>
                    <td class="col-sm-3 center">
                        <div class="input-group">
                            <span class="input-group-addon tourism-tax-child-row-type-sign"></span>
                            <input type="text" name="child_value[' . $rangeRowIndex . ']"
                                value="' . htmlspecialchars($rangeVal) . '" class="form-control' . (!empty($rangeRowErrors['value']) ? ' error-border' : '') . '" />
                        </div>
                    </td>
                    <td class="col-sm-1 center">
                        <input type="hidden" name="child_id[' . $rangeRowIndex . ']" value="' . $rangeId . '" />
                        <a href="#" class="btn btn-default tourism-tax-remove-row">
                            <i class="icon-trash"></i>
                        </a>
                    </td>
                </tr>';
        }
        $ageBandsHtml .= '
                </tbody>
            </table>
            <div class="form-group tourism-tax-add-row-btn">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-default" id="tourism-tax-add-child">
                        <i class="icon-plus-circle"></i> ' . $this->l('Add Range') . '
                    </button>
                </div>
            </div>
            <script type="text/x-template" id="tourism-tax-child-row-tpl">
                <tr class="tourism-tax-child-row nodrag nodrop">
                    <td class="col-sm-4 center">
                        <input type="text" name="child_min[__IDX__]" value="" class="form-control" placeholder="0" />
                    </td>
                    <td class="col-sm-4 center">
                        <input type="text" name="child_max[__IDX__]" value="" class="form-control" placeholder="' . $this->l('No cap') . '" />
                    </td>
                    <td class="col-sm-3 center">
                        <div class="input-group">
                            <span class="input-group-addon tourism-tax-child-row-type-sign"></span>
                            <input type="text" name="child_value[__IDX__]" value="" class="form-control" />
                        </div>
                    </td>
                    <td class="col-sm-1 center">
                        <input type="hidden" name="child_id[__IDX__]" value="0" />
                        <a href="#" class="btn btn-default tourism-tax-remove-row">
                            <i class="icon-trash"></i>
                        </a>
                    </td>
                </tr>
            </script>';

        $validityRangesHtml = '
            <table class="table" id="tourism-tax-validity-table">
                <thead><tr class="nodrag nodrop">
                    <th class="col-sm-5 center"><label class="control-label">' . $this->l('From') . '</label></th>
                    <th class="col-sm-5 center"><label class="control-label">' . $this->l('To') . '</label></th>
                    <th class="col-sm-2 center">--</th>
                </tr></thead>
                <tbody id="tourism-tax-validity-body">';
        foreach ($validityRanges as $rangeRowIndex => $range) {
            $rangeFrom = isset($range['valid_from']) ? $range['valid_from'] : '';
            if ($rangeFrom === '0000-00-00') {
                $rangeFrom = '';
            }
            $rangeTo = isset($range['valid_to']) ? $range['valid_to'] : '';
            if ($rangeTo === '0000-00-00') {
                $rangeTo = '';
            }
            $rangeId = isset($range['id_validity_range']) ? (int) $range['id_validity_range'] : 0;
            $validityRowErrors = isset($this->invalidValidityFields[$rangeRowIndex]) ? $this->invalidValidityFields[$rangeRowIndex] : array();

            $validityRangesHtml .= '
                <tr class="tourism-tax-validity-row nodrag nodrop">
                    <td class="col-sm-5 center">
                        <div class="input-group">
                            <input type="text" name="validity_from[' . $rangeRowIndex . ']"
                                value="' . htmlspecialchars($rangeFrom) . '" class="datepicker tourism-tax-validity-date form-control' . (!empty($validityRowErrors['from']) ? ' error-border' : '') . '" autocomplete="off" />
                            <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                        </div>
                    </td>
                    <td class="col-sm-5 center">
                        <div class="input-group">
                            <input type="text" name="validity_to[' . $rangeRowIndex . ']"
                                value="' . htmlspecialchars($rangeTo) . '" class="datepicker tourism-tax-validity-date form-control' . (!empty($validityRowErrors['to']) ? ' error-border' : '') . '" autocomplete="off" />
                            <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                        </div>
                    </td>
                    <td class="col-sm-2 center">
                        <input type="hidden" name="validity_id[' . $rangeRowIndex . ']" value="' . $rangeId . '" />
                        <a href="#" class="btn btn-default tourism-tax-remove-row">
                            <i class="icon-trash"></i>
                        </a>
                    </td>
                </tr>';
        }
        $validityRangesHtml .= '
                </tbody>
            </table>
            <div class="form-group tourism-tax-add-row-btn">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-default" id="tourism-tax-add-validity">
                        <i class="icon-plus-circle"></i> ' . $this->l('Add Range') . '
                    </button>
                </div>
            </div>
            <script type="text/x-template" id="tourism-tax-validity-row-tpl">
                <tr class="tourism-tax-validity-row nodrag nodrop">
                    <td class="col-sm-5 center">
                        <div class="input-group">
                            <input type="text" name="validity_from[__IDX__]" value="" class="datepicker tourism-tax-validity-date form-control" autocomplete="off" />
                            <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                        </div>
                    </td>
                    <td class="col-sm-5 center">
                        <div class="input-group">
                            <input type="text" name="validity_to[__IDX__]" value="" class="datepicker tourism-tax-validity-date form-control" autocomplete="off" />
                            <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                        </div>
                    </td>
                    <td class="col-sm-2 center">
                        <input type="hidden" name="validity_id[__IDX__]" value="0" />
                        <a href="#" class="btn btn-default tourism-tax-remove-row">
                            <i class="icon-trash"></i>
                        </a>
                    </td>
                </tr>
            </script>';

        $weekdayLabels = array(
            'mon' => $this->l('Mon'), 'tue' => $this->l('Tue'), 'wed' => $this->l('Wed'),
            'thu' => $this->l('Thu'), 'fri' => $this->l('Fri'), 'sat' => $this->l('Sat'),
            'sun' => $this->l('Sun'),
        );

        $weekdayCheckboxesHtml = '<div>';
        foreach ($weekdayLabels as $dayKey => $label) {
            $checked = in_array($dayKey, $specialDaysChecked) ? ' checked="checked"' : '';
            $weekdayCheckboxesHtml .= '
                <label class="checkbox-inline">
                    <input type="checkbox" name="valid_days[]" value="' . $dayKey . '"' . $checked . ' /> '
                    . $label . '
                </label>';
        }
        $weekdayCheckboxesHtml .= '</div>';

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Taxes'),
                'icon' => 'icon-money',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->l('Tax name to display in carts and on invoices (e.g. "City Tax").')
                        . ' - ' . $this->l('Invalid characters') . ' <>;=#{}',
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Rate'),
                    'name' => 'rate_wrap',
                    'html_content' => $rateInputHtml,
                    'hint' => $this->l('Format: XX.XX or XX.XXX (e.g. 19.60 or 13.925)')
                        . ' - ' . $this->l('Invalid characters') . ' <>;=#{}',
                    'form_group_class' => 'tourism-tax-non-tourism',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is tourism tax'),
                    'name' => 'is_tourism_tax',
                    'is_bool' => true,
                    'hint' => $this->l('When enabled, this tax will be used for tourism tax purposes only.'),
                    'values' => array(
                        array('id' => 'is_tourism_tax_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'is_tourism_tax_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Tax Calculation Type'),
                    'name' => 'tax_calc_type',
                    'form_group_class' => 'tourism-tax-field',
                    'options' => array(
                        'query' => array(
                            array('id' => 0, 'name' => $this->l('Fixed amount')),
                            array('id' => 1, 'name' => $this->l('Percentage')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Tax value'),
                    'name' => 'tax_value_wrap',
                    'required' => true,
                    'html_content' => $taxValueInputHtml,
                    'form_group_class' => 'tourism-tax-field',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Per night'),
                    'name' => 'is_per_night',
                    'is_bool' => true,
                    'hint' => $this->l('When enabled, the tax amount applies for each night of the booking instead of once per booking.'),
                    'form_group_class' => 'tourism-tax-field',
                    'default_value' => 1,
                    'values' => array(
                        array('id' => 'per_night_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'per_night_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Per occupancy'),
                    'name' => 'is_per_person',
                    'is_bool' => true,
                    'hint' => $this->l('When enabled, the tax amount multiplies by the number of adult occupants.'),
                    'form_group_class' => 'tourism-tax-field',
                    'values' => array(
                        array('id' => 'per_person_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'per_person_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Apply on child'),
                    'name' => 'has_child_rate',
                    'is_bool' => true,
                    'hint' => sprintf(
                        $this->l('When enabled, children pay the adult rate by default (default child age ranges %d–%d according to current general settings.'),
                        $infantMaxAge,
                        $childMaxAge - 1
                    ),
                    'form_group_class' => 'tourism-tax-field',
                    'values' => array(
                        array('id' => 'child_rate_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'child_rate_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Set child age range'),
                    'name' => 'has_child_age_range',
                    'is_bool' => true,
                    'desc' => $this->l('When enabled, define a set of child age bands below, each with its own tax value.'),
                    'form_group_class' => 'tourism-tax-field tourism-tax-child-field',
                    'values' => array(
                        array('id' => 'child_age_range_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'child_age_range_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Child Tax Calculation Type'),
                    'name' => 'child_calc_type',
                    'form_group_class' => 'tourism-tax-field tourism-tax-child-band-field',
                    'hint' => $this->l('Fixed: each age range charges its own flat amount. Percentage: each age range charges a percentage of the adult\'s own calculated tax.'),
                    'options' => array(
                        'query' => array(
                            array('id' => 0, 'name' => $this->l('Fixed')),
                            array('id' => 1, 'name' => $this->l('Percentage')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Child age ranges'),
                    'name' => 'child_ranges',
                    'html_content' => $ageBandsHtml,
                    'form_group_class' => 'tourism-tax-field tourism-tax-child-band-field',
                    'desc' => sprintf(
                        $this->l('Leave blank for no limit; ages must be ≥ %d and < %d. Children outside all ranges are charged the adult’s calculated tax.'),
                        $infantMaxAge,
                        $childMaxAge
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Tiered pricing'),
                    'name' => 'is_tiered',
                    'is_bool' => true,
                    'desc' => $this->l('When enabled, the tax amount is determined by the matching price range below. Prices outside all ranges use the base Tax value above.'),
                    'form_group_class' => 'tourism-tax-field',
                    'values' => array(
                        array('id' => 'tiered_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'tiered_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Price ranges'),
                    'name' => 'tiers',
                    'html_content' => $priceBracketsHtml,
                    'form_group_class' => 'tourism-tax-field tourism-tax-tiered-field',
                    'desc' => $this->l('Leave blank for no limit; the maximum price is exclusive. Prices outside all ranges are charged the adult’s calculated tax.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Set valid date ranges'),
                    'name' => 'has_multiple_valid_ranges',
                    'is_bool' => true,
                    'desc' => $this->l('When enabled, define one or more validity date ranges below — the tax only applies within them. When disabled, there is no date restriction at all.'),
                    'form_group_class' => 'tourism-tax-field',
                    'values' => array(
                        array('id' => 'multi_valid_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'multi_valid_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Valid ranges'),
                    'name' => 'validity_ranges',
                    'html_content' => $validityRangesHtml,
                    'form_group_class' => 'tourism-tax-field tourism-tax-multi-valid-field',
                    'desc' => $this->l('Leave "From" blank for no start date and "To" for no end date. Ranges cannot overlap.'),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Valid days'),
                    'name' => 'valid_days_wrap',
                    'html_content' => $weekdayCheckboxesHtml
                        . '<p class="help-block">'
                            . $this->l('Uncheck days when the tax should not apply within the date range.')
                        . '</p>',
                    'form_group_class' => 'tourism-tax-field',
                ),
            ),
            'submit' => array('title' => $this->l('Save')),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        return parent::renderForm();
    }

    /**
     * List column callback: display a Yes/No badge for is_tourism_tax.
     *
     * @param mixed $value  1 or 0
     * @param array $row
     * @return string
     */
    public function displayTourismTaxBadge($value, $row)
    {
        if ($value) {
            return '<span class="badge badge-success">' . $this->l('Yes') . '</span>';
        }
        return '<span class="badge badge-danger">' . $this->l('No') . '</span>';
    }

    /**
     * List column callback: display tax rate with the correct symbol.
     * Tourism-fixed taxes show currency sign; tourism-percent and regular taxes show %.
     *
     * @param mixed $value  ps_tax.rate
     * @param array $row    Full list row (includes tourism_tax_calc_type, tourism_tax_value from join)
     * @return string
     */
    public function displayRate($value, $row)
    {
        return TaxConfiguration::getFormattedRateForDisplay($value, $row, $this->context->currency);
    }

    public function postProcess()
    {
        if ($this->action == 'save') {
            if ((int) Tools::getValue('is_tourism_tax')) {
                $_POST['rate'] = '0';
            }
            /* Checking fields validity */
            $this->validateRules();
            $this->validateTourismTaxFields();
            if (!count($this->errors)) {
                $existingTaxId = (int)(Tools::getValue('id_'.$this->table));
                $isTourismTax = (int) Tools::getValue('is_tourism_tax');

                /* Object update */
                if (isset($existingTaxId) && !empty($existingTaxId)) {
                    /** @var Tax $object */
                    $object = new $this->className($existingTaxId);
                    if (Validate::isLoadedObject($object)) {
                        $wasTourismTax = (int) $object->is_tourism_tax;
                        $this->copyFromPost($object, $this->table);
                        $result = $object->update(false, false);

                        if (!$result) {
                            $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b>';
                        } elseif ($this->postImage($object->id)) {
                            if ($isTourismTax) {
                                self::saveTourismTaxSubtype($object->id);
                            }
                            if ($wasTourismTax && !$isTourismTax) {
                                $this->cleanTaxFromTourismTaxRulesGroups($object->id);
                                $tourismTax = TaxConfiguration::getByTaxId($object->id);
                                if ($tourismTax) {
                                    $tourismTax->delete();
                                }
                            }
                            if (!$wasTourismTax && $isTourismTax) {
                                $this->cleanTaxFromVatTaxRulesGroups($object->id);
                            }
                            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                                Tools::redirectAdmin(self::$currentIndex.'&update'.$this->table.'&id_'.$this->table.'='.$object->id.'&conf=4'.'&token='.$this->token);
                            } else {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                            }
                        }
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                    }
                }

                /* Object creation */
                else {
                    /** @var Tax $object */
                    $object = new $this->className();
                    $this->copyFromPost($object, $this->table);
                    if (!$object->add()) {
                        $this->errors[] = Tools::displayError('An error occurred while creating an object.').' <b>'.$this->table.'</b>';
                    } elseif ($this->postImage($object->id)) {
                        if ($isTourismTax) {
                            self::saveTourismTaxSubtype($object->id);
                        }
                        if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                            Tools::redirectAdmin(self::$currentIndex.'&update'.$this->table.'&id_'.$this->table.'='.$object->id.'&conf=3'.'&token='.$this->token);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                        }
                    }
                }
            } else {
                $this->display = 'add';
            }
        } else {
            parent::postProcess();
        }
    }

    /**
     * Validate tourism-tax-specific fields and append to $this->errors.
     */
    protected function validateTourismTaxFields()
    {
        if (!(int) Tools::getValue('is_tourism_tax')) {
            return;
        }

        $taxValue = Tools::getValue('tax_value', '');
        if ($taxValue === '') {
            $this->errors[] = $this->l('Tax value is required.');
            $this->invalidCoreFields['tax_value'] = true;
        } else {
            if (!is_numeric($taxValue)) {
                $this->errors[] = $this->l('Tax value must be a valid number.');
                $this->invalidCoreFields['tax_value'] = true;
            } elseif ((float) $taxValue < 0) {
                $this->errors[] = $this->l('Tax value cannot be negative.');
                $this->invalidCoreFields['tax_value'] = true;
            }
        }

        if ((int) Tools::getValue('has_multiple_valid_ranges')) {
            $submittedRangeFroms = Tools::getValue('validity_from', array());
            $submittedRangeTos = Tools::getValue('validity_to', array());
            $filledRanges = array();
            foreach ($submittedRangeFroms as $rowIndex => $fromRaw) {
                $toRaw = isset($submittedRangeTos[$rowIndex]) ? $submittedRangeTos[$rowIndex] : '';
                $fromRaw = trim($fromRaw);
                $toRaw = trim($toRaw);
                if ($fromRaw === '0000-00-00') { $fromRaw = ''; }
                if ($toRaw === '0000-00-00') { $toRaw = ''; }
                $rowNum = (int) $rowIndex + 1;
                if ($fromRaw === '' && $toRaw === '') {
                    continue;
                }
                if ($fromRaw !== '' && !Validate::isDate($fromRaw)) {
                    $this->errors[] = sprintf($this->l('Valid range #%d: "From" is not a valid date.'), $rowNum);
                    $this->invalidValidityFields[$rowIndex]['from'] = true;
                }
                if ($toRaw !== '' && !Validate::isDate($toRaw)) {
                    $this->errors[] = sprintf($this->l('Valid range #%d: "To" is not a valid date.'), $rowNum);
                    $this->invalidValidityFields[$rowIndex]['to'] = true;
                }
                if ($fromRaw !== '' && $toRaw !== '' && Validate::isDate($fromRaw) && Validate::isDate($toRaw)
                    && strtotime($toRaw) < strtotime($fromRaw)
                ) {
                    $this->errors[] = sprintf($this->l('Valid range #%d: "To" must be on or after "From".'), $rowNum);
                    $this->invalidValidityFields[$rowIndex]['from'] = true;
                    $this->invalidValidityFields[$rowIndex]['to'] = true;
                }
                $filledRanges[] = array(
                    'from' => ($fromRaw !== '' && Validate::isDate($fromRaw)) ? strtotime($fromRaw) : null,
                    'to' => ($toRaw !== '' && Validate::isDate($toRaw)) ? strtotime($toRaw) : null,
                    'rowNum' => $rowNum,
                    'rowIndex' => $rowIndex,
                );
            }
            if (empty($filledRanges)) {
                $this->errors[] = $this->l('Add at least one range or disable "Set valid date ranges".');
            }
            $rangeCount = count($filledRanges);
            for ($outerIdx = 0; $outerIdx < $rangeCount; $outerIdx++) {
                for ($innerIdx = $outerIdx + 1; $innerIdx < $rangeCount; $innerIdx++) {
                    $outerRange = $filledRanges[$outerIdx];
                    $innerRange = $filledRanges[$innerIdx];
                    $outerMinEffective = ($outerRange['from'] === null) ? -PHP_INT_MAX : $outerRange['from'];
                    $outerMaxEffective = ($outerRange['to'] === null) ? PHP_INT_MAX : $outerRange['to'];
                    $innerMinEffective = ($innerRange['from'] === null) ? -PHP_INT_MAX : $innerRange['from'];
                    $innerMaxEffective = ($innerRange['to'] === null) ? PHP_INT_MAX : $innerRange['to'];
                    if ($outerMinEffective <= $innerMaxEffective && $outerMaxEffective >= $innerMinEffective) {
                        $this->errors[] = sprintf(
                            $this->l('Valid ranges #%d and #%d overlap. Each range must cover a distinct period.'),
                            $outerRange['rowNum'],
                            $innerRange['rowNum']
                        );
                        $this->invalidValidityFields[$outerRange['rowIndex']]['from'] = true;
                        $this->invalidValidityFields[$outerRange['rowIndex']]['to'] = true;
                        $this->invalidValidityFields[$innerRange['rowIndex']]['from'] = true;
                        $this->invalidValidityFields[$innerRange['rowIndex']]['to'] = true;
                        break 2;
                    }
                }
            }
        }

        if ((int) Tools::getValue('has_child_rate') && (int) Tools::getValue('has_child_age_range')) {
            $infantMaxAge = (int) Configuration::get('QLO_GLOBAL_MAX_INFANT_AGE');
            $childMaxAge = (int) Configuration::get('WK_GLOBAL_CHILD_MAX_AGE');
            $submittedBandMins = Tools::getValue('child_min', array());
            $submittedBandMaxs = Tools::getValue('child_max', array());
            $submittedBandValues = Tools::getValue('child_value', array());
            if (empty($submittedBandValues)) {
                $this->errors[] = $this->l('Add at least one range or disable "Set child age range".');
            }
            $filledBands = array();
            foreach ($submittedBandValues as $rowIndex => $taxValue) {
                $bandMinRaw = isset($submittedBandMins[$rowIndex]) ? $submittedBandMins[$rowIndex] : '';
                $bandMaxRaw = isset($submittedBandMaxs[$rowIndex]) ? $submittedBandMaxs[$rowIndex] : '';
                $rowNum = (int) $rowIndex + 1;
                if ($taxValue === '' || $taxValue === false) {
                    $this->errors[] = sprintf($this->l('Child range #%d: tax value is required.'), $rowNum);
                    $this->invalidChildFields[$rowIndex]['value'] = true;
                    continue;
                }
                $bandMin = ($bandMinRaw !== '') ? (int) $bandMinRaw : 0;
                $bandMax = ($bandMaxRaw !== '') ? (int) $bandMaxRaw : 0;
                if (!is_numeric($taxValue)) {
                    $this->errors[] = sprintf($this->l('Child range #%d: tax value must be a valid number.'), $rowNum);
                    $this->invalidChildFields[$rowIndex]['value'] = true;
                } elseif ((float) $taxValue < 0) {
                    $this->errors[] = sprintf($this->l('Child range #%d: tax value cannot be negative.'), $rowNum);
                    $this->invalidChildFields[$rowIndex]['value'] = true;
                }
                if ($bandMax > 0 && $bandMin > $bandMax) {
                    $this->errors[] = sprintf($this->l('Child range #%d: min age cannot be greater than max age.'), $rowNum);
                    $this->invalidChildFields[$rowIndex]['min'] = true;
                    $this->invalidChildFields[$rowIndex]['max'] = true;
                }
                if ($bandMin > 0 && $bandMin < $infantMaxAge) {
                    $this->errors[] = sprintf($this->l('Child range #%d: min age cannot be below the infant age (%d).'), $rowNum, $infantMaxAge);
                    $this->invalidChildFields[$rowIndex]['min'] = true;
                }
                if ($bandMax > 0 && $bandMax >= $childMaxAge) {
                    $this->errors[] = sprintf($this->l('Child range #%d: max age cannot be %d or above (the general child age setting).'), $rowNum, $childMaxAge);
                    $this->invalidChildFields[$rowIndex]['max'] = true;
                }
                $filledBands[] = array('minAge' => $bandMin, 'maxAge' => $bandMax, 'rowNum' => $rowNum, 'rowIndex' => $rowIndex);
            }
            $bandCount = count($filledBands);
            for ($outerIdx = 0; $outerIdx < $bandCount; $outerIdx++) {
                for ($innerIdx = $outerIdx + 1; $innerIdx < $bandCount; $innerIdx++) {
                    $outerBand = $filledBands[$outerIdx];
                    $innerBand = $filledBands[$innerIdx];
                    $outerMaxEffective = ($outerBand['maxAge'] == 0) ? PHP_INT_MAX : $outerBand['maxAge'];
                    $innerMaxEffective = ($innerBand['maxAge'] == 0) ? PHP_INT_MAX : $innerBand['maxAge'];
                    if ($outerBand['minAge'] <= $innerMaxEffective && $outerMaxEffective >= $innerBand['minAge']) {
                        $this->errors[] = sprintf(
                            $this->l('Child age ranges #%d and #%d overlap. Each range must cover a distinct age range.'),
                            $outerBand['rowNum'],
                            $innerBand['rowNum']
                        );
                        $this->invalidChildFields[$outerBand['rowIndex']]['min'] = true;
                        $this->invalidChildFields[$outerBand['rowIndex']]['max'] = true;
                        $this->invalidChildFields[$innerBand['rowIndex']]['min'] = true;
                        $this->invalidChildFields[$innerBand['rowIndex']]['max'] = true;
                        break 2;
                    }
                }
            }
        }

        if ((int) Tools::getValue('is_tiered')) {
            $submittedTierMins = Tools::getValue('tier_min', array());
            $submittedTierMaxs = Tools::getValue('tier_max', array());
            $submittedTierValues = Tools::getValue('tier_value', array());
            if (empty($submittedTierValues)) {
                $this->errors[] = $this->l('Add at least one range or disable "Tiered pricing".');
            }
            $filledTiers = array();
            foreach ($submittedTierValues as $rowIndex => $taxValue) {
                $tierMinRaw = isset($submittedTierMins[$rowIndex]) ? $submittedTierMins[$rowIndex] : '';
                $tierMaxRaw = isset($submittedTierMaxs[$rowIndex]) ? $submittedTierMaxs[$rowIndex] : '';
                $rowNum = (int) $rowIndex + 1;
                if ($taxValue === '' || $taxValue === false) {
                    $this->errors[] = sprintf($this->l('Tier #%d: tax value is required.'), $rowNum);
                    $this->invalidTierFields[$rowIndex]['value'] = true;
                    continue;
                }
                $tierMin = ($tierMinRaw !== '') ? (float) $tierMinRaw : 0.0;
                $tierMax = ($tierMaxRaw !== '') ? (float) $tierMaxRaw : 0.0;
                if (!is_numeric($taxValue)) {
                    $this->errors[] = sprintf($this->l('Tier #%d: tax value must be a valid number.'), $rowNum);
                    $this->invalidTierFields[$rowIndex]['value'] = true;
                } elseif ((float) $taxValue < 0) {
                    $this->errors[] = sprintf($this->l('Tier #%d: tax value cannot be negative.'), $rowNum);
                    $this->invalidTierFields[$rowIndex]['value'] = true;
                }
                if ($tierMax > 0 && $tierMin > $tierMax) {
                    $this->errors[] = sprintf($this->l('Tier #%d: min price cannot be greater than max price.'), $rowNum);
                    $this->invalidTierFields[$rowIndex]['min'] = true;
                    $this->invalidTierFields[$rowIndex]['max'] = true;
                }
                $filledTiers[] = array('minPrice' => $tierMin, 'maxPrice' => $tierMax, 'rowNum' => $rowNum, 'rowIndex' => $rowIndex);
            }
            $tierCount = count($filledTiers);
            for ($outerIdx = 0; $outerIdx < $tierCount; $outerIdx++) {
                for ($innerIdx = $outerIdx + 1; $innerIdx < $tierCount; $innerIdx++) {
                    $outerTier = $filledTiers[$outerIdx];
                    $innerTier = $filledTiers[$innerIdx];
                    $outerMaxEffective = ($outerTier['maxPrice'] == 0) ? PHP_FLOAT_MAX : $outerTier['maxPrice'];
                    $innerMaxEffective = ($innerTier['maxPrice'] == 0) ? PHP_FLOAT_MAX : $innerTier['maxPrice'];
                    if ($outerTier['minPrice'] < $innerMaxEffective && $outerMaxEffective > $innerTier['minPrice']) {
                        $this->errors[] = sprintf(
                            $this->l('Price ranges #%d and #%d overlap. Each range must cover a distinct price range.'),
                            $outerTier['rowNum'],
                            $innerTier['rowNum']
                        );
                        $this->invalidTierFields[$outerTier['rowIndex']]['min'] = true;
                        $this->invalidTierFields[$outerTier['rowIndex']]['max'] = true;
                        $this->invalidTierFields[$innerTier['rowIndex']]['min'] = true;
                        $this->invalidTierFields[$innerTier['rowIndex']]['max'] = true;
                        break 2;
                    }
                }
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        if ($this->display == 'edit' || $this->display == 'add') {
            $this->addCSS(_MODULE_DIR_ . 'hotelreservationsystem/views/css/HotelReservationAdmin.css');
            $this->addJs(_MODULE_DIR_ . 'hotelreservationsystem/views/js/TourismTaxForm.js');
        }
    }

    /**
     * @param int $idTax
     */
    protected static function saveTourismTaxSubtype($idTax)
    {
        $idTax = (int) $idTax;
        $tourismTax = TaxConfiguration::getByTaxId($idTax);
        $tourismTaxExists = (bool) $tourismTax;
        if (!$tourismTax) {
            $tourismTax = new TaxConfiguration();
            $tourismTax->id = $idTax;
        }
        $tourismTax->calculation_type = (int) Tools::getValue('tax_calc_type', 0);
        $tourismTax->per_night = (int) Tools::getValue('is_per_night', 1);
        $tourismTax->per_person = (int) Tools::getValue('is_per_person', 0);
        $tourismTax->tax_value = (float) Tools::getValue('tax_value', 0);
        $tourismTax->has_tiered_pricing = (int) Tools::getValue('is_tiered', 0);
        $tourismTax->apply_on_child = (int) Tools::getValue('has_child_rate', 0);
        $tourismTax->has_child_age_range = (int) Tools::getValue('has_child_age_range', 0);
        $tourismTax->child_calculation_type = (int) Tools::getValue('child_calc_type', 0);
        $tourismTax->has_multiple_valid_ranges = (int) Tools::getValue('has_multiple_valid_ranges', 0);

        $submittedDays = Tools::getValue('valid_days', array());
        if (!is_array($submittedDays)) {
            $submittedDays = array();
        }
        $submittedDays = array_values(array_intersect(TaxConfiguration::DAY_KEYS, $submittedDays));
        if (empty($submittedDays) || count($submittedDays) === 7) {
            $tourismTax->special_days = null;
        } else {
            $tourismTax->special_days = json_encode($submittedDays);
        }

        if ($tourismTaxExists) {
            $tourismTax->update();
        } else {
            $tourismTax->add();
        }

        TaxPriceTier::saveAll(
            $idTax,
            Tools::getValue('tier_min', array()),
            Tools::getValue('tier_max', array()),
            Tools::getValue('tier_value', array())
        );
        TaxChildRange::saveAll(
            $idTax,
            Tools::getValue('child_min', array()),
            Tools::getValue('child_max', array()),
            Tools::getValue('child_value', array())
        );
        TaxValidityRange::saveAll(
            $idTax,
            Tools::getValue('validity_from', array()),
            Tools::getValue('validity_to', array())
        );
    }

    /**
     * Remove this tax from all tourism tax rules groups when it is demoted from tourism to regular.
     * Flashes a warning listing how many rule rows were deleted.
     *
     * @param int $idTax
     */
    protected function cleanTaxFromTourismTaxRulesGroups($idTax)
    {
        $count = TaxConfiguration::cleanFromTourismTaxRulesGroups($idTax);
        if ($count) {
            $this->warnings[] = sprintf(
                $this->l('This tax was removed from %d tourism tax rules group(s) because it is no longer marked as a tourism tax.'),
                $count
            );
        }
    }

    /**
     * Remove this tax from all standard (non-tourism) tax rules groups when it is promoted to a
     * tourism tax. Flashes a warning listing how many rule rows were deleted.
     *
     * @param int $idTax
     */
    protected function cleanTaxFromVatTaxRulesGroups($idTax)
    {
        $count = TaxConfiguration::cleanFromVatTaxRulesGroups($idTax);
        if ($count) {
            $this->warnings[] = sprintf(
                $this->l('This tax was removed from %d standard tax rules group(s) because it is now marked as a tourism tax.'),
                $count
            );
        }
    }

    public function updateOptionPsUseEcotax($value)
    {
        $old_value = (int)Configuration::get('PS_USE_ECOTAX');

        if ($old_value != $value) {
            // Reset ecotax
            if ($value == 0) {
                Product::resetEcoTax();
            }

            Configuration::updateValue('PS_USE_ECOTAX', (int)$value);
        }
    }
}
