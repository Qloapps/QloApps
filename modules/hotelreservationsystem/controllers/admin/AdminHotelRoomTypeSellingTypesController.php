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

class AdminHotelRoomTypeSellingTypesController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_room_type_selling_type';
        $this->className = 'HotelRoomTypeSellingType';
        $this->identifier = 'id_htl_room_type_selling_type';
        $this->lang = true;
        $this->context = Context::getContext();
        $this->toolbar_title = $this->l('Property & Room Types');

        parent::__construct();

        $this->propertyTypes = array(
            HotelRoomTypeSellingType::HOTEL_PROPERTY_TYPE => $this->l('Property type'),
            HotelRoomTypeSellingType::ROOM_TYPE_OBJECT_SELLING_TYPE => $this->l('Room selling type'),
        );

        $this->fields_list = array(
            'id_htl_room_type_selling_type' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'left',
            ),
            'type' => array(
                'title' => $this->l('Type'),
                'align' => 'center',
                'type' => 'select',
                'list' => $this->propertyTypes,
                'filter_key' => 'a!type',
                'callback' => 'getSellingTypeLabel',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
            ),
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
    }

    public function initPageHeaderToolbar()
    {
        if (!$this->display || $this->display == 'list') {
            $this->page_header_toolbar_btn['new_type'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add new', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initToolbar()
    {
        $this->toolbar_btn = array();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $typeSelect = array();
        foreach ($this->propertyTypes as $id => $name) {
            $typeSelect[] = array('id' => $id, 'name' => $name);
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Type'),
                'icon' => 'icon-tags',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Displayed name for this type.'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'required' => true,
                    'options' => array(
                        'query' => $typeSelect,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        return parent::renderForm();
    }

    public function getSellingTypeLabel($value, $row)
    {
        return isset($this->propertyTypes[$value]) ? $this->propertyTypes[$value] : $value;
    }

}
