<?php
/**
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
 *  @author		PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class HookCore extends ObjectModel
{
    /**
     * @var string Hook name identifier
     */
    public $name;

    /**
     * @var string Hook title (displayed in BO)
     */
    public $title;

    /**
     * @var string Hook description
     */
    public $description;

    /**
     * @var bool
     */
    public $position = false;

    /**
     * @var bool Is this hook usable with live edit ?
     */
    public $live_edit = false;

    /**
     * @var array List of executed hooks on this page
     */
    public static $executed_hooks = array();

    public static $native_module;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'hook',
        'primary' => 'id_hook',
        'fields' => array(
            'name' =>            array('type' => self::TYPE_STRING, 'validate' => 'isHookName', 'required' => true, 'size' => 64),
            'title' =>            array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'description' =>    array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'position' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'live_edit' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    /**
     * @deprecated 1.5.0
     */
    protected static $_hook_modules_cache = null;

    /**
     * @deprecated 1.5.0
     */
    protected static $_hook_modules_cache_exec = null;

    public function add($autodate = true, $null_values = false)
    {
        Cache::clean('hook_idsbyname');
        return parent::add($autodate, $null_values);
    }

    /**
     * Return Hooks List
     *
     * @param bool $position
     * @return array Hooks List
     */
    public static function getHooks($position = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * FROM `'._DB_PREFIX_.'hook` h
			'.($position ? 'WHERE h.`position` = 1' : '').'
			ORDER BY `name`'
        );
    }

    /**
     * Return hook ID from name
     *
     * @param string $hook_name Hook name
     * @return int Hook ID
     */
    public static function getIdByName($hook_name)
    {
        $hook_name = strtolower($hook_name);
        if (!Validate::isHookName($hook_name)) {
            return false;
        }

        $cache_id = 'hook_idsbyname';
        if (!Cache::isStored($cache_id)) {
            // Get all hook ID by name and alias
            $hook_ids = array();
            $db = Db::getInstance();
            $result = $db->ExecuteS('
			SELECT `id_hook`, `name`
			FROM `'._DB_PREFIX_.'hook`
			UNION
			SELECT `id_hook`, ha.`alias` as name
			FROM `'._DB_PREFIX_.'hook_alias` ha
			INNER JOIN `'._DB_PREFIX_.'hook` h ON ha.name = h.name', false);
            while ($row = $db->nextRow($result)) {
                $hook_ids[strtolower($row['name'])] = $row['id_hook'];
            }
            Cache::store($cache_id, $hook_ids);
        } else {
            $hook_ids = Cache::retrieve($cache_id);
        }

        return (isset($hook_ids[$hook_name]) ? $hook_ids[$hook_name] : false);
    }

    /**
     * Return hook ID from name
     */
    public static function getNameById($hook_id)
    {
        $cache_id = 'hook_namebyid_'.$hook_id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->getValue('
							SELECT `name`
							FROM `'._DB_PREFIX_.'hook`
							WHERE `id_hook` = '.(int)$hook_id);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Return hook live edit bool from ID
     */
    public static function getLiveEditById($hook_id)
    {
        $cache_id = 'hook_live_editbyid_'.$hook_id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->getValue('
							SELECT `live_edit`
							FROM `'._DB_PREFIX_.'hook`
							WHERE `id_hook` = '.(int)$hook_id);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get list of hook alias
     *
     * @since 1.5.0
     * @return array
     */
    public static function getHookAliasList()
    {
        $cache_id = 'hook_alias';
        if (!Cache::isStored($cache_id)) {
            $hook_alias_list = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'hook_alias`');
            $hook_alias = array();
            if ($hook_alias_list) {
                foreach ($hook_alias_list as $ha) {
                    $hook_alias[strtolower($ha['alias'])] = $ha['name'];
                }
            }
            Cache::store($cache_id, $hook_alias);
            return $hook_alias;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Return backward compatibility hook name
     *
     * @since 1.5.0
     * @param string $hook_name Hook name
     * @return int Hook ID
     */
    public static function getRetroHookName($hook_name)
    {
        $alias_list = Hook::getHookAliasList();
        if (isset($alias_list[strtolower($hook_name)])) {
            return $alias_list[strtolower($hook_name)];
        }

        $retro_hook_name = array_search($hook_name, $alias_list);
        if ($retro_hook_name === false) {
            return '';
        }
        return $retro_hook_name;
    }

    /**
     * Get list of all registered hooks with modules
     *
     * @since 1.5.0
     * @return array
     */
    public static function getHookModuleList()
    {
        $cache_id = 'hook_module_list';
        if (!Cache::isStored($cache_id)) {
            $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT h.id_hook, h.name as h_name, title, description, h.position, live_edit, hm.position as hm_position, m.id_module, m.name, active
			FROM `'._DB_PREFIX_.'hook_module` hm
			STRAIGHT_JOIN `'._DB_PREFIX_.'hook` h ON (h.id_hook = hm.id_hook AND hm.id_shop = '.(int)Context::getContext()->shop->id.')
			STRAIGHT_JOIN `'._DB_PREFIX_.'module` as m ON (m.id_module = hm.id_module)
			ORDER BY hm.position');
            $list = array();
            foreach ($results as $result) {
                if (!isset($list[$result['id_hook']])) {
                    $list[$result['id_hook']] = array();
                }

                $list[$result['id_hook']][$result['id_module']] = array(
                    'id_hook' => $result['id_hook'],
                    'title' => $result['title'],
                    'description' => $result['description'],
                    'hm.position' => $result['position'],
                    'live_edit' => $result['live_edit'],
                    'm.position' => $result['hm_position'],
                    'id_module' => $result['id_module'],
                    'name' => $result['name'],
                    'active' => $result['active'],
                );
            }
            Cache::store($cache_id, $list);

            // @todo remove this in 1.6, we keep it in 1.5 for retrocompatibility
            Hook::$_hook_modules_cache = $list;
            return $list;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * Return Hooks List
     *
     * @since 1.5.0
     * @param int $id_hook
     * @param int $id_module
     * @return array Modules List
     */
    public static function getModulesFromHook($id_hook, $id_module = null)
    {
        $hm_list = Hook::getHookModuleList();
        $module_list = (isset($hm_list[$id_hook])) ? $hm_list[$id_hook] : array();

        if ($id_module) {
            return (isset($module_list[$id_module])) ? array($module_list[$id_module]) : array();
        }
        return $module_list;
    }

    /**
     * Get list of modules we can execute per hook
     *
     * @since 1.5.0
     * @param string $hook_name Get list of modules for this hook if given
     * @return array
     */
    public static function getHookModuleExecList($hook_name = null)
    {
        $context = Context::getContext();
        $cache_id = 'hook_module_exec_list_'.(isset($context->shop->id) ? '_'.$context->shop->id : '').((isset($context->customer)) ? '_'.$context->customer->id : '');
        if (!Cache::isStored($cache_id) || $hook_name == 'displayPayment' || $hook_name == 'displayBackOfficeHeader') {
            $frontend = true;
            $groups = array();
            $use_groups = Group::isFeatureActive();
            if (isset($context->employee)) {
                $frontend = false;
            } else {
                // Get groups list
                if ($use_groups) {
                    if (isset($context->customer) && $context->customer->isLogged()) {
                        $groups = $context->customer->getGroups();
                    } elseif (isset($context->customer) && $context->customer->isLogged(true)) {
                        $groups = array((int)Configuration::get('PS_GUEST_GROUP'));
                    } else {
                        $groups = array((int)Configuration::get('PS_UNIDENTIFIED_GROUP'));
                    }
                }
            }

            // SQL Request
            $sql = new DbQuery();
            $sql->select('h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module, h.`live_edit`');
            $sql->from('module', 'm');
            if ($hook_name != 'displayBackOfficeHeader') {
                $sql->join(Shop::addSqlAssociation('module', 'm', true, 'module_shop.enable_device & '.(int)Context::getContext()->getDevice()));
                $sql->innerJoin('module_shop', 'ms', 'ms.`id_module` = m.`id_module`');
            }
            $sql->innerJoin('hook_module', 'hm', 'hm.`id_module` = m.`id_module`');
            $sql->innerJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
            if ($hook_name != 'displayPayment') {
                $sql->where('h.name != "displayPayment"');
            }
            // For payment modules, we check that they are available in the contextual country
            elseif ($frontend) {
                if (Validate::isLoadedObject($context->country)) {
                    $sql->where('(h.name = "displayPayment" AND (SELECT id_country FROM '._DB_PREFIX_.'module_country mc WHERE mc.id_module = m.id_module AND id_country = '.(int)$context->country->id.' AND id_shop = '.(int)$context->shop->id.' LIMIT 1) = '.(int)$context->country->id.')');
                }
                if (Validate::isLoadedObject($context->currency)) {
                    $sql->where('(h.name = "displayPayment" AND (SELECT id_currency FROM '._DB_PREFIX_.'module_currency mcr WHERE mcr.id_module = m.id_module AND id_currency IN ('.(int)$context->currency->id.', -1, -2) LIMIT 1) IN ('.(int)$context->currency->id.', -1, -2))');
                }
            }
            if (Validate::isLoadedObject($context->shop)) {
                $sql->where('hm.id_shop = '.(int)$context->shop->id);
            }

            if ($frontend) {
                if ($use_groups) {
                    $sql->leftJoin('module_group', 'mg', 'mg.`id_module` = m.`id_module`');
                    if (Validate::isLoadedObject($context->shop)) {
                        $sql->where('mg.id_shop = '.((int)$context->shop->id).(count($groups) ? ' AND  mg.`id_group` IN ('.implode(', ', $groups).')' : ''));
                    } elseif (count($groups)) {
                        $sql->where('mg.`id_group` IN ('.implode(', ', $groups).')');
                    }
                }
            }

            $sql->groupBy('hm.id_hook, hm.id_module');
            $sql->orderBy('hm.`position`');

            $list = array();
            if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                foreach ($result as $row) {
                    $row['hook'] = strtolower($row['hook']);
                    if (!isset($list[$row['hook']])) {
                        $list[$row['hook']] = array();
                    }

                    $list[$row['hook']][] = array(
                        'id_hook' => $row['id_hook'],
                        'module' => $row['module'],
                        'id_module' => $row['id_module'],
                        'live_edit' => $row['live_edit'],
                    );
                }
            }
            if ($hook_name != 'displayPayment' && $hook_name != 'displayBackOfficeHeader') {
                Cache::store($cache_id, $list);
                // @todo remove this in 1.6, we keep it in 1.5 for backward compatibility
                self::$_hook_modules_cache_exec = $list;
            }
        } else {
            $list = Cache::retrieve($cache_id);
        }

        // If hook_name is given, just get list of modules for this hook
        if ($hook_name) {
            $retro_hook_name = strtolower(Hook::getRetroHookName($hook_name));
            $hook_name = strtolower($hook_name);

            $return = array();
            $inserted_modules = array();
            if (isset($list[$hook_name])) {
                $return = $list[$hook_name];
            }
            foreach ($return as $module) {
                $inserted_modules[] = $module['id_module'];
            }
            if (isset($list[$retro_hook_name])) {
                foreach ($list[$retro_hook_name] as $retro_module_call) {
                    if (!in_array($retro_module_call['id_module'], $inserted_modules)) {
                        $return[] = $retro_module_call;
                    }
                }
            }

            return (count($return) > 0 ? $return : false);
        } else {
            return $list;
        }
    }

    /**
     * Execute modules for specified hook
     *
     * @param string $hook_name Hook Name
     * @param array $hook_args Parameters for the functions
     * @param int $id_module Execute hook for this module only
     * @param bool $array_return If specified, module output will be set by name in an array
     * @param bool $check_exceptions Check permission exceptions
     * @param bool $use_push Force change to be refreshed on Dashboard widgets
     * @param int $id_shop If specified, hook will be execute the shop with this ID
     *
     * @throws PrestaShopException
     *
     * @return string/array modules output
     */
    public static function exec($hook_name, $hook_args = array(), $id_module = null, $array_return = false, $check_exceptions = true,
                                $use_push = false, $id_shop = null)
    {
        if (defined('PS_INSTALLATION_IN_PROGRESS')) {
            return;
        }

        static $disable_non_native_modules = null;
        if ($disable_non_native_modules === null) {
            $disable_non_native_modules = (bool)Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
        }

        // Check arguments validity
        if (($id_module && !is_numeric($id_module)) || !Validate::isHookName($hook_name)) {
            throw new PrestaShopException('Invalid id_module or hook_name');
        }

        // If no modules associated to hook_name or recompatible hook name, we stop the function

        if (!$module_list = Hook::getHookModuleExecList($hook_name)) {
            return '';
        }

        // Check if hook exists
        if (!$id_hook = Hook::getIdByName($hook_name)) {
            return false;
        }

        // Store list of executed hooks on this page
        Hook::$executed_hooks[$id_hook] = $hook_name;

        $live_edit = false;
        $context = Context::getContext();
        if (!isset($hook_args['cookie']) || !$hook_args['cookie']) {
            $hook_args['cookie'] = $context->cookie;
        }
        if (!isset($hook_args['cart']) || !$hook_args['cart']) {
            $hook_args['cart'] = $context->cart;
        }

        $retro_hook_name = Hook::getRetroHookName($hook_name);

        // Look on modules list
        $altern = 0;
        $output = '';

        if ($disable_non_native_modules && !isset(Hook::$native_module)) {
            Hook::$native_module = Module::getNativeModuleList();
        }

        $different_shop = false;
        if ($id_shop !== null && Validate::isUnsignedId($id_shop) && $id_shop != $context->shop->getContextShopID()) {
            $old_context = $context->shop->getContext();
            $old_shop = clone $context->shop;
            $shop = new Shop((int)$id_shop);
            if (Validate::isLoadedObject($shop)) {
                $context->shop = $shop;
                $context->shop->setContext(Shop::CONTEXT_SHOP, $shop->id);
                $different_shop = true;
            }
        }

        foreach ($module_list as $array) {
            // Check errors
            if ($id_module && $id_module != $array['id_module']) {
                continue;
            }

            if ((bool)$disable_non_native_modules && Hook::$native_module && count(Hook::$native_module) && !in_array($array['module'], self::$native_module)) {
                continue;
            }

            // Check permissions
            if ($check_exceptions) {
                $exceptions = Module::getExceptionsStatic($array['id_module'], $array['id_hook']);

                $controller = Dispatcher::getInstance()->getController();
                $controller_obj = Context::getContext()->controller;

                //check if current controller is a module controller
                if (isset($controller_obj->module) && Validate::isLoadedObject($controller_obj->module)) {
                    $controller = 'module-'.$controller_obj->module->name.'-'.$controller;
                }

                if (in_array($controller, $exceptions)) {
                    continue;
                }

                //Backward compatibility of controller names
                $matching_name = array(
                    'authentication' => 'auth',
                    'productscomparison' => 'compare'
                );
                if (isset($matching_name[$controller]) && in_array($matching_name[$controller], $exceptions)) {
                    continue;
                }
                if (Validate::isLoadedObject($context->employee) && !Module::getPermissionStatic($array['id_module'], 'view', $context->employee)) {
                    continue;
                }
            }

            if (!($moduleInstance = Module::getInstanceByName($array['module']))) {
                continue;
            }

            if ($use_push && !$moduleInstance->allow_push) {
                continue;
            }
            // Check which / if method is callable
            $hook_callable = is_callable(array($moduleInstance, 'hook'.$hook_name));
            $hook_retro_callable = is_callable(array($moduleInstance, 'hook'.$retro_hook_name));

            if (($hook_callable || $hook_retro_callable) && Module::preCall($moduleInstance->name)) {
                $hook_args['altern'] = ++$altern;

                if ($use_push && isset($moduleInstance->push_filename) && file_exists($moduleInstance->push_filename)) {
                    Tools::waitUntilFileIsModified($moduleInstance->push_filename, $moduleInstance->push_time_limit);
                }

                // Call hook method
                if ($hook_callable) {
                    $display = Hook::coreCallHook($moduleInstance, 'hook'.$hook_name, $hook_args);
                } elseif ($hook_retro_callable) {
                    $display = Hook::coreCallHook($moduleInstance, 'hook'.$retro_hook_name, $hook_args);
                }

                // Live edit
                if (!$array_return && $array['live_edit'] && Tools::isSubmit('live_edit') && Tools::getValue('ad')
                    && Tools::getValue('liveToken') == Tools::getAdminToken('AdminModulesPositions'
                        .(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee'))) {
                    $live_edit = true;
                    $output .= self::wrapLiveEdit($display, $moduleInstance, $array['id_hook']);
                } elseif ($array_return) {
                    $output[$moduleInstance->name] = $display;
                } else {
                    $output .= $display;
                }
            }
        }

        if ($different_shop) {
            $context->shop = $old_shop;
            $context->shop->setContext($old_context, $shop->id);
        }

        if ($array_return) {
            return $output;
        } else {
            return ($live_edit ? '<script type="text/javascript">hooks_list.push(\''.$hook_name.'\');</script>
				<div id="'.$hook_name.'" class="dndHook" style="min-height:50px">' : '').$output.($live_edit ? '</div>' : '');
        }// Return html string
    }

    public static function coreCallHook($module, $method, $params)
    {
        // Define if we will log modules performances for this session
        if (Module::$_log_modules_perfs === null) {
            $modulo = _PS_DEBUG_PROFILING_ ? 1 : Configuration::get('PS_log_modules_perfs_MODULO');
            Module::$_log_modules_perfs = ($modulo && mt_rand(0, $modulo - 1) == 0);
            if (Module::$_log_modules_perfs) {
                Module::$_log_modules_perfs_session = mt_rand();
            }
        }

        // Immediately return the result if we do not log performances
        if (!Module::$_log_modules_perfs) {
            return $module->{$method}($params);
        }

        // Store time and memory before and after hook call and save the result in the database
        $time_start = microtime(true);
        $memory_start = memory_get_usage(true);

        // Call hook
        $r = $module->{$method}($params);

        $time_end = microtime(true);
        $memory_end = memory_get_usage(true);

        Db::getInstance()->execute('
		INSERT INTO '._DB_PREFIX_.'modules_perfs (session, module, method, time_start, time_end, memory_start, memory_end)
		VALUES ('.(int)Module::$_log_modules_perfs_session.', "'.pSQL($module->name).'", "'.pSQL($method).'", "'.pSQL($time_start).'", "'.pSQL($time_end).'", '.(int)$memory_start.', '.(int)$memory_end.')');

        return $r;
    }

    public static function wrapLiveEdit($display, $moduleInstance, $id_hook)
    {
        return '<script type="text/javascript"> modules_list.push(\''.Tools::safeOutput($moduleInstance->name).'\');</script>
				<div id="hook_'.(int)$id_hook.'_module_'.(int)$moduleInstance->id.'_moduleName_'.str_replace('_', '-', Tools::safeOutput($moduleInstance->name)).'"
				class="dndModule" style="border: 1px dotted red;'.(!strlen($display) ? 'height:50px;' : '').'">
					<span style="font-family: Georgia;font-size:13px;font-style:italic;">
						<img style="padding-right:5px;" src="'._MODULE_DIR_.Tools::safeOutput($moduleInstance->name).'/logo.gif">'
                .Tools::safeOutput($moduleInstance->displayName).'<span style="float:right">
				<a href="#" id="'.(int)$id_hook.'_'.(int)$moduleInstance->id.'" class="moveModule">
					<img src="'._PS_ADMIN_IMG_.'arrow_out.png"></a>
				<a href="#" id="'.(int)$id_hook.'_'.(int)$moduleInstance->id.'" class="unregisterHook">
					<img src="'._PS_ADMIN_IMG_.'delete.gif"></a></span>
				</span>'.$display.'</div>';
    }

    /**
     * @deprecated 1.5.0
     */
    public static function updateOrderStatus($new_order_status_id, $id_order)
    {
        Tools::displayAsDeprecated();
        $order = new Order((int)$id_order);
        $new_os = new OrderState((int)$new_order_status_id, $order->id_lang);

        $return = ((int)$new_os->id == Configuration::get('PS_OS_PAYMENT')) ? Hook::exec('paymentConfirm', array('id_order' => (int)($order->id))) : true;
        $return = Hook::exec('updateOrderStatus', array('newOrderStatus' => $new_os, 'id_order' => (int)($order->id))) && $return;
        return $return;
    }

    /**
     * @deprecated 1.5.0
     */
    public static function postUpdateOrderStatus($new_order_status_id, $id_order)
    {
        Tools::displayAsDeprecated();
        $order = new Order((int)$id_order);
        $new_os = new OrderState((int)$new_order_status_id, $order->id_lang);
        $return = Hook::exec('postUpdateOrderStatus', array('newOrderStatus' => $new_os, 'id_order' => (int)($order->id)));
        return $return;
    }

    /**
     * @deprecated 1.5.0
     */
    public static function orderConfirmation($id_order)
    {
        Tools::displayAsDeprecated();
        if (Validate::isUnsignedId($id_order)) {
            $params = array();
            $order = new Order((int)$id_order);
            $currency = new Currency((int)$order->id_currency);

            if (Validate::isLoadedObject($order)) {
                $cart = new Cart((int)$order->id_cart);
                $params['total_to_pay'] = $cart->getOrderTotal();
                $params['currency'] = $currency->sign;
                $params['objOrder'] = $order;
                $params['currencyObj'] = $currency;

                return Hook::exec('orderConfirmation', $params);
            }
        }
        return false;
    }

    /**
     * @deprecated 1.5.0
     */
    public static function paymentReturn($id_order, $id_module)
    {
        Tools::displayAsDeprecated();
        if (Validate::isUnsignedId($id_order) && Validate::isUnsignedId($id_module)) {
            $params = array();
            $order = new Order((int)($id_order));
            $currency = new Currency((int)($order->id_currency));

            if (Validate::isLoadedObject($order)) {
                $cart = new Cart((int)$order->id_cart);
                $params['total_to_pay'] = $cart->getOrderTotal();
                $params['currency'] = $currency->sign;
                $params['objOrder'] = $order;
                $params['currencyObj'] = $currency;

                return Hook::exec('paymentReturn', $params, (int)($id_module));
            }
        }
        return false;
    }

    /**
     * @deprecated 1.5.0
     */
    public static function PDFInvoice($pdf, $id_order)
    {
        Tools::displayAsDeprecated();
        if (!is_object($pdf) || !Validate::isUnsignedId($id_order)) {
            return false;
        }
        return Hook::exec('PDFInvoice', array('pdf' => $pdf, 'id_order' => $id_order));
    }

    /**
     * @deprecated 1.5.0
     */
    public static function backBeforePayment($module)
    {
        Tools::displayAsDeprecated();
        if ($module) {
            return Hook::exec('backBeforePayment', array('module' => strval($module)));
        }
    }

    /**
     * @deprecated 1.5.0
     */
    public static function updateCarrier($id_carrier, $carrier)
    {
        Tools::displayAsDeprecated();
        if (!Validate::isUnsignedId($id_carrier) || !is_object($carrier)) {
            return false;
        }
        return Hook::exec('updateCarrier', array('id_carrier' => $id_carrier, 'carrier' => $carrier));
    }

    /**
     * Preload hook modules cache
     *
     * @deprecated 1.5.0 use Hook::getHookModuleList() instead
     *
     * @return bool preload_needed
     */
    public static function preloadHookModulesCache()
    {
        Tools::displayAsDeprecated();

        if (!is_null(self::$_hook_modules_cache)) {
            return false;
        }

        self::$_hook_modules_cache = Hook::getHookModuleList();
        return true;
    }

    /**
     * Return hook ID from name
     *
     * @param string $hook_name Hook name
     * @return int Hook ID
     *
     * @deprecated since 1.5.0 use Hook::getIdByName() instead
     */
    public static function get($hook_name)
    {
        Tools::displayAsDeprecated();
        if (!Validate::isHookName($hook_name)) {
            die(Tools::displayError());
        }

        $result = Db::getInstance()->getRow('
		SELECT `id_hook`, `name`
		FROM `'._DB_PREFIX_.'hook`
		WHERE `name` = \''.pSQL($hook_name).'\'');

        return ($result ? $result['id_hook'] : false);
    }

    /**
     * Called when quantity of a product is updated.
     *
     * @deprecated 1.5.3.0
     *
     * @param Cart $cart
     * @param Order $order
     * @param Customer $customer
     * @param Currency $currency
     * @param $orderStatus
     *
     * @throws PrestaShopException
     *
     * @return string
     */
    public static function newOrder($cart, $order, $customer, $currency, $order_status)
    {
        Tools::displayAsDeprecated();
        return Hook::exec('newOrder', array(
            'cart' => $cart,
            'order' => $order,
            'customer' => $customer,
            'currency' => $currency,
            'orderStatus' => $order_status));
    }

    /**
     * @deprecated 1.5.0
     */
    public static function updateQuantity($product, $order = null)
    {
        Tools::displayAsDeprecated();
        return Hook::exec('updateQuantity', array('product' => $product, 'order' => $order));
    }

    /**
     * @deprecated 1.5.0
     */
    public static function productFooter($product, $category)
    {
        Tools::displayAsDeprecated();
        return Hook::exec('productFooter', array('product' => $product, 'category' => $category));
    }

    /**
     * @deprecated 1.5.0
     */
    public static function productOutOfStock($product)
    {
        Tools::displayAsDeprecated();
        return Hook::exec('productOutOfStock', array('product' => $product));
    }

    /**
     * @deprecated 1.5.0
     */
    public static function addProduct($product)
    {
        Tools::displayAsDeprecated();
        return Hook::exec('addProduct', array('product' => $product));
    }

    /**
     * @deprecated 1.5.0
     */
    public static function updateProduct($product)
    {
        Tools::displayAsDeprecated();
        return Hook::exec('updateProduct', array('product' => $product));
    }

    /**
     * @deprecated 1.5.0
     */
    public static function deleteProduct($product)
    {
        Tools::displayAsDeprecated();
        return Hook::exec('deleteProduct', array('product' => $product));
    }

    /**
     * @deprecated 1.5.0
     */
    public static function updateProductAttribute($id_product_attribute)
    {
        Tools::displayAsDeprecated();
        return Hook::exec('updateProductAttribute', array('id_product_attribute' => $id_product_attribute));
    }
}
