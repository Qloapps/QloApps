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

class AdminRoomStatusesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_booking_status';
        $this->className = 'HotelBookingStatus';
        $this->identifier = 'id_booking_status';
        $this->context = Context::getContext();
        $this->lang = true;

        parent::__construct();
        $this->_new_list_header_design = true;

        // edit only — the 5 statuses are fixed (their IDs are the
        // HotelBookingDetail::STATUS_* constants used everywhere), no add/delete
        $this->addRowAction('edit');

        $this->fields_list = array(
            'id_booking_status' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'center',
                'color' => 'color',
            ),
            'is_terminal' => array(
                'title' => $this->l('Terminal (final) status'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'getTerminalStatusLabel',
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ),
        );
    }

    public function getTerminalStatusLabel($value)
    {
        return $value ? $this->l('Yes') : $this->l('No');
    }

    public function initToolbar()
    {
        // no "Add new" toolbar button — statuses are fixed
        $this->toolbar_btn = array();
    }

    public function postProcess()
    {
        // server-side guard, not just hidden buttons: this table's 5 rows are fixed
        if (Tools::isSubmit('add'.$this->table)
            || Tools::isSubmit('delete'.$this->table)
            || Tools::isSubmit('submitBulkdelete'.$this->table)
        ) {
            $this->errors[] = $this->l('Room statuses cannot be added or deleted.');
            return;
        }

        parent::postProcess();
    }

    public function renderForm()
    {
        if (!$this->loadObject()) {
            return;
        }

        // decoupled from the real 'is_terminal' field name on purpose: copyFromPost()
        // writes any POST key matching a real object property, and 'disabled' on the
        // switch only blocks submission client-side (removable via devtools). Using
        // a name with no matching property makes server-side tampering impossible,
        // not just hidden.
        $this->fields_value['is_terminal_display'] = $this->object->is_terminal;

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Room Status'),
                'icon' => 'icon-bookmark',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'col' => 4,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Color'),
                    'name' => 'color',
                    'required' => true,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Terminal (final) status'),
                    'name' => 'is_terminal_display',
                    'hint' => $this->l('If set to Yes, a room with this status cannot be edited, only deleted. If No, the room can still be edited. This setting is fixed for each status, so it is disabled here.'),
                    'disabled' => true,
                    'values' => array(
                        array(
                            'id' => 'is_terminal_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'is_terminal_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        if (!$this->loadObject()) {
            return;
        }

        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        if (!trim(Tools::getValue('name_'.$defaultLangId))) {
            $this->errors[] = $this->l('Status name is required at least in ').$objDefaultLanguage['name'];
        }

        return parent::processSave();
    }
}
