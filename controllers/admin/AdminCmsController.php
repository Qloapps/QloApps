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
 * @property CMS $object
 */
class AdminCmsControllerCore extends AdminController
{
    protected $category;

    public $id_cms_category;

    protected $position_identifier = 'id_cms';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'cms';
        $this->list_id = 'cms';
        $this->className = 'CMS';
        $this->lang = true;
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
        $this->fields_list = array(
            'id_cms' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'link_rewrite' => array('title' => $this->l('URL')),
            'meta_title' => array('title' => $this->l('Title'), 'filter_key' => 'b!meta_title'),
            'position' => array('title' => $this->l('Position'),'filter_key' => 'position', 'align' => 'center', 'class' => 'fixed-width-sm', 'position' => 'position'),
            'active' => array('title' => $this->l('Displayed'), 'align' => 'center', 'active' => 'status', 'class' => 'fixed-width-sm', 'type' => 'bool', 'orderby' => false)
        );

        // The controller can't be call directly
        // In this case, AdminCmsContentController::getCurrentCMSCategory() is null
        if (!AdminCmsContentController::getCurrentCMSCategory()) {
            $this->redirect_after = '?controller=AdminCmsContent&token='.Tools::getAdminTokenLite('AdminCmsContent');
            $this->redirect();
        }

        $this->_category = AdminCmsContentController::getCurrentCMSCategory();
        $this->tpl_list_vars['icon'] = 'icon-folder-close';
        $this->tpl_list_vars['title'] = sprintf($this->l('Pages in category "%s"'),
            $this->_category->name[Context::getContext()->employee->id_lang]);
        $this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'cms_category` c ON (c.`id_cms_category` = a.`id_cms_category`)';
        $this->_select = 'a.position ';
        $this->_where = ' AND c.id_cms_category = '.(int)$this->_category->id;

        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['save-and-preview'] = array(
            'href' => '#',
            'desc' => $this->l('Save and preview', null, null, false)
        );
        $this->page_header_toolbar_btn['save-and-stay'] = array(
            'short' => $this->l('Save and stay', null, null, false),
            'href' => '#',
            'desc' => $this->l('Save and stay', null, null, false),
        );

        return parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $categories = CMSCategory::getCategories($this->context->language->id, false);
        $html_categories = CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_cms_category'), 1);

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('CMS Page'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                // custom template
                array(
                    'type' => 'select_category',
                    'label' => $this->l('CMS Category'),
                    'name' => 'id_cms_category',
                    'options' => array(
                        'html' => $html_categories,
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'lang' => true,
                    'required' => true,
                    'class' => 'copyMeta2friendlyURL',
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
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' => array(
                        $this->l('To add "tags" click in the field, write something, and then press "Enter."'),
                        $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Only letters and the hyphen (-) character are allowed.')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Page content'),
                    'name' => 'content',
                    'autoload_rte' => true,
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Indexation by search engines'),
                    'name' => 'indexation',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'indexation_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'indexation_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
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
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'viewcms',
                    'type' => 'submit',
                    'title' => $this->l('Save and preview'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-preview'
                )
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (Validate::isLoadedObject($this->object)) {
            $this->context->smarty->assign('url_prev', $this->getPreviewUrl($this->object));
        }

        $this->tpl_form_vars = array(
            'active' => $this->object->active,
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        );
        return parent::renderForm();
    }

    public function renderList()
    {
        $this->_group = 'GROUP BY a.`id_cms`';
        //self::$currentIndex = self::$currentIndex.'&cms';
        $this->position_group_identifier = (int)$this->id_cms_category;

        $this->toolbar_title = $this->l('Pages in this category');
        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&id_cms_category='.(int)$this->id_cms_category.'&token='.$this->token,
            'desc' => $this->l('Add new')
        );

        return parent::renderList();
    }

