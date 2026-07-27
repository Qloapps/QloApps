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

class AdminStatsControllerCore extends AdminStatsTabController
{
    public static function getVisits($unique, $date_from, $date_to, $granularity = false)
    {
        $visits = ($granularity == false) ? 0 : array();
        $objGoogleAnalytics = Module::isEnabled('qlogoogleanalytics') ? Module::getInstanceByName('qlogoogleanalytics') : false;
        if (Validate::isLoadedObject($objGoogleAnalytics) && $objGoogleAnalytics->isConfigured()) {
            $metric = $unique ? 'visitors' : 'visits';
            if ($result = $objGoogleAnalytics->requestReportData($granularity ? 'ga:date' : '', 'ga:'.$metric, $date_from, $date_to, null, null, 1, 5000)) {
                foreach ($result as $row) {
                    if ($granularity == 'day') {
                        $visits[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-$3', $row['dimensions']['date']))] = $row['metrics'][$metric];
                    } elseif ($granularity == 'month') {
                        if (!isset($visits[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-01', $row['dimensions']['date']))])) {
                            $visits[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-01', $row['dimensions']['date']))] = 0;
                        }
                        $visits[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-01', $row['dimensions']['date']))] += $row['metrics'][$metric];
                    } else {
                        $visits = $row['metrics'][$metric];
                    }
                }
            }
        } else {
            if ($granularity == 'day') {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT LEFT(`date_add`, 10) as date, COUNT('.($unique ? 'DISTINCT id_guest' : '*').') as visits
				FROM `'._DB_PREFIX_.'connections`
				WHERE `date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction().'
				GROUP BY LEFT(`date_add`, 10)');
                foreach ($result as $row) {
                    $visits[strtotime($row['date'])] = $row['visits'];
                }
            } elseif ($granularity == 'month') {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT LEFT(`date_add`, 7) as date, COUNT('.($unique ? 'DISTINCT id_guest' : '*').') as visits
				FROM `'._DB_PREFIX_.'connections`
				WHERE `date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction().'
				GROUP BY LEFT(`date_add`, 7)');
                foreach ($result as $row) {
                    $visits[strtotime($row['date'].'-01')] = $row['visits'];
                }
            } else {
                $visits = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT('.($unique ? 'DISTINCT id_guest' : '*').') as visits
				FROM `'._DB_PREFIX_.'connections`
				WHERE `date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction());
            }
        }
        return $visits;
    }


    public static function getAbandonedCarts($date_from, $date_to, $timeDiff = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(`id_cart`)
		FROM `'._DB_PREFIX_.'cart` c
		WHERE `date_add` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"'.
        (($timeDiff) ? 'AND TIME_TO_SEC(TIMEDIFF(\''.pSQL(date('Y-m-d H:i:00', time())).'\', `date_add`)) > '.$timeDiff : ' ').'
		AND NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'orders` o WHERE o.`id_cart` = c.`id_cart`)
		'.Shop::addSqlRestriction());
    }

    public static function getInstalledModules()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT m.`id_module`)
		FROM `'._DB_PREFIX_.'module` m
		'.Shop::addSqlAssociation('module', 'm'));
    }

    public static function getDisabledModules()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'module` m
		'.Shop::addSqlAssociation('module', 'm', false).'
		WHERE module_shop.id_module IS NULL OR m.active = 0');
    }

    public static function getModulesToUpdate()
    {
        $context = Context::getContext();
        $logged_on_addons = false;
        if (isset($context->cookie->username_addons) && isset($context->cookie->password_addons)
        && !empty($context->cookie->username_addons) && !empty($context->cookie->password_addons)) {
            $logged_on_addons = true;
        }
        $modules = Module::getModulesOnDisk(true, $logged_on_addons, $context->employee->id);
        $upgrade_available = 0;
        foreach ($modules as $km => $module) {
            if ($module->installed && isset($module->version_addons) && $module->version_addons) { // SimpleXMLElement
                ++$upgrade_available;
            }
        }
        return $upgrade_available;
    }

    public static function getPercentProductStock()
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(IF(IFNULL(stock.quantity, 0) > 0, 1, 0)) as with_stock, COUNT(*) as products
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON p.id_product = pa.id_product
		'.Product::sqlStock('p', 'pa').'
		WHERE product_shop.active = 1');
        return round($row['products'] ? 100 * $row['with_stock'] / $row['products'] : 0, 2).'%';
    }

    public static function getPercentProductOutOfStock()
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(IF(IFNULL(stock.quantity, 0) = 0, 1, 0)) as without_stock, COUNT(*) as products
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON p.id_product = pa.id_product
		'.Product::sqlStock('p', 'pa').'
		WHERE product_shop.active = 1');
        return round($row['products'] ? 100 * $row['without_stock'] / $row['products'] : 0, 2).'%';
    }

    public static function getProductAverageGrossMargin()
    {
        $sql = 'SELECT AVG(1 - (IF(IFNULL(product_attribute_shop.wholesale_price, 0) = 0, product_shop.wholesale_price,product_attribute_shop.wholesale_price) / (IFNULL(product_attribute_shop.price, 0) + product_shop.price)))
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON p.id_product = pa.id_product
		'.Shop::addSqlAssociation('product_attribute', 'pa', false).'
		WHERE product_shop.active = 1';
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        return round(100 * $value, 2).'%';
    }

    public static function getDisabledCategories()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		WHERE c.active = 0');
    }

