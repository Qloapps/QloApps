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
class AdminCarriersControllerCore extends AdminController
{
    protected $position_identifier = 'id_carrier';

    public function __construct()
    {
        if ($id_carrier = Tools::getValue('id_carrier') && !Tools::isSubmit('deletecarrier') && !Tools::isSubmit('statuscarrier') && !Tools::isSubmit('isFreecarrier') && !Tools::isSubmit('onboarding_carrier')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminCarrierWizard').'&id_carrier='.(int)$id_carrier);
        }

        $this->bootstrap = true;
        $this->table = 'carrier';
        $this->className = 'Carrier';
        $this->lang = false;
        $this->deleted = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_defaultOrderBy = 'position';

        $this->context = Context::getContext();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fieldImageSettings = array(
            'name' => 'logo',
            'dir' => 's'
        );

        $this->fields_list = array(
            'id_carrier' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name')
            ),
            'image' => array(
                'title' => $this->l('Logo'),
                'align' => 'center',
                'image' => 's',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false
            ),
            'delay' => array(
                'title' => $this->l('Delay'),
                'orderby' => false
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false,
            ),
            'is_free' => array(
                'title' => $this->l('Free Shipping'),
                'align' => 'center',
                'active' => 'isFree',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false,
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position'
            )
        );
        parent::__construct();

