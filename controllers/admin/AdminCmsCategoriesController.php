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
 * @property CMSCategory $object
 */
class AdminCmsCategoriesControllerCore extends AdminController
{
    /** @var object CMSCategory() instance for navigation*/
    protected $cms_category;

    protected $position_identifier = 'id_cms_category_to_move';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->is_cms = true;
        $this->table = 'cms_category';
        $this->list_id = 'cms_category';
        $this->className = 'CMSCategory';
        $this->lang = true;
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->_orderBy = 'position';

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
        $this->tpl_list_vars['icon'] = 'icon-folder-close';
        $this->tpl_list_vars['title'] = $this->l('Categories');
        $this->fields_list = array(
        'id_cms_category' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
        'name' => array('title' => $this->l('Name'), 'width' => 'auto', 'callback' => 'hideCMSCategoryPosition', 'callback_object' => 'CMSCategory'),
        'description' => array('title' => $this->l('Description'), 'maxlength' => 90, 'orderby' => false),
        'position' => array('title' => $this->l('Position'),'filter_key' => 'position', 'align' => 'center', 'class' => 'fixed-width-sm', 'position' => 'position'),
        'active' => array(
            'title' => $this->l('Displayed'), 'class' => 'fixed-width-sm', 'active' => 'status',
            'align' => 'center','type' => 'bool', 'orderby' => false
        ));

        // The controller can't be call directly
        // In this case, AdminCmsContentController::getCurrentCMSCategory() is null
        if (!AdminCmsContentController::getCurrentCMSCategory()) {
            $this->redirect_after = '?controller=AdminCmsContent&token='.Tools::getAdminTokenLite('AdminCmsContent');
            $this->redirect();
        }

        $this->cms_category = AdminCmsContentController::getCurrentCMSCategory();
        $this->_where = ' AND `id_parent` = '.(int)$this->cms_category->id;
        $this->_select = 'position ';

        parent::__construct();
    }

    public function renderList()
    {
        $this->initToolbar();
        $this->_group = 'GROUP BY a.`id_cms_category`';
        if (isset($this->toolbar_btn['new'])) {
            $this->toolbar_btn['new']['href'] .= '&id_parent='.(int)Tools::getValue('id_cms_category');
        }
        return parent::renderList();
    }

    public function postProcess()
    {
        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $this->action = 'save';
            if ($id_cms_category = (int)Tools::getValue('id_cms_category')) {
                $this->id_object = $id_cms_category;
                if (!CMSCategory::checkBeforeMove($id_cms_category, (int)Tools::getValue('id_parent'))) {
                    $this->errors[] = Tools::displayError('The CMS Category cannot be moved here.');
                    return false;
                }
            }
            $object = parent::postProcess();
            $this->updateAssoShop((int)Tools::getValue('id_cms_category'));
            if ($object !== false) {
                if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                    if ($this->id_object) {
                        Tools::redirectAdmin(self::$currentIndex.'&update'.$this->table.'&conf=4&id_cms_category='.(int)$object->id.'&token='.Tools::getValue('token'));
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&update'.$this->table.'&conf=3&id_cms_category='.(int)$object->id.'&token='.Tools::getValue('token'));
                    }
                } else {
                    if ($this->id_object) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&id_cms_category='.(int)$object->id.'&token='.Tools::getValue('token'));
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=3&id_cms_category='.(int)$object->id.'&token='.Tools::getValue('token'));
                    }
                }
            }
            return $object;
        }
        /* Change object statuts (active, inactive) */
        elseif (Tools::isSubmit('statuscms_category') && Tools::getValue($this->identifier)) {
            if ($this->tabAccess['edit'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    if ($object->toggleStatus()) {
                        $identifier = ((int)$object->id_parent ? '&id_cms_category='.(int)$object->id_parent : '');
                        Tools::redirectAdmin(self::$currentIndex.'&conf=5'.$identifier.'&token='.Tools::getValue('token'));
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred while updating the status.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                        .' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        /* Delete object */
        elseif (Tools::isSubmit('delete'.$this->table)) {
            if ($this->tabAccess['delete'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings)) {
                    // check if request at least one object with noZeroObject
                    if (isset($object->noZeroObject) && count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1) {
                        $this->errors[] = Tools::displayError('You need at least one object.')
                            .' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
                    } else {
                        $identifier = ((int)$object->id_parent ? '&'.$this->identifier.'='.(int)$object->id_parent : '');
                        if ($this->deleted) {
                            $object->deleted = 1;
                            if ($object->update()) {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token').$identifier);
                            }
                        } elseif ($object->delete()) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token').$identifier);
                        }
                        $this->errors[] = Tools::displayError('An error occurred during deletion.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('An error occurred while deleting the object.')
                        .' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        } elseif (Tools::isSubmit('position')) {
            $object = new CMSCategory((int)Tools::getValue($this->identifier, Tools::getValue('id_cms_category_to_move', 1)));
            if ($this->tabAccess['edit'] !== '1') {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            } elseif (!Validate::isLoadedObject($object)) {
                $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                    .' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
            } elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position'))) {
                $this->errors[] = Tools::displayError('Failed to update the position.');
            } else {
                $identifier = ((int)$object->id_parent ? '&'.$this->identifier.'='.(int)$object->id_parent : '');
                $token = Tools::getAdminTokenLite('AdminCmsContent');
                Tools::redirectAdmin(
                    self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.$identifier.'&token='.$token
                );
            }
        }
        /* Delete multiple objects */
        elseif (Tools::getValue('submitDel'.$this->table) || Tools::isSubmit('submitBulkdelete'.$this->table)) {
            if ($this->tabAccess['delete'] === '1') {
                if (Tools::isSubmit($this->table.'Box')) {
                    $cms_category = new CMSCategory();
                    $result = true;
                    $result = $cms_category->deleteSelection(Tools::getValue($this->table.'Box'));
                    if ($result) {
                        $cms_category->cleanPositions((int)Tools::getValue('id_cms_category'));
                        $token = Tools::getAdminTokenLite('AdminCmsContent');
                        Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$token.'&id_cms_category='.(int)Tools::getValue('id_cms_category'));
                    }
                    $this->errors[] = Tools::displayError('An error occurred while deleting this selection.');
                } else {
                    $this->errors[] = Tools::displayError('You must select at least one element to delete.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        }
        parent::postProcess();
    }

    public function renderForm()
    {
        $this->display = 'edit';
        $this->initToolbar();
        if (!$this->loadObject(true)) {
            return;
        }

        $categories = CMSCategory::getCategories($this->context->language->id, false);
        $html_categories = CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_parent'), 1);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('CMS Category'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'class' => 'copyMeta2friendlyURL',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Displayed'),
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
                    ),
                ),
                // custom template
                array(
                    'type' => 'select_category',
                    'label' => $this->l('Parent CMS Category'),
                    'name' => 'id_parent',
                    'options' => array(
                        'html' => $html_categories,
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Only letters and the minus (-) character are allowed.')
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

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
        return parent::renderForm();
    }
}
