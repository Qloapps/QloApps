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

    /**
     * List column callback: display the terminal (final) flag as a Yes/No badge.
     *
     * @param mixed $value 1 or 0
     * @return string
     */
    public function getTerminalStatusLabel($value)
    {
        if ($value) {
            return '<span class="badge badge-success">'.$this->l('Yes').'</span>';
        }

        return '<span class="badge badge-danger">'.$this->l('No').'</span>';
    }

    /**
     * Whether the terminal (final) flag of the given status can be changed by the admin.
     * Only No-show is configurable for now; every other status keeps its fixed value.
     *
     * @param int $idBookingStatus
     * @return bool
     */
    protected function isTerminalEditable($idBookingStatus)
    {
        return (int) $idBookingStatus == HotelBookingDetail::STATUS_NO_SHOW;
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

        // Only No-show gets an editable switch posting the real 'is_terminal' field.
        // Every other status shows a read-only Yes/No badge and posts nothing for it;
        // processSave() also rejects a tampered 'is_terminal' for those statuses.
        if ($this->isTerminalEditable($this->object->id)) {
            $this->fields_value['is_terminal'] = $this->object->is_terminal;
            $terminalInput = array(
                'type' => 'switch',
                'label' => $this->l('Terminal (final) status'),
                'name' => 'is_terminal',
                'hint' => $this->l('If set to Yes, a room with this status cannot be edited, only deleted. If No, the room status can still be changed, e.g. to undo an accidental no-show or for a late check-in.'),
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
            );
        } else {
            $terminalInput = array(
                'type' => 'html',
                'label' => $this->l('Terminal (final) status'),
                'name' => 'is_terminal_display',
                'hint' => $this->l('If set to Yes, a room with this status cannot be edited, only deleted. If No, the room can still be edited. This setting is fixed for this status and cannot be changed.'),
                'html_content' => '<p class="form-control-static">'.$this->getTerminalStatusLabel($this->object->is_terminal).'</p>',
            );
        }

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
                $terminalInput,
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

        if (Tools::getIsset('is_terminal')) {
            $isTerminal = Tools::getValue('is_terminal');
            if (!$this->isTerminalEditable($this->object->id)) {
                if ((int) $isTerminal != (int) $this->object->is_terminal) {
                    $this->errors[] = $this->l('Terminal (final) status can not be changed for this room status.');
                }
            } elseif (!Validate::isBool($isTerminal)) {
                $this->errors[] = $this->l('Invalid value for terminal (final) status.');
            }
        }

        if (!empty($this->errors)) {
            $this->display = 'edit';
            return false;
        }

        return parent::processSave();
    }
}