        if (Tools::isSubmit('onboarding_carrier')) {
            $this->display = 'view';
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if (isset($this->toolbar_btn['new']) && $this->display != 'view') {
            $this->toolbar_btn['new']['href'] = $this->context->link->getAdminLink('AdminCarriers').'&onboarding_carrier';
        }
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('Carriers');
        if ($this->display != 'view') {
            $this->page_header_toolbar_btn['new_carrier'] = array(
                'href' => $this->context->link->getAdminLink('AdminCarriers').'&onboarding_carrier',
                'desc' => $this->l('Add new carrier', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderView()
    {
        $this->initTabModuleList();
        $this->filterTabModuleList();
        $this->context->smarty->assign('panel_title', $this->l('Use one of our recommended carrier modules'));
        $this->tpl_view_vars = array('modules_list' => $this->renderModulesList());
        unset($this->page_header_toolbar_btn['modules-list']);
        return parent::renderView();
    }

    public function renderList()
    {
        $this->_select = 'b.*';
        $this->_join = 'INNER JOIN `'._DB_PREFIX_.'carrier_lang` b ON a.id_carrier = b.id_carrier'.Shop::addSqlRestrictionOnLang('b').' AND b.id_lang = '.$this->context->language->id.' LEFT JOIN `'._DB_PREFIX_.'carrier_tax_rules_group_shop` ctrgs ON (a.`id_carrier` = ctrgs.`id_carrier` AND ctrgs.id_shop='.(int)$this->context->shop->id.')';
        $this->_use_found_rows = false;
        return parent::renderList();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Carriers'),
                'icon' => 'icon-truck'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Company'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => array(
                        sprintf($this->l('Allowed characters: letters, spaces and %s'), '().-'),
                        $this->l('Carrier name displayed during checkout'),
                        $this->l('For in-store pickup, enter 0 to replace the carrier name with your shop name.')
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Logo'),
                    'name' => 'logo',
                    'hint' => $this->l('Upload a logo from your computer.').' (.gif, .jpg, .jpeg '.$this->l('or').' .png)'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Transit time'),
                    'name' => 'delay',
                    'lang' => true,
                    'required' => true,
                    'maxlength' => 128,
                    'hint' => $this->l('Estimated delivery time will be displayed during checkout.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Speed grade'),
                    'name' => 'grade',
                    'required' => false,
                    'hint' => $this->l('Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('URL'),
                    'name' => 'url',
                    'hint' => $this->l('Delivery tracking URL: Type \'@\' where the tracking number should appear. It will then be automatically replaced by the tracking number.')
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Zone'),
                    'name' => 'zone',
                    'values' => array(
                        'query' => Zone::getZones(false),
                        'id' => 'id_zone',
                        'name' => 'name'
                    ),
                    'hint' => $this->l('The zones in which this carrier will be used.')
                ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Group access'),
                    'name' => 'groupBox',
                    'values' => Group::getGroups(Context::getContext()->language->id),
                    'hint' => $this->l('Mark the groups that are allowed access to this carrier.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
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
                    ),
                    'hint' => $this->l('Enable the carrier in the front office.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Apply shipping cost'),
                    'name' => 'is_free',
                    'required' => false,
                    'class' => 't',
                    'values' => array(
                        array(
                            'id' => 'is_free_on',
                            'value' => 0,
                            'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />'
                        ),
                        array(
                            'id' => 'is_free_off',
                            'value' => 1,
                            'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" />'
                        )
                    ),
                    'hint' => $this->l('Apply both regular shipping cost and product-specific shipping costs.')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Tax'),
                    'name' => 'id_tax_rules_group',
                    'options' => array(
                        'query' => TaxRulesGroup::getTaxRulesGroups(true),
                        'id' => 'id_tax_rules_group',
                        'name' => 'name',
                        'default' => array(
                            'label' => $this->l('No Tax'),
                            'value' => 0
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Shipping and handling'),
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
                    'hint' => $this->l('Include the shipping and handling costs in the carrier price.')
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Billing'),
                    'name' => 'shipping_method',
                    'required' => false,
                    'class' => 't',
                    'br' => true,
                    'values' => array(
                        array(
                            'id' => 'billing_default',
                            'value' => Carrier::SHIPPING_METHOD_DEFAULT,
                            'label' => $this->l('Default behavior')
                        ),
                        array(
                            'id' => 'billing_price',
                            'value' => Carrier::SHIPPING_METHOD_PRICE,
                            'label' => $this->l('According to total price')
                        ),
                        array(
                            'id' => 'billing_weight',
                            'value' => Carrier::SHIPPING_METHOD_WEIGHT,
                            'label' => $this->l('According to total weight')
                        )
                    )
                ),
                array(
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
                    'hint' => $this->l('Out-of-range behavior occurs when none is defined (e.g. when a customer\'s cart weight is greater than the highest range limit).')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum package height'),
                    'name' => 'max_height',
                    'required' => false,
                    'hint' => $this->l('Maximum height managed by this carrier. Set the value to "0," or leave this field blank to ignore.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum package width'),
                    'name' => 'max_width',
                    'required' => false,
                    'hint' => $this->l('Maximum width managed by this carrier. Set the value to "0," or leave this field blank to ignore.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum package depth'),
                    'name' => 'max_depth',
                    'required' => false,
                    'hint' => $this->l('Maximum depth managed by this carrier. Set the value to "0," or leave this field blank to ignore.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum package weight'),
                    'name' => 'max_weight',
                    'required' => false,
                    'hint' => $this->l('Maximum weight managed by this carrier. Set the value to "0," or leave this field blank to ignore.')
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'is_module'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'external_module_name',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'shipping_external'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'need_range'
                ),
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->getFieldsValues($obj);
        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::getValue('action') == 'GetModuleQuickView' && Tools::getValue('ajax') == '1') {
            $this->ajaxProcessGetModuleQuickView();
        }

        if (Tools::getValue('submitAdd'.$this->table)) {
            /* Checking fields validity */
            $this->validateRules();
            if (!count($this->errors)) {
                $id = (int)Tools::getValue('id_'.$this->table);

                /* Object update */
                if (isset($id) && !empty($id)) {
                    try {
                        if ($this->tabAccess['edit'] === '1') {
                            $current_carrier = new Carrier($id);
                            if (!Validate::isLoadedObject($current_carrier)) {
                                throw new PrestaShopException('Cannot load Carrier object');
                            }

                            /** @var Carrier $new_carrier */
                            // Duplicate current Carrier
                            $new_carrier = $current_carrier->duplicateObject();
                            if (Validate::isLoadedObject($new_carrier)) {
                                // Set flag deteled to true for historization
                                $current_carrier->deleted = true;
                                $current_carrier->update();

                                // Fill the new carrier object
                                $this->copyFromPost($new_carrier, $this->table);
                                $new_carrier->position = $current_carrier->position;
                                $new_carrier->update();

                                $this->updateAssoShop($new_carrier->id);
                                $new_carrier->copyCarrierData((int)$current_carrier->id);
                                $this->changeGroups($new_carrier->id);
                                // Call of hooks
                                Hook::exec('actionCarrierUpdate', array(
                                    'id_carrier' => (int)$current_carrier->id,
                                    'carrier' => $new_carrier
                                ));
                                $this->postImage($new_carrier->id);
                                $this->changeZones($new_carrier->id);
                                $new_carrier->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group'));
                                Tools::redirectAdmin(self::$currentIndex.'&id_'.$this->table.'='.$current_carrier->id.'&conf=4&token='.$this->token);
                            } else {
                                $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b>';
                            }
                        } else {
                            $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                        }
                    } catch (PrestaShopException $e) {
                        $this->errors[] = $e->getMessage();
                    }
                }

                /* Object creation */
                else {
                    if ($this->tabAccess['add'] === '1') {
                        // Create new Carrier
                        $carrier = new Carrier();
                        $this->copyFromPost($carrier, $this->table);
                        $carrier->position = Carrier::getHigherPosition() + 1;
                        if ($carrier->add()) {
                            if (($_POST['id_'.$this->table] = $carrier->id /* voluntary */) && $this->postImage($carrier->id) && $this->_redirect) {
                                $carrier->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group'), true);
                                $this->changeZones($carrier->id);
                                $this->changeGroups($carrier->id);
                                $this->updateAssoShop($carrier->id);
                                Tools::redirectAdmin(self::$currentIndex.'&id_'.$this->table.'='.$carrier->id.'&conf=3&token='.$this->token);
                            }
                        } else {
                            $this->errors[] = Tools::displayError('An error occurred while creating an object.').' <b>'.$this->table.'</b>';
                        }
                    } else {
                        $this->errors[] = Tools::displayError('You do not have permission to add this.');
                    }
                }
            }
            parent::postProcess();
        }
        /*
elseif ((isset($_GET['status'.$this->table]) || isset($_GET['status'])) && Tools::getValue($this->identifier))
        {
            if ($this->tabAccess['edit'] === '1')
            {
                if (Tools::getValue('id_carrier') == Configuration::get('PS_CARRIER_DEFAULT'))
                    $this->errors[] = Tools::displayError('You cannot disable the default carrier, however you can change your default carrier. ');
                else
                    parent::postProcess();
            }
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }
*/
        elseif (isset($_GET['isFree'.$this->table])) {
            $this->processIsFree();
        } else {
            /*
    if ((Tools::isSubmit('submitDel'.$this->table) && in_array(Configuration::get('PS_CARRIER_DEFAULT'), Tools::getValue('carrierBox')))
                || (isset($_GET['delete'.$this->table]) && Tools::getValue('id_carrier') == Configuration::get('PS_CARRIER_DEFAULT')))
                    $this->errors[] = $this->l('Please set another carrier as default before deleting this one.');
            else
            {
*/
                // if deletion : removes the carrier from the warehouse/carrier association
                if (Tools::isSubmit('delete'.$this->table)) {
                    $id = (int)Tools::getValue('id_'.$this->table);
                    // Delete from the reference_id and not from the carrier id
                    $carrier = new Carrier((int)$id);
                    Warehouse::removeCarrier($carrier->id_reference);
                } elseif (Tools::isSubmit($this->table.'Box') && count(Tools::isSubmit($this->table.'Box')) > 0) {
                    $ids = Tools::getValue($this->table.'Box');
                    array_walk($ids, 'intval');
                    foreach ($ids as $id) {
                        // Delete from the reference_id and not from the carrier id
                        $carrier = new Carrier((int)$id);
                        Warehouse::removeCarrier($carrier->id_reference);
                    }
                }
            parent::postProcess();
            Carrier::cleanPositions();
            //}
        }
    }

    public function processIsFree()
    {
        $carrier = new Carrier($this->id_object);
        if (!Validate::isLoadedObject($carrier)) {
            $this->errors[] = Tools::displayError('An error occurred while updating carrier information.');
        }
        $carrier->is_free = $carrier->is_free ? 0 : 1;
        if (!$carrier->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating carrier information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    /**
     * Overload the property $fields_value
     *
     * @param object $obj
     */
    public function getFieldsValues($obj)
    {
        if ($this->getFieldValue($obj, 'is_module')) {
            $this->fields_value['is_module'] = 1;
        }

        if ($this->getFieldValue($obj, 'shipping_external')) {
            $this->fields_value['shipping_external'] = 1;
        }

        if ($this->getFieldValue($obj, 'need_range')) {
            $this->fields_value['need_range'] = 1;
        }
        // Added values of object Zone
        $carrier_zones = $obj->getZones();
        $carrier_zones_ids = array();
        if (is_array($carrier_zones)) {
            foreach ($carrier_zones as $carrier_zone) {
                $carrier_zones_ids[] = $carrier_zone['id_zone'];
            }
        }

        $zones = Zone::getZones(false);
        foreach ($zones as $zone) {
            $this->fields_value['zone_'.$zone['id_zone']] = Tools::getValue('zone_'.$zone['id_zone'], (in_array($zone['id_zone'], $carrier_zones_ids)));
        }

        // Added values of object Group
        $carrier_groups = $obj->getGroups();
        $carrier_groups_ids = array();
        if (is_array($carrier_groups)) {
            foreach ($carrier_groups as $carrier_group) {
                $carrier_groups_ids[] = $carrier_group['id_group'];
            }
        }

        $groups = Group::getGroups($this->context->language->id);

        foreach ($groups as $group) {
            $this->fields_value['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], (in_array($group['id_group'], $carrier_groups_ids) || empty($carrier_groups_ids) && !$obj->id));
        }

        $this->fields_value['id_tax_rules_group'] = $this->object->getIdTaxRulesGroup($this->context);
    }

    /**
     * @param Carrier $object
     * @return int
     */
    protected function beforeDelete($object)
    {
        return $object->isUsed();
    }

    protected function changeGroups($id_carrier, $delete = true)
    {
        if ($delete) {
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'carrier_group WHERE id_carrier = '.(int)$id_carrier);
        }
        $groups = Db::getInstance()->executeS('SELECT id_group FROM `'._DB_PREFIX_.'group`');
        foreach ($groups as $group) {
            if (Tools::getIsset('groupBox') && in_array($group['id_group'], Tools::getValue('groupBox'))) {
                Db::getInstance()->execute('
					INSERT INTO '._DB_PREFIX_.'carrier_group (id_group, id_carrier)
					VALUES('.(int)$group['id_group'].','.(int)$id_carrier.')
				');
            }
        }
    }

    public function changeZones($id)
    {
        /** @var Carrier $carrier */
        $carrier = new $this->className($id);
        if (!Validate::isLoadedObject($carrier)) {
            die(Tools::displayError('The object cannot be loaded.'));
        }
        $zones = Zone::getZones(false);
        foreach ($zones as $zone) {
            if (count($carrier->getZone($zone['id_zone']))) {
                if (!isset($_POST['zone_'.$zone['id_zone']]) || !$_POST['zone_'.$zone['id_zone']]) {
                    $carrier->deleteZone($zone['id_zone']);
                }
            } elseif (isset($_POST['zone_'.$zone['id_zone']]) && $_POST['zone_'.$zone['id_zone']]) {
                $carrier->addZone($zone['id_zone']);
            }
        }
    }

    /**
     * Modifying initial getList method to display position feature (drag and drop)
     *
     * @param int         $id_lang
     * @param string|null $order_by
     * @param string|null $order_way
     * @param int         $start
     * @param int|null    $limit
     * @param int|bool    $id_lang_shop
     *
     * @throws PrestaShopException
     */
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        foreach ($this->_list as $key => $list) {
            if ($list['name'] == '0') {
                $this->_list[$key]['name'] = Carrier::getCarrierNameFromShopName();
            }
        }
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int)(Tools::getValue('way'));
        $id_carrier = (int)(Tools::getValue('id'));
        $positions = Tools::getValue($this->table);

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int)$pos[2] === $id_carrier) {
                if ($carrier = new Carrier((int)$pos[2])) {
                    if (isset($position) && $carrier->updatePosition($way, $position)) {
                        echo 'ok position '.(int)$position.' for carrier '.(int)$pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update carrier '.(int)$id_carrier.' to position '.(int)$position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This carrier ('.(int)$id_carrier.') can t be loaded"}';
                }

                break;
            }
        }
    }

    public function displayEditLink($token = null, $id, $name = null)
    {
        if ($this->tabAccess['edit'] == 1) {
            $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
            if (!array_key_exists('Edit', self::$cache_lang)) {
                self::$cache_lang['Edit'] = $this->l('Edit', 'Helper');
            }

            $tpl->assign(array(
                'href' => $this->context->link->getAdminLink('AdminCarrierWizard').'&id_carrier='.(int)$id,
                'action' => self::$cache_lang['Edit'],
                'id' => $id
            ));

            return $tpl->fetch();
        } else {
            return;
        }
    }

    public function displayDeleteLink($token = null, $id, $name = null)
    {
        if ($this->tabAccess['delete'] == 1) {
            $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');

            if (!array_key_exists('Delete', self::$cache_lang)) {
                self::$cache_lang['Delete'] = $this->l('Delete', 'Helper');
            }

            if (!array_key_exists('DeleteItem', self::$cache_lang)) {
                self::$cache_lang['DeleteItem'] = $this->l('Delete selected item?', 'Helper');
            }

            if (!array_key_exists('Name', self::$cache_lang)) {
                self::$cache_lang['Name'] = $this->l('Name:', 'Helper');
            }

            if (!is_null($name)) {
                $name = '\n\n'.self::$cache_lang['Name'].' '.$name;
            }

            $data = array(
                $this->identifier => $id,
                'href' => $this->context->link->getAdminLink('AdminCarriers').'&id_carrier='.(int)$id.'&deletecarrier=1',
                'action' => self::$cache_lang['Delete'],
            );

            if ($this->specificConfirmDelete !== false) {
                $data['confirm'] = !is_null($this->specificConfirmDelete) ? '\r'.$this->specificConfirmDelete : addcslashes(Tools::htmlentitiesDecodeUTF8(self::$cache_lang['DeleteItem'].$name), '\'');
            }

            $tpl->assign(array_merge($this->tpl_delete_link_vars, $data));

            return $tpl->fetch();
        } else {
            return;
        }
    }

    protected function initTabModuleList()
    {
        if (Tools::isSubmit('onboarding_carrier')) {
            parent::initTabModuleList();
            $this->filter_modules_list = $this->tab_modules_list['default_list'] = $this->tab_modules_list['slider_list'];
        }
    }
}
