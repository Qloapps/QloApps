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

class AdminHotelBedTypesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_bed_type';
        $this->className = 'HotelBedType';
        $this->identifier = 'id_bed_type';
        $this->context = Context::getContext();
        $this->lang = true;

        parent::__construct();

        $this->_new_list_header_design = true;
         // field options for global fields
        $this->fields_options = array(
            'global' => array(
                'title' => $this->l('Dimension Unit Setting'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'WK_DIMENSION_UNIT' => array(
                        'title' => $this->l('Dimension Unit'),
                        'type' => 'textLang',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'hint' => $this->l('Enter a dimension unit for the bed types.')
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
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

    public function initContent()
    {
        parent::initContent();
        // to customize the view as per our requirements
        if ($this->display != 'add' && $this->display != 'edit') {
            $this->content = $this->renderOptions();
            $this->content .= $this->renderList();
            $this->context->smarty->assign('content', $this->content);
        }
    }

    public function initPageHeaderToolbar()
    {
        if (!$this->display || $this->display == 'list') {
            $this->page_header_toolbar_btn['new_product'] = array(
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

    public function renderList()
    {
        // added here instead of the constructor since the dimension unit will be update afer post processs.
        $this->fields_list = array(
            'id_bed_type' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'center',
            ),
            'width' => array(
                'title' => $this->l('Width'),
                'align' => 'center',
                'suffix' => Configuration::get('WK_DIMENSION_UNIT', $this->context->language->id)
            ),
            'length' => array(
                'title' => $this->l('Lenght'),
                'align' => 'center',
                'suffix' => Configuration::get('WK_DIMENSION_UNIT', $this->context->language->id)
            ),
        );
        return parent::renderList();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Bed Type'),
                'icon' => 'icon-bed'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Bed Type Name'),
                    'name' => 'name',
                    'required' => true,
                    'col' => 4,
                    'lang' => true,
                    'hint' => $this->l('Enter the name of Bed type.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Width'),
                    'name' => 'width',
                    'required' => true,
                    'col' => 4,
                    'hint' => $this->l('Enter the width of the Bed type in ').Configuration::get('WK_DIMENSION_UNIT', $this->context->language->id),
                    'suffix' => Configuration::get('WK_DIMENSION_UNIT', $this->context->language->id),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Length'),
                    'name' => 'length',
                    'col' => 4,
                    'required' => true,
                    'hint' => $this->l('Enter the length of the Bed type in ').Configuration::get('WK_DIMENSION_UNIT', $this->context->language->id),
                    'suffix' => Configuration::get('WK_DIMENSION_UNIT', $this->context->language->id),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save')
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

    public function beforeUpdateOptions()
    {
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('WK_DIMENSION_UNIT_'.$defaultLangId))
            && Tools::getValue('WK_DIMENSION_UNIT_'.$defaultLangId)
        ) {
            $this->errors[] = $this->l('Default dimension unit is required at least in ').$objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (Tools::getValue('WK_DIMENSION_UNIT_'.$lang['id_lang'])
                    && (!trim(Tools::getValue('WK_DIMENSION_UNIT_'.$lang['id_lang']))
                    || !Validate::isGenericName(Tools::getValue('WK_DIMENSION_UNIT_'.$lang['id_lang'])))
                ) {
                    $this->errors[] = $this->l('Invalid dimension unit in ').$lang['name'];
                }

                if (!trim(Tools::getValue('WK_DIMENSION_UNIT_'.$lang['id_lang']))) {
                    $_POST['WK_DIMENSION_UNIT_'.$lang['id_lang']] = Tools::getValue('WK_DIMENSION_UNIT_'.$defaultLangId);
                }
            }
        }
    }

    public function processSave()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('name_'.$defaultLangId))) {
            $this->errors[] = $this->l('Bed type name is required at least in ').$objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('name_'.$lang['id_lang'])) && !Validate::isGenericName(Tools::getValue('name_'.$lang['id_lang']))) {
                    $this->errors[] = $this->l('Invalid bed type name in ').$lang['name'];
                }
            }
        }

        if (!(float) Tools::getValue('width')) {
            $this->errors[] = $this->l('The width field is required');
        }

        if (!(float) Tools::getValue('length')) {
            $this->errors[] = $this->l('The length field is required');
        }

        return parent::processSave();
    }

}
