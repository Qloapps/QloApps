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

class AdminHotelImageCategoryController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_image_category';
        $this->className = 'HotelImageCategory';
        $this->identifier = 'id_htl_image_category';
        $this->lang = true;
        $this->context = Context::getContext();

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_htl_image_category' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'center',
            ),
            'date_add' => array(
                'title' => $this->l('Created On'),
                'type' => 'date',
                'align' => 'right'
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        if (!$this->display || $this->display == 'list') {
            $this->page_header_toolbar_btn['new'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add new category'),
            );
        }
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Hotel Image Category'),
                'icon' => 'icon-tags',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->l('Enter the name of the hotel image category.'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
        $defaultLanguage = Language::getLanguage($defaultLangId);
        $languages = Language::getLanguages(false);

        if (!trim(Tools::getValue('name_'.$defaultLangId))) {
            $this->errors[] = $this->l('Category name is required at least in ').$defaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                $langName = trim(Tools::getValue('name_'.$lang['id_lang']));
                if ($langName && !Validate::isCatalogName($langName)) {
                    $this->errors[] = $this->l('Invalid category name in ').$lang['name'];
                }
            }
        }

        return parent::processSave();
    }

    public function initPageHeaderToolbar()
    {
        if (!$this->display || $this->display == 'list') {
            $back = $this->context->link->getAdminLink('AdminAddHotel');
            $this->page_header_toolbar_btn['back_to_list'] = array(
                'href' => $back,
                'desc' => $this->l('Back to hotel list'),
                'icon' => 'process-icon-back'
            );
        }
        parent::initPageHeaderToolbar();
    }

}
