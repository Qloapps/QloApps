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
* Do not edit or add to this file if you wish to upgrade QloApps to newer
* versions in the future. If you wish to customize QloApps for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class AdminHotelFixedTaxControllerCore extends AdminController
{
    private $cachedHotelBranches = null;

    public function __construct()
    {
        $this->table      = 'htl_fixed_tax';
        $this->className  = 'HotelFixedTax';
        $this->bootstrap  = true;
        $this->identifier = 'id_fixed_tax';
        $this->lang       = false;

        parent::__construct();

        $this->toolbar_title = $this->l('Fixed Taxes');

        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_fixed_tax_lang` ftl
            ON (a.`id_fixed_tax` = ftl.`id_fixed_tax` AND ftl.`id_lang` = '.(int)$this->context->language->id.')';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_branch_info` hbi
            ON (hbi.`id` = a.`id_hotel`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
            ON (hbl.`id` = a.`id_hotel` AND hbl.`id_lang` = '.(int)$this->context->language->id.')';

        $this->_select .= ' a.`id_hotel`, ftl.`name` AS tax_name, hbl.`hotel_name`';

        $priceCalcTypeList = array(
            HotelFixedTax::FIXED_TAX_CALCULATION_METHOD_PER_STAY  => $this->l('Per Stay'),
            HotelFixedTax::FIXED_TAX_CALCULATION_METHOD_PER_NIGHT => $this->l('Per Night'),
        );

        $hotelList = array();
        foreach ($this->getHotelBranches() as $hotel) {
            $hotelList[$hotel['id']] = $hotel['hotel_name'];
        }

        $this->fields_list = array(
            'id_fixed_tax' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'hotel_name' => array(
                'title'        => $this->l('Hotel'),
                'align'        => 'left',
                'havingFilter' => true,
                'filter_key'   => 'hotel_name',
                'callback'     => 'formatHotelName',
            ),
            'tax_name' => array(
                'title'        => $this->l('Name'),
                'align'        => 'left',
                'havingFilter' => true,
                'filter_key'   => 'tax_name',
            ),
            'amount' => array(
                'title'    => $this->l('Amount'),
                'align'    => 'center',
                'callback' => 'formatAmount',
            ),
            'price_calc_type' => array(
                'title'      => $this->l('Calc. Type'),
                'align'      => 'center',
                'type'       => 'select',
                'list'       => $priceCalcTypeList,
                'filter_key' => 'price_calc_type',
                'callback'   => 'formatCalcType',
            ),
            'occupancy_based_price' => array(
                'title'    => $this->l('Occupancy Based'),
                'align'    => 'center',
                'type'     => 'bool',
                'orderby'  => false,
                'callback' => 'formatOccupancy',
            ),
            'active' => array(
                'title'  => $this->l('Active'),
                'align'  => 'center',
                'active' => 'status',
                'type'   => 'bool',
                'class'  => 'fixed-width-xs',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text'    => $this->l('Delete selected'),
                'icon'    => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
    }

    public function formatHotelName($hotelName, $row)
    {
        $link = $this->context->link->getAdminLink('AdminAddHotel')
              . '&id=' . (int)$row['id_hotel'] . '&updatehtl_branch_info';
        return '<a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($hotelName) . '</a>';
    }

    public function formatAmount($amount, $row)
    {
        return Tools::displayPrice((float)$amount);
    }

    public function formatCalcType($type, $row)
    {
        if ((int)$type === HotelFixedTax::FIXED_TAX_CALCULATION_METHOD_PER_NIGHT) {
            return '<span class="badge badge-success">' . $this->l('Per Night') . '</span>';
        }
        return '<span class="badge badge-info">' . $this->l('Per Stay') . '</span>';
    }

    public function formatOccupancy($value, $row)
    {
        if ($value) {
            return '<span class="badge badge-success">' . $this->l('Yes') . '</span>';
        }
        return '<span class="badge badge-default">' . $this->l('No') . '</span>';
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('Add New Fixed Tax'),
            'icon' => 'process-icon-new',
        );
        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_PS_JS_DIR_ . 'admin/AdminHotelFixedTax.js');
    }

    private function getHotelBranches()
    {
        if ($this->cachedHotelBranches === null) {
            $obj = new HotelBranchInformation();
            $this->cachedHotelBranches = $obj->getActiveHotelBranchesInfo() ?: array();
        }
        return $this->cachedHotelBranches;
    }

    public function renderForm()
    {
        $languages = Language::getLanguages(false);

        $hotelList = array();
        foreach ($this->getHotelBranches() as $hotel) {
            $hotelList[] = array(
                'id'   => (int)$hotel['id'],
                'name' => $hotel['hotel_name'],
            );
        }

        $priceCalcTypeList = array(
            array(
                'id'   => HotelFixedTax::FIXED_TAX_CALCULATION_METHOD_PER_STAY,
                'name' => $this->l('Per Stay'),
            ),
            array(
                'id'   => HotelFixedTax::FIXED_TAX_CALCULATION_METHOD_PER_NIGHT,
                'name' => $this->l('Per Night'),
            ),
        );

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Fixed Tax'),
                'icon'  => 'icon-money',
            ),
            'input' => array(
                array(
                    'type'     => 'switch',
                    'label'    => $this->l('Active'),
                    'name'     => 'active',
                    'required' => false,
                    'is_bool'  => true,
                    'values'   => array(
                        array('id' => 'active_on',  'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'active_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type'     => 'select',
                    'label'    => $this->l('Hotel'),
                    'name'     => 'id_hotel',
                    'required' => true,
                    'options'  => array(
                        'query'   => $hotelList,
                        'id'      => 'id',
                        'name'    => 'name',
                        'default' => array('value' => 0, 'label' => $this->l('-- Select Hotel --')),
                    ),
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Tax Name'),
                    'name'     => 'name',
                    'lang'     => true,
                    'required' => true,
                    'class'    => 'col-lg-4',
                    'hint'     => $this->l('E.g. Tourism Tax, City Tax, Resort Fee'),
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Amount'),
                    'name'     => 'amount',
                    'required' => true,
                    'class'    => 'col-lg-4',
                    'prefix'   => $this->context->currency->sign,
                    'hint'     => $this->l('Flat monetary amount (not a percentage)'),
                ),
                array(
                    'type'    => 'select',
                    'label'   => $this->l('Calculation Type'),
                    'name'    => 'price_calc_type',
                    'class'   => 'col-lg-4',
                    'options' => array(
                        'query' => $priceCalcTypeList,
                        'id'    => 'id',
                        'name'  => 'name',
                    ),
                    'hint' => $this->l('Per Stay: charged once for the booking. Per Night: charged for each night.'),
                ),
                array(
                    'type'    => 'switch',
                    'label'   => $this->l('Occupancy Based'),
                    'name'    => 'occupancy_based_price',
                    'hint'    => $this->l('Multiply amount by number of eligible guests'),
                    'is_bool' => true,
                    'values'  => array(
                        array('id' => 'occ_on',  'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'occ_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type'             => 'switch',
                    'label'            => $this->l('Apply on Children'),
                    'name'             => 'apply_on_child',
                    'hint'             => $this->l('Count children in the occupancy multiplier'),
                    'is_bool'          => true,
                    'form_group_class' => 'wk-occupancy-sub-fields',
                    'values'           => array(
                        array('id' => 'child_on',  'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'child_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type'             => 'switch',
                    'label'            => $this->l('Apply on Infants'),
                    'name'             => 'apply_on_infant',
                    'is_bool'          => true,
                    'form_group_class' => 'wk-occupancy-sub-fields',
                    'values'           => array(
                        array('id' => 'infant_on',  'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'infant_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        if ($this->display === 'add') {
            $this->object          = new HotelFixedTax();
            $this->object->active  = 1;
            $this->object->id_hotel = (int)Tools::getValue('id_hotel', 0);
        }

        return parent::renderForm();
    }

    public function processSave()
    {
        $idHotel       = (int)Tools::getValue('id_hotel');
        $amount        = Tools::getValue('amount');
        $defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $defaultLang   = Language::getLanguage($defaultLangId);
        $languages     = Language::getLanguages(false);

        if (!$idHotel) {
            $this->errors[] = $this->l('Please select a hotel.');
        }

        if (!Validate::isPrice($amount) || (float)$amount <= 0) {
            $this->errors[] = $this->l('Please enter a valid amount greater than zero.');
        }

        if (!trim(Tools::getValue('name_' . $defaultLangId))) {
            $this->errors[] = $this->l('Tax name is required in ') . $defaultLang['name'];
        } else {
            foreach ($languages as $lang) {
                $nameVal = Tools::getValue('name_' . $lang['id_lang']);
                if (trim($nameVal) && !Validate::isGenericName($nameVal)) {
                    $this->errors[] = $this->l('Invalid tax name in ') . $lang['name'];
                }
            }

            if ($idHotel) {
                $nameDefault    = Tools::getValue('name_' . $defaultLangId);
                $idFixedTaxCurrent = (int)Tools::getValue('id_fixed_tax');
                if (HotelFixedTax::nameExists($nameDefault, $idHotel, $defaultLangId, $idFixedTaxCurrent)) {
                    $this->errors[] = $this->l('A fixed tax with this name already exists for this hotel.');
                }
            }
        }

        if ($this->errors) {
            return false;
        }

        $idFixedTax  = (int)Tools::getValue('id_fixed_tax');
        $objFixedTax = ($idFixedTax && Validate::isLoadedObject($obj = new HotelFixedTax($idFixedTax)))
            ? $obj
            : new HotelFixedTax();

        $objFixedTax->id_hotel              = $idHotel;
        $objFixedTax->amount                = (float)$amount;
        $objFixedTax->price_calc_type       = (int)Tools::getValue('price_calc_type');
        $objFixedTax->occupancy_based_price = (int)Tools::getValue('occupancy_based_price');
        $objFixedTax->apply_on_child        = (int)Tools::getValue('apply_on_child');
        $objFixedTax->apply_on_infant       = (int)Tools::getValue('apply_on_infant');
        $objFixedTax->active                = (int)Tools::getValue('active');

        foreach ($languages as $lang) {
            $nameVal = trim(Tools::getValue('name_' . $lang['id_lang']));
            $objFixedTax->name[$lang['id_lang']] = $nameVal
                ? $nameVal
                : Tools::getValue('name_' . $defaultLangId);
        }

        if (!$objFixedTax->save()) {
            $this->errors[] = $this->l('An error occurred while saving the fixed tax.');
            return false;
        }

        $this->confirmations[] = $this->l('Fixed tax saved successfully.');
        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }

    public function processDelete()
    {
        $idFixedTax = (int)Tools::getValue('id_fixed_tax');
        if ($idFixedTax && Validate::isLoadedObject($objFixedTax = new HotelFixedTax($idFixedTax))) {
            $objFixedTax->delete();
        }
        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }
}