    public function displayList($token = null)
    {
        /* Display list header (filtering, pagination and column names) */
        $this->displayListHeader($token);
        if (!count($this->_list)) {
            echo '<tr><td class="center" colspan="'.(count($this->fields_list) + 2).'">'.$this->l('No items found').'</td></tr>';
        }

        /* Show the content of the table */
        $this->displayListContent($token);

        /* Close list table and submit button */
        $this->displayListFooter($token);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('viewcms') && ($id_cms = (int)Tools::getValue('id_cms'))) {
            parent::postProcess();
            if (($cms = new CMS($id_cms, $this->context->language->id)) && Validate::isLoadedObject($cms)) {
                Tools::redirectAdmin(self::$currentIndex.'&id_cms='.$id_cms.'&conf=4&updatecms&token='.Tools::getAdminTokenLite('AdminCmsContent').'&url_preview=1');
            }
        } elseif (Tools::isSubmit('deletecms')) {
            if (Tools::getValue('id_cms') == Configuration::get('PS_CONDITIONS_CMS_ID')) {
                Configuration::updateValue('PS_CONDITIONS', 0);
                Configuration::updateValue('PS_CONDITIONS_CMS_ID', 0);
            }
            $cms = new CMS((int)Tools::getValue('id_cms'));
            $cms->cleanPositions($cms->id_cms_category);
            if (!$cms->delete()) {
                $this->errors[] = Tools::displayError('An error occurred while deleting the object.')
                    .' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&id_cms_category='.$cms->id_cms_category.'&conf=1&token='.Tools::getAdminTokenLite('AdminCmsContent'));
            }
        }/* Delete multiple objects */
        elseif (Tools::getValue('submitDel'.$this->table)) {
            if ($this->tabAccess['delete'] === '1') {
                if (Tools::isSubmit($this->table.'Box')) {
                    $cms = new CMS();
                    $result = true;
                    $result = $cms->deleteSelection(Tools::getValue($this->table.'Box'));
                    if ($result) {
                        $cms->cleanPositions((int)Tools::getValue('id_cms_category'));
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
        } elseif (Tools::isSubmit('submitAddcms') || Tools::isSubmit('submitAddcmsAndPreview')) {
            parent::validateRules();
            if (count($this->errors)) {
                return false;
            }
            if (!$id_cms = (int)Tools::getValue('id_cms')) {
                $cms = new CMS();
                $this->copyFromPost($cms, 'cms');
                if (!$cms->add()) {
                    $this->errors[] = Tools::displayError('An error occurred while creating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    $this->updateAssoShop($cms->id);
                }
            } else {
                $cms = new CMS($id_cms);
                $this->copyFromPost($cms, 'cms');
                if (!$cms->update()) {
                    $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    $this->updateAssoShop($cms->id);
                }
            }
            if (Tools::isSubmit('view'.$this->table)) {
                Tools::redirectAdmin(self::$currentIndex.'&id_cms='.$cms->id.'&conf=4&updatecms&token='.Tools::getAdminTokenLite('AdminCmsContent').'&url_preview=1');
            } elseif (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$cms->id.'&conf=4&update'.$this->table.'&token='.Tools::getAdminTokenLite('AdminCmsContent'));
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&id_cms_category='.$cms->id_cms_category.'&conf=4&token='.Tools::getAdminTokenLite('AdminCmsContent'));
            }
        } elseif (Tools::isSubmit('way') && Tools::isSubmit('id_cms') && (Tools::isSubmit('position'))) {
            /** @var CMS $object */
            if ($this->tabAccess['edit'] !== '1') {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            } elseif (!Validate::isLoadedObject($object = $this->loadObject())) {
                $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                    .' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
            } elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position'))) {
                $this->errors[] = Tools::displayError('Failed to update the position.');
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=4&id_cms_category='.(int)$object->id_cms_category.'&token='.Tools::getAdminTokenLite('AdminCmsContent'));
            }
        }
        /* Change object statuts (active, inactive) */
        elseif (Tools::isSubmit('statuscms') && Tools::isSubmit($this->identifier)) {
            if ($this->tabAccess['edit'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    /** @var CMS $object */
                    if ($object->toggleStatus()) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=5&id_cms_category='.(int)$object->id_cms_category.'&token='.Tools::getValue('token'));
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
        /* Delete multiple CMS content */
        elseif (Tools::isSubmit('submitBulkdeletecms')) {
            if ($this->tabAccess['delete'] === '1') {
                $this->action = 'bulkdelete';
                $this->boxes = Tools::getValue($this->table.'Box');
                if (is_array($this->boxes) && array_key_exists(0, $this->boxes)) {
                    $firstCms = new CMS((int)$this->boxes[0]);
                    $id_cms_category = (int)$firstCms->id_cms_category;
                    if (!$res = parent::postProcess(true)) {
                        return $res;
                    }
                    Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.Tools::getAdminTokenLite('AdminCmsContent').'&id_cms_category='.$id_cms_category);
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        } else {
            parent::postProcess(true);
        }
    }

    public function getPreviewUrl(CMS $cms)
    {
        $preview_url = $this->context->link->getCMSLink($cms, null, null, $this->context->language->id);
        if (!$cms->active) {
            $params = http_build_query(array(
                'adtoken' => Tools::getAdminTokenLite('AdminCmsContent'),
                'ad' => basename(_PS_ADMIN_DIR_),
                'id_employee' => (int)$this->context->employee->id
                )
            );
            $preview_url .= (strpos($preview_url, '?') === false ? '?' : '&').$params;
        }

        return $preview_url;
    }
}
