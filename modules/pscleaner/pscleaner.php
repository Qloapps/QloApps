<?php
/*
 * 2007-2016 PrestaShop
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
 *  @copyright  2007-2016 PrestaShop SA
 *  @version  Release: $Revision: 7060 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}
require_once dirname(__FILE__).'/classes/WkPsCleanerHelper.php';

class PSCleaner extends Module
{
    public function __construct()
    {
        $this->name = 'pscleaner';
        $this->tab = 'administration';
        $this->version = '1.0.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        if (version_compare(_PS_VERSION_, '1.6.0.0 ', '>=')) {
            $this->multishop_context = Shop::CONTEXT_ALL;
        }

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('QloApps Data Cleaner');
        $this->description = $this->l('Check and fix functional integrity constraints and remove default data');
        $this->secure_key = Tools::encrypt($this->name);
    }

    protected function getMultiShopValues($key)
    {
        if (version_compare(_PS_VERSION_, '1.6.0.3', '>=') === true) {
            return Configuration::getMultiShopValues($key);
        } else {
            $shops = Shop::getShops(false, null, true);
            $id_lang = (int) $this->context->language->id;
            $results = array();
            array_push($results, Configuration::get($key));

            foreach ($shops as $id_shop) {
                array_push($results, Configuration::get($key, $id_lang, null, $id_shop));
            }

            return $results;
        }
    }

    public function getContent()
    {
        $html = '<h2>'.$this->l('Be really careful with this tool - There is no possible rollback!').'</h2>';
        if (Tools::isSubmit('submitCheckAndFix')) {
            $logs = self::checkAndFix();
            if (count($logs)) {
                $this->context->smarty->assign('logs', $logs);
                $conf = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/functional_integrity_conf.tpl');
            } else {
                $conf = $this->l('Nothing that need to be fixed please run database cleaning to clean your database');
            }
            $html .= $this->displayConfirmation($conf);
        } elseif (Tools::isSubmit('submitCleanAndOptimize')) {
            $logs = self::cleanAndOptimize();
            if (count($logs)) {
                $this->context->smarty->assign('logs', $logs);
                $conf = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/databases_cleaning_conf.tpl');
            } else {
                $conf = $this->l('Nothing that need to be cleaned');
            }
            $html .= $this->displayConfirmation($conf);
        } elseif (Tools::getValue('submitTruncateCatalog') && Tools::getValue('checkTruncateCatalog')) {
            self::truncate('catalog');
            $html .= $this->displayConfirmation($this->l('Catalog truncated successfuly please run functional Integrity constraints. to make integrity in data tables'));
        } elseif (Tools::getValue('submitTruncateSales') && Tools::getValue('checkTruncateSales')) {
            self::truncate('sales');
            $html .= $this->displayConfirmation($this->l('Orders and customers truncated successfuly please run functional Integrity constraints. to make integrity in data tables'));
        }

        $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/pscleaner_script.tpl');

        return $html.$this->renderForm();
    }

    public static function checkAndFix()
    {
        $db = Db::getInstance();
        $logs = array();

        // Remove doubles in the configuration
        $filtered_configuration = array();
        $result = $db->ExecuteS('SELECT * FROM '._DB_PREFIX_.'configuration');
        foreach ($result as $row) {
            $key = $row['id_shop_group'].'-|-'.$row['id_shop'].'-|-'.$row['name'];
            if (in_array($key, $filtered_configuration)) {
                $query = 'DELETE FROM '._DB_PREFIX_.'configuration WHERE id_configuration = '.(int)$row['id_configuration'];
                $db->Execute($query);
                $logs[$query] = 1;
            } else {
                $filtered_configuration[] = $key;
            }
        }
        unset($filtered_configuration);

        // Remove inexisting or monolanguage configuration value from configuration_lang
        $query = 'DELETE FROM `'._DB_PREFIX_.'configuration_lang`
        WHERE `id_configuration` NOT IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration`)
        OR `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE name IS NULL OR name = "")';
        if ($db->Execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        // Simple Cascade Delete
        $queries = self::getCheckAndFixQueries();

        $queries = self::bulle($queries);
        foreach ($queries as $query_array) {
            // If this is a module and the module is not installed, we continue
            if (isset($query_array[4]) && !Module::isInstalled($query_array[4])) {
                continue;
            }

            $query = 'DELETE FROM `'._DB_PREFIX_.$query_array[0].'` WHERE `'.$query_array[1].'` NOT IN (SELECT `'.$query_array[3].'` FROM `'._DB_PREFIX_.$query_array[2].'`)';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
        }

        // _lang table cleaning
        $tables = Db::getInstance()->executeS('SHOW TABLES LIKE "'.preg_replace('/([%_])/', '\\$1', _DB_PREFIX_).'%_\\_lang"');
        foreach ($tables as $table) {
            $table_lang = current($table);
            $table = str_replace('_lang', '', $table_lang);
            $id_table = 'id_'.preg_replace('/^'._DB_PREFIX_.'/', '', $table);

            $query = 'DELETE FROM `'.bqSQL($table_lang).'` WHERE `'.bqSQL($id_table).'` NOT IN (SELECT `'.bqSQL($id_table).'` FROM `'.bqSQL($table).'`)';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }

            $query = 'DELETE FROM `'.bqSQL($table_lang).'` WHERE `id_lang` NOT IN (SELECT `id_lang` FROM `'._DB_PREFIX_.'lang`)';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
        }

        // _shop table cleaning
        $tables = Db::getInstance()->executeS('SHOW TABLES LIKE "'.preg_replace('/([%_])/', '\\$1', _DB_PREFIX_).'%_\\_shop"');
        foreach ($tables as $table) {
            $table_shop = current($table);
            $table = str_replace('_shop', '', $table_shop);
            $id_table = 'id_'.preg_replace('/^'._DB_PREFIX_.'/', '', $table);

            if (in_array($table_shop, array(_DB_PREFIX_.'carrier_tax_rules_group_shop'))) {
                continue;
            }

            $query = 'DELETE FROM `'.bqSQL($table_shop).'` WHERE `'.bqSQL($id_table).'` NOT IN (SELECT `'.bqSQL($id_table).'` FROM `'.bqSQL($table).'`)';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }

            $query = 'DELETE FROM `'.bqSQL($table_shop).'` WHERE `id_shop` NOT IN (SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`)';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
        }

        // stock_available
        $query = 'DELETE FROM `'._DB_PREFIX_.'stock_available` WHERE `id_shop` NOT IN (SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`) AND `id_shop_group` NOT IN (SELECT `id_shop_group` FROM `'._DB_PREFIX_.'shop_group`)';
        if ($db->Execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        Category::regenerateEntireNtree();

        // @Todo: Remove attachment files, images...
        Image::clearTmpDir();
        self::clearAllCaches();

        return $logs;
    }

    public function truncate($case)
    {
        $db = Db::getInstance();
        $db->execute('SET FOREIGN_KEY_CHECKS = 0;');

        switch ($case) {
            case 'catalog':
                $id_home = $this->getMultiShopValues('PS_HOME_CATEGORY');
                $id_root = $this->getMultiShopValues('PS_ROOT_CATEGORY');
                $db->execute('DELETE FROM `'._DB_PREFIX_.'category` WHERE id_category NOT IN ('.implode(',', array_map('intval', $id_home)).', '.implode(',', array_map('intval', $id_root)).')');
                $db->execute('DELETE FROM `'._DB_PREFIX_.'category_lang` WHERE id_category NOT IN ('.implode(',', array_map('intval', $id_home)).', '.implode(',', array_map('intval', $id_root)).')');
                $db->execute('DELETE FROM `'._DB_PREFIX_.'category_shop` WHERE id_category NOT IN ('.implode(',', array_map('intval', $id_home)).', '.implode(',', array_map('intval', $id_root)).')');
                foreach (scandir(_PS_CAT_IMG_DIR_) as $dir) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $dir)) {
                        unlink(_PS_CAT_IMG_DIR_.$dir);
                    }
                }
                $tables = self::getCatalogRelatedTables();
                foreach ($tables as $table) {
                    $db->execute('TRUNCATE TABLE `'._DB_PREFIX_.bqSQL($table).'`');
                }
                $db->execute('DELETE FROM `'._DB_PREFIX_.'address` WHERE id_manufacturer > 0 OR id_supplier > 0 OR id_warehouse > 0');

                Image::deleteAllImages(_PS_PROD_IMG_DIR_);
                if (!file_exists(_PS_PROD_IMG_DIR_)) {
                    mkdir(_PS_PROD_IMG_DIR_);
                }

                foreach (scandir(_PS_MANU_IMG_DIR_) as $dir) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $dir)) {
                        unlink(_PS_MANU_IMG_DIR_.$dir);
                    }
                }
                foreach (scandir(_PS_SUPP_IMG_DIR_) as $dir) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $dir)) {
                        unlink(_PS_SUPP_IMG_DIR_.$dir);
                    }
                }

                // Delete qlo modules's images
                WkPsCleanerHelper::deleteFolderImages(
                    _PS_MODULE_DIR_.'wkabouthotelblock/views/img/hotel_interior/'
                );
                WkPsCleanerHelper::deleteFolderImages(
                    _PS_MODULE_DIR_.'wkhotelfeaturesblock/views/img/hotels_features_img/'
                );
                WkPsCleanerHelper::deleteFolderImages(
                    _PS_MODULE_DIR_.'wktestimonialblock/views/img/hotels_testimonials_img/'
                );
                //delete Qlo modules configurations from configuration table
                WkPsCleanerHelper::deleteModulesConfigurations();

                break;

            case 'sales':
                $tables = self::getSalesRelatedTables();

                $modules_tables = array(
                    'sekeywords' => array('sekeyword'),
                    'pagesnotfound' => array('pagenotfound'),
                    'paypal' => array('paypal_customer', 'paypal_order')
                );

                foreach ($modules_tables as $name => $module_tables) {
                    if (Module::isInstalled($name)) {
                        $tables = array_merge($tables, $module_tables);
                    }
                }

                foreach ($tables as $table) {
                    $db->execute('TRUNCATE TABLE `'._DB_PREFIX_.bqSQL($table).'`');
                }
                $db->execute('DELETE FROM `'._DB_PREFIX_.'address` WHERE id_customer > 0');
                $db->execute('UPDATE `'._DB_PREFIX_.'employee` SET `id_last_order` = 0,`id_last_customer_message` = 0,`id_last_customer` = 0');

                break;
        }
        self::clearAllCaches();
        $db->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }

    public static function cleanAndOptimize()
    {
        $logs = array();

        $query = '
        DELETE FROM `'._DB_PREFIX_.'cart`
        WHERE id_cart NOT IN (SELECT id_cart FROM `'._DB_PREFIX_.'orders`)
        AND date_add < "'.pSQL(date('Y-m-d', strtotime('-1 month'))).'"';
        if (Db::getInstance()->Execute($query)) {
            if ($affected_rows = Db::getInstance()->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $query = '
        DELETE FROM `'._DB_PREFIX_.'cart_rule`
        WHERE (
            active = 0
            OR quantity = 0
            OR date_to < "'.pSQL(date('Y-m-d')).'"
        )
        AND date_add < "'.pSQL(date('Y-m-d', strtotime('-1 month'))).'"';
        if (Db::getInstance()->Execute($query)) {
            if ($affected_rows = Db::getInstance()->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        // Delete Qlo tables
        $query = 'DELETE FROM `'._DB_PREFIX_.'htl_order_refund_rules`';
        if (Db::getInstance()->Execute($query)) {
            if ($affected_rows = Db::getInstance()->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $query = 'DELETE FROM `'._DB_PREFIX_.'htl_order_refund_rules_lang`';
        if (Db::getInstance()->Execute($query)) {
            if ($affected_rows = Db::getInstance()->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $parents = Db::getInstance()->ExecuteS('SELECT DISTINCT id_parent FROM '._DB_PREFIX_.'tab');
        foreach ($parents as $parent) {
            $children = Db::getInstance()->ExecuteS('SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE id_parent = '.(int)$parent['id_parent'].' ORDER BY IF(class_name IN ("AdminHome", "AdminDashboard"), 1, 2), position ASC');
            $i = 1;
            foreach ($children as $child) {
                $query = 'UPDATE '._DB_PREFIX_.'tab SET position = '.(int)($i++).' WHERE id_tab = '.(int)$child['id_tab'].' AND id_parent = '.(int)$parent['id_parent'];
                if (Db::getInstance()->Execute($query)) {
                    if ($affected_rows = Db::getInstance()->Affected_Rows()) {
                        $logs[$query] = $affected_rows;
                    }
                }
            }
        }

        return $logs;
    }

    protected static function bulle($array)
    {
        $sorted = false;
        $size = count($array);
        while (!$sorted) {
            $sorted = true;
            for ($i = 0; $i < $size - 1; ++$i) {
                for ($j = $i + 1; $j < $size; ++$j) {
                    if ($array[$i][2] == $array[$j][0]) {
                        $tmp = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $tmp;
                        $sorted = false;
                    }
                }
            }
        }
        return $array;
    }

    protected static function clearAllCaches()
    {
        $index = file_exists(_PS_TMP_IMG_DIR_.'index.php') ? file_get_contents(_PS_TMP_IMG_DIR_.'index.php') : '';
        Tools::deleteDirectory(_PS_TMP_IMG_DIR_, false);
        file_put_contents(_PS_TMP_IMG_DIR_.'index.php', $index);
        Context::getContext()->smarty->clearAllCache();
    }

    public function renderForm()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Catalog'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('I understand that all the catalog data will be removed without possible rollback: hotels, roomtypes, features, tags, images, prices..'),
                        'name' => 'checkTruncateCatalog',
                        'values' => array(
                            array(
                                'id' => 'checkTruncateCatalog_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'checkTruncateCatalog_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Delete Catalog'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitTruncateCatalog',
                    'id' => 'submitTruncateCatalog',
                )
            )
        );

        $fields_form_2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Orders and Customers'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('I understand that all the orders and customers will be removed without possible rollback: customers, carts, orders, connections, guests, messages, stats...'),
                        'name' => 'checkTruncateSales',
                        'values' => array(
                            array(
                                'id' => 'checkTruncateSales_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'checkTruncateSales_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Delete orders & customers'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitTruncateSales',
                    'id' => 'submitTruncateSales',
                )
            )
        );

        $fields_form_3 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Functional integrity constraints'),
                    'icon' => 'icon-cogs'
                ),
                'description' => $this->l('integrity constraint is used to protect the database from accidental damage.'),
                'submit' => array(
                    'title' => $this->l('Check & fix'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitCheckAndFix',
                )
            )
        );
        $fields_form_4 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Database Cleaning'),
                    'icon' => 'icon-cogs'
                ),
                'description' => $this->l('Cleaning your database will reclaim unused space in your tables, reducing storage space and improving table access efficiency.'),
                'submit' => array(
                    'title' => $this->l('Clean & Optimize'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitCleanAndOptimize',
                )
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form_1, $fields_form_2, $fields_form_3, $fields_form_4));
    }

    public function getConfigFieldsValues()
    {
        return array('checkTruncateSales' => 0, 'checkTruncateCatalog' => 0);
    }

    public static function getCheckAndFixQueries()
    {
        $append = array();
        return array_merge(
            $append,
            array(
                // 0 => DELETE FROM __table__, 1 => WHERE __id__ NOT IN, 2 => NOT IN __table__, 3 => __id__ used in the "NOT IN" table, 4 => module_name
                array('access', 'id_profile', 'profile', 'id_profile'),
                array('accessory', 'id_product_1', 'product', 'id_product'),
                array('accessory', 'id_product_2', 'product', 'id_product'),
                array('address_format', 'id_country', 'country', 'id_country'),
                array('attribute', 'id_attribute_group', 'attribute_group', 'id_attribute_group'),
                array('carrier_group', 'id_carrier', 'carrier', 'id_carrier'),
                array('carrier_group', 'id_group', 'group', 'id_group'),
                array('carrier_zone', 'id_carrier', 'carrier', 'id_carrier'),
                array('carrier_zone', 'id_zone', 'zone', 'id_zone'),
                array('cart_cart_rule', 'id_cart', 'cart', 'id_cart'),
                array('cart_product', 'id_cart', 'cart', 'id_cart'),
                array('cart_rule_carrier', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
                array('cart_rule_carrier', 'id_carrier', 'carrier', 'id_carrier'),
                array('cart_rule_combination', 'id_cart_rule_1', 'cart_rule', 'id_cart_rule'),
                array('cart_rule_combination', 'id_cart_rule_2', 'cart_rule', 'id_cart_rule'),
                array('cart_rule_country', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
                array('cart_rule_country', 'id_country', 'country', 'id_country'),
                array('cart_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
                array('cart_rule_group', 'id_group', 'group', 'id_group'),
                array('cart_rule_product_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
                array('cart_rule_product_rule', 'id_product_rule_group', 'cart_rule_product_rule_group', 'id_product_rule_group'),
                array('cart_rule_product_rule_value', 'id_product_rule', 'cart_rule_product_rule', 'id_product_rule'),
                array('category_group', 'id_category', 'category', 'id_category'),
                array('category_group', 'id_group', 'group', 'id_group'),
                array('category_product', 'id_category', 'category', 'id_category'),
                array('category_product', 'id_product', 'product', 'id_product'),
                array('cms', 'id_cms_category', 'cms_category', 'id_cms_category'),
                array('cms_block', 'id_cms_category', 'cms_category', 'id_cms_category', 'blockcms'),
                array('cms_block_page', 'id_cms', 'cms', 'id_cms', 'blockcms'),
                array('cms_block_page', 'id_cms_block', 'cms_block', 'id_cms_block', 'blockcms'),
                array('connections', 'id_shop_group', 'shop_group', 'id_shop_group'),
                array('connections', 'id_shop', 'shop', 'id_shop'),
                array('connections_page', 'id_connections', 'connections', 'id_connections'),
                array('connections_page', 'id_page', 'page', 'id_page'),
                array('connections_source', 'id_connections', 'connections', 'id_connections'),
                array('customer', 'id_shop_group', 'shop_group', 'id_shop_group'),
                array('customer', 'id_shop', 'shop', 'id_shop'),
                array('customer_group', 'id_group', 'group', 'id_group'),
                array('customer_group', 'id_customer', 'customer', 'id_customer'),
                array('customer_message', 'id_customer_thread', 'customer_thread', 'id_customer_thread'),
                array('customer_thread', 'id_shop', 'shop', 'id_shop'),
                array('customization', 'id_cart', 'cart', 'id_cart'),
                array('customization_field', 'id_product', 'product', 'id_product'),
                array('customized_data', 'id_customization', 'customization', 'id_customization'),
                array('delivery', 'id_shop', 'shop', 'id_shop'),
                array('delivery', 'id_shop_group', 'shop_group', 'id_shop_group'),
                array('delivery', 'id_carrier', 'carrier', 'id_carrier'),
                array('delivery', 'id_zone', 'zone', 'id_zone'),
                array('editorial', 'id_shop', 'shop', 'id_shop', 'editorial'),
                array('favorite_product', 'id_product', 'product', 'id_product', 'favoriteproducts'),
                array('favorite_product', 'id_customer', 'customer', 'id_customer', 'favoriteproducts'),
                array('favorite_product', 'id_shop', 'shop', 'id_shop', 'favoriteproducts'),
                array('feature_product', 'id_feature', 'feature', 'id_feature'),
                array('feature_product', 'id_product', 'product', 'id_product'),
                array('feature_value', 'id_feature', 'feature', 'id_feature'),
                array('group_reduction', 'id_group', 'group', 'id_group'),
                array('group_reduction', 'id_category', 'category', 'id_category'),
                array('homeslider', 'id_shop', 'shop', 'id_shop', 'homeslider'),
                array('homeslider', 'id_homeslider_slides', 'homeslider_slides', 'id_homeslider_slides', 'homeslider'),
                array('hook_module', 'id_hook', 'hook', 'id_hook'),
                array('hook_module', 'id_module', 'module', 'id_module'),
                array('hook_module_exceptions', 'id_hook', 'hook', 'id_hook'),
                array('hook_module_exceptions', 'id_module', 'module', 'id_module'),
                array('hook_module_exceptions', 'id_shop', 'shop', 'id_shop'),
                array('image', 'id_product', 'product', 'id_product'),
                array('message', 'id_cart', 'cart', 'id_cart'),
                array('message_readed', 'id_message', 'message', 'id_message'),
                array('message_readed', 'id_employee', 'employee', 'id_employee'),
                array('module_access', 'id_profile', 'profile', 'id_profile'),
                array('module_country', 'id_module', 'module', 'id_module'),
                array('module_country', 'id_country', 'country', 'id_country'),
                array('module_country', 'id_shop', 'shop', 'id_shop'),
                array('module_currency', 'id_module', 'module', 'id_module'),
                array('module_currency', 'id_currency', 'currency', 'id_currency'),
                array('module_currency', 'id_shop', 'shop', 'id_shop'),
                array('module_group', 'id_module', 'module', 'id_module'),
                array('module_group', 'id_group', 'group', 'id_group'),
                array('module_group', 'id_shop', 'shop', 'id_shop'),
                array('module_preference', 'id_employee', 'employee', 'id_employee'),
                array('orders', 'id_shop', 'shop', 'id_shop'),
                array('orders', 'id_shop_group', 'group_shop', 'id_shop_group'),
                array('order_carrier', 'id_order', 'orders', 'id_order'),
                array('order_cart_rule', 'id_order', 'orders', 'id_order'),
                array('order_detail', 'id_order', 'orders', 'id_order'),
                array('order_detail_tax', 'id_order_detail', 'order_detail', 'id_order_detail'),
                array('order_history', 'id_order', 'orders', 'id_order'),
                array('order_invoice', 'id_order', 'orders', 'id_order'),
                array('order_invoice_payment', 'id_order', 'orders', 'id_order'),
                array('order_invoice_tax', 'id_order_invoice', 'order_invoice', 'id_order_invoice'),
                array('order_return', 'id_order', 'orders', 'id_order'),
                array('order_return_detail', 'id_order_return', 'order_return', 'id_order_return'),
                array('order_slip', 'id_order', 'orders', 'id_order'),
                array('order_slip_detail', 'id_order_slip', 'order_slip', 'id_order_slip'),
                array('pack', 'id_product_pack', 'product', 'id_product'),
                array('pack', 'id_product_item', 'product', 'id_product'),
                array('page', 'id_page_type', 'page_type', 'id_page_type'),
                array('page_viewed', 'id_shop', 'shop', 'id_shop'),
                array('page_viewed', 'id_shop_group', 'shop_group', 'id_shop_group'),
                array('page_viewed', 'id_date_range', 'date_range', 'id_date_range'),
                array('product_attachment', 'id_attachment', 'attachment', 'id_attachment'),
                array('product_attachment', 'id_product', 'product', 'id_product'),
                array('product_attribute', 'id_product', 'product', 'id_product'),
                array('product_attribute_combination', 'id_product_attribute', 'product_attribute', 'id_product_attribute'),
                array('product_attribute_combination', 'id_attribute', 'attribute', 'id_attribute'),
                array('product_attribute_image', 'id_image', 'image', 'id_image'),
                array('product_attribute_image', 'id_product_attribute', 'product_attribute', 'id_product_attribute'),
                array('product_carrier', 'id_product', 'product', 'id_product'),
                array('product_carrier', 'id_shop', 'shop', 'id_shop'),
                array('product_carrier', 'id_carrier_reference', 'carrier', 'id_reference'),
                array('product_country_tax', 'id_product', 'product', 'id_product'),
                array('product_country_tax', 'id_country', 'country', 'id_country'),
                array('product_country_tax', 'id_tax', 'tax', 'id_tax'),
                array('product_download', 'id_product', 'product', 'id_product'),
                array('product_group_reduction_cache', 'id_product', 'product', 'id_product'),
                array('product_group_reduction_cache', 'id_group', 'group', 'id_group'),
                array('product_sale', 'id_product', 'product', 'id_product'),
                array('product_supplier', 'id_product', 'product', 'id_product'),
                array('product_supplier', 'id_supplier', 'supplier', 'id_supplier'),
                array('product_tag', 'id_product', 'product', 'id_product'),
                array('product_tag', 'id_tag', 'tag', 'id_tag'),
                array('range_price', 'id_carrier', 'carrier', 'id_carrier'),
                array('range_weight', 'id_carrier', 'carrier', 'id_carrier'),
                array('referrer_cache', 'id_referrer', 'referrer', 'id_referrer'),
                array('referrer_cache', 'id_connections_source', 'connections_source', 'id_connections_source'),
                array('search_index', 'id_product', 'product', 'id_product'),
                array('search_word', 'id_lang', 'lang', 'id_lang'),
                array('search_word', 'id_shop', 'shop', 'id_shop'),
                array('shop_url', 'id_shop', 'shop', 'id_shop'),
                array('specific_price_priority', 'id_product', 'product', 'id_product'),
                array('stock', 'id_warehouse', 'warehouse', 'id_warehouse'),
                array('stock', 'id_product', 'product', 'id_product'),
                array('stock_available', 'id_product', 'product', 'id_product'),
                array('stock_mvt', 'id_stock', 'stock', 'id_stock'),
                array('tab_module_preference', 'id_employee', 'employee', 'id_employee'),
                array('tab_module_preference', 'id_tab', 'tab', 'id_tab'),
                array('tax_rule', 'id_country', 'country', 'id_country'),
                array('warehouse_carrier', 'id_warehouse', 'warehouse', 'id_warehouse'),
                array('warehouse_carrier', 'id_carrier', 'carrier', 'id_carrier'),
                array('warehouse_product_location', 'id_product', 'product', 'id_product'),
                array('warehouse_product_location', 'id_warehouse', 'warehouse', 'id_warehouse'),
                array('product_comment', 'id_product', 'product', 'id_product'),
                array('specific_price', 'id_product', 'customer', 'id_customer'),
                array('specific_price', 'id_group', 'group', 'id_group'),
                array('htl_features_block_data_lang', 'id_features_block', 'htl_features_block_data', 'id_features_block'),
                array('cart_rule', 'id_customer', 'customer', 'id_customer'),
                array('htl_testimonials_block_data_lang', 'id_testimonial_block', 'htl_testimonials_block_data', 'id_testimonial_block'),
                array('htl_room_type_global_demand', 'id_tax_rules_group', 'tax_rules_group', 'id_tax_rules_group'),
                array('htl_room_type_global_demand_advance_option', 'id_global_demand', 'htl_room_type_global_demand', 'id_global_demand'),
                array('htl_room_type_global_demand_advance_option_lang', 'id_option', 'htl_room_type_global_demand_advance_option', 'id_option'),
                array('htl_room_type_global_demand_lang', 'id_global_demand', 'htl_room_type_global_demand', 'id_global_demand'),
                array('htl_branch_info_lang', 'id_lang', 'lang', 'id_lang'),
                array('htl_branch_info_lang', 'id', 'htl_branch_info', 'id'),
                array('htl_features_lang', 'id_lang', 'lang', 'id_lang'),
                array('htl_features_lang', 'id', 'htl_features', 'id'),
                array('htl_room_type_demand', 'id_product', 'product', 'id_product'),
                array('htl_room_type_demand_price', 'id_product', 'product', 'id_product'),
                array('htl_room_type_global_demand_advance_option_lang', 'id_lang', 'lang', 'id_lang'),
                array('htl_room_type_global_demand_advance_option_lang', 'id_option', 'htl_room_type_global_demand_advance_option', 'id_option'),
                array('htl_room_type_feature_pricing_group', 'id_lang', 'lang', 'id_lang'),
                array('htl_booking_demands_tax', 'id_tax', 'tax', 'id_tax'),
                array('htl_booking_detail', 'id_product', 'product', 'id_product'),
                array('htl_booking_detail', 'id_order', 'orders', 'id_order'),
                array('htl_booking_demands', 'id_htl_booking', 'htl_booking_detail', 'id_htl_booking'),
                array('htl_cart_booking_data', 'id_order', 'orders', 'id_order'),
                array('htl_cart_booking_data', 'id_customer', 'customer', 'id_customer'),
                array('htl_branch_features', 'id_hotel', 'htl_branch_info', 'id'),
                array('htl_image', 'id_hotel', 'htl_branch_info', 'id'),
                array('htl_branch_info', 'id_category', 'category', 'id_category'),
                array('htl_room_information', 'id_product', 'product', 'id_product'),
                array('htl_room_information', 'id_hotel', 'htl_branch_info', 'id'),
                array('htl_room_type', 'id_product', 'product', 'id_product'),
                array('htl_room_type', 'id_hotel', 'htl_branch_info', 'id'),
                array('profile_lang', 'id_profile', 'profile', 'id_profile'),
                array('profile_lang', 'id_lang', 'lang', 'id_lang'),
                array('htl_access', 'id_profile', 'profile', 'id_profile'),
                array('htl_access', 'id_hotel', 'htl_branch_info', 'id'),
                array('htl_room_disable_dates', 'id_room_type', 'htl_room_type', 'id'),
                array('htl_room_type_feature_pricing_lang', 'id_feature_price', 'htl_room_type_feature_pricing', 'id_feature_price'),
                array('htl_room_type_feature_pricing_lang', 'id_lang', 'lang', 'id_lang'),
                array('htl_order_restrict_date', 'id_hotel', 'htl_branch_info', 'id'),
                array('htl_branch_refund_rules', 'id_hotel', 'htl_branch_info', 'id'),
                array('htl_branch_refund_rules', 'id_hotel_refund_rule', 'htl_branch_refund_rules', 'id_hotel_refund_rule'),
                array('htl_order_refund_rules_lang', 'id_refund_rule', 'htl_branch_refund_rules', 'id_refund_rule'),
                array('htl_advance_payment', 'id_product', 'product', 'id_product'),
            )
        );
    }

    public static function getCatalogRelatedTables()
    {
        $append = array();
        return array_merge(
            $append,
            array(
                'product',
                'product_shop',
                'feature_product',
                'product_lang',
                'category_product',
                'product_tag',
                'tag',
                'image',
                'image_lang',
                'image_shop',
                'specific_price',
                'specific_price_priority',
                'product_carrier',
                'cart_product',
                'product_attachment',
                'product_country_tax',
                'product_download',
                'product_group_reduction_cache',
                'product_sale',
                'product_supplier',
                'warehouse_product_location',
                'stock',
                'stock_available',
                'stock_mvt',
                'customization',
                'customization_field',
                'supply_order_detail',
                'attribute_impact',
                'product_attribute',
                'product_attribute_shop',
                'product_attribute_combination',
                'product_attribute_image',
                'attribute_impact',
                'attribute_lang',
                'attribute_group',
                'attribute_group_lang',
                'attribute_group_shop',
                'attribute_shop',
                'product_attribute',
                'product_attribute_shop',
                'product_attribute_combination',
                'product_attribute_image',
                'stock_available',
                'manufacturer',
                'manufacturer_lang',
                'manufacturer_shop',
                'supplier',
                'supplier_lang',
                'supplier_shop',
                'customization',
                'customization_field',
                'customization_field_lang',
                'customized_data',
                'feature',
                'feature_lang',
                'feature_product',
                'feature_shop',
                'feature_value',
                'feature_value_lang',
                'pack',
                'search_index',
                'search_word',
                'specific_price',
                'specific_price_priority',
                'specific_price_rule',
                'specific_price_rule_condition',
                'specific_price_rule_condition_group',
                'stock',
                'stock_available',
                'stock_mvt',
                'warehouse',
                //Qlo modules tables
                'htl_room_type',
                'htl_room_information',
                'htl_branch_info',
                'htl_branch_info_lang',
                'htl_image',
                'htl_branch_features',
                'htl_features',
                'htl_features_lang',
                'htl_advance_payment',
                'htl_branch_refund_rules',
                'htl_order_restrict_date',
                'htl_room_type_feature_pricing',
                'htl_room_type_feature_pricing_lang',
                'htl_room_type_feature_pricing_group',
                'htl_room_type_demand_price',
                'htl_room_type_demand',
                'htl_room_disable_dates',
                'htl_interior_image',
                'htl_room_block_data',
                'htl_features_block_data',
                'htl_testimonials_block_data',
                'htl_room_type_global_demand'
            )
        );
    }

    public static function getSalesRelatedTables()
    {
        return array(
            'customer',
            'cart',
            'cart_product',
            'connections',
            'connections_page',
            'connections_source',
            'customer_group',
            'customer_message',
            'customer_message_sync_imap',
            'customer_thread',
            'guest',
            'message',
            'message_readed',
            'orders',
            'order_carrier',
            'order_cart_rule',
            'order_detail',
            'order_detail_tax',
            'order_history',
            'order_invoice',
            'order_invoice_payment',
            'order_invoice_tax',
            'order_message',
            'order_message_lang',
            'order_payment',
            'order_return',
            'order_return_detail',
            'order_slip',
            'order_slip_detail',
            'page',
            'page_type',
            'page_viewed',
            'product_sale',
            'referrer_cache',
            //Qlo modules order tables
            'htl_cart_booking_data',
            'htl_booking_detail',
            'htl_booking_demands',
            'htl_booking_demands_tax',
        );
    }
}
