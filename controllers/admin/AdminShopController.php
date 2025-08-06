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
 * @property Shop $object
 */
class AdminShopControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'shop';
        $this->className = 'Shop';
        $this->multishop_context = Shop::CONTEXT_ALL;

        $this->id_shop_group = (int)Tools::getValue('id_shop_group');

        /* if $_GET['id_shop'] is transmitted, virtual url can be loaded in config.php, so we wether transmit shop_id in herfs */
        if ($this->id_shop = (int)Tools::getValue('shop_id')) {
            $_GET['id_shop'] = $this->id_shop;
        }

        $this->list_skip_actions['delete'] = array((int)Configuration::get('PS_SHOP_DEFAULT'));
        $this->fields_list = array(
            'id_shop' => array(
                'title' => $this->l('Shop ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Shop name'),
                'filter_key' => 'a!name',
                'width' => 200,
            ),
            'shop_group_name' => array(
                'title' => $this->l('Shop group'),
                'width' => 150,
                'filter_key' => 'gs!name'
            ),
            'category_name' => array(
                'title' => $this->l('Root category'),
                'width' => 150,
                'filter_key' => 'cl!name'
            ),
            'url' => array(
                'title' => $this->l('Main URL for this shop'),
                'havingFilter' => 'url',
            ),
            /*'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'active',
                'width' => 50,
            )*/
        );

        parent::__construct();
    }

    public function viewAccess($disable = false)
    {
        return Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if (!$this->display && $this->id_shop_group) {
            if ($this->id_object) {
                $this->loadObject();
            }

            if (!$this->id_shop_group && $this->object && $this->object->id_shop_group) {
                $this->id_shop_group = $this->object->id_shop_group;
            }

            $this->page_header_toolbar_btn['edit'] = array(
                'desc' => $this->l('Edit this shop group'),
                'href' => $this->context->link->getAdminLink('AdminShopGroup').'&updateshop_group&id_shop_group='
                    .$this->id_shop_group,
            );

            $this->page_header_toolbar_btn['new'] = array(
                'desc' => $this->l('Add new shop'),
                'href' => $this->context->link->getAdminLink('AdminShop').'&add'.$this->table.'&id_shop_group='
                    .$this->id_shop_group,
            );
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if ($this->display != 'edit' && $this->display != 'add') {
            if ($this->id_object) {
                $this->loadObject();
            }

            if (!$this->id_shop_group && $this->object && $this->object->id_shop_group) {
                $this->id_shop_group = $this->object->id_shop_group;
            }

            $this->toolbar_btn['new'] = array(
                'desc' => $this->l('Add new shop'),
                'href' => $this->context->link->getAdminLink('AdminShop').'&add'.$this->table.'&id_shop_group='
                    .$this->id_shop_group,
            );
        }
    }

    public function initContent()
    {
        parent::initContent();

        $this->addJqueryPlugin('cooki-plugin');
        $data = Shop::getTree();

        foreach ($data as $key_group => &$group) {
            foreach ($group['shops'] as $key_shop => &$shop) {
                $current_shop = new Shop($shop['id_shop']);
                $urls = $current_shop->getUrls();

                foreach ($urls as $key_url => &$url) {
                    $title = $url['domain'].$url['physical_uri'].$url['virtual_uri'];
                    if (strlen($title) > 23) {
                        $title = substr($title, 0, 23).'...';
                    }

                    $url['name'] = $title;
                    $shop['urls'][$url['id_shop_url']] = $url;
                }
            }
        }

        $shops_tree = new HelperTreeShops('shops-tree', $this->l('Multistore tree'));
        $shops_tree->setNodeFolderTemplate('shop_tree_node_folder.tpl')->setNodeItemTemplate('shop_tree_node_item.tpl')
            ->setHeaderTemplate('shop_tree_header.tpl')->setActions(array(
                new TreeToolbarLink(
                    'Collapse All',
                    '#',
                    '$(\'#'.$shops_tree->getId().'\').tree(\'collapseAll\'); return false;',
                    'icon-collapse-alt'),
                new TreeToolbarLink(
                    'Expand All',
                    '#',
                    '$(\'#'.$shops_tree->getId().'\').tree(\'expandAll\'); return false;',
                    'icon-expand-alt')
            ))
            ->setAttribute('url_shop_group', $this->context->link->getAdminLink('AdminShopGroup'))
            ->setAttribute('url_shop', $this->context->link->getAdminLink('AdminShop'))
            ->setAttribute('url_shop_url', $this->context->link->getAdminLink('AdminShopUrl'))
            ->setData($data);
        $shops_tree = $shops_tree->render(null, false, false);

        if ($this->display == 'edit') {
            $this->toolbar_title[] = $this->object->name;
        } elseif (!$this->display && $this->id_shop_group) {
            $group = new ShopGroup($this->id_shop_group);
            $this->toolbar_title[] = $group->name;
        }

        $this->context->smarty->assign(array(
            'toolbar_scroll' => 1,
            'toolbar_btn' => $this->toolbar_btn,
            'title' => $this->toolbar_title,
            'shops_tree' => $shops_tree
        ));
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'gs.name shop_group_name, cl.name category_name, CONCAT(\'http://\', su.domain, su.physical_uri, su.virtual_uri) AS url';
        $this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'shop_group` gs
				ON (a.id_shop_group = gs.id_shop_group)
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
				ON (a.id_category = cl.id_category AND cl.id_lang='.(int)$this->context->language->id.')
			LEFT JOIN '._DB_PREFIX_.'shop_url su
				ON a.id_shop = su.id_shop AND su.main = 1
		';
        $this->_group = 'GROUP BY a.id_shop';

        if ($id_shop_group = (int)Tools::getValue('id_shop_group')) {
            $this->_where = 'AND a.id_shop_group = '.$id_shop_group;
        }

        return parent::renderList();
    }

    public function displayAjaxGetCategoriesFromRootCategory()
    {
        if (Tools::isSubmit('id_category')) {
            $selected_cat = array((int)Tools::getValue('id_category'));
            $children = Category::getChildren((int)Tools::getValue('id_category'), $this->context->language->id);
            foreach ($children as $child) {
                $selected_cat[] = $child['id_category'];
            }

            $helper = new HelperTreeCategories('categories-tree', null, (int)Tools::getValue('id_category'), null, false);
            $this->content = $helper->setSelectedCategories($selected_cat)->setUseSearch(true)->setUseCheckBox(true)
                ->render();
        }
        parent::displayAjax();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('id_category_default')) {
            $_POST['id_category'] = Tools::getValue('id_category_default');
        }
        /*if ((Tools::isSubmit('status') ||
            Tools::isSubmit('status'.$this->table) ||
            (Tools::isSubmit('submitAdd'.$this->table) && Tools::getValue($this->identifier) && !Tools::getValue('active'))) &&
            $this->loadObject() && $this->loadObject()->active)
        {
            if (Tools::getValue('id_shop') == Configuration::get('PS_SHOP_DEFAULT'))
                $this->errors[] = Tools::displayError('You cannot disable the default shop.');
            elseif (Shop::getTotalShops() == 1)
                $this->errors[] = Tools::displayError('You cannot disable the last shop.');
        }*/

        if (Tools::isSubmit('submitAddshopAndStay') || Tools::isSubmit('submitAddshop')) {
            $shop_group = new ShopGroup((int)Tools::getValue('id_shop_group'));
            if ($shop_group->shopNameExists(Tools::getValue('name'), (int)Tools::getValue('id_shop'))) {
                $this->errors[] = Tools::displayError('You cannot have two shops with the same name in the same group.');
            }
        }

        if (count($this->errors)) {
            return false;
        }

        /** @var Shop|bool $result */
        $result = parent::postProcess();

        if ($result != false && (Tools::isSubmit('submitAddshopAndStay') || Tools::isSubmit('submitAddshop')) && (int)$result->id_category != (int)Configuration::get('PS_HOME_CATEGORY', null, null, (int)$result->id)) {
            Configuration::updateValue('PS_HOME_CATEGORY', (int)$result->id_category, false, null, (int)$result->id);
        }

        if ($this->redirect_after) {
            $this->redirect_after .= '&id_shop_group='.$this->id_shop_group;
        }

        return $result;
    }

    public function processDelete()
    {
        if (!Validate::isLoadedObject($object = $this->loadObject())) {
            $this->errors[] = Tools::displayError('Unable to load this shop.');
        } elseif (!Shop::hasDependency($object->id)) {
            $result = Category::deleteCategoriesFromShop($object->id) && parent::processDelete();
            Tools::generateHtaccess();
            return $result;
        } else {
            $this->errors[] = Tools::displayError('You can\'t delete this shop (customer and/or order dependency).');
        }

        return false;
    }

    /**
     * @param Shop $new_shop
     * @return bool
     */
    protected function afterAdd($new_shop)
    {
        $import_data = Tools::getValue('importData', array());

        // The root category should be at least imported
        $new_shop->copyShopData((int)Tools::getValue('importFromShop'), $import_data);

        // copy default data
        if (!Tools::getValue('useImportData') || (is_array($import_data) && !isset($import_data['group']))) {
            $sql = 'INSERT INTO `'._DB_PREFIX_.'group_shop` (`id_shop`, `id_group`)
					VALUES
					('.(int)$new_shop->id.', '.(int)Configuration::get('PS_UNIDENTIFIED_GROUP').'),
					('.(int)$new_shop->id.', '.(int)Configuration::get('PS_GUEST_GROUP').'),
					('.(int)$new_shop->id.', '.(int)Configuration::get('PS_CUSTOMER_GROUP').')
				';
            Db::getInstance()->execute($sql);
        }

        return parent::afterAdd($new_shop);
    }

    /**
     * @param Shop $new_shop
     * @return bool
     */
    protected function afterUpdate($new_shop)
    {
        $categories = Tools::getValue('categoryBox');

        if (!is_array($categories)) {
            $this->errors[] = $this->l('Please create some sub-categories for this root category.');
            return false;
        }

        array_unshift($categories, Configuration::get('PS_ROOT_CATEGORY'));

        if (!Category::updateFromShop($categories, $new_shop->id)) {
            $this->errors[] = $this->l('You need to select at least the root category.');
        }
        if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data)) {
            $new_shop->copyShopData((int)Tools::getValue('importFromShop'), $import_data);
        }

        if (Tools::isSubmit('submitAddshopAndStay') || Tools::isSubmit('submitAddshop')) {
            $this->redirect_after = self::$currentIndex.'&shop_id='.(int)$new_shop->id.'&conf=4&token='.$this->token;
        }

        return parent::afterUpdate($new_shop);
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $this->_where .= ' AND a.id_shop_group = '.(int)Shop::getContextShopGroupID();
        }

        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        $shop_delete_list = array();

        // don't allow to remove shop which have dependencies (customers / orders / ... )
        foreach ($this->_list as &$shop) {
            if (Shop::hasDependency($shop['id_shop'])) {
                $shop_delete_list[] = $shop['id_shop'];
            }
        }
        $this->context->smarty->assign('shops_having_dependencies', $shop_delete_list);
    }

    public function renderForm()
    {
        /** @var Shop $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Shop'),
                'icon' => 'icon-shopping-cart'
            ),
            'identifier' => 'shop_id',
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Shop name'),
                    'desc' => array($this->l('This field does not refer to the shop name visible in the front office.'),
                            sprintf($this->l('Follow %sthis link%s to edit the shop name used on the front office.'), '<a href="'.$this->context->link->getAdminLink('AdminStores').'#store_fieldset_general">', '</a>')),
                    'name' => 'name',
                    'required' => true,
                )
            )
        );

        $display_group_list = true;
        if ($this->display == 'edit') {
            $group = new ShopGroup($obj->id_shop_group);
            if ($group->share_customer || $group->share_order || $group->share_stock) {
                $display_group_list = false;
            }
        }

        if ($display_group_list) {
            $options = array();
            foreach (ShopGroup::getShopGroups() as $group) {
                /** @var ShopGroup $group */
                if ($this->display == 'edit' && ($group->share_customer || $group->share_order || $group->share_stock) && ShopGroup::hasDependency($group->id)) {
                    continue;
                }

                $options[] = array(
                    'id_shop_group' =>    $group->id,
                    'name' =>            $group->name,
                );
            }

            if ($this->display == 'add') {
                $group_desc = $this->l('Warning: You won\'t be able to change the group of this shop if this shop belongs to a group with one of these options activated: Share Customers, Share Quantities or Share Orders.');
            } else {
                $group_desc = $this->l('You can only move your shop to a shop group with all "share" options disabled -- or to a shop group with no customers/orders.');
            }

            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Shop group'),
                'desc' => $group_desc,
                'name' => 'id_shop_group',
                'options' => array(
                    'query' => $options,
                    'id' => 'id_shop_group',
                    'name' => 'name',
                ),
            );
        } else {
            $this->fields_form['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_shop_group',
                'default' => $group->name
            );
            $this->fields_form['input'][] = array(
                'type' => 'textShopGroup',
                'label' => $this->l('Shop group'),
                'desc' => $this->l('You can\'t edit the shop group because the current shop belongs to a group with the "share" option enabled.'),
                'name' => 'id_shop_group',
                'value' => $group->name
            );
        }

        $categories = Category::getRootCategories($this->context->language->id);
        $this->fields_form['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Category root'),
            'desc' => sprintf($this->l('This is the root category of the store that you\'ve created. To define a new root category for your store, %splease click here%s.'), '<a href="'.$this->context->link->getAdminLink('AdminCategories').'&addcategoryroot" target="_blank">', '</a>'),
            'name' => 'id_category',
            'options' => array(
                'query' => $categories,
                'id' => 'id_category',
                'name' => 'name'
            )
        );

        if (Tools::isSubmit('id_shop')) {
            $shop = new Shop((int)Tools::getValue('id_shop'));
            $id_root = $shop->id_category;
        } else {
            $id_root = $categories[0]['id_category'];
        }


        $id_shop = (int)Tools::getValue('id_shop');
        self::$currentIndex = self::$currentIndex.'&id_shop_group='.(int)(Tools::getValue('id_shop_group') ?
            Tools::getValue('id_shop_group') : (isset($obj->id_shop_group) ? $obj->id_shop_group : Shop::getContextShopGroupID()));
        $shop = new Shop($id_shop);
        $selected_cat = Shop::getCategories($id_shop);

        if (empty($selected_cat)) {
            // get first category root and preselect all these children
            $root_categories = Category::getRootCategories();
            $root_category = new Category($root_categories[0]['id_category']);
            $children = $root_category->getAllChildren($this->context->language->id);
            $selected_cat[] = $root_categories[0]['id_category'];

            foreach ($children as $child) {
                $selected_cat[] = $child->id;
            }
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Tools::isSubmit('id_shop')) {
            $root_category = new Category($shop->id_category);
        } else {
            $root_category = new Category($id_root);
        }

        $this->fields_form['input'][] = array(
            'type' => 'categories',
            'name' => 'categoryBox',
            'label' => $this->l('Associated categories'),
            'tree' => array(
                'id' => 'categories-tree',
                'selected_categories' => $selected_cat,
                'root_category' => $root_category->id,
                'use_search' => true,
                'use_checkbox' => true
            ),
            'desc' => $this->l('By selecting associated categories, you are choosing to share the categories between shops. Once associated between shops, any alteration of this category will impact every shop.')
        );
        /*$this->fields_form['input'][] = array(
            'type' => 'switch',
            'label' => $this->l('Enabled'),
            'name' => 'active',
            'required' => true,
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0
                )
            ),
            'desc' => $this->l('Enable or disable your store?')
        );*/

        $themes = Theme::getThemes();
        if (!isset($obj->id_theme)) {
            foreach ($themes as $theme) {
                if (isset($theme->id)) {
                    $id_theme = $theme->id;
                    break;
                }
            }
        }

        $this->fields_form['input'][] = array(
            'type' => 'theme',
            'label' => $this->l('Theme'),
            'name' => 'theme',
            'values' => $themes
        );

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        if (Shop::getTotalShops() > 1 && $obj->id) {
            $disabled = array('active' => false);
        } else {
            $disabled = false;
        }

        $import_data = array(
            'carrier' => $this->l('Carriers'),
            'cms' => $this->l('CMS pages'),
            'contact' => $this->l('Contact information'),
            'country' => $this->l('Countries'),
            'currency' => $this->l('Currencies'),
            'discount' => $this->l('Discount prices'),
            'employee' => $this->l('Employees'),
            'image' => $this->l('Images'),
            'lang' => $this->l('Languages'),
            'manufacturer' => $this->l('Manufacturers'),
            'module' => $this->l('Modules'),
            'hook_module' => $this->l('Module hooks'),
            'meta_lang' => $this->l('Meta information'),
            'product' => $this->l('Products'),
            'product_attribute' => $this->l('Product combinations'),
            'scene' => $this->l('Scenes'),
            'stock_available' => $this->l('Available quantities for sale'),
            'store' => $this->l('Stores'),
            'warehouse' => $this->l('Warehouses'),
            'webservice_account' => $this->l('Webservice accounts'),
            'attribute_group' => $this->l('Attribute groups'),
            'feature' => $this->l('Features'),
            'group' => $this->l('Customer groups'),
            'tax_rules_group' => $this->l('Tax rules groups'),
            'supplier' => $this->l('Suppliers'),
            'referrer' => $this->l('Referrers/affiliates'),
            'zone' => $this->l('Zones'),
            'cart_rule' => $this->l('Cart rules'),
        );

        // Hook for duplication of shop data
        $modules_list = Hook::getHookModuleExecList('actionShopDataDuplication');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                $import_data['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
            }
        }

        asort($import_data);

        if (!$this->object->id) {
            $this->fields_import_form = array(
                'radio' => array(
                    'type' => 'radio',
                    'label' => $this->l('Import data'),
                    'name' => 'useImportData',
                    'value' => 1
                ),
                'select' => array(
                    'type' => 'select',
                    'name' => 'importFromShop',
                    'label' => $this->l('Choose the source shop'),
                    'options' => array(
                        'query' => Shop::getShops(false),
                        'name' => 'name'
                    )
                ),
                'allcheckbox' => array(
                    'type' => 'checkbox',
                    'label' => $this->l('Choose data to import'),
                    'values' => $import_data
                ),
                'desc' => $this->l('Use this option to associate data (products, modules, etc.) the same way for each selected shop.')
            );
        }

        $this->fields_value = array(
            'id_shop_group' => (Tools::getValue('id_shop_group') ? Tools::getValue('id_shop_group') :
                ((isset($obj->id_shop_group)) ? $obj->id_shop_group : Shop::getContextShopGroupID())),
            'id_category' => (Tools::getValue('id_category') ? Tools::getValue('id_category') :
                ((isset($obj->id_category)) ? $obj->id_category : (int)Configuration::get('PS_HOME_CATEGORY'))),
            'id_theme_checked' => (isset($obj->id_theme) ? $obj->id_theme : $id_theme)
        );

        $ids_category = array();
        $shops = Shop::getShops(false);
        foreach ($shops as $shop) {
            $ids_category[$shop['id_shop']] = $shop['id_category'];
        }

        $this->tpl_form_vars = array(
            'disabled' => $disabled,
            'checked' => (Tools::getValue('addshop') !== false) ? true : false,
            'defaultShop' => (int)Configuration::get('PS_SHOP_DEFAULT'),
            'ids_category' => $ids_category,
        );
        if (isset($this->fields_import_form)) {
            $this->tpl_form_vars = array_merge($this->tpl_form_vars, array('form_import' => $this->fields_import_form));
        }

        return parent::renderForm();
    }


    /**
     * Object creation
     */
    public function processAdd()
    {
        if (!Tools::getValue('categoryBox') || !in_array(Tools::getValue('id_category'), Tools::getValue('categoryBox'))) {
            $this->errors[] = $this->l('You need to select at least the root category.');
        }

        if (Tools::isSubmit('id_category_default')) {
            $_POST['id_category'] = (int)Tools::getValue('id_category_default');
        }

        /* Checking fields validity */
        $this->validateRules();

        if (!count($this->errors)) {
            /** @var Shop $object */
            $object = new $this->className();
            $this->copyFromPost($object, $this->table);
            $this->beforeAdd($object);
            if (!$object->add()) {
                $this->errors[] = Tools::displayError('An error occurred while creating an object.').
                    ' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            }
            /* voluntary do affectation here */
            elseif (($_POST[$this->identifier] = $object->id) && $this->postImage($object->id) && !count($this->errors) && $this->_redirect) {
                $parent_id = (int)Tools::getValue('id_parent', 1);
                $this->afterAdd($object);
                $this->updateAssoShop($object->id);
                // Save and stay on same form
                if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                    $this->redirect_after = self::$currentIndex.'&shop_id='.(int)$object->id.'&conf=3&update'.$this->table.'&token='.$this->token;
                }
                // Save and back to parent
                if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent')) {
                    $this->redirect_after = self::$currentIndex.'&shop_id='.(int)$parent_id.'&conf=3&token='.$this->token;
                }
                // Default behavior (save and back)
                if (empty($this->redirect_after)) {
                    $this->redirect_after = self::$currentIndex.($parent_id ? '&shop_id='.$object->id : '').'&conf=3&token='.$this->token;
                }
            }
        }

        $this->errors = array_unique($this->errors);
        if (count($this->errors) > 0) {
            $this->display = 'add';
            return;
        }

        $object->associateSuperAdmins();

        $categories = Tools::getValue('categoryBox');
        array_unshift($categories, Configuration::get('PS_ROOT_CATEGORY'));
        Category::updateFromShop($categories, $object->id);
        if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data) && isset($import_data['product'])) {
            ini_set('max_execution_time', 7200); // like searchcron.php
            Search::indexation(true);
        }
        return $object;
    }

    public function displayEditLink($token = null, $id, $name = null)
    {
        if ($this->tabAccess['edit'] == 1) {
            $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
            if (!array_key_exists('Edit', self::$cache_lang)) {
                self::$cache_lang['Edit'] = $this->l('Edit', 'Helper');
            }

            $tpl->assign(array(
                'href' => $this->context->link->getAdminLink('AdminShop').'&shop_id='.(int)$id.'&update'.$this->table,
                'action' => self::$cache_lang['Edit'],
                'id' => $id
            ));

            return $tpl->fetch();
        } else {
            return;
        }
    }

    public function initCategoriesAssociation($id_root = null)
    {
        if (is_null($id_root)) {
            $id_root = Configuration::get('PS_ROOT_CATEGORY');
        }
        $id_shop = (int)Tools::getValue('id_shop');
        $shop = new Shop($id_shop);
        $selected_cat = Shop::getCategories($id_shop);
        if (empty($selected_cat)) {
            // get first category root and preselect all these children
            $root_categories = Category::getRootCategories();
            $root_category = new Category($root_categories[0]['id_category']);
            $children = $root_category->getAllChildren($this->context->language->id);
            $selected_cat[] = $root_categories[0]['id_category'];

            foreach ($children as $child) {
                $selected_cat[] = $child->id;
            }
        }
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Tools::isSubmit('id_shop')) {
            $root_category = new Category($shop->id_category);
        } else {
            $root_category = new Category($id_root);
        }
        $root_category = array('id_category' => $root_category->id, 'name' => $root_category->name[$this->context->language->id]);

        $helper = new Helper();
        return $helper->renderCategoryTree($root_category, $selected_cat, 'categoryBox', false, true);
    }

    public function ajaxProcessTree()
    {
        $tree = array();
        $sql = 'SELECT g.id_shop_group, g.name as group_name, s.id_shop, s.name as shop_name, u.id_shop_url, u.domain, u.physical_uri, u.virtual_uri
				FROM '._DB_PREFIX_.'shop_group g
				LEFT JOIN  '._DB_PREFIX_.'shop s ON g.id_shop_group = s.id_shop_group
				LEFT JOIN  '._DB_PREFIX_.'shop_url u ON u.id_shop = s.id_shop
				ORDER BY g.name, s.name, u.domain';
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($results as $row) {
            $id_shop_group = $row['id_shop_group'];
            $id_shop = $row['id_shop'];
            $id_shop_url = $row['id_shop_url'];

            // Group list
            if (!isset($tree[$id_shop_group])) {
                $tree[$id_shop_group] = array(
                    'data' => array(
                        'title' => '<b>'.$this->l('Group').'</b> '.$row['group_name'],
                        'icon' => 'themes/'.$this->context->employee->bo_theme.'/img/tree-multishop-groups.png',
                        'attr' => array(
                            'href' => $this->context->link->getAdminLink('AdminShop').'&id_shop_group='.$id_shop_group,
                            'title' => sprintf($this->l('Click here to display the shops in the %s shop group', 'AdminShop', false, false), $row['group_name']),
                        ),
                    ),
                    'attr' => array(
                        'id' => 'tree-group-'.$id_shop_group,
                    ),
                    'children' => array(),
                );
            }

            // Shop list
            if (!$id_shop) {
                continue;
            }

            if (!isset($tree[$id_shop_group]['children'][$id_shop])) {
                $tree[$id_shop_group]['children'][$id_shop] = array(
                    'data' => array(
                        'title' => $row['shop_name'],
                        'icon' => 'themes/'.$this->context->employee->bo_theme.'/img/tree-multishop-shop.png',
                        'attr' => array(
                            'href' => $this->context->link->getAdminLink('AdminShopUrl').'&shop_id='.(int)$id_shop,
                            'title' => sprintf($this->l('Click here to display the URLs of the %s shop', 'AdminShop', false, false), $row['shop_name']),
                        )
                    ),
                    'attr' => array(
                        'id' => 'tree-shop-'.$id_shop,
                    ),
                    'children' => array(),
                );
            }
            // Url list
            if (!$id_shop_url) {
                continue;
            }

            if (!isset($tree[$id_shop_group]['children'][$id_shop]['children'][$id_shop_url])) {
                $url = $row['domain'].$row['physical_uri'].$row['virtual_uri'];
                if (strlen($url) > 23) {
                    $url = substr($url, 0, 23).'...';
                }

                $tree[$id_shop_group]['children'][$id_shop]['children'][$id_shop_url] = array(
                    'data' => array(
                        'title' => $url,
                        'icon' => 'themes/'.$this->context->employee->bo_theme.'/img/tree-multishop-url.png',
                        'attr' => array(
                            'href' => $this->context->link->getAdminLink('AdminShopUrl').'&updateshop_url&id_shop_url='.$id_shop_url,
                            'title' => $row['domain'].$row['physical_uri'].$row['virtual_uri'],
                        )
                    ),
                    'attr' => array(
                        'id' => 'tree-url-'.$id_shop_url,
                    ),
                );
            }
        }

        // jstree need to have children as array and not object, so we use sort to get clean keys
        // DO NOT REMOVE this code, even if it seems really strange ;)
        sort($tree);
        foreach ($tree as &$groups) {
            sort($groups['children']);
            foreach ($groups['children'] as &$shops) {
                sort($shops['children']);
            }
        }

        $tree = array(array(
            'data' => array(
                'title' => '<b>'.$this->l('Shop groups list').'</b>',
                'icon' => 'themes/'.$this->context->employee->bo_theme.'/img/tree-multishop-root.png',
                'attr' => array(
                    'href' => $this->context->link->getAdminLink('AdminShopGroup'),
                    'title' => $this->l('Click here to display the list of shop groups', 'AdminShop', false, false),
                )
            ),
            'attr' => array(
                'id' => 'tree-root',
            ),
            'state' => 'open',
            'children' => $tree,
        ));

        die(json_encode($tree));
    }
}
