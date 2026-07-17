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

class AdminBookingStatusesController extends ModuleAdminController
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
                'orderby' => false,
                'search' => false,
                'class' => 'fixed-width-sm',
            ),
        );
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
            $this->errors[] = $this->l('Booking statuses cannot be added or deleted.');
            return;
        }

        parent::postProcess();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Booking Status'),
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
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        if (!$this->loadObject(true)) {
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
