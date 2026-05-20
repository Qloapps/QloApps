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

class AdminRoomTypeSellingObjectsController extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'room_type_selling_object';
        $this->className = 'RoomTypeSellingObject';
        $this->identifier = 'id_room_type_selling_object';
        $this->lang = true;
        $this->context = Context::getContext();
        $this->toolbar_title = $this->l('Room Type Selling Objects');

        parent::__construct();

        $this->fields_list = array(
            'id_room_type_selling_object' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'left',
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
                'icon' => 'icon-trash',
            ),
        );
    }

    public function initPageHeaderToolbar()
    {
        if (!$this->display || $this->display == 'list') {
            $this->page_header_toolbar_btn['new_room_selling_type'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add new Selling Object', null, null, false),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Room Type Selling Object'),
                'icon' => 'icon-tags',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Displayed name for this room type selling object.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'is_bool' => true,
                    'default_value' => 1,
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

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $languages = Language::getLanguages(false);
            $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
            $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
            $idSellingObject = Tools::getValue($this->identifier);

            if (!trim(Tools::getValue('name_'.$defaultLangId))) {
                $this->errors[] = $this->l('Name is required at least in ').$objDefaultLanguage['name'];
            } else {
                foreach ($languages as $lang) {
                    if (trim(Tools::getValue('name_'.$lang['id_lang']))) {
                        if (!Validate::isGenericName(Tools::getValue('name_'.$lang['id_lang']))) {
                            $this->errors[] = $this->l('Invalid Name in ').$lang['name'];
                        }
                    }
                }
            }

            if ($idSellingObject) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }

            if (!$this->errors) {
                parent::postProcess();
            }
        } else {
            parent::postProcess();
        }
    }
}
