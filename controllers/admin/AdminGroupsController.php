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
 * @property Group $object
 */
class AdminGroupsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'group';
        $this->className = 'Group';
        $this->list_id = 'group';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $groups_to_keep = array(
            Configuration::get('PS_UNIDENTIFIED_GROUP'),
            Configuration::get('PS_GUEST_GROUP'),
            Configuration::get('PS_CUSTOMER_GROUP')
        );

        $this->addRowActionSkipList('delete', $groups_to_keep);

        parent::__construct();

        $this->_select .= '(SELECT COUNT(jcg.`id_customer`)
		FROM `'._DB_PREFIX_.'customer_group` jcg
		LEFT JOIN `'._DB_PREFIX_.'customer` jc ON (jc.`id_customer` = jcg.`id_customer`)
		WHERE jc.`deleted` != 1
		'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
		AND jcg.`id_group` = a.`id_group`) AS nb';
        $this->_use_found_rows = false;

        $groups = Group::getGroups(Context::getContext()->language->id, true);
        if (Shop::isFeatureActive()) {
            $this->fields_options = array(
                'general' => array(
                    'title' =>    $this->l('Default groups options'),
                    'fields' =>    array(
                        'PS_UNIDENTIFIED_GROUP' => array(
                            'title' => $this->l('Visitors group'),
                            'desc' => $this->l('The group defined for your un-identified visitors.'),
                            'cast' => 'intval',
                            'type' => 'select',
                            'list' => $groups,
                            'identifier' => 'id_group'
                        ),
                        'PS_GUEST_GROUP' => array(
                            'title' => $this->l('Guests group'),
                            'desc' => $this->l('The group defined for your identified guest customers (used in guest checkout).'),
                            'cast' => 'intval',
                            'type' => 'select',
                            'list' => $groups,
                            'identifier' => 'id_group'
                        ),
                        'PS_CUSTOMER_GROUP' => array(
                            'title' => $this->l('Customers group'),
                            'desc' => $this->l('The group defined for your identified registered customers.'),
                            'cast' => 'intval',
                            'type' => 'select',
                            'list' => $groups,
                            'identifier' => 'id_group'
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                    )
                ),
            );
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('fancybox');
        $this->addJqueryUi('ui.sortable');
    }

    public function initToolbar()
    {
        if ($this->display == 'add' || $this->display == 'edit') {
            $this->toolbar_btn['save-and-stay'] = array(
                'short' => 'SaveAndStay',
                'href' => '#',
                'desc' => $this->l('Save, then add a category reduction.'),
                'force_desc' => true,
            );
        }
        parent::initToolbar();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_group'] = array(
                'href' => self::$currentIndex.'&addgroup&token='.$this->token,
                'desc' => $this->l('Add new group', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initProcess()
    {
        $this->id_object = Tools::getValue('id_'.$this->table);

        if (Tools::isSubmit('changeShowPricesVal') && $this->id_object) {
            $this->action = 'change_show_prices_val';
        }

        parent::initProcess();
    }

    public function postProcess()
    {
        if (isset($_POST['submitResetcustomer_group'])) {
            $this->processResetFilters('customer_group');
        }
        return parent::postProcess();
    }

    public function renderView()
    {
        $this->context = Context::getContext();
        if (!($group = $this->loadObject(true))) {
            return;
        }

        $this->tpl_view_vars = array(
            'group' => $group,
            'language' => $this->context->language,
            'customerList' => $this->renderCustomersList($group),
            'categorieReductions' => $this->formatCategoryDiscountList($group->id)
        );

        return parent::renderView();
    }

    protected function renderCustomersList($group)
    {
        $genders = array(0 => '?');
        $genders_icon = array('default' => 'unknown.gif');
        foreach (Gender::getGenders() as $gender) {
            /** @var Gender $gender */
            $genders_icon[$gender->id] = '../genders/'.(int)$gender->id.'.jpg';
            $genders[$gender->id] = $gender->name;
        }
        $this->table = 'customer_group';
        $this->lang = false;
        $this->list_id = 'customer_group';
        $this->actions = array();
        $this->addRowAction('edit');
        $this->identifier = 'id_customer';
        $this->bulk_actions = false;
        $this->list_no_link = true;
        $this->explicitSelect = true;

        $this->fields_list = (array(
            'id_customer' => array('title' => $this->l('ID'), 'align' => 'center', 'filter_key' => 'c!id_customer', 'class' => 'fixed-width-xs'),
            'id_gender' => array('title' => $this->l('Social title'), 'icon' => $genders_icon, 'list' => $genders),
            'firstname' => array('title' => $this->l('First name')),
            'lastname' => array('title' => $this->l('Last name')),
            'email' => array('title' => $this->l('Email address'), 'filter_key' => 'c!email', 'orderby' => true),
            'birthday' => array('title' => $this->l('Birth date'), 'type' => 'date', 'class' => 'fixed-width-md', 'align' => 'center'),
            'date_add' => array('title' => $this->l('Registration date'), 'type' => 'date', 'class' => 'fixed-width-md', 'align' => 'center'),
            'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'class' => 'fixed-width-sm', 'type' => 'bool', 'search' => false, 'orderby' => false, 'filter_key' => 'c!active', 'callback' => 'printOptinIcon')
        ));
        $this->_select = 'c.*, a.id_group';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (a.`id_customer` = c.`id_customer`)';
        $this->_where = 'AND a.`id_group` = '.(int)$group->id.' AND c.`deleted` != 1';
        $this->_where .= Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c');
        self::$currentIndex = self::$currentIndex.'&id_group='.(int)$group->id.'&viewgroup';

        $this->processFilter();
        return parent::renderList();
    }

    public function printOptinIcon($value, $customer)
    {
        return ($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>');
    }


    public function renderForm()
    {
        if (!($group = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Customer group'),
                'icon' => 'icon-group'
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->l('Forbidden characters:').' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Discount'),
                    'name' => 'reduction',
                    'suffix' => '%',
                    'col' => 1,
                    'hint' => $this->l('Automatically apply this value as a discount on all products for members of this customer group.')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Price display method'),
                    'name' => 'price_display_method',
                    'col' => 2,
                    'hint' => $this->l('How prices are displayed in the order summary for this customer group.'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id_method' => PS_TAX_EXC,
                                'name' => $this->l('Tax excluded')
                            ),
                            array(
                                'id_method' => PS_TAX_INC,
                                'name' => $this->l('Tax included')
                            )
                        ),
                        'id' => 'id_method',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show prices'),
                    'name' => 'show_prices',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'show_prices_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'show_prices_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'hint' => $this->l('Customers in this group can view prices.')
                ),
                array(
                    'type' => 'group_discount_category',
                    'label' => $this->l('Category discount'),
                    'name' => 'reduction',
                    'values' => ($group->id ? $this->formatCategoryDiscountList((int)$group->id) : array())
                ),
                array(
                    'type' => 'modules',
                    'label' => $this->l('Modules Authorization'),
                    'name' => 'auth_modules',
                    'values' => $this->formatModuleListAuth($group->id)
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

        if (Tools::getIsset('addgroup')) {
            $this->fields_value['price_display_method'] = Configuration::get('PRICE_DISPLAY_METHOD');
        }

        $this->fields_value['reduction'] = isset($group->reduction) ? $group->reduction : 0;

        $tree = new HelperTreeCategories('categories-tree');
        $this->tpl_form_vars['categoryTreeView'] = $tree->setRootCategory((int)Category::getRootCategory()->id)->render();

        return parent::renderForm();
    }

    protected function formatCategoryDiscountList($id_group)
    {
        $group_reductions = GroupReduction::getGroupReductions((int)$id_group, $this->context->language->id);
        $category_reductions = array();
        $category_reduction = Tools::getValue('category_reduction');

        foreach ($group_reductions as $category) {
            if (is_array($category_reduction) && array_key_exists($category['id_category'], $category_reduction)) {
                $category['reduction'] = $category_reduction[$category['id_category']];
            }

            $category_reductions[(int)$category['id_category']] = array(
                'path' => getPath(Context::getContext()->link->getAdminLink('AdminCategories'), (int)$category['id_category']),
                'reduction' => (float)$category['reduction'] * 100,
                'id_category' => (int)$category['id_category']
            );
        }

        if (is_array($category_reduction)) {
            foreach ($category_reduction as $key => $val) {
                if (!array_key_exists($key, $category_reductions)) {
                    $category_reductions[(int)$key] = array(
                        'path' => getPath(Context::getContext()->link->getAdminLink('AdminCategories'), $key),
                        'reduction' => (float)$val * 100,
                        'id_category' => (int)$key
                    );
                }
            }
        }

        return $category_reductions;
    }

    public function formatModuleListAuth($id_group)
    {
        $modules = Module::getModulesInstalled();
        $authorized_modules = '';

        $auth_modules = array();
        $unauth_modules = array();

        if ($id_group) {
            $authorized_modules = Module::getAuthorizedModules($id_group);
        }

        if (is_array($authorized_modules)) {
            foreach ($modules as $module) {
                $authorized = false;
                foreach ($authorized_modules as $auth_module) {
                    if ($module['id_module'] == $auth_module['id_module']) {
                        $authorized = true;
                    }
                }

                if ($authorized) {
                    $auth_modules[] = $module;
                } else {
                    $unauth_modules[] = $module;
                }
            }
        } else {
            $auth_modules = $modules;
        }
        $auth_modules_tmp = array();
        foreach ($auth_modules as $key => $val) {
            if ($module = Module::getInstanceById($val['id_module'])) {
                $auth_modules_tmp[] = $module;
            }
        }

        $auth_modules = $auth_modules_tmp;

        $unauth_modules_tmp = array();
        foreach ($unauth_modules as $key => $val) {
            if (($tmp_obj = Module::getInstanceById($val['id_module']))) {
                $unauth_modules_tmp[] = $tmp_obj;
            }
        }

        $unauth_modules = $unauth_modules_tmp;

        return array('unauth_modules' => $unauth_modules, 'auth_modules' => $auth_modules);
    }

    public function processSave()
    {
        if (!$this->validateDiscount(Tools::getValue('reduction'))) {
            $this->errors[] = Tools::displayError('The discount value is incorrect (must be a percentage).');
        } else {
            $this->updateCategoryReduction();
            $object = parent::processSave();
            $this->updateRestrictions();
            return $object;
        }
    }

    protected function validateDiscount($reduction)
    {
        if (!Validate::isPrice($reduction) || $reduction > 100 || $reduction < 0) {
            return false;
        } else {
            return true;
        }
    }

    public function ajaxProcessAddCategoryReduction()
    {
        $category_reduction = Tools::getValue('category_reduction');
        $id_category = Tools::getValue('id_category'); //no cast validation is done with Validate::isUnsignedId($id_category)

        $result = array();
        if (!Validate::isUnsignedId($id_category)) {
            $result['errors'][] = Tools::displayError('Wrong category ID.');
            $result['hasError'] = true;
        } elseif (!$this->validateDiscount($category_reduction)) {
            $result['errors'][] = Tools::displayError('The discount value is incorrect (must be a percentage).');
            $result['hasError'] = true;
        } else {
            $result['id_category'] = (int)$id_category;
            $result['catPath'] = getPath(self::$currentIndex.'?tab=AdminCategories', (int)$id_category);
            $result['discount'] = $category_reduction;
            $result['hasError'] = false;
        }
        die(Tools::jsonEncode($result));
    }

    /**
     * Update (or create) restrictions for modules by group
     */
    protected function updateRestrictions()
    {
        $id_group = Tools::getValue('id_group');
        $auth_modules = Tools::getValue('modulesBoxAuth');
        $return = true;
        if ($id_group) {
            Group::truncateModulesRestrictions((int)$id_group);
        }
        $shops = Shop::getShops(true, null, true);
        if (is_array($auth_modules)) {
            $return &= Group::addModulesRestrictions($id_group, $auth_modules, $shops);
        }
        return $return;
    }

    protected function updateCategoryReduction()
    {
        $category_reduction = Tools::getValue('category_reduction');
        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'group_reduction`
			WHERE `id_group` = '.(int)Tools::getValue('id_group')
        );
        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'product_group_reduction_cache`
			WHERE `id_group` = '.(int)Tools::getValue('id_group')
        );
        if (is_array($category_reduction) && count($category_reduction)) {
            if (!Configuration::getGlobalValue('PS_GROUP_FEATURE_ACTIVE')) {
                Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', 1);
            }
            foreach ($category_reduction as $cat => $reduction) {
                if (!Validate::isUnsignedId($cat) || !$this->validateDiscount($reduction)) {
                    $this->errors[] = Tools::displayError('The discount value is incorrect.');
                } else {
                    $category = new Category((int)$cat);
                    $category->addGroupsIfNoExist((int)Tools::getValue('id_group'));
                    $group_reduction = new GroupReduction();
                    $group_reduction->id_group = (int)Tools::getValue('id_group');
                    $group_reduction->reduction = (float)($reduction / 100);
                    $group_reduction->id_category = (int)$cat;
                    if (!$group_reduction->save()) {
                        $this->errors[] = Tools::displayError('You cannot save group reductions.');
                    }
                }
            }
        }
    }

    /**
     * Toggle show prices flag
     */
    public function processChangeShowPricesVal()
    {
        $group = new Group($this->id_object);
        if (!Validate::isLoadedObject($group)) {
            $this->errors[] = Tools::displayError('An error occurred while updating this group.');
        }
        $update = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'group` SET show_prices = '.($group->show_prices ? 0 : 1).' WHERE `id_group` = '.(int)$group->id);
        if (!$update) {
            $this->errors[] = Tools::displayError('An error occurred while updating this group.');
        }
        Tools::clearSmartyCache();
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    /**
     * Print enable / disable icon for show prices option
     *
     * @param $id_group integer Group ID
     * @param $tr array Row data
     * @return string HTML link and icon
     */
    public function printShowPricesIcon($id_group, $tr)
    {
        $group = new Group($tr['id_group']);
        if (!Validate::isLoadedObject($group)) {
            return;
        }
        return '<a class="list-action-enable'.($group->show_prices ? ' action-enabled' : ' action-disabled').'" href="index.php?tab=AdminGroups&amp;id_group='.(int)$group->id.'&amp;changeShowPricesVal&amp;token='.Tools::getAdminTokenLite('AdminGroups').'">
				'.($group->show_prices ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
            '</a>';
    }

    public function renderList()
    {
        $unidentified = new Group(Configuration::get('PS_UNIDENTIFIED_GROUP'));
        $guest = new Group(Configuration::get('PS_GUEST_GROUP'));
        $default = new Group(Configuration::get('PS_CUSTOMER_GROUP'));

        $unidentified_group_information = sprintf(
            $this->l('%s - All persons without a customer account or customers that are not logged in.'),
            '<b>'.$unidentified->name[$this->context->language->id].'</b>'
        );
        $guest_group_information = sprintf(
            $this->l('%s - All persons who placed an order through Guest Checkout.'),
            '<b>'.$guest->name[$this->context->language->id].'</b>'
        );
        $default_group_information = sprintf(
            $this->l('%s - All persons who created an account on this site.'),
            '<b>'.$default->name[$this->context->language->id].'</b>'
        );

        $this->displayInformation($this->l('Qloapps has three default customer groups:'));
        $this->displayInformation($unidentified_group_information);
        $this->displayInformation($guest_group_information);
        $this->displayInformation($default_group_information);

        $this->fields_list = array(
            'id_group' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Group name'),
                'filter_key' => 'b!name'
            ),
            'reduction' => array(
                'title' => $this->l('Discount (%)'),
                'align' => 'right',
                'type' => 'percent'
            ),
            'nb' => array(
                'title' => $this->l('Members'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'show_prices' => array(
                'title' => $this->l('Show prices'),
                'align' => 'center',
                'type' => 'bool',
                'callback' => 'printShowPricesIcon',
                'orderby' => false
            ),
            'date_add' => array(
                'title' => $this->l('Creation date'),
                'type' => 'date',
                'align' => 'right'
            )
        );

        $this->processFilter();

        return parent::renderList();
    }

    public function displayEditLink($token = null, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
        if (!array_key_exists('Edit', self::$cache_lang)) {
            self::$cache_lang['Edit'] = $this->l('Edit', 'Helper');
        }

        $href = self::$currentIndex.'&'.$this->identifier.'='.$id.'&update'.$this->table.'&token='.($token != null ? $token : $this->token);

        if ($this->display == 'view') {
            $href = Context::getContext()->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$id.'&updatecustomer&back='.urlencode($href);
        }

        $tpl->assign(array(
            'href' => $href,
            'action' => self::$cache_lang['Edit'],
            'id' => $id
        ));

        return $tpl->fetch();
    }
}