    public static function getTotalCategories()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c'));
    }

    public static function getDisabledRoomTypes($idHotel = null)
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		INNER JOIN `'._DB_PREFIX_.'htl_room_type` hrt
		ON (hrt.`id_product` = p.`id_product`)
		'.Shop::addSqlAssociation('product', 'p').'
		WHERE product_shop.active = 0 AND p.`booking_product` = 1'.
		(!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hrt') : ''));
    }

    public static function getTotalRoomTypes($idHotel = null)
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		INNER JOIN `'._DB_PREFIX_.'htl_room_type` hrt
		ON (hrt.`id_product` = p.`id_product`)
		'.Shop::addSqlAssociation('product', 'p').'
        WHERE p.`booking_product` = 1'.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hrt') : '')
    );
    }

    public static function getDisabledProducts()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		WHERE product_shop.active = 0 AND p.`booking_product` = 0');
    }

    public static function getTotalProducts()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
        WHERE p.`booking_product` = 0');
    }

    public static function get8020SalesCatalog($date_from, $date_to)
    {
        $distinct_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT od.product_id)
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
        AND o.`valid` = 1
		'.Shop::addSqlRestriction(false, 'o'));
        if (!$distinct_products) {
            return '0%';
        }
        return round(100 * $distinct_products / AdminStatsController::getTotalRoomTypes()).'%';
    }

    public static function getEmptyCategories()
    {
        $total = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		AND c.active = 1
		AND c.nright = c.nleft + 1');
        $used = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT cp.id_category)
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON c.id_category = cp.id_category
		'.Shop::addSqlAssociation('category', 'c').'
		AND c.active = 1
		AND c.nright = c.nleft + 1');
        return intval($total - $used);
    }

    public static function getCustomerMainGender()
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(IF(c.id_gender IS NOT NULL, 1, 0)) as total, SUM(IF(type = 0, 1, 0)) as male, SUM(IF(type = 1, 1, 0)) as female, SUM(IF(type = 2, 1, 0)) as neutral
		FROM `'._DB_PREFIX_.'customer` c
		LEFT JOIN `'._DB_PREFIX_.'gender` g ON c.id_gender = g.id_gender
		WHERE 1 AND c.deleted = 0 '.Shop::addSqlRestriction());

        if (!$row['total']) {
            return false;
        } elseif ($row['male'] > $row['female'] && $row['male'] >= $row['neutral']) {
            return array('type' => 'male', 'value' => round(100 * $row['male'] / $row['total']));
        } elseif ($row['female'] >= $row['male'] && $row['female'] >= $row['neutral']) {
            return array('type' => 'female', 'value' => round(100 * $row['female'] / $row['total']));
        }
        return array('type' => 'neutral', 'value' => round(100 * $row['neutral'] / $row['total']));
    }

    // @todo price conversion for admin selected currency is to be corrected
    public static function getBestCategory($date_from, $date_to)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT ca.`id_category`
		FROM `'._DB_PREFIX_.'category` ca
		LEFT JOIN `'._DB_PREFIX_.'category_product` capr ON ca.`id_category` = capr.`id_category`
		LEFT JOIN (
			SELECT pr.`id_product`, t.`totalPriceSold`
			FROM `'._DB_PREFIX_.'product` pr
			LEFT JOIN (
				SELECT pr.`id_product`,
					IFNULL(SUM(cp.`product_quantity`), 0) AS totalQuantitySold,
					IFNULL(SUM(cp.`product_price` * cp.`product_quantity`), 0) / o.conversion_rate AS totalPriceSold
				FROM `'._DB_PREFIX_.'product` pr
				LEFT OUTER JOIN `'._DB_PREFIX_.'order_detail` cp ON pr.`id_product` = cp.`product_id`
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = cp.`id_order`
				WHERE o.invoice_date BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				GROUP BY pr.`id_product`
			) t ON t.`id_product` = pr.`id_product`
		) t	ON t.`id_product` = capr.`id_product`
        RIGHT JOIN `'._DB_PREFIX_.'category` c2
        ON c2.`id_category` = '.(int)Configuration::get('PS_SERVICE_CATEGORY').' AND ca.`nleft` >= c2.`nleft` AND ca.`nright` <= c2.`nright`
		WHERE ca.`level_depth` > 2
		GROUP BY ca.`id_category`
		ORDER BY SUM(t.`totalPriceSold`) DESC');
    }

    public static function getMainCountry($date_from, $date_to)
    {
        $total_orders = Order::getOrderCount(array('date_from' => $date_from, 'date_to' => $date_to));
        if (!$total_orders) {
            return false;
        }
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT hbd.id_country, COUNT(*) as orders
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'address` hbd ON o.id_address_delivery = hbd.id_address
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction());
        $row['orders'] = round(100 * $row['orders'] / $total_orders, 1);
        return $row;
    }

    public static function getPendingMessages()
    {
        $pendingStatuses = array(
            CustomerThread::QLO_CUSTOMER_THREAD_STATUS_PENDING2,
            CustomerThread::QLO_CUSTOMER_THREAD_STATUS_PENDING1
        );

        return CustomerThread::getTotalCustomerThreads(
            '`status` IN ('.implode(',', $pendingStatuses).')'.Shop::addSqlRestriction()
        );
    }

    public static function getAverageMessageResponseTime($date_from, $date_to, $return_seconds = false)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT MIN(cm1.date_add) as question, MIN(cm2.date_add) as reply
		FROM `'._DB_PREFIX_.'customer_message` cm1
		INNER JOIN `'._DB_PREFIX_.'customer_message` cm2 ON (cm1.id_customer_thread = cm2.id_customer_thread AND cm1.date_add < cm2.date_add)
		JOIN `'._DB_PREFIX_.'customer_thread` ct ON (cm1.id_customer_thread = ct.id_customer_thread)
		WHERE cm1.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		AND cm1.id_employee = 0 AND cm2.id_employee != 0
		'.Shop::addSqlRestriction().'
		GROUP BY cm1.id_customer_thread');
        $total_questions = $total_replies = $threads = 0;
        foreach ($result as $row) {
            ++$threads;
            $total_questions += strtotime($row['question']);
            $total_replies += strtotime($row['reply']);
        }
        if (!$threads) {
            return 0;
        }

        $seconds = ($total_replies - $total_questions) / $threads;

        return $return_seconds ? $seconds : Tools::ps_round($seconds / 3600, 1);
    }

    public static function getMessagesPerThread($date_from, $date_to)
    {
        $accessWhere = '';
        $employee = Context::getContext()->employee;
        if (!$employee->isSuperAdmin()) {
            $idProfile = $employee->id_profile;
            if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($idProfile, 1, 1)) {
                $accessWhere = ' AND ct.`id_order` IN (SELECT `id_order` FROM `'._DB_PREFIX_.'htl_booking_detail` hbd WHERE `id_hotel` IN ('.implode(',', $acsHtls).'))';
            } else {
                $accessWhere = ' AND ct.`id_order` = 0 ';
            }
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT COUNT(*) as messages
		FROM `'._DB_PREFIX_.'customer_thread` ct
		LEFT JOIN `'._DB_PREFIX_.'customer_message` cm ON (ct.id_customer_thread = cm.id_customer_thread)
		WHERE ct.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction().$accessWhere.'
		GROUP BY ct.id_customer_thread');
        $threads = $messages = 0;
        foreach ($result as $row) {
            ++$threads;
            $messages += $row['messages'];
        }
        if (!$threads) {
            return 0;
        }
        return round($messages / $threads, 1);
    }

    public function displayAjaxGetKpi()
    {
        $value = $this->getLatestKpiValue(Tools::getValue('kpi'), Tools::getValue('id_hotels'));
        if ($value !== false) {
            $array = array('value' => $value);
            if (isset($data)) {
                $array['data'] = $data;
            }
            die(json_encode($array));
        }
        die(json_encode(array('has_errors' => true)));
    }

    public function getLatestKpiValue($kpi, $idHotels)
    {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $value = false;
        switch ($kpi) {
            case 'conversion_rate':
                $nbDaysConversionRate = Validate::isUnsignedInt(Configuration::get('PS_KPI_CONVERSION_RATE_NB_DAYS')) ? Configuration::get('PS_KPI_CONVERSION_RATE_NB_DAYS') : 30;
                if ($nbDaysConversionRate == 1) {
                    $dateFrom = date('Y-m-d');
                } else {
                    $dateFrom = date('Y-m-d', strtotime('-'.($nbDaysConversionRate - 1).' day'));
                }
                $dateTo = date('Y-m-d');
                $visitors = AdminStatsController::getVisits(
                    false,
                    $dateFrom,
                    $dateTo,
                    false /*'day'*/
                );

                $orders = Order::getOrderCount(array(
                    'date_from' => $dateFrom,
                    'date_to'   => $dateTo,
                    'id_hotel'  => $idHotels,
                ));

                $visits_sum = $visitors; //array_sum($visitors);
                $orders_sum = $orders; //array_sum($orders);
                if ($visits_sum) {
                    $value = sprintf('%0.2f', 100 * $orders_sum / $visits_sum);
                } elseif ($orders_sum) {
                    $value = '&infin;';
                } else {
                    $value = 0;
                }
                $value .= '%';

                // ConfigurationKPI::updateValue('CONVERSION_RATE_CHART', Tools::jsonEncode($data));
                break;

            case 'abandoned_cart':
                $value = AdminStatsController::getAbandonedCarts(
                    date('Y-m-d', strtotime('-2 day')).' 0:0:0',
                    date('Y-m-d', strtotime('-1 day')).' 23:59:59',
                    _TIME_1_DAY_
                );
                break;

            case 'installed_modules':
                $value = AdminStatsController::getInstalledModules();
                break;

            case 'disabled_modules':
                $value = AdminStatsController::getDisabledModules();
                break;

            case 'update_modules':
                $value = AdminStatsController::getModulesToUpdate();
                break;

            case 'percent_product_stock':
                $value = AdminStatsController::getPercentProductStock();
                ConfigurationKPI::updateValue('PERCENT_PRODUCT_STOCK', $value);
                ConfigurationKPI::updateValue('PERCENT_PRODUCT_STOCK_EXPIRE', strtotime('+4 hour'));
                break;

            case 'percent_product_out_of_stock':
                $value = AdminStatsController::getPercentProductOutOfStock();
                ConfigurationKPI::updateValue('PERCENT_PRODUCT_OUT_OF_STOCK', $value);
                ConfigurationKPI::updateValue('PERCENT_PRODUCT_OUT_OF_STOCK_EXPIRE', strtotime('+4 hour'));
                break;

            case 'product_avg_gross_margin':
                $value = AdminStatsController::getProductAverageGrossMargin();
                break;

            case 'disabled_categories':
                $value = AdminStatsController::getDisabledCategories();
                break;

            case 'disabled_room_types':
                $value = AdminStatsController::getDisabledRoomTypes($idHotels);
                ConfigurationKPI::updateValue('DISABLED_ROOM_TYPES', $value);
                ConfigurationKPI::updateValue('DISABLED_ROOM_TYPES_EXPIRE', strtotime('+2 hour'));
                break;

            case 'disabled_products':
                if (AdminStatsController::getTotalProducts()) {
                    $value = round(100 * AdminStatsController::getDisabledProducts() / AdminStatsController::getTotalProducts(), 2).'%';
                } else {
                    $value = '0%';
                }
                break;

            case '8020_sales_catalog':
                $value = AdminStatsController::get8020SalesCatalog(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'));
                $value = sprintf($this->l('%d%% of your Catalog'), $value);
                break;

            case 'empty_categories':
                $value = AdminStatsController::getEmptyCategories();
                break;

            case 'customer_main_gender':
                $value = AdminStatsController::getCustomerMainGender();

                if ($value === false) {
                    $value = $this->l('No customers', null, null, false);
                } elseif ($value['type'] == 'female') {
                    $value = sprintf($this->l('%d%% Female customers', null, null, false), $value['value']);
                } elseif ($value['type'] == 'male') {
                    $value = sprintf($this->l('%d%% Male customers', null, null, false), $value['value']);
                } else {
                    $value = sprintf($this->l('%d%% Neutral customers', null, null, false), $value['value']);
                }

                break;

            case 'pending_messages':
                $value = (int)AdminStatsController::getPendingMessages();
                break;

            case 'avg_msg_response_time':
                $value = AdminStatsController::getAverageMessageResponseTime(date('Y-m-d', strtotime('-30 day')), date('Y-m-d'), true);

                if ($value <= 0) {
                    $value = '--';
                } elseif ($value < 60) {
                    $value = sprintf($this->l('%d seconds', null, null, false), $value);
                } elseif ($value < 3600) {
                    $value = sprintf($this->l('%d minutes', null, null, false), (int) $value / 60);
                } else {
                    $value = sprintf($this->l('%.1f hours', null, null, false), $value / 3600);
                }
                break;

            case 'messages_per_thread':
                $value = round(AdminStatsController::getMessagesPerThread(date('Y-m-d', strtotime('-31 day')), date('Y-m-d')), 1);
                break;

            case 'enabled_languages':
                $value = Language::countActiveLanguages();
                break;

            case 'frontoffice_translations':
                $value = self::getFrontOfficeTranslations();
                break;

            case 'backoffice_translations':
                $value = self::getBackOfficeTranslations();
                break;

            case 'main_country':
                if (!($row = AdminStatsController::getMainCountry(date('Y-m-d', strtotime('-30 day')), date('Y-m-d')))) {
                    $value = $this->l('No orders', null, null, false);
                } else {
                    $country = new Country($row['id_country'], $this->context->language->id);
                    $value = sprintf($this->l('%d%% %s', null, null, false), $row['orders'], $country->name);
                }

                break;

            case 'orders_per_customer':
                $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                SELECT COUNT(*)
                FROM `'._DB_PREFIX_.'customer` c
                WHERE c.active = 1
                '.Shop::addSqlRestriction());
                if ($value) {
                    $orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                    SELECT COUNT(*)
                    FROM `'._DB_PREFIX_.'orders` o
                    WHERE o.valid = 1
                    '.Shop::addSqlRestriction());
                    $value = round($orders / $value, 2);
                }

                break;

            case 'average_order_value':
                $daysForAvgOrderVal = Validate::isUnsignedInt(Configuration::get('PS_ORDER_KPI_AVG_ORDER_VALUE_NB_DAYS')) ? Configuration::get('PS_ORDER_KPI_AVG_ORDER_VALUE_NB_DAYS') : 30;

                if ($daysForAvgOrderVal == 1) {
                    $dateFrom = date('Y-m-d');
                } else {
                    $dateFrom = date('Y-m-d', strtotime('-'.($daysForAvgOrderVal -1).' day'));
                }
                $dateTo = date('Y-m-d');
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                SELECT
                    COUNT(o.`id_order`) as orders,
                    SUM(o.`total_paid_tax_excl` / o.`conversion_rate`) as total_paid_tax_excl
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = o.`current_state`
                WHERE o.`invoice_date` BETWEEN "'.pSQL($dateFrom).' 00:00:00"
                AND "'.pSQL($dateTo).' 23:59:59" AND os.`logable` = 1
                AND (
                    EXISTS (
                        SELECT 1
                        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                        WHERE hbd.`id_order` = o.`id_order`' . HotelBranchInformation::addHotelRestriction($idHotels).'
                    ) OR EXISTS (
                        SELECT 1
                        FROM `'._DB_PREFIX_.'service_product_order_detail` spod
                        WHERE spod.`id_order` = o.`id_order`' . HotelBranchInformation::addHotelRestriction($idHotels, 'spod').'
                    )'.(!$idHotels ? ' OR EXISTS (
                        SELECT 1
                        FROM `'._DB_PREFIX_.'service_product_order_detail` spod
                        WHERE spod.`id_order` = o.`id_order` AND spod.`id_hotel` = 0 AND spod.`id_htl_booking_detail` = 0
                    )' : '').'
                )');
                $value = Tools::displayPrice($row['orders'] ? $row['total_paid_tax_excl'] / $row['orders'] : 0, $currency).' ('.$this->l('tax excl.').')';

                break;

            case 'netprofit_visit':
                $daysForProfitPerVisitor = Validate::isUnsignedInt(Configuration::get('PS_ORDER_KPI_PER_VISITOR_PROFIT_NB_DAYS')) ? Configuration::get('PS_ORDER_KPI_PER_VISITOR_PROFIT_NB_DAYS') : 30;

                if ($daysForProfitPerVisitor == 1) {
                    $dateFrom = date('Y-m-d');
                } else {
                    $dateFrom = date('Y-m-d', strtotime('-'.($daysForProfitPerVisitor -1).' day'));
                }

                $dateTo = date('Y-m-d');
                $total_visitors = AdminStatsController::getVisits(false, $dateFrom, $dateTo);
                $net_profits = Order::getTotalSales(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotels));
                $net_profits -= Order::getExpenses(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotels));
                $net_profits -= Order::getTotalPurchases(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotels));

                if ($total_visitors) {
                    $value = Tools::displayPrice($net_profits / $total_visitors, $currency);
                } elseif ($net_profits) {
                    $value = '&infin;';
                } else {
                    $value = Tools::displayPrice(0, $currency);
                }

                break;

            case 'products_per_category':
                $products = AdminStatsController::getTotalProducts();
                $categories = AdminStatsController::getTotalCategories();
                $value = round($products / $categories);
                break;

            case 'top_category':
                if (!($id_category = AdminStatsController::getBestCategory(date('Y-m-d', strtotime('-1 month')), date('Y-m-d', strtotime('+1 month'))))) {
                    $value = $this->l('No category', null, null, false);
                } else {
                    $category = new Category($id_category, $this->context->language->id);
                    $value = $category->name;
                }

                break;

            case 'best_selling_room_type':
                $nbDaysBestSelling = Validate::isUnsignedInt(Configuration::get('PS_KPI_BEST_SELLING_ROOM_TYPE_NB_DAYS')) ? Configuration::get('PS_KPI_BEST_SELLING_ROOM_TYPE_NB_DAYS') : 30;

                if ($nbDaysBestSelling == 1) {
                    $dateFrom = date('Y-m-d');
                } else {
                    $dateFrom  = date('Y-m-d', strtotime('-'.($nbDaysBestSelling - 1).' day'));
                }

                $dateTo = date('Y-m-d');
                if (!($idProduct = HotelBookingDetail::getBestSellingRoomType(array(
                    'date_from' => $dateFrom,
                    'date_to'   => $dateTo,
                    'id_hotel'  => $idHotels,
                )))) {
                    $value = $this->l('--', null, null, false);
                } else {
                    $objProduct = new Product($idProduct, false, $this->context->language->id);
                    $value = $objProduct->name;
                }

                break;

            case 'total_rooms':
                $value = HotelRoomInformation::getTotalRooms($idHotels);

                break;

            case 'occupied_rooms':
                $value = HotelBookingDetail::getDistinctRoomBookingsCount(
                    date('Y-m-d', strtotime('-1 day')),
                    date('Y-m-d'),
                    $idHotels,
                    HotelBookingDetail::STATUS_CHECKED_IN
                );

                break;

            case 'vacant_rooms':
                $totalUnAvailRooms = AdminStatsController::getDisabledRoomsForDiscreteDates(date('Y-m-d'), null, $idHotels);
                $totalUnAvailRooms = $totalUnAvailRooms[strtotime(date('Y-m-d'))];
                $totalRooms = HotelRoomInformation::getTotalRooms($idHotels);
                $totalOccupiedRooms = HotelBookingDetail::getDistinctRoomBookingsCount(
                    date('Y-m-d', strtotime('-1 day')),
                    date('Y-m-d'),
                    $idHotels,
                    HotelBookingDetail::STATUS_CHECKED_IN
                );
                $value = $totalRooms - ($totalUnAvailRooms + $totalOccupiedRooms);

                break;

            case 'booked_rooms':
                $value = HotelBookingDetail::getDistinctRoomBookingsCount(
                    date('Y-m-d'),
                    date('Y-m-d', strtotime('+1 day')),
                    $idHotels,
                    HotelBookingDetail::STATUS_ALLOTED
                );

                break;

            case 'disabled_rooms':
                $value = AdminStatsController::getDisabledRoomsForDiscreteDates(date('Y-m-d'), null, $idHotels);
                $value = $value[strtotime(date('Y-m-d'))];

                break;

            case 'online_bookable_rooms':
                $value = HotelRoomInformation::getAvailableRoomsForDiscreteDates(array('date_from' => date('Y-m-d'), 'id_hotel' => $idHotels, 'show_at_front' => 1));
                $value = $value[strtotime(date('Y-m-d'))];

                break;

            case 'offline_bookable_rooms':
                $value = HotelRoomInformation::getAvailableRoomsForDiscreteDates(array('date_from' => date('Y-m-d'), 'id_hotel' => $idHotels));
                $value = $value[strtotime(date('Y-m-d'))];

                break;

            case 'total_frequent_customers':
                $nbOrdersFrequentCustomers = Configuration::get('PS_KPI_FREQUENT_CUSTOMER_NB_ORDERS');

                $value = AdminStatsController::getTotalFrequentCustomers(
                    date('Y-m-d', strtotime('-365 day')),
                    date('Y-m-d'),
                    $nbOrdersFrequentCustomers,
                    $idHotels
                );

                break;

            case 'revenue_per_available_customer':
                $nbDaysRevPac = Configuration::get('PS_KPI_REVPAC_NB_DAYS');
                if ($nbDaysRevPac == 1) {
                    $dateFrom = date('Y-m-d');
                } else {
                    $dateFrom = date('Y-m-d', strtotime('-'.($nbDaysRevPac -1).' day'));
                }

                $dateTo = date('Y-m-d');
                $value = AdminStatsController::getRevenuePerAvailableCustomer(
                    $dateFrom,
                    $dateTo,
                    $idHotels
                );

                $value = Tools::displayPrice($value, $currency);

                break;

            case 'total_newsletter_registrations':
                $value = AdminStatsController::getTotalNewsletterRegistrations();

                break;

            case 'total_new_customers':
                $nbDaysNewCustomers = Validate::isUnsignedInt(Configuration::get('PS_KPI_NEW_CUSTOMERS_NB_DAYS')) ? Configuration::get('PS_KPI_NEW_CUSTOMERS_NB_DAYS') : 30;

                $value = AdminStatsController::getTotalNewCustomers($nbDaysNewCustomers);

                break;

            case 'total_banned_customers':
                $value = AdminStatsController::getTotalBannedCustomers();

                break;
            case 'total_sales':
                $totalSales = Order::getTotalSales(array('date_from' => '', 'date_to' => '', 'id_hotel' => $idHotels));
                if ($totalSales > 0) {
                    $value = Tools::displayPrice($totalSales, $currency);
                } else {
                    $value = Tools::displayPrice(0, $currency);
                }
                break;
            case 'today_arrivals':
                $dateToday = date('Y-m-d');
                $value = 0;
                if ($arrivalsData = HotelBookingDetail::getArrivalsByDate($dateToday, $idHotels)) {
                    $value = $arrivalsData['total_arrivals'];
                }
                break;
            case 'today_departures':
                $dateToday = date('Y-m-d');
                $value = 0;
                if ($departureData = HotelBookingDetail::getDeparturesByDate($dateToday, $idHotels)) {
                    $value = $departureData['total_departures'];
                }
                break;
            case 'today_stay_over':
                $dateToday = date('Y-m-d');
                $value = HotelBookingDetail::getStayOvers(array('date_from' => $dateToday, 'id_hotel' => $idHotels));
                break;
            case 'total_due_amount':
                $dueAmount = Order::getTotalDueAmount(array('date_from' => '', 'date_to' => '', 'id_hotel' => $idHotels));
                if ($dueAmount > 0) {
                    $value = Tools::displayPrice($dueAmount, $currency);
                } else {
                    $value = Tools::displayPrice(0, $currency);
                }
                break;
            case 'average_lead_time':
                $value = Tools::ps_round(HotelBookingDetail::getAverageLeadTime(array('date_from' => '', 'date_to' => '', 'id_hotel' => $idHotels)), 2);
                if ($value && $value <= 1) {
                    $value .= ' '.$this->l('day');
                } else {
                    $value .= ' '.$this->l('days');
                }

                break;
            case 'average_guest_in_booking':
                $value = HotelBookingDetail::getAverageGuestsPerBooking(array('date_from' => '', 'date_to' => '', 'id_hotel' => $idHotels));
                $value = Tools::ps_round($value['avg_adults'], 2).''.$this->l('Adults').', '.Tools::ps_round($value['avg_children'], 2).''.$this->l('Children');
                break;
            default:
                $value = false;
        }

        return $value;
    }

    public static function getFrontOfficeTranslations()
    {
        $themes = Theme::getThemes();
        $languages = Language::getLanguages();
        $total = $translated = 0;
        foreach ($themes as $theme) {
            foreach ($languages as $language) {
                $result = Translate::getTranslationsCountFrontOffice($theme->name, $language['iso_code']);
                $total += $result['total'];
                $translated += $result['translated'];
            }
        }

        return sprintf('%0.2f', $total ? ($translated / $total) * 100 : 0).'%';
    }

    public static function getBackOfficeTranslations()
    {
        $languages = Language::getLanguages();
        $total = $translated = 0;
        foreach ($languages as $language) {
            $result = Translate::getTranslationsCountBackOffice($language['iso_code']);
            $total += $result['total'];
            $translated += $result['translated'];
        }

        return sprintf('%0.2f', $total ? ($translated / $total) * 100 : 0).'%';
    }

    /**
     * $dateFrom is inclusive
     */
    public static function getAvailabilityLineChartData($days, $dateFrom, $idHotel = null)
    {
        if ($days == 0) {
            return array();
        }

        $dateTo = date('Y-m-d', strtotime($dateFrom.'+'.$days.' days'));
        $availableRoomsDiscrete = HotelRoomInformation::getAvailableRoomsForDiscreteDates(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotel));
        $availabilityData = array();
        if ($availableRoomsDiscrete) {
            foreach ($availableRoomsDiscrete as $timestamp => $availableRoom) {
                $availabilityData['values'][] = array(
                    $timestamp,
                    sprintf('%02d', $availableRoom)
                );
            }
        }

        return $availabilityData;
    }

    public static function getTotalRevenueForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $useCache = true)
    {
        $result = array();
        $servicesRevenueByDates = ServiceProductOrderDetail::getServicesRevenueForDiscreteDates(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotel));
        if ($roomsRevenueByDates = HotelBookingDetail::getRoomRevenueForDiscreteDates(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotel))) {
            foreach ($roomsRevenueByDates as $key => $value) {
                $result[$key] = (isset($servicesRevenueByDates[$key]) ? $servicesRevenueByDates[$key] : 0) + $value;
            }
        }

        return $result;
    }

    public static function getDisabledRoomsForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $useCache = true)
    {
        $dateTo = !$dateTo ? date('Y-m-d', strtotime('+1 day', strtotime($dateFrom))) : $dateTo;
        $discreteDates = array();
        $dateTemp = $dateFrom;
        while ($dateTemp <= $dateTo) {
            $dateNext = date('Y-m-d', strtotime('+1 day', strtotime($dateTemp)));
            $discreteDates[] = array(
                'date_from' => $dateTemp,
                'date_to' => $dateNext,
                'timestamp_from' => strtotime($dateTemp),
            );
            $dateTemp = $dateNext;
        };

        $result = array();
        foreach ($discreteDates as $discreteDate) {
            $cacheKey = 'AdminStats::getDisabledRoomsForDiscreteDates'.'_'.(int) $discreteDate['timestamp_from'].'_'.
            (!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
            if (!Cache::isStored($cacheKey) || !$useCache) {
                $sql = 'SELECT (num_room_type_disabled + num_inactive + num_temporarily_inactive) AS num_disabled
                FROM (
                    SELECT (
                        SELECT IFNULL(COUNT(hri.`id`), 0)
                        FROM `'._DB_PREFIX_.'htl_room_information` hri
                        LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                        WHERE p.`active` = 0'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '').'
                    ) AS num_room_type_disabled,
                    (
                        SELECT IFNULL(COUNT(hri.`id`), 0)
                        FROM `'._DB_PREFIX_.'htl_room_information` hri
                        LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                        WHERE hri.`id_status` = '.(int) HotelRoomInformation::STATUS_INACTIVE.'
                        AND p.`active` = 1'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '').'
                    ) AS num_inactive,
                    (
                        SELECT IFNULL(COUNT(hri.`id`), 0)
                        FROM `'._DB_PREFIX_.'htl_room_information` hri
                        LEFT JOIN `'._DB_PREFIX_.'htl_room_disable_dates` hrdd
                        ON (hrdd.`id_room` = hri.`id`)
                        LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                        WHERE hri.`id_status` = '.(int) HotelRoomInformation::STATUS_TEMPORARY_INACTIVE.'
                        AND ("'.pSQL($discreteDate['date_from']).'" >= hrdd.`date_from` AND "'.pSQL($discreteDate['date_from']).'" < hrdd.`date_to`)
                        AND p.`active` = 1'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '').'
                    ) AS num_temporarily_inactive
                ) AS t';

                $value = Db::getInstance()->getValue($sql);
                Cache::store($cacheKey, $value);
            }

            $result[$discreteDate['timestamp_from']] = Cache::retrieve($cacheKey);
        }

        return $result;
    }

    public static function getTotalFrequentCustomers($dateFrom, $dateTo, $nbOrders = 5, $idHotel = null)
    {
        $sql = 'SELECT COUNT(t.`id_customer`)
        FROM (
            SELECT o.`id_customer`, COUNT(o.`id_order`) AS nb_orders
            FROM `'._DB_PREFIX_.'orders` o
            WHERE o.`valid` = 1 AND o.`date_add` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"
            AND EXISTS (
                SELECT 1
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                WHERE hbd.`id_order` = o.`id_order`' . HotelBranchInformation::addHotelRestriction($idHotel).'
            )
            GROUP BY o.`id_customer`
        ) AS t
        WHERE t.`nb_orders` >= '.(int) $nbOrders;
        $result = Db::getInstance()->getValue($sql);

        return $result;
    }

    public static function getRevenuePerAvailableCustomer($dateFrom, $dateTo, $idHotel = null)
    {
        $totalRevenue = Order::getNetRevenue(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotel));

        $totalCustomers = Db::getInstance()->getValue(
            'SELECT COUNT(c.`id_customer`)
            FROM `'._DB_PREFIX_.'customer` c
            WHERE c.`date_add` <= "'.pSQL($dateTo).' 23:59:59"'
        );

        return $totalCustomers ? $totalRevenue / $totalCustomers : 0;
    }

    public static function getTotalNewsletterRegistrations()
    {
        $customerRegistrations = Db::getInstance()->getValue(
            'SELECT COUNT(c.`id_customer`)
            FROM `'._DB_PREFIX_.'customer` c
            WHERE c.`newsletter` = 1'
        );

        $visitorRegistrations = 0;
        if (Module::isInstalled('blocknewsletter')) {
            $visitorRegistrations = Db::getInstance()->getValue(
                'SELECT COUNT(n.`id`)
                FROM `'._DB_PREFIX_.'newsletter` n
                WHERE n.`active` = 1'
            );
        }

        return $customerRegistrations + $visitorRegistrations;
    }

    public static function getTotalNewCustomers($nbDaysNewCustomers)
    {
        if ($nbDaysNewCustomers == 1) {
            $maxDateAdd = date('Y-m-d');
        } else {
            $maxDateAdd = date('Y-m-d', strtotime('-'.($nbDaysNewCustomers -1).' day'));
        }

        $sql = 'SELECT COUNT(c.`id_customer`)
        FROM `'._DB_PREFIX_.'customer` c
        WHERE c.`date_add` >= "'.pSQL($maxDateAdd).'" AND c.`deleted` = 0';
        $result = Db::getInstance()->getValue($sql);

        return $result;
    }

    public static function getTotalBannedCustomers()
    {
        $sql = 'SELECT COUNT(c.`id_customer`)
        FROM `'._DB_PREFIX_.'customer` c
        WHERE c.`deleted` = 1';
        $result = Db::getInstance()->getValue($sql);

        return $result;
    }

    public static function getGrossOperatingProfitPerAvailableRoomForDiscreteDates($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $roomsRevenues = HotelBookingDetail::getRoomRevenueForDiscreteDates(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotel));
        $operatingExpenses = HotelBookingDetail::getOperatingExpensesForDiscreteDates(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotel));
        $totalRooms = HotelRoomInformation::getTotalRoomsForDiscreteDates(array('date_from' => $dateFrom, 'date_to' => $dateTo, 'id_hotel' => $idHotel));

        if (!(count($roomsRevenues) == count($operatingExpenses)
            && count($operatingExpenses) == count($totalRooms))
        ) {
            return false;
        }

        $operatingProfits = array();
        foreach ($roomsRevenues as $key => $roomsRevenue) {
            if ($roomsRevenue != 0 && $totalRooms[$key] != 0) {
                $operatingProfits[$key] = (($roomsRevenue - $operatingExpenses[$key]) / $totalRooms[$key]);
            } else {
                $operatingProfits[$key] = 0;
            }
        }

        return $operatingProfits;
    }

    public static function getRoomNightsData($dateFrom, $dateTo = null, $idHotel = null, $useCache = true, $average = false, $roundAvg = false)
    {
        $dateTo = !$dateTo ? date('Y-m-d', strtotime('+1 day', strtotime($dateFrom))) : $dateTo;

        $idsHotel = array();
        if (is_int($idHotel)) {
            $idsHotel[] = $idHotel;
        } else {
            $idsHotel = $idHotel;
        }

        // collect data
        $hotelsData = array();
        foreach ($idsHotel as $idHotel) {
            $hotelsData[$idHotel] = HotelBookingDetail::getOccupiedRoomsForDiscreteDates(array(
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'id_hotel'  => $idHotel,
            ));
        }

        // calculate sums
        $result = array();
        foreach ($hotelsData as $hotelData) {
            foreach ($hotelData as $timestamp => $value) {
                if (!array_key_exists($timestamp, $result)) {
                    $result[$timestamp] = $value;
                } else {
                    $result[$timestamp] += $value;
                }
            }
        }

        // calculate averages
        if ($average) {
            $totalHotels = count($idsHotel);
            if ($totalHotels > 1) {
                foreach ($result as $timestamp => &$value) {
                    $value = $value / $totalHotels;
                    if ($roundAvg) {
                        $value = Tools::ps_round($value, 2);
                    }
                }
            }
        }

        return $result;
    }

}
