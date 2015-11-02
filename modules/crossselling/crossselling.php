<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class CrossSelling extends Module
{
    protected $html;

    public function __construct()
    {
        $this->name = 'crossselling';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Cross-selling');
        $this->description = $this->l('Adds a "Customers who bought this product also bought..." section to every product page.');
        $this->ps_versions_compliancy = array('min' => '1.5.6.1', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('productFooter') ||
            !$this->registerHook('header') ||
            !$this->registerHook('shoppingCart') ||
            !$this->registerHook('actionOrderStatusPostUpdate') ||
            !Configuration::updateValue('CROSSSELLING_DISPLAY_PRICE', 0) ||
            !Configuration::updateValue('CROSSSELLING_NBR', 10)
        ) {
            return false;
        }
        $this->_clearCache('crossselling.tpl');

        return true;
    }

    public function uninstall()
    {
        $this->_clearCache('crossselling.tpl');
        if (!parent::uninstall() ||
            !Configuration::deleteByName('CROSSSELLING_DISPLAY_PRICE') ||
            !Configuration::deleteByName('CROSSSELLING_NBR')
        ) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $this->html = '';

        if (Tools::isSubmit('submitCross')) {
            if (Tools::getValue('displayPrice') != 0 && Tools::getValue('CROSSSELLING_DISPLAY_PRICE') != 1) {
                $this->html .= $this->displayError('Invalid displayPrice');
            } elseif (!($product_nbr = Tools::getValue('CROSSSELLING_NBR')) || empty($product_nbr)) {
                $this->html .= $this->displayError('You must fill in the "Number of displayed products" field.');
            } elseif ((int)$product_nbr == 0) {
                $this->html .= $this->displayError('Invalid number.');
            } else {
                Configuration::updateValue('CROSSSELLING_DISPLAY_PRICE', (int)Tools::getValue('CROSSSELLING_DISPLAY_PRICE'));
                Configuration::updateValue('CROSSSELLING_NBR', (int)Tools::getValue('CROSSSELLING_NBR'));
                $this->_clearCache('crossselling.tpl');
                $this->html .= $this->displayConfirmation($this->l('Settings updated successfully'));
            }
        }

        return $this->html.$this->renderForm();
    }

    public function hookHeader()
    {
        if (!isset($this->context->controller->php_self) || !in_array(
                $this->context->controller->php_self, array(
                    'product',
                    'order',
                    'order-opc'
                )
            )
        ) {
            return;
        }
        if (in_array($this->context->controller->php_self, array('order')) && Tools::getValue('step')) {
            return;
        }
        $this->context->controller->addCSS(($this->_path).'css/crossselling.css', 'all');
        $this->context->controller->addJS(($this->_path).'js/crossselling.js');
        $this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll', 'bxslider'));
    }

    /**
     * @param array $products_id an array of product ids
     * @return array
     */
    protected function getOrderProducts(array $products_id)
    {
        $q_orders = 'SELECT o.id_order
        FROM '._DB_PREFIX_.'orders o
        LEFT JOIN '._DB_PREFIX_.'order_detail od ON (od.id_order = o.id_order)
        WHERE o.valid = 1 AND od.product_id IN ('.implode(',', $products_id).')';
        $orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_orders);

        $final_products_list = array();

        if (count($orders) > 0) {
            $list = '';
            foreach ($orders as $order) {
                $list .= (int)$order['id_order'].',';
            }
            $list = rtrim($list, ',');

            $list_product_ids = join(',', $products_id);

            if (Group::isFeatureActive()) {
                $sql_groups_join = '
                LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = product_shop.id_category_default
                    AND cp.id_product = product_shop.id_product)
                LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.`id_category` = cg.`id_category`)';
                $groups = FrontController::getCurrentCustomerGroups();
                $sql_groups_where = 'AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '='.(int)Group::getCurrent()->id);
            }

            $order_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT DISTINCT od.product_id, pl.name, pl.description_short, pl.link_rewrite, p.reference, i.id_image, product_shop.show_price,
                    cl.link_rewrite category, p.ean13, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
                FROM '._DB_PREFIX_.'order_detail od
                LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)
                '.Shop::addSqlAssociation('product', 'p').
                (Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
                ON (p.`id_product` = pa.`id_product`)
                '.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
                '.Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop) :  Product::sqlStock('p', 'product', false,
                    $this->context->shop)).'
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = od.product_id'.Shop::addSqlRestrictionOnLang('pl').')
                LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'
                    .Shop::addSqlRestrictionOnLang('cl').')
                LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = od.product_id)
                '.(Group::isFeatureActive() ? $sql_groups_join : '').'
                WHERE od.id_order IN ('.$list.')
                AND pl.id_lang = '.(int)$this->context->language->id.'
                AND cl.id_lang = '.(int)$this->context->language->id.'
                AND od.product_id NOT IN ('.$list_product_ids.')
                AND i.cover = 1
                AND product_shop.active = 1
                '.(Group::isFeatureActive() ? $sql_groups_where : '').'
                ORDER BY RAND()
                LIMIT '.(int)Configuration::get('CROSSSELLING_NBR'));

            $tax_calc = Product::getTaxCalculationMethod();

            foreach ($order_products as &$order_product) {
                $order_product['id_product'] = (int)$order_product['product_id'];
                $order_product['image'] = $this->context->link->getImageLink($order_product['link_rewrite'],
                    (int)$order_product['product_id'].'-'.(int)$order_product['id_image'], ImageType::getFormatedName('home'));
                $order_product['link'] = $this->context->link->getProductLink((int)$order_product['product_id'], $order_product['link_rewrite'],
                    $order_product['category'], $order_product['ean13']);
                if (Configuration::get('CROSSSELLING_DISPLAY_PRICE') && ($tax_calc == 0 || $tax_calc == 2)) {
                    $order_product['displayed_price'] = Product::getPriceStatic((int)$order_product['product_id'], true, null);
                } elseif (Configuration::get('CROSSSELLING_DISPLAY_PRICE') && $tax_calc == 1) {
                    $order_product['displayed_price'] = Product::getPriceStatic((int)$order_product['product_id'], false, null);
                }
                $order_product['allow_oosp'] = Product::isAvailableWhenOutOfStock((int)$order_product['out_of_stock']);

                if (!isset($final_products_list[$order_product['product_id'].'-'.$order_product['id_image']])) {
                    $final_products_list[$order_product['product_id'].'-'.$order_product['id_image']] = $order_product;
                }
            }
        }

        return $final_products_list;
    }

    /**
     * Returns module content
     */
    public function hookshoppingCart($params)
    {
        if (!$params['products']) {
            return;
        }

        $products_id = array();
        foreach ($params['products'] as $product) {
            $products_id[] = (int)$product['id_product'];
        }

        $cache_id = 'crossselling|shoppingcart|'.implode('|', $products_id);

        if (!$this->isCached('crossselling.tpl', $this->getCacheId($cache_id))) {
            $final_products_list = $this->getOrderProducts($products_id);

            if (count($final_products_list) > 0) {
                $this->smarty->assign(
                    array(
                        'orderProducts' => $final_products_list,
                        'middlePosition_crossselling' => round(count($final_products_list) / 2, 0),
                        'crossDisplayPrice' => Configuration::get('CROSSSELLING_DISPLAY_PRICE')
                    )
                );
            }
        }

        return $this->display(__FILE__, 'crossselling.tpl', $this->getCacheId($cache_id));
    }

    public function hookProductTabContent($params)
    {
        return $this->hookProductFooter($params);
    }

    public function displayProductListReviews($params)
    {
        return $this->hookProductFooter($params);
    }

    /**
     * Returns module content for product footer
     */
    public function hookProductFooter($params)
    {
        $cache_id = 'crossselling|productfooter|'.(int)$params['product']->id;

        if (!$this->isCached('crossselling.tpl', $this->getCacheId($cache_id))) {
            $final_products_list = $this->getOrderProducts(array($params['product']->id));

            if (count($final_products_list) > 0) {
                $this->smarty->assign(
                    array(
                        'orderProducts' => $final_products_list,
                        'middlePosition_crossselling' => round(count($final_products_list) / 2, 0),
                        'crossDisplayPrice' => Configuration::get('CROSSSELLING_DISPLAY_PRICE')
                    )
                );
            }
        }

        return $this->display(__FILE__, 'crossselling.tpl', $this->getCacheId($cache_id));
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $this->_clearCache('crossselling.tpl');
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display price on products'),
                        'name' => 'CROSSSELLING_DISPLAY_PRICE',
                        'desc' => $this->l('Show the price on the products in the block.'),
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
                    array(
                        'type' => 'text',
                        'label' => $this->l('Number of displayed products'),
                        'name' => 'CROSSSELLING_NBR',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Set the number of products displayed in this block.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCross';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab
            .'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'CROSSSELLING_NBR' => Tools::getValue('CROSSSELLING_NBR', Configuration::get('CROSSSELLING_NBR')),
            'CROSSSELLING_DISPLAY_PRICE' => Tools::getValue('CROSSSELLING_DISPLAY_PRICE', Configuration::get('CROSSSELLING_DISPLAY_PRICE')),
        );
    }
}
