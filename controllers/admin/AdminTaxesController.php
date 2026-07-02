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
                'search' => false,
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

        $ecotax_desc = '';
        if (Configuration::get('PS_USE_ECOTAX')) {
            $ecotax_desc = $this->l('If you disable the ecotax, the ecotax for all your products will be set to 0.');
        }

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
                    'PS_USE_ECOTAX' => array(
                        'title' => $this->l('Use ecotax'),
                        'desc' => $ecotax_desc,
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                        ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        if (Configuration::get('PS_USE_ECOTAX') || Tools::getValue('PS_USE_ECOTAX')) {
            $this->fields_options['general']['fields']['PS_ECOTAX_TAX_RULES_GROUP_ID'] = array(
                'title' => $this->l('Ecotax'),
                'hint' => $this->l('Define the ecotax (e.g. French ecotax: 19.6%).'),
                'cast' => 'intval',
                'type' => 'select',
                'identifier' => 'id_tax_rules_group',
                'list' => TaxRulesGroup::getTaxRulesGroupsForOptions()
                );
        }

        $this->fields_options['general']['fields']['QLO_USE_TOURISM_TAX'] = array(
            'title' => $this->l('Use tourism tax'),
            'hint' => $this->l('Enable tourism / city tax calculation for hotel room bookings.'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
        );

        if (Configuration::get('QLO_USE_TOURISM_TAX') || Tools::getValue('QLO_USE_TOURISM_TAX')) {
            $this->fields_options['general']['fields']['QLO_TOURISM_TAX_GROSSED_UP'] = array(
                'title' => $this->l('Include tourism tax in displayed room price'),
                'hint' => $this->l('Yes: rolled into the headline room price. No: shown as a separate line item.'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
            );
        }

        parent::__construct();

        $this->_where .= ' AND a.deleted = 0';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_tourism_tax` ht ON ht.`id_tax` = a.`id_tax`';
        $extraSelect = 'IFNULL(ht.`tax_type`, 0) AS `ht_tax_type`, IFNULL(ht.`tax_value`, 0) AS `ht_tax_value`';
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

    public function renderForm()
    {
        $idTax = (int) Tools::getValue('id_' . $this->table);
        $subtype = null;
        $tiers = array();
        $childRanges = array();
        $contextCurrency = $this->context->currency;
        $currencySign = Tools::safeOutput(trim($contextCurrency->prefix . $contextCurrency->suffix));

        $isAnySubmit = Tools::isSubmit('submitAdd' . $this->table) || Tools::isSubmit('submitAdd' . $this->table . 'AndStay');

        if ($idTax && !$isAnySubmit) {
            $subtype = HotelTourismTax::getByTaxId($idTax);
            if ($subtype) {
                $_POST['htl_tax_type'] = $subtype->tax_type;
                $_POST['htl_is_per_night'] = $subtype->is_per_night;
                $_POST['htl_is_per_person'] = $subtype->is_per_person;
                $_POST['htl_tax_value'] = $subtype->tax_value;
                $_POST['htl_is_tiered'] = $subtype->is_tiered;
                $_POST['htl_has_child_rate'] = $subtype->has_child_rate;
                $_POST['htl_valid_from'] = $subtype->valid_from ?: '';
                $_POST['htl_valid_to'] = $subtype->valid_to ?: '';

                $tiers       = HotelTourismTaxTier::getByTaxId($idTax);
                $childRanges = HotelTourismTaxChildRange::getByTaxId($idTax);
            }
        } elseif ($isAnySubmit) {
            $postTierIds = (array) Tools::getValue('htl_tier_id', array());
            $postTierMins = (array) Tools::getValue('htl_tier_min', array());
            $postTierMaxs = (array) Tools::getValue('htl_tier_max', array());
            $postTierValues = (array) Tools::getValue('htl_tier_value', array());
            foreach ($postTierIds as $i => $tierId) {
                $tiers[] = array(
                    'id_tier' => (int) $tierId,
                    'min_amount' => isset($postTierMins[$i]) ? $postTierMins[$i] : '',
                    'max_amount' => isset($postTierMaxs[$i]) ? $postTierMaxs[$i] : '',
                    'tax_value' => isset($postTierValues[$i]) ? $postTierValues[$i] : '',
                );
            }

            $postChildIds = (array) Tools::getValue('htl_child_id', array());
            $postChildMins = (array) Tools::getValue('htl_child_min', array());
            $postChildMaxs = (array) Tools::getValue('htl_child_max', array());
            $postChildValues = (array) Tools::getValue('htl_child_value', array());
            foreach ($postChildIds as $i => $childId) {
                $childRanges[] = array(
                    'id_child_range' => (int) $childId,
                    'min_age' => isset($postChildMins[$i]) ? $postChildMins[$i] : '',
                    'max_age' => isset($postChildMaxs[$i]) ? $postChildMaxs[$i] : '',
                    'tax_value' => isset($postChildValues[$i]) ? $postChildValues[$i] : '',
                );
            }
        }

        $taxType = (int) Tools::getValue('htl_tax_type', 0);
        $baseTaxValue = (float) Tools::getValue('htl_tax_value', $subtype ? $subtype->tax_value : 0);
        $allDayKeys = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
        if ($subtype && (int) $subtype->is_special_days_exists && $subtype->special_days) {
            $defaultSpecialDays = json_decode($subtype->special_days, true) ?: $allDayKeys;
        } else {
            $defaultSpecialDays = $allDayKeys;
        }
        $specialDaysChecked = Tools::getValue('htl_valid_days', $defaultSpecialDays);
        if (!is_array($specialDaysChecked)) {
            $specialDaysChecked = $allDayKeys;
        }

        $initialTypeSign = ($taxType == 1) ? '%' : $currencySign;
        $requiredMarker = ' <span class="tt-req-star">*</span>';
        $taxValueInputHtml = '
            <div class="input-group" style="max-width:220px">
                <span class="input-group-addon" id="tt-type-sign" data-currency="' . htmlspecialchars($currencySign) . '">' . $initialTypeSign . '</span>
                <input type="text" name="htl_tax_value" id="htl_tax_value"
                    class="form-control" value="' . (float) $baseTaxValue . '" />
            </div>';
        $rateCurrentValue = (float) Tools::getValue('rate', (isset($this->object) && $this->object) ? $this->object->rate : 0);
        $rateInputHtml = '
            <div class="input-group" style="max-width:220px">
                <span class="input-group-addon">%</span>
                <input type="text" name="rate" id="rate" class="form-control"
                    value="' . $rateCurrentValue . '" maxlength="6" />
            </div>';
        $priceBracketsHtml = '
            <table class="tt-subtable" id="tt-tiers-table">
                <thead><tr>
                    <th>' . $this->l('Min price (from)') . $requiredMarker . '</th>
                    <th>' . $this->l('Max price (up to, 0 = no cap)') . $requiredMarker . '</th>
                    <th>' . $this->l('Tax value') . $requiredMarker . '</th>
                    <th></th>
                </tr></thead>
                <tbody id="tt-tiers-body">';
        foreach ($tiers as $tierRowIndex => $tier) {
            $priceBracketsHtml .= $this->buildTierRow($tierRowIndex, $tier);
        }
        $priceBracketsHtml .= '
                </tbody>
            </table>
            <div class="form-group tt-add-row-btn">
                <button type="button" class="btn btn-default" id="tt-add-tier">
                    <i class="icon-plus-circle"></i> ' . $this->l('Add bracket') . '
                </button>
            </div>
            <script type="text/x-template" id="tt-tier-row-tpl">'
                . $this->buildTierRow('__IDX__', array())
            . '</script>';

        $ageBandsHtml = '
            <table class="tt-subtable" id="tt-child-table">
                <thead><tr>
                    <th>' . $this->l('Min age') . '</th>
                    <th>' . $this->l('Max age (0 = no cap)') . '</th>
                    <th>' . $this->l('Tax value') . $requiredMarker . '</th>
                    <th></th>
                </tr></thead>
                <tbody id="tt-child-body">';
        foreach ($childRanges as $rangeRowIndex => $range) {
            $ageBandsHtml .= $this->buildChildRangeRow($rangeRowIndex, $range);
        }
        $ageBandsHtml .= '
                </tbody>
            </table>
            <div class="form-group tt-add-row-btn">
                <button type="button" class="btn btn-default" id="tt-add-child">
                    <i class="icon-plus-circle"></i> ' . $this->l('Add band') . '
                </button>
            </div>
            <script type="text/x-template" id="tt-child-row-tpl">'
                . $this->buildChildRangeRow('__IDX__', array())
            . '</script>';

        $validFromValue = Tools::getValue('htl_valid_from', $subtype ? ($subtype->valid_from ?: '') : '');
        $validToValue = Tools::getValue('htl_valid_to', $subtype ? ($subtype->valid_to ?: '') : '');
        $weekdayLabels = array(
            'mon' => $this->l('Mon'), 'tue' => $this->l('Tue'), 'wed' => $this->l('Wed'),
            'thu' => $this->l('Thu'), 'fri' => $this->l('Fri'), 'sat' => $this->l('Sat'),
            'sun' => $this->l('Sun'),
        );

        $weekdayCheckboxesHtml = '<div style="margin-top:8px">';
        foreach ($weekdayLabels as $dayKey => $label) {
            $checked = in_array($dayKey, $specialDaysChecked) ? ' checked="checked"' : '';
            $weekdayCheckboxesHtml .= '
                <label class="checkbox-inline" style="margin-right:12px">
                    <input type="checkbox" name="htl_valid_days[]" value="' . $dayKey . '"' . $checked . ' /> '
                    . $label . '
                </label>';
        }
        $weekdayCheckboxesHtml .= '</div>';

        $validityInputHtml = '
            <div class="row">
                <div class="col-sm-5">
                    <div class="input-group">
                        <span class="input-group-addon">' . $this->l('From') . '</span>
                        <input type="text" name="htl_valid_from" id="htl_valid_from"
                            class="datepicker" autocomplete="off"
                            value="' . htmlspecialchars($validFromValue) . '" />
                        <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="input-group">
                        <span class="input-group-addon">' . $this->l('To') . '</span>
                        <input type="text" name="htl_valid_to" id="htl_valid_to"
                            class="datepicker" autocomplete="off"
                            value="' . htmlspecialchars($validToValue) . '" />
                        <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                    </div>
                    <p class="help-block" id="tt-date-error" style="color:#a94442;display:none;margin-top:4px"></p>
                </div>
            </div>';

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Taxes'),
                'icon' => 'icon-money',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is tourism tax'),
                    'name' => 'is_tourism_tax',
                    'is_bool' => true,
                    'hint' => $this->l('When enabled, this tax is used as a tourism / city tax. Rate is forced to 0 and parameters below apply.'),
                    'values' => array(
                        array('id' => 'is_tourism_tax_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'is_tourism_tax_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Tax name to display in carts and on invoices (e.g. "City Tax").')
                        . ' - ' . $this->l('Invalid characters') . ' <>;=#{}',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Tax Calculation Type'),
                    'name' => 'htl_tax_type',
                    'form_group_class' => 'tt-field',
                    'options' => array(
                        'query' => array(
                            array('id' => 0, 'name' => $this->l('Fixed amount per unit')),
                            array('id' => 1, 'name' => $this->l('Percentage of room price (excl. VAT)')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Tax value'),
                    'name' => 'htl_tax_value_wrap',
                    'html_content' => $taxValueInputHtml,
                    'hint' => $this->l('Leave 0 and enable tiered pricing below for bracket-based rates.'),
                    'form_group_class' => 'tt-field',
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Rate'),
                    'name' => 'rate_wrap',
                    'html_content' => $rateInputHtml,
                    'hint' => $this->l('Format: XX.XX or XX.XXX (e.g. 19.60 or 13.925)')
                        . ' - ' . $this->l('Invalid characters') . ' <>;=#{}',
                    'form_group_class' => 'tt-non-tourism',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Price calculation method'),
                    'name' => 'htl_is_per_night',
                    'hint' => $this->l('Select whether the tax amount is applied per night or once for the entire booking range.'),
                    'form_group_class' => 'tt-field',
                    'options' => array(
                        'query' => array(
                            array('id' => 1, 'name' => $this->l('Add per each night of booking')),
                            array('id' => 0, 'name' => $this->l('Add once for the booking range')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
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
                    'type' => 'switch',
                    'label' => $this->l('Per person (occupancy wise)'),
                    'name' => 'htl_is_per_person',
                    'is_bool' => true,
                    'hint' => $this->l('When enabled, the tax amount multiplies by the number of adult occupants.'),
                    'form_group_class' => 'tt-field',
                    'values' => array(
                        array('id' => 'per_person_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'per_person_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Apply on child'),
                    'name' => 'htl_has_child_rate',
                    'is_bool' => true,
                    'hint' => $this->l('Define the age band below. Children within the band pay the band\'s rate; all others pay the adult rate.'),
                    'form_group_class' => 'tt-field',
                    'values' => array(
                        array('id' => 'child_rate_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'child_rate_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Child age bands'),
                    'name' => 'htl_child_ranges',
                    'html_content' => $ageBandsHtml,
                    'form_group_class' => 'tt-field tt-child-field',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Tiered pricing'),
                    'name' => 'htl_is_tiered',
                    'is_bool' => true,
                    'hint' => $this->l('When a room price falls within a bracket, that bracket\'s value is used. Prices outside all brackets fall back to the base Tax value above.'),
                    'form_group_class' => 'tt-field',
                    'values' => array(
                        array('id' => 'tiered_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'tiered_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Price brackets'),
                    'name' => 'htl_tiers',
                    'html_content' => $priceBracketsHtml,
                    'form_group_class' => 'tt-field tt-tiered-field',
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Valid'),
                    'name' => 'htl_validity_wrap',
                    'html_content' => $validityInputHtml,
                    'hint' => $this->l('Leave blank for always active.'),
                    'form_group_class' => 'tt-field',
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Valid days'),
                    'name' => 'htl_valid_days_wrap',
                    'html_content' => $weekdayCheckboxesHtml
                        . '<p class="help-block">'
                            . $this->l('Uncheck days when the tax should not apply within the date range.')
                        . '</p>',
                    'form_group_class' => 'tt-field',
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
     * @param int|string $idx  Row index or '__IDX__' for JS template
     * @param array      $tier
     * @return string
     */
    protected function buildTierRow($idx, array $tier)
    {
        $min = isset($tier['min_amount']) ? (float) $tier['min_amount'] : '';
        $max = isset($tier['max_amount']) ? (float) $tier['max_amount'] : '';
        $val = isset($tier['tax_value']) ? (float) $tier['tax_value'] : '';
        $id = isset($tier['id_tier']) ? (int) $tier['id_tier'] : 0;

        return '
            <tr class="tt-tier-row">
                <td>
                    <input type="number" step="0.01" min="0" name="htl_tier_min[' . $idx . ']"
                        value="' . $min . '" class="form-control tt-req" placeholder="0" />
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="htl_tier_max[' . $idx . ']"
                        value="' . $max . '" class="form-control tt-req" placeholder="' . $this->l('0 = no cap') . '" />
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon tt-row-type-sign"></span>
                        <input type="number" step="0.01" min="0" name="htl_tier_value[' . $idx . ']"
                            value="' . $val . '" class="form-control tt-req" />
                    </div>
                </td>
                <td class="center" style="width:50px">
                    <input type="hidden" name="htl_tier_id[' . $idx . ']" value="' . $id . '" />
                    <a href="#" class="btn btn-default tt-remove-row">
                        <i class="icon-trash"></i>
                    </a>
                </td>
            </tr>';
    }

    /**
     * @param int|string $idx  Row index or '__IDX__' for JS template
     * @param array      $range
     * @return string
     */
    protected function buildChildRangeRow($idx, array $range)
    {
        $min = isset($range['min_age']) ? (int) $range['min_age'] : '';
        $max = isset($range['max_age']) ? (int) $range['max_age'] : '';
        $val = isset($range['tax_value']) ? (float) $range['tax_value'] : '';
        $id = isset($range['id_child_range']) ? (int) $range['id_child_range'] : 0;

        return '
            <tr class="tt-child-row">
                <td>
                    <input type="number" step="1" min="0" name="htl_child_min[' . $idx . ']"
                        value="' . $min . '" class="form-control" placeholder="0" />
                </td>
                <td>
                    <input type="number" step="1" min="0" name="htl_child_max[' . $idx . ']"
                        value="' . $max . '" class="form-control" placeholder="' . $this->l('0 = no cap') . '" />
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon tt-row-type-sign"></span>
                        <input type="number" step="0.01" min="0" name="htl_child_value[' . $idx . ']"
                            value="' . $val . '" class="form-control tt-req" />
                    </div>
                </td>
                <td class="center" style="width:50px">
                    <input type="hidden" name="htl_child_id[' . $idx . ']" value="' . $id . '" />
                    <a href="#" class="btn btn-default tt-remove-row">
                        <i class="icon-trash"></i>
                    </a>
                </td>
            </tr>';
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
     * @param array $row    Full list row (includes ht_tax_type, ht_tax_value from join)
     * @return string
     */
    public function displayRate($value, $row)
    {
        if (!(int) $row['is_tourism_tax']) {
            return Tools::safeOutput($value) . '%';
        }
        $displayValue    = Tools::safeOutput((float) $row['ht_tax_value']);
        if ((int) $row['ht_tax_type'] === 1) {
            return $displayValue . '%';
        }
        $activeCurrency = $this->context->currency;
        $currencySign   = Tools::safeOutput(trim($activeCurrency->prefix . $activeCurrency->suffix));
        return $currencySign . ' ' . $displayValue;
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
                        if ($isTourismTax) {
                            $object->rate = 0;
                        }
                        $result = $object->update(false, false);

                        if (!$result) {
                            $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b>';
                        } elseif ($this->postImage($object->id)) {
                            if ($object->id !== $existingTaxId) {
                                $oldSubtype = HotelTourismTax::getByTaxId($existingTaxId);
                                if ($oldSubtype) {
                                    $oldSubtype->delete();
                                }
                            }
                            if ($isTourismTax) {
                                $this->saveTourismTaxSubtype($object->id);
                            }
                            if ($wasTourismTax && !$isTourismTax) {
                                $this->cleanTaxFromTourismTrgs($object->id);
                            }
                            if (!$wasTourismTax && $isTourismTax) {
                                $this->cleanTaxFromVatTrgs($object->id);
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
                    if ($isTourismTax) {
                        $object->rate = 0;
                    }
                    if (!$object->add()) {
                        $this->errors[] = Tools::displayError('An error occurred while creating an object.').' <b>'.$this->table.'</b>';
                    } elseif ($this->postImage($object->id)) {
                        if ($isTourismTax) {
                            $this->saveTourismTaxSubtype($object->id);
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

        $taxValue = Tools::getValue('htl_tax_value', '');
        if ($taxValue !== '') {
            if (!is_numeric($taxValue)) {
                $this->errors[] = $this->l('Tax value must be a valid number.');
            } elseif ((float) $taxValue < 0) {
                $this->errors[] = $this->l('Tax value cannot be negative.');
            }
        }

        $validFrom = trim(Tools::getValue('htl_valid_from', ''));
        $validTo = trim(Tools::getValue('htl_valid_to', ''));
        if ($validFrom === '0000-00-00') { $validFrom = ''; }
        if ($validTo === '0000-00-00') { $validTo = ''; }
        if ($validFrom !== '' && !Validate::isDate($validFrom)) {
            $this->errors[] = $this->l('"Valid from" is not a valid date (use YYYY-MM-DD).');
        }
        if ($validTo !== '' && !Validate::isDate($validTo)) {
            $this->errors[] = $this->l('"Valid to" is not a valid date (use YYYY-MM-DD).');
        }
        if ($validFrom !== '' && $validTo !== ''
            && Validate::isDate($validFrom) && Validate::isDate($validTo)
            && strtotime($validTo) < strtotime($validFrom)
        ) {
            $this->errors[] = $this->l('"Valid to" must be on or after "Valid from".');
        }

        if ((int) Tools::getValue('htl_has_child_rate')) {
            $submittedBandMins = Tools::getValue('htl_child_min', array());
            $submittedBandMaxs = Tools::getValue('htl_child_max', array());
            $submittedBandValues = Tools::getValue('htl_child_value', array());
            $filledBands = array();
            foreach ($submittedBandValues as $rowIndex => $taxValue) {
                if ($taxValue === '' || $taxValue === false) {
                    continue;
                }
                $rowNum = (int) $rowIndex + 1;
                $bandMin = (isset($submittedBandMins[$rowIndex]) && $submittedBandMins[$rowIndex] !== '')
                    ? (int) $submittedBandMins[$rowIndex] : 0;
                $bandMax = (isset($submittedBandMaxs[$rowIndex]) && $submittedBandMaxs[$rowIndex] !== '')
                    ? (int) $submittedBandMaxs[$rowIndex] : 0;
                if (!is_numeric($taxValue)) {
                    $this->errors[] = sprintf($this->l('Child band #%d: tax value must be a valid number.'), $rowNum);
                } elseif ((float) $taxValue < 0) {
                    $this->errors[] = sprintf($this->l('Child band #%d: tax value cannot be negative.'), $rowNum);
                }
                if ($bandMax > 0 && $bandMin > $bandMax) {
                    $this->errors[] = sprintf($this->l('Child band #%d: min age cannot be greater than max age.'), $rowNum);
                }
                $filledBands[] = array('minAge' => $bandMin, 'maxAge' => $bandMax, 'rowNum' => $rowNum);
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
                            $this->l('Child age bands #%d and #%d overlap. Each band must cover a distinct age range.'),
                            $outerBand['rowNum'],
                            $innerBand['rowNum']
                        );
                        break 2;
                    }
                }
            }
        }

        if ((int) Tools::getValue('htl_is_tiered')) {
            $submittedTierMins = Tools::getValue('htl_tier_min', array());
            $submittedTierMaxs = Tools::getValue('htl_tier_max', array());
            $submittedTierValues = Tools::getValue('htl_tier_value', array());
            $filledTiers = array();
            foreach ($submittedTierValues as $rowIndex => $taxValue) {
                if ($taxValue === '' || $taxValue === false) {
                    continue;
                }
                $rowNum = (int) $rowIndex + 1;
                $tierMin = (isset($submittedTierMins[$rowIndex]) && $submittedTierMins[$rowIndex] !== '')
                    ? (float) $submittedTierMins[$rowIndex] : 0.0;
                $tierMax = (isset($submittedTierMaxs[$rowIndex]) && $submittedTierMaxs[$rowIndex] !== '')
                    ? (float) $submittedTierMaxs[$rowIndex] : 0.0;
                if (!is_numeric($taxValue)) {
                    $this->errors[] = sprintf($this->l('Tier #%d: tax value must be a valid number.'), $rowNum);
                } elseif ((float) $taxValue < 0) {
                    $this->errors[] = sprintf($this->l('Tier #%d: tax value cannot be negative.'), $rowNum);
                }
                if ($tierMax > 0 && $tierMin > $tierMax) {
                    $this->errors[] = sprintf($this->l('Tier #%d: min price cannot be greater than max price.'), $rowNum);
                }
                $filledTiers[] = array('minPrice' => $tierMin, 'maxPrice' => $tierMax, 'rowNum' => $rowNum);
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
                            $this->l('Price brackets #%d and #%d overlap. Each bracket must cover a distinct price range.'),
                            $outerTier['rowNum'],
                            $innerTier['rowNum']
                        );
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
            $this->addCSS(_MODULE_DIR_ . 'hotelreservationsystem/views/css/TourismTaxAdmin.css');
            $this->addJs(_MODULE_DIR_ . 'hotelreservationsystem/views/js/TourismTaxForm.js');
        }
    }

    /**
     * @param int $idTax
     */
    protected function saveTourismTaxSubtype($idTax)
    {
        $tourismTax = HotelTourismTax::getByTaxId((int) $idTax);
        if (!$tourismTax) {
            $tourismTax = new HotelTourismTax();
            $tourismTax->id = (int) $idTax;
        }
        $tourismTax->tax_type = (int) Tools::getValue('htl_tax_type', 0);
        $tourismTax->is_per_night = (int) Tools::getValue('htl_is_per_night', 1);
        $tourismTax->is_per_person = (int) Tools::getValue('htl_is_per_person', 0);
        $tourismTax->tax_value = (float) Tools::getValue('htl_tax_value', 0);
        $tourismTax->is_tiered = (int) Tools::getValue('htl_is_tiered', 0);
        $tourismTax->has_child_rate = (int) Tools::getValue('htl_has_child_rate', 0);
        $validFromRaw = trim(Tools::getValue('htl_valid_from', ''));
        $validToRaw = trim(Tools::getValue('htl_valid_to', ''));
        $tourismTax->valid_from = ($validFromRaw && $validFromRaw !== '0000-00-00') ? $validFromRaw : null;
        $tourismTax->valid_to = ($validToRaw && $validToRaw !== '0000-00-00') ? $validToRaw : null;

        $submittedDays = Tools::getValue('htl_valid_days', array());
        if (!is_array($submittedDays)) {
            $submittedDays = array();
        }
        $validDayKeys = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
        $submittedDays = array_values(array_intersect($validDayKeys, $submittedDays));
        if (empty($submittedDays) || count($submittedDays) === 7) {
            $tourismTax->is_special_days_exists = 0;
            $tourismTax->special_days           = null;
        } else {
            $tourismTax->is_special_days_exists = 1;
            $tourismTax->special_days           = json_encode($submittedDays);
        }

        if (HotelTourismTax::existsForTax((int) $idTax)) {
            $tourismTax->update();
        } else {
            $tourismTax->add();
        }

        HotelTourismTaxTier::saveAll(
            (int) $idTax,
            Tools::getValue('htl_tier_min', array()),
            Tools::getValue('htl_tier_max', array()),
            Tools::getValue('htl_tier_value', array())
        );
        HotelTourismTaxChildRange::saveAll(
            (int) $idTax,
            Tools::getValue('htl_child_min', array()),
            Tools::getValue('htl_child_max', array()),
            Tools::getValue('htl_child_value', array())
        );
    }

    /**
     * Remove this tax from all tourism TRGs when it is demoted from tourism to regular.
     * Flashes a warning listing how many rule rows were deleted.
     *
     * @param int $idTax
     */
    protected function cleanTaxFromTourismTrgs($idTax)
    {
        $count = HotelTourismTax::cleanFromTourismTrgs((int) $idTax);
        if ($count) {
            $this->warnings[] = sprintf(
                $this->l('This tax was removed from %d tourism tax rules group(s) because it is no longer marked as a tourism tax.'),
                $count
            );
        }
    }

    /**
     * Remove this tax from all standard (non-tourism) TRGs when it is promoted to a tourism tax.
     * Flashes a warning listing how many rule rows were deleted.
     *
     * @param int $idTax
     */
    protected function cleanTaxFromVatTrgs($idTax)
    {
        $count = HotelTourismTax::cleanFromVatTrgs((int) $idTax);
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
