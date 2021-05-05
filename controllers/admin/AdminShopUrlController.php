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
 * @property ShopUrl $object
 */
class AdminShopUrlControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'shop_url';
        $this->className = 'ShopUrl';
        $this->lang = false;
        $this->requiredDatabase = true;
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->bulk_actions = array();

        /* if $_GET['id_shop'] is transmitted, virtual url can be loaded in config.php, so we wether transmit shop_id in herfs */
        if ($this->id_shop = (int)Tools::getValue('shop_id')) {
            $_GET['id_shop'] = $this->id_shop;
        } else {
            $this->id_shop = (int)Tools::getValue('id_shop');
        }

        $this->context = Context::getContext();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->fields_list = array(
            'id_shop_url' => array(
                'title' => $this->l('Shop URL ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'shop_name' => array(
                'title' => $this->l('Shop name'),
                'filter_key' => 's!name'
            ),
            'url' => array(
                'title' => $this->l('URL'),
                'filter_key' => 'url',
                'havingFilter' => true
            ),
            'main' => array(
                'title' => $this->l('Is it the main URL?'),
                'align' => 'center',
                'activeVisu' => 'main',
                'active' => 'main',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'main',
                'class' => 'fixed-width-md'
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'active',
                'class' => 'fixed-width-md'
            ),
        );

        parent::__construct();
    }

    public function viewAccess($disable = false)
    {
        return Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
    }

    public function renderList()
    {
        $this->addRowActionSkipList('delete', array(1));

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 's.name AS shop_name, CONCAT(\'http://\', a.domain, a.physical_uri, a.virtual_uri) AS url';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.id_shop = a.id_shop)';

        if ($id_shop = (int)Tools::getValue('id_shop')) {
            $this->_where = 'AND a.id_shop = '.$id_shop;
        }
        $this->_use_found_rows = false;

        return parent::renderList();
    }

    public function renderForm()
    {
        $update_htaccess = Tools::modRewriteActive() && ((file_exists('.htaccess') && is_writable('.htaccess')) || is_writable(dirname('.htaccess')));

        $this->multiple_fieldsets = true;
        if (!$update_htaccess) {
            $desc_virtual_uri = array(
                '<span class="warning_mod_rewrite">'.$this->l('If you want to add a virtual URL, you need to activate URL rewriting on your web server and enable Friendly URL option.').'</span>'
            );
        } else {
            $desc_virtual_uri = array(
                $this->l('You can use this option if you want to create a store with a URL that doesn\'t exist on your server (e.g. if you want your store to be available with the URL www.example.com/my-store/shoes/, you have to set shoes/ in this field, assuming that my-store/ is your Physical URL).'),
                '<strong>'.$this->l('URL rewriting must be activated on your server to use this feature.').'</strong>'
            );
        }
        $this->fields_form = array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('URL options'),
                        'icon' => 'icon-cogs'
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Shop'),
                            'name' => 'id_shop',
                            'onchange' => 'checkMainUrlInfo(this.value);',
                            'options' => array(
                                'optiongroup' => array(
                                    'query' =>  Shop::getTree(),
                                    'label' => 'name'
                                ),
                                'options' => array(
                                    'query' => 'shops',
                                    'id' => 'id_shop',
                                    'name' => 'name'
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Is it the main URL for this shop?'),
                            'name' => 'main',
                            'is_bool' => true,
                            'class' => 't',
                            'values' => array(
                                array(
                                    'id' => 'main_on',
                                    'value' => 1
                                ),
                                array(
                                    'id' => 'main_off',
                                    'value' => 0
                                )
                            ),
                            'desc' => array(
                                $this->l('If you set this URL as the Main URL for the selected shop, all URLs set to this shop will be redirected to this URL (you can only have one Main URL per shop).'),
                                array(
                                    'text' => $this->l('Since the selected shop has no main URL, you have to set this URL as the Main URL.'),
                                    'id' => 'mainUrlInfo'
                                ),
                                array(
                                    'text' => $this->l('The selected shop already has a Main URL. Therefore, if you set this one as the Main URL, the older Main URL will be set as a regular URL.'),
                                    'id' => 'mainUrlInfoExplain'
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enabled'),
                            'name' => 'active',
                            'required' => false,
                            'is_bool' => true,
                            'class' => 't',
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0
                                )
                            )
                        )
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                    ),
                ),
            ),
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Shop URL'),
                        'icon' => 'icon-shopping-cart'
                    ),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('Domain'),
                            'name' => 'domain',
                            'size' => 50,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('SSL Domain'),
                            'name' => 'domain_ssl',
                            'size' => 50,
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                    ),
                ),
            ),
        );

        if (!defined('_PS_HOST_MODE_')) {
            $this->fields_form[1]['form']['input'] = array_merge($this->fields_form[1]['form']['input'],
                array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Physical URL'),
                        'name' => 'physical_uri',
                        'desc' => $this->l('This is the physical folder for your store on the web server. Leave this field empty if your store is installed on the root path. For instance, if your store is available at www.example.com/my-store/, you must input my-store/ in this field.'),
                        'size' => 50,
                    )
                )
            );
        }

        $this->fields_form[1]['form']['input'] = array_merge($this->fields_form[1]['form']['input'],
            array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Virtual URL'),
                    'name' => 'virtual_uri',
                    'desc' => $desc_virtual_uri,
                    'size' => 50,
                    'hint' => (!$update_htaccess) ? $this->l('Warning: URL rewriting (e.g. mod_rewrite for Apache) seems to be disabled. If your Virtual URL doesn\'t work, please check with your hosting provider on how to activate URL rewriting.') : null,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Final URL'),
                    'name' => 'final_url',
                    'size' => 76,
                    'readonly' => true
                ),
            )
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        self::$currentIndex = self::$currentIndex.($obj->id ? '&shop_id='.(int)$obj->id_shop : '');

        $current_shop = Shop::initialize();

        $list_shop_with_url = array();
        foreach (Shop::getShops(false, null, true) as $id) {
            $list_shop_with_url[$id] = (bool)count(ShopUrl::getShopUrls($id));
        }

        $this->tpl_form_vars = array(
            'js_shop_url' => json_encode($list_shop_with_url)
        );

        $this->fields_value = array(
            'domain' => trim(Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'domain') : $current_shop->domain),
            'domain_ssl' => trim(Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'domain_ssl') : $current_shop->domain_ssl),
            'physical_uri' => trim(Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'physical_uri') : $current_shop->physical_uri),
            'active' => trim(Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'active') : true)
        );

        return parent::renderForm();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if ($this->display != 'add' && $this->display != 'edit') {
            if ($this->id_object) {
                $this->loadObject();
            }

            if (!$this->id_shop && $this->object && $this->object->id_shop) {
                $this->id_shop = $this->object->id_shop;
            }

            $this->page_header_toolbar_btn['edit'] = array(
                'desc' => $this->l('Edit this shop'),
                'href' => $this->context->link->getAdminLink('AdminShop').'&updateshop&shop_id='.(int)$this->id_shop,
            );

            $this->page_header_toolbar_btn['new'] = array(
                'desc' => $this->l('Add a new URL'),
                'href' => $this->context->link->getAdminLink('AdminShopUrl').'&add'.$this->table.'&shop_id='.(int)$this->id_shop,
            );
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if ($this->display != 'add' && $this->display != 'edit') {
            if ($this->id_object) {
                $this->loadObject();
            }

            if (!$this->id_shop && $this->object && $this->object->id_shop) {
                $this->id_shop = $this->object->id_shop;
            }

            $this->toolbar_btn['new'] = array(
                'desc' => $this->l('Add a new URL'),
                'href' => $this->context->link->getAdminLink('AdminShopUrl').'&add'.$this->table.'&shop_id='.(int)$this->id_shop,
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

        if (!$this->display && $this->id_shop) {
            $shop = new Shop($this->id_shop);
            $this->toolbar_title[] = $shop->name;
        }

        $this->context->smarty->assign(array(
            'toolbar_scroll' => 1,
            'toolbar_btn' => $this->toolbar_btn,
            'title' => $this->toolbar_title,
            'shops_tree' => $shops_tree
        ));
    }

    public function postProcess()
    {
        $token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

        $result = true;

        if ((Tools::isSubmit('status'.$this->table) || Tools::isSubmit('status')) && Tools::getValue($this->identifier)) {
            if ($this->tabAccess['edit'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    /** @var ShopUrl $object */
                    if ($object->main) {
                        $this->errors[] = Tools::displayError('You cannot disable the Main URL.');
                    } elseif ($object->toggleStatus()) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$token);
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred while updating the status.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('main'.$this->table) && Tools::getValue($this->identifier)) {
            if ($this->tabAccess['edit'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    /** @var ShopUrl $object */
                    if (!$object->main) {
                        $result = $object->setMain();
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$token);
                    } else {
                        $this->errors[] = Tools::displayError('You cannot change a main URL to a non-main URL. You have to set another URL as your Main URL for the selected shop.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } else {
            $result = parent::postProcess();
        }

        if ($this->redirect_after) {
            $this->redirect_after .= '&shop_id='.(int)$this->id_shop;
        }

        return $result;
    }

    public function processSave()
    {
        /** @var ShopUrl $object */
        $object = $this->loadObject(true);
        if ($object->canAddThisUrl(Tools::getValue('domain'), Tools::getValue('domain_ssl'), Tools::getValue('physical_uri'), Tools::getValue('virtual_uri'))) {
            $this->errors[] = Tools::displayError('A shop URL that uses this domain already exists.');
        }

        $unallowed = str_replace('/', '', Tools::getValue('virtual_uri'));
        if ($unallowed == 'c' || $unallowed == 'img' || is_numeric($unallowed)) {
            $this->errors[] = sprintf(Tools::displayError('A shop virtual URL can not be "%s"'), $unallowed);
        }
        $return = parent::processSave();
        if (!$this->errors) {
            Tools::generateHtaccess();
            Tools::clearSmartyCache();
            Media::clearCache();
        }

        return $return;
    }

    public function processAdd()
    {
        /** @var ShopUrl $object */
        $object = $this->loadObject(true);

        if ($object->canAddThisUrl(Tools::getValue('domain'), Tools::getValue('domain_ssl'), Tools::getValue('physical_uri'), Tools::getValue('virtual_uri'))) {
            $this->errors[] = Tools::displayError('A shop URL that uses this domain already exists.');
        }

        if (Tools::getValue('main') && !Tools::getValue('active')) {
            $this->errors[] = Tools::displayError('You cannot disable the Main URL.');
        }

        return parent::processAdd();
    }

    public function processUpdate()
    {
        $this->redirect_shop_url = false;
        $current_url = parse_url($_SERVER['REQUEST_URI']);
        if (trim(dirname(dirname($current_url['path'])), '/') == trim($this->object->getBaseURI(), '/')) {
            $this->redirect_shop_url = true;
        }

        /** @var ShopUrl $object */
        $object = $this->loadObject(true);

        if ($object->main && !Tools::getValue('main')) {
            $this->errors[] = Tools::displayError('You cannot change a main URL to a non-main URL. You have to set another URL as your Main URL for the selected shop.');
        }

        if (($object->main || Tools::getValue('main')) && !Tools::getValue('active')) {
            $this->errors[] = Tools::displayError('You cannot disable the Main URL.');
        }

        return parent::processUpdate();
    }

    /**
     * @param ShopUrl $object
     * @return void
     */
    protected function afterUpdate($object)
    {
        if ($object->id && Tools::getValue('main')) {
            $object->setMain();
        }

        if ($this->redirect_shop_url) {
            $this->redirect_after = $object->getBaseURI().basename(_PS_ADMIN_DIR_).'/'.$this->context->link->getAdminLink('AdminShopUrl');
        }
    }

    /**
     * @param string $token
     * @param int $id
     * @param string $name
     * @return mixed
     */
    public function displayDeleteLink($token = null, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');

        if (!array_key_exists('Delete', self::$cache_lang)) {
            self::$cache_lang['Delete'] = $this->l('Delete', 'Helper');
        }

        if (!array_key_exists('DeleteItem', self::$cache_lang)) {
            self::$cache_lang['DeleteItem'] = $this->l('Delete selected item?', 'Helper');
        }

        if (!array_key_exists('Name', self::$cache_lang)) {
            self::$cache_lang['Name'] = $this->l('Name:', 'Helper');
        }

        if (!is_null($name)) {
            $name = '\n\n'.self::$cache_lang['Name'].' '.$name;
        }

        $data = array(
            $this->identifier => $id,
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&shop_id='.(int)$this->id_shop.'&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['Delete'],
        );

        if ($this->specificConfirmDelete !== false) {
            $data['confirm'] = !is_null($this->specificConfirmDelete) ? '\r'.$this->specificConfirmDelete : self::$cache_lang['DeleteItem'].$name;
        }

        $tpl->assign(array_merge($this->tpl_delete_link_vars, $data));

        return $tpl->fetch();
    }
}
