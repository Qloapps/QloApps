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
    public static function getVisits($unique = false, $date_from, $date_to, $granularity = false)
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

    public static function getAbandonedCarts($date_from, $date_to)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT id_guest)
		FROM `'._DB_PREFIX_.'cart`
		WHERE `date_add` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"
		AND NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'orders` WHERE `'._DB_PREFIX_.'orders`.id_cart = `'._DB_PREFIX_.'cart`.id_cart)
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

    public static function getDisabledRoomTypes()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		WHERE product_shop.active = 0 AND p.`booking_product` = 1');
    }

    public static function getTotalRoomTypes()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
        WHERE p.`booking_product` = 1');
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

    public static function getTotalSales($date_from, $date_to, $granularity = false, $id_hotel = false)
    {
        if ($granularity == 'day') {
            $sales = array();
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
                'SELECT LEFT(`invoice_date`, 10) AS date, SUM(total_paid_tax_excl / o.conversion_rate) AS sales,
                (
                    SELECT hbd.`id_hotel`
                    FROM`'._DB_PREFIX_.'htl_booking_detail` hbd
                    WHERE hbd.`id_order` = o.`id_order` LIMIT 1
                ) AS id_hotel
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
                WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
                GROUP BY LEFT(`invoice_date`, 10)
                HAVING 1 '.HotelBranchInformation::addHotelRestriction($id_hotel)
            );

            foreach ($result as $row) {
                $sales[strtotime($row['date'])] = $row['sales'];
            }

            return $sales;
        } elseif ($granularity == 'month') {
            $sales = array();
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
                'SELECT LEFT(`invoice_date`, 7) AS date, SUM(total_paid_tax_excl / o.conversion_rate) AS sales,
                (
                    SELECT hbd.`id_hotel`
                    FROM`'._DB_PREFIX_.'htl_booking_detail` hbd
                    WHERE hbd.`id_order` = o.`id_order` LIMIT 1
                ) AS id_hotel
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
                WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
                GROUP BY LEFT(`invoice_date`, 7)
                HAVING 1 '.HotelBranchInformation::addHotelRestriction($id_hotel)
            );

            foreach ($result as $row) {
                $sales[strtotime($row['date'].'-01')] = $row['sales'];
            }

            return $sales;
        } else {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                'SELECT SUM(total_paid_tax_excl / o.conversion_rate),
                (
                    SELECT hbd.`id_hotel`
                    FROM`'._DB_PREFIX_.'htl_booking_detail` hbd
                    WHERE hbd.`id_order` = o.`id_order` LIMIT 1
                ) AS id_hotel
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
                WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
                HAVING 1 '.HotelBranchInformation::addHotelRestriction($id_hotel)
            );
        }
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
        return round(100 * $distinct_products / AdminStatsController::getTotalProducts()).'%';
    }

    public static function getOrders($date_from, $date_to, $granularity = false, $id_hotel = false)
    {
        if ($granularity == 'day') {
            $orders = array();
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT LEFT(`invoice_date`, 10) as date, COUNT(DISTINCT o.`id_order`) as orders
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id_order` = o.`id_order`)
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o')
            .HotelBranchInformation::addHotelRestriction($id_hotel, 'hbd').'
			GROUP BY LEFT(`invoice_date`, 10)');
            foreach ($result as $row) {
                $orders[strtotime($row['date'])] = $row['orders'];
            }
            return $orders;
        } elseif ($granularity == 'month') {
            $orders = array();
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT LEFT(`invoice_date`, 7) as date, COUNT(DISTINCT o.`id_order`) as orders
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id_order` = o.`id_order`)
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o')
            .HotelBranchInformation::addHotelRestriction($id_hotel, 'hbd').'
			GROUP BY LEFT(`invoice_date`, 7)');
            foreach ($result as $row) {
                $orders[strtotime($row['date'].'-01')] = $row['orders'];
            }
            return $orders;
        } else {
            $orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(DISTINCT o.`id_order`) as orders
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id_order` = o.`id_order`)
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o')
            .HotelBranchInformation::addHotelRestriction($id_hotel, 'hbd'));
        }

        return $orders;
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
		SELECT SUM(IF(g.id_gender IS NOT NULL, 1, 0)) as total, SUM(IF(type = 0, 1, 0)) as male, SUM(IF(type = 1, 1, 0)) as female, SUM(IF(type = 2, 1, 0)) as neutral
		FROM `'._DB_PREFIX_.'customer` c
		LEFT JOIN `'._DB_PREFIX_.'gender` g ON c.id_gender = g.id_gender
		WHERE c.active = 1 '.Shop::addSqlRestriction());

        if (!$row['total']) {
            return false;
        } elseif ($row['male'] > $row['female'] && $row['male'] >= $row['neutral']) {
            return array('type' => 'male', 'value' => round(100 * $row['male'] / $row['total']));
        } elseif ($row['female'] >= $row['male'] && $row['female'] >= $row['neutral']) {
            return array('type' => 'female', 'value' => round(100 * $row['female'] / $row['total']));
        }
        return array('type' => 'neutral', 'value' => round(100 * $row['neutral'] / $row['total']));
    }

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
		WHERE ca.`level_depth` > 1
		GROUP BY ca.`id_category`
		ORDER BY SUM(t.`totalPriceSold`) DESC');
    }

    public static function getMainCountry($date_from, $date_to)
    {
        $total_orders = AdminStatsController::getOrders($date_from, $date_to);
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

    public static function getAverageCustomerAge()
    {
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT AVG(DATEDIFF("'.date('Y-m-d').' 00:00:00", birthday))
		FROM `'._DB_PREFIX_.'customer` c
		WHERE active = 1
		AND birthday IS NOT NULL AND birthday != "0000-00-00" '.Shop::addSqlRestriction());
        return round($value / 365);
    }

    public static function getPendingMessages()
    {
        return CustomerThread::getTotalCustomerThreads('status LIKE "%pending%" OR status = "open"'.Shop::addSqlRestriction());
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
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT COUNT(*) as messages
		FROM `'._DB_PREFIX_.'customer_thread` ct
		LEFT JOIN `'._DB_PREFIX_.'customer_message` cm ON (ct.id_customer_thread = cm.id_customer_thread)
		WHERE ct.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction().'
		AND status = "closed"
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

    public static function getPurchases($date_from, $date_to, $granularity = false)
    {
        if ($granularity == 'day') {
            $purchases = array();
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT
				LEFT(`invoice_date`, 10) as date,
				SUM(od.`product_quantity` * IF(
					od.`purchase_supplier_price` > 0,
					od.`purchase_supplier_price` / `conversion_rate`,
					od.`original_product_price` * '.(int)Configuration::get('CONF_AVERAGE_PRODUCT_MARGIN').' / 100
				)) as total_purchase_price
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o').'
			GROUP BY LEFT(`invoice_date`, 10)');
            foreach ($result as $row) {
                $purchases[strtotime($row['date'])] = $row['total_purchase_price'];
            }
            return $purchases;
        } else {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT SUM(od.`product_quantity` * IF(
				od.`purchase_supplier_price` > 0,
				od.`purchase_supplier_price` / `conversion_rate`,
				od.`original_product_price` * '.(int)Configuration::get('CONF_AVERAGE_PRODUCT_MARGIN').' / 100
			)) as total_purchase_price
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o'));
        }
    }

    public static function getExpenses($date_from, $date_to, $granularity = false)
    {
        $expenses = ($granularity == 'day' ? array() : 0);

        $orders = Db::getInstance()->ExecuteS('
		SELECT
			LEFT(`invoice_date`, 10) as date,
			total_paid_tax_incl / o.conversion_rate as total_paid_tax_incl,
			total_shipping_tax_excl / o.conversion_rate as total_shipping_tax_excl,
			o.module,
			hbd.id_country,
			o.id_currency,
			c.id_reference as carrier_reference
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'address` hbd ON o.id_address_delivery = hbd.id_address
		LEFT JOIN `'._DB_PREFIX_.'carrier` c ON o.id_carrier = c.id_carrier
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
		'.Shop::addSqlRestriction(false, 'o'));
        foreach ($orders as $order) {
            // Add flat fees for this order
            $flat_fees = Configuration::get('CONF_ORDER_FIXED') + (
                $order['id_currency'] == Configuration::get('PS_CURRENCY_DEFAULT')
                    ? Configuration::get('CONF_'.strtoupper($order['module']).'_FIXED')
                    : Configuration::get('CONF_'.strtoupper($order['module']).'_FIXED_FOREIGN')
                );

            // Add variable fees for this order
            $var_fees = $order['total_paid_tax_incl'] * (
                $order['id_currency'] == Configuration::get('PS_CURRENCY_DEFAULT')
                    ? Configuration::get('CONF_'.strtoupper($order['module']).'_VAR')
                    : Configuration::get('CONF_'.strtoupper($order['module']).'_VAR_FOREIGN')
                ) / 100;

            // Tally up these fees
            if ($granularity == 'day') {
                if (!isset($expenses[strtotime($order['date'])])) {
                    $expenses[strtotime($order['date'])] = 0;
                }
                $expenses[strtotime($order['date'])] += $flat_fees + $var_fees;
            } else {
                $expenses += $flat_fees + $var_fees;
            }
        }
        return $expenses;
    }

    public function displayAjaxGetKpi()
    {
        $value = $this->getLatestKpiValue(Tools::getValue('kpi'));
        if ($value !== false) {
            $array = array('value' => $value);
            if (isset($data)) {
                $array['data'] = $data;
            }
            die(json_encode($array));
        }
        die(json_encode(array('has_errors' => true)));
    }

    public function getLatestKpiValue($kpi)
    {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $value = false;
        switch ($kpi) {
            case 'conversion_rate':
                $visitors = AdminStatsController::getVisits(true, date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), false /*'day'*/);
                $orders = AdminStatsController::getOrders(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), false /*'day'*/);

                // $data = array();
                // $from = strtotime(date('Y-m-d 00:00:00', strtotime('-31 day')));
                // $to = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
                // for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date))
                    // if (isset($visitors[$date]) && $visitors[$date])
                        // $data[$date] = round(100 * ((isset($orders[$date]) && $orders[$date]) ? $orders[$date] : 0) / $visitors[$date], 2);
                    // else
                        // $data[$date] = 0;

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
                $value = AdminStatsController::getAbandonedCarts(date('Y-m-d H:i:s', strtotime('-2 day')), date('Y-m-d H:i:s', strtotime('-1 day')));
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
                if (AdminStatsController::getTotalRoomTypes()) {
                    $value = round(100 * AdminStatsController::getDisabledRoomTypes() / AdminStatsController::getTotalRoomTypes(), 2).'%';
                } else {
                    $value = '0%';
                }
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

            case 'avg_customer_age':
                $value = sprintf($this->l('%d years', null, null, false), AdminStatsController::getAverageCustomerAge(), 1);
                break;

            case 'pending_messages':
                $value = (int)AdminStatsController::getPendingMessages();
                break;

            case 'avg_msg_response_time':
                $value = AdminStatsController::getAverageMessageResponseTime(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), true);

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
                $value = round(AdminStatsController::getMessagesPerThread(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day'))), 1);
                break;

            case 'newsletter_registrations':
                $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                SELECT COUNT(*)
                FROM `'._DB_PREFIX_.'customer`
                WHERE newsletter = 1
                '.Shop::addSqlRestriction(Shop::SHARE_ORDER));
                if (Module::isInstalled('blocknewsletter')) {
                    $value += Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                    SELECT COUNT(*)
                    FROM `'._DB_PREFIX_.'newsletter`
                    WHERE active = 1
                    '.Shop::addSqlRestriction(Shop::SHARE_ORDER));
                }

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
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                SELECT
                    COUNT(o.`id_order`) as orders,
                    SUM(o.`total_paid_tax_excl` / o.`conversion_rate`) as total_paid_tax_excl
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = o.`current_state`
                WHERE o.`invoice_date` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-31 day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59" AND os.`logable` = 1
                '.Shop::addSqlRestriction());
                $value = Tools::displayPrice($row['orders'] ? $row['total_paid_tax_excl'] / $row['orders'] : 0, $currency);
                break;

            case 'netprofit_visit':
                $date_from = date('Y-m-d', strtotime('-31 day'));
                $date_to = date('Y-m-d', strtotime('-1 day'));

                $total_visitors = AdminStatsController::getVisits(false, $date_from, $date_to);
                $net_profits = AdminStatsController::getTotalSales($date_from, $date_to);
                $net_profits -= AdminStatsController::getExpenses($date_from, $date_to);
                $net_profits -= AdminStatsController::getPurchases($date_from, $date_to);

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
                if (!($id_category = AdminStatsController::getBestCategory(date('Y-m-d', strtotime('-1 month')), date('Y-m-d')))) {
                    $value = $this->l('No category', null, null, false);
                } else {
                    $category = new Category($id_category, $this->context->language->id);
                    $value = $category->name;
                }

                break;
            default:
                $value = false;
        }
        if ($value !== false) {
            $array = array('value' => $value);
            if (isset($data)) {
                $array['data'] = $data;
            }
            die(json_encode($array));
        }
        die(json_encode(array('has_errors' => true)));
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

    public static function getArrivalsByDate($date, $idHotel = false)
    {
        $totalArrivals = Db::getInstance()->getValue(
            'SELECT COUNT(hbd.`id_room`)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`is_refunded` = 0 AND hbd.`is_back_order` = 0
            AND hbd.`date_from` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );

        $arrived = Db::getInstance()->getValue(
            'SELECT COUNT(hbd.`id_room`)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`is_refunded` = 0 AND hbd.`is_back_order` = 0
            AND hbd.`date_from` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"
            AND hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_IN.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );

        return array('arrived' => $arrived, 'total_arrivals' => $totalArrivals);
    }

    public static function getDeparturesByDate($date, $idHotel = false)
    {
        $totalDepartures = Db::getInstance()->getValue(
            'SELECT COUNT(hbd.`id_room`)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`is_refunded` = 0
            AND ((hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_IN.') OR
            (hbd.`check_in` != "0000:00:00 00:00:00" AND hbd.`check_out` != "0000:00:00 00:00:00"))
            AND hbd.`date_to` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );

        $departed = Db::getInstance()->getValue(
            'SELECT COUNT(hbd.`id_room`)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`is_refunded` = 0
            AND hbd.`date_to` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"
            AND hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_OUT.'
            AND hbd.`check_in` != "0000:00:00 00:00:00"'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );

        return array('departed' => $departed, 'total_departures' => $totalDepartures);
    }

    public static function getBookingsByDate($date, $idHotel = false)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(DISTINCT hbd.`id_order`)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`date_add` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );
    }

    public static function getStayOversByDate($date, $idHotel = false)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(hbd.`id_room`)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`is_refunded` = 0 AND hbd.`is_back_order` = 0
            AND hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_IN.'
            AND hbd.`date_to` > "'.pSQL($date).' 00:00:00"'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );
    }

    public static function getCancelledBookingsByDate($date, $idHotel = false)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(DISTINCT o.`id_order`)
            FROM `'._DB_PREFIX_.'orders` o
            LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = o.`current_state`)
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id_order` = o.`id_order`)
            WHERE o.`date_upd` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"
            AND o.`current_state` IN ('.implode(',', array(
                (int) Configuration::get('PS_OS_CANCELED'),
                (int) Configuration::get('PS_OS_REFUND'))
            ).')'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );
    }

    public static function getGuestsByDate($date, $idHotel = false)
    {
        return Db::getInstance()->getRow(
            'SELECT SUM(hbd.`adults`) AS `adults`, SUM(hbd.`children`) AS `children`
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`is_refunded` = 0 AND hbd.`is_back_order` = 0
            AND hbd.`date_from` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );
    }

    public static function getTotalRooms($idHotel = false)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(`id`) FROM `'._DB_PREFIX_.'htl_room_information` hri
            WHERE 1'.HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
        );
    }

    public static function getOccupancyData($dateFrom, $dateTo, $idsHotel = false)
    {
        $occupancyData = array('count_total' => 0, 'count_occupied' => 0, 'count_available' => 0, 'count_unavailable' => 0);

        $countTotal = Db::getInstance()->getValue(
            'SELECT COUNT(hri.`id`)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi
            ON (hbi.`id` = hri.`id_hotel`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
            ON (p.`id_product` = hri.`id_product`)
            WHERE p.`active` = 1'.
            HotelBranchInformation::addHotelRestriction($idsHotel, 'hri')
        );
        $occupancyData['count_total'] = $countTotal;

        $countOccupied = 0;
        if ($dateFrom != $dateTo) {
            $countOccupied = Db::getInstance()->getValue(
                'SELECT COUNT(DISTINCT hbd.`id_room`)
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri
                ON (hri.`id` = hbd.`id_room`)
                LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hri.`id_product`)
                WHERE p.`active` = 1
                AND hbd.`date_from` < "'.pSQL($dateTo).' 00:00:00" AND hbd.`date_to` > "'.pSQL($dateFrom).' 00:00:00"'.
                HotelBranchInformation::addHotelRestriction($idsHotel, 'hbd')
            );
        } else {
            $countOccupied = Db::getInstance()->getValue(
                'SELECT COUNT(DISTINCT hbd.`id_room`)
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri
                ON (hri.`id` = hbd.`id_room`)
                LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hri.`id_product`)
                WHERE p.`active` = 1
                AND hbd.`date_from` <= "'.pSQL($dateFrom).' 00:00:00" AND hbd.`date_to` > "'.pSQL($dateFrom).' 00:00:00"'.
                HotelBranchInformation::addHotelRestriction($idsHotel, 'hbd')
            );
        }
        $occupancyData['count_occupied'] = $countOccupied;

        $countUnavailable = Db::getInstance()->getValue(
            'SELECT COUNT(hri.`id`)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
            ON (hbd.`id` = hri.`id_hotel`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
            ON (p.`id_product` = hri.`id_product`)
            WHERE p.`active` = 1
            AND hri.`id_status` != '.(int) HotelRoomInformation::STATUS_ACTIVE.
            HotelBranchInformation::addHotelRestriction($idsHotel, 'hbd')
        );
        $occupancyData['count_unavailable'] = $countUnavailable;

        $occupancyData['count_available'] = $countTotal - $countOccupied - $countUnavailable;

        return $occupancyData;
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
        $availableRoomsDiscrete = self::getAvailableRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);
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

    public static function getAverageDailyRateForDiscreteDates($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $occupiedRooms = self::getOccupiedRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $roomsRevenues = self::getRoomsRevenueForDiscreteDates($dateFrom, $dateTo, $idHotel);

        if (count($occupiedRooms) != count($roomsRevenues)) {
            return false;
        }

        $averageDailyRates = array();
        foreach ($occupiedRooms as $key => $occupiedRoom) {
            $averageDailyRates[$key] = $occupiedRoom ? $roomsRevenues[$key] / $occupiedRoom : 0;
        }

        return $averageDailyRates;
    }

    public static function getAverageDailyRate($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $averageDailyRates = self::getAverageDailyRateForDiscreteDates($dateFrom, $dateTo, $idHotel);

        if (!is_array($averageDailyRates)) {
            return 0;
        }

        $sumAverageDailyRates = 0;
        $countAverageDailyRates = 0;
        foreach ($averageDailyRates as $averageDailyRate) {
            if ($averageDailyRate != 0) {
                $sumAverageDailyRates += $averageDailyRate;
                $countAverageDailyRates++;
            }
        }

        return $countAverageDailyRates ? $sumAverageDailyRates / $countAverageDailyRates : 0;
    }

    public static function getCancellationRate($dateFrom, $dateTo, $idHotel = false)
    {
        $numAllOrders = Db::getInstance()->getValue(
            'SELECT COUNT(DISTINCT o.`id_order`) FROM `'._DB_PREFIX_.'orders` o
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id_order` = o.`id_order`)
            WHERE o.`date_add` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );

        $numCancelledOrders = Db::getInstance()->getValue(
            'SELECT COUNT(DISTINCT o.`id_order`) FROM `'._DB_PREFIX_.'orders` o
            LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = o.`current_state`)
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id_order` = o.`id_order`)
            WHERE o.`date_add` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"
            AND o.`current_state` IN ('.implode(',', array(
                (int) Configuration::get('PS_OS_CANCELED'),
                (int) Configuration::get('PS_OS_REFUND'))
            ).')'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );

        if ($numAllOrders != 0) {
            return $numCancelledOrders / $numAllOrders * 100;
        } else {
            return 0;
        }
    }

    public static function getRevenue($dateFrom, $dateTo, $idHotel = false)
    {
        $result = Db::getInstance()->getValue(
            'SELECT SUM(total_paid_tax_excl - refunded_amount)
            FROM (
                SELECT o.`total_paid_tax_excl` / o.`conversion_rate` AS total_paid_tax_excl,
                IFNULL(orr.`refunded_amount`, 0) AS refunded_amount,
                (
                    SELECT hbd.`id_hotel`
                    FROM`'._DB_PREFIX_.'htl_booking_detail` hbd
                    WHERE hbd.`id_order` = o.`id_order` LIMIT 1
                ) AS id_hotel
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `' ._DB_PREFIX_.'order_return` orr ON orr.`id_order` = o.`id_order`
                WHERE o.`valid` = 1 AND o.`invoice_date` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"
                HAVING 1 '.HotelBranchInformation::addHotelRestriction($idHotel).'
            ) AS t'
        );

        return $result ? $result : 0;
    }

    public static function getNightsStayed($dateFrom, $dateTo, $idHotel = false)
    {
        $dateFrom = date('Y-m-d H:i:s', strtotime($dateFrom));
        $dateTo = date('Y-m-d H:i:s', strtotime($dateTo));

        return Db::getInstance()->getValue(
            'SELECT IFNULL(SUM(DATEDIFF(
                IF (hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_OUT.', IF ("'.$dateTo.'" > check_out, check_out, "'.$dateTo.'"), IF ("'.$dateTo.'" > date_to, date_to, "'.$dateTo.'")),
                IF (hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_OUT.', IF("'.$dateFrom.'" < check_in, check_in, "'.$dateFrom.'"), IF("'.$dateFrom.'" < date_from, date_from, "'.$dateFrom.'"))
            )), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`is_refunded` = 0 AND hbd.`is_back_order` = 0 AND
            (IF (hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_OUT.',
                (hbd.`check_in` < \''.pSQL($dateTo).'\' AND hbd.`check_out` >= \''.pSQL($dateFrom).'\'),
                (hbd.`date_from` < \''.pSQL($dateTo).'\' AND hbd.`date_to` >= \''.pSQL($dateFrom).'\')
            ))'.HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );
    }

    public static function getRecentOrdersByHotel($idHotel = null, $limit = null)
    {
        return Db::getInstance()->executeS(
            'SELECT *, (
                SELECT osl.`name`
                FROM `'._DB_PREFIX_.'order_state_lang` osl
                WHERE osl.`id_order_state` = o.`current_state`
                AND osl.`id_lang` = '.(int) Context::getContext()->language->id.'
                LIMIT 1
            ) AS `state_name`, o.`date_add` AS `date_add`, o.`date_upd` AS `date_upd`
            FROM `'._DB_PREFIX_.'orders` o
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id_order` = o.`id_order`)
            WHERE 1'.HotelBranchInformation::addHotelRestriction($idHotel, 'hbd').'
            GROUP BY o.`id_order`
            ORDER BY o.`date_add` DESC'.
            ((int) $limit ? ' LIMIT 0, '.(int) $limit : '')
        );
    }

    public static function getArrivalsInfoByDate($date, $idHotel = null)
    {
        $sql = 'SELECT hbd.*, CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
        DATEDIFF(hbd.`date_to`, hbd.`date_from`) AS los
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = hbd.`id_customer`)
        WHERE hbd.`is_refunded` = 0 AND hbd.`date_from` = "'.pSQL($date).' 00:00:00"
        AND hbd.`id_status` != '.(int) HotelBookingDetail::STATUS_CHECKED_IN.'
        AND hbd.`id_status` != '.(int) HotelBookingDetail::STATUS_CHECKED_OUT.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public static function getDeparturesInfoByDate($date, $idHotel = null)
    {
        $sql = 'SELECT hbd.*, CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
        DATEDIFF(hbd.`date_to`, hbd.`date_from`) AS los
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = hbd.`id_customer`)
        WHERE hbd.`is_refunded` = 0 AND hbd.`date_to` = "'.pSQL($date).' 00:00:00"
        AND hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_IN.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public static function getInHousesInfo($idHotel = null)
    {
        $sql = 'SELECT hbd.*, CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
        DATEDIFF(hbd.`date_to`, hbd.`date_from`) AS los
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = hbd.`id_customer`)
        WHERE hbd.`is_refunded` = 0 AND hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_IN.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public static function getNewBookingsInfoByDate($date, $idHotel = null)
    {
        $sql = 'SELECT hbd.`id_customer`, CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
        COUNT(hbd.`id`) AS total_rooms, SUM(hbd.`adults` + hbd.`children`) AS total_guests,
        hbd.`id_hotel`, hbd.`hotel_name`, hbd.`id_order`, o.`total_paid_tax_excl`, o.`id_currency`, osl.`name` AS `state_name`
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
        LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl
        ON (osl.`id_order_state` = o.`current_state` AND osl.`id_lang` = '.(int) Context::getContext()->language->id.')
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = hbd.`id_customer`)
        WHERE hbd.`date_add` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"'.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
        GROUP BY hbd.`id_order`';
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public static function getCancellationsInfoByDate($date, $idHotel = null)
    {
        $sql = 'SELECT orr.`id_order_return`, orr.`id_customer`, hbd.`room_num`, hbd.`id_product`, hbd.`room_type_name`,
        CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
        hbd.`id_hotel`, hbd.`hotel_name`, SUM(hbd.`adults` + hbd.`children`) AS total_guests,
        hbd.`date_from`, hbd.`date_to`, orr.`id_order`
        FROM `'._DB_PREFIX_.'order_return` orr
        LEFT JOIN `'._DB_PREFIX_.'order_return_detail` ord ON (ord.`id_order_return` = orr.`id_order_return`)
        LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = orr.`id_order`)
        LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id` = ord.`id_htl_booking`)
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = orr.`id_customer`)
        WHERE orr.`date_add` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"
        AND orr.`state` = '.(int) OrderReturnState::ORDER_RETRUN_FIRST_STATUS.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
        GROUP BY ord.`id_htl_booking`
        ORDER BY orr.`date_add` DESC';
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public static function getOccupiedRoomsForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $useCache = true)
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
            $cacheKey = 'AdminStats::getOccupiedRoomsForDiscreteDates'.'_'.(int) $discreteDate['timestamp_from'].'_'.
            (!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
            if (!Cache::isStored($cacheKey) || !$useCache) {
                $sql = 'SELECT COUNT(DISTINCT hbd.`id_room`)
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri
                ON (hri.`id` = hbd.`id_room`)
                LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hri.`id_product`)
                WHERE p.`active` = 1
                AND hbd.`is_refunded` = 0
                AND hbd.`date_from` < "'.pSQL($discreteDate['date_to']).' 00:00:00" AND hbd.`date_to` > "'.pSQL($discreteDate['date_from']).' 00:00:00"'.
                (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');

                $value = Db::getInstance()->getValue($sql);
                Cache::store($cacheKey, $value);
            }

            $result[$discreteDate['timestamp_from']] = Cache::retrieve($cacheKey);
        }

        return $result;
    }

    public static function getRoomsRevenueForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $useCache = true)
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
            $cacheKey = 'AdminStats::getRoomsRevenueForDiscreteDates'.'_'.(int) $discreteDate['timestamp_from'].'_'.
            (!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
            if (!Cache::isStored($cacheKey) || !$useCache) {
                $sql = 'SELECT IFNULL(SUM(hbd.`total_price_tax_excl` / DATEDIFF(hbd.`date_to`, hbd.`date_from`)), 0)
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hbd.`id_product`)
                WHERE p.`active` = 1
                AND hbd.`is_refunded` = 0
                AND hbd.`date_from` < "'.pSQL($discreteDate['date_to']).' 00:00:00" AND hbd.`date_to` > "'.pSQL($discreteDate['date_from']).' 00:00:00"'.
                (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');

                $value = Db::getInstance()->getValue($sql);
                Cache::store($cacheKey, $value);
            }

            $result[$discreteDate['timestamp_from']] = Cache::retrieve($cacheKey);
        }

        return $result;
    }

    public static function getTotalRevenueForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $useCache = true)
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
            $cacheKey = 'AdminStats::getTotalRevenueForDiscreteDates'.'_'.(int) $discreteDate['timestamp_from'].'_'.
            (!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
            if (!Cache::isStored($cacheKey) || !$useCache) {
                $sql = 'SELECT SUM((current_parts / total_parts) * total_paid_tax_excl) AS total_revenue
                FROM (
                    SELECT o.`id_order`,
                    o.`total_paid_tax_excl` / o.`conversion_rate` AS total_paid_tax_excl,
                    (
                        SELECT SUM(DATEDIFF(hbd.`date_to`, hbd.`date_from`))
                        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                        INNER JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hbd.`id_product`)
                        WHERE hbd.`id_order` = o.`id_order`
                        AND p.`active` = 1 AND hbd.`is_refunded` = 0'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
                    ) AS total_parts,
                    (
                        SELECT COUNT(*)
                        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                        INNER JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hbd.`id_product`)
                        WHERE hbd.`id_order` = o.`id_order`
                        AND hbd.`date_from` <= "'.pSQL($discreteDate['date_from']).' 00:00:00" AND hbd.`date_to` >= "'.pSQL($discreteDate['date_to']).' 00:00:00"
                        AND p.`active` = 1 AND hbd.`is_refunded` = 0'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
                    ) AS current_parts
                    FROM `'._DB_PREFIX_.'orders` o
                    HAVING total_parts IS NOT NULL AND current_parts > 0
                    ORDER BY o.`id_order`
                ) AS t';

                $value = Db::getInstance()->getValue($sql);
                $value = !$value ? 0: (float) $value;
                Cache::store($cacheKey, $value);
            }

            $result[$discreteDate['timestamp_from']] = Cache::retrieve($cacheKey);
        }

        return $result;
    }

    /**
     * total rooms (available for booking) for each date
     */
    public static function getTotalRoomsForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $useCache = true)
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
            $cacheKey = 'AdminStats::getTotalRoomsForDiscreteDates'.'_'.(int) $discreteDate['timestamp_from'].'_'.
            (!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
            if (!Cache::isStored($cacheKey) || !$useCache) {
                $sql = 'SELECT (num_total_added - num_inactive - num_temporarily_inactive) AS num_total
                FROM (
                    SELECT (
                        SELECT IFNULL(COUNT(hri.`id`), 0)
                        FROM `'._DB_PREFIX_.'htl_room_information` hri
                        LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                        WHERE p.`active` = 1'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '').'
                    ) AS num_total_added,
                    (
                        SELECT COUNT(DISTINCT hbd.`id_room`)
                        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                        LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri
                        ON (hri.`id` = hbd.`id_room`)
                        LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                        WHERE p.`active` = 1
                        AND hbd.`is_refunded` = 0
                        AND hbd.`date_from` < "'.pSQL($discreteDate['date_to']).' 00:00:00" AND hbd.`date_to` > "'.pSQL($discreteDate['date_from']).' 00:00:00"'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
                    ) AS num_booked,
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

    /**
     * total available (unoccupied) for each date
     */
    public static function getAvailableRoomsForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $useCache = true)
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
            $cacheKey = 'AdminStats::getAvailableRoomsForDiscreteDates'.'_'.(int) $discreteDate['timestamp_from'].'_'.
            (!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
            if (!Cache::isStored($cacheKey) || !$useCache) {
                $sql = 'SELECT (num_total - num_booked - num_inactive - num_temporarily_inactive) AS num_available
                FROM (
                    SELECT (
                        SELECT IFNULL(COUNT(hri.`id`), 0)
                        FROM `'._DB_PREFIX_.'htl_room_information` hri
                        LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                        WHERE p.`active` = 1'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '').'
                    ) AS num_total,
                    (
                        SELECT COUNT(DISTINCT hbd.`id_room`)
                        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                        LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri
                        ON (hri.`id` = hbd.`id_room`)
                        LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                        WHERE p.`active` = 1
                        AND hbd.`is_refunded` = 0
                        AND hbd.`date_from` < "'.pSQL($discreteDate['date_to']).' 00:00:00" AND hbd.`date_to` > "'.pSQL($discreteDate['date_from']).' 00:00:00"'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
                    ) AS num_booked,
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

    public static function getOccupancyRateForDiscreteDates($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $occupiedRooms = self::getOccupiedRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $totalRooms = self::getTotalRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);

        if (count($occupiedRooms) != count($totalRooms)) {
            return false;
        }

        $occupancyRates = array();
        foreach ($totalRooms as $key => $totalRoom) {
            $occupancyRates[$key] = $totalRoom ? $occupiedRooms[$key] / $totalRoom : 0;
        }

        return $occupancyRates;
    }

    public static function getAverageOccupancyRate($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $occupancyRates = self::getOccupancyRateForDiscreteDates($dateFrom, $dateTo, $idHotel);

        if (!is_array($occupancyRates)) {
            return 0;
        }

        return array_sum($occupancyRates) / count($occupancyRates);
    }

    public static function getRevenuePerAvailableRoom($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $averageDailyRates = self::getAverageDailyRateForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $occupancyRates = self::getOccupancyRateForDiscreteDates($dateFrom, $dateTo, $idHotel);

        if (count($averageDailyRates) != count($occupancyRates)) {
            return false;
        }

        $sumRevPars = 0;
        $countRevPars = 0;
        foreach ($averageDailyRates as $key => $averageDailyRate) {
            if ($averageDailyRate != 0) {
                $sumRevPars += ($averageDailyRate * $occupancyRates[$key]);
                $countRevPars++;
            }
        }

        return $countRevPars ? $sumRevPars / $countRevPars : 0;
    }

    public static function getTotalRevenuePerAvailableRoom($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $totalRevenues = self::getTotalRevenueForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $totalRooms = self::getTotalRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);

        if (count($totalRevenues) != count($totalRooms)) {
            return false;
        }

        $sumTrevPars = 0;
        $countTrevPars = 0;
        foreach ($totalRevenues as $key => $totalRevenue) {
            if ($totalRevenue != 0) {
                $sumTrevPars += ($totalRevenue / $totalRooms[$key]);
                $countTrevPars++;
            }
        }

        return $countTrevPars ? $sumTrevPars / $countTrevPars : 0;
    }

    public static function getAverageLengthOfStay($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $sql = 'SELECT AVG(DATEDIFF(hbd.`date_to`, hbd.`date_from`))
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'product` p
        ON (p.`id_product` = hbd.`id_product`)
        WHERE p.`active` = 1
        AND hbd.`is_refunded` = 0
        AND hbd.`date_from` < "'.pSQL($dateTo).' 00:00:00" AND hbd.`date_to` > "'.pSQL($dateFrom).' 00:00:00"'.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');
        $result = Db::getInstance()->getValue($sql);

        return $result ? $result : 0;
    }

    public static function getDirectRevenueRatio($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $sql = 'SELECT SUM(total_paid_tax_excl - refunded_amount)
        FROM (
            SELECT o.`id_order`, o.`total_paid_tax_excl` / o.`conversion_rate` AS total_paid_tax_excl,
            IFNULL(orr.`refunded_amount`, 0) AS refunded_amount,
            (SELECT hbd.`id_hotel`
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                WHERE hbd.`id_order` = o.`id_order` LIMIT 1
            ) AS id_hotel
            FROM `'._DB_PREFIX_.'orders` o
            INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
            ON (hbd.`id_order` = o.`id_order`)
            INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi
            ON (hbi.`id` = hbd.`id_hotel`)
            LEFT JOIN `' ._DB_PREFIX_.'order_return` orr
            ON (orr.`id_order` = o.`id_order`)
            WHERE o.`valid` = 1 AND hbi.`active` = 1 AND hbd.`is_refunded` = 0
            AND o.`invoice_date` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"
            AND o.`source` = "'.pSQL(Configuration::get('PS_SHOP_DOMAIN')).'"'.
            (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').
            'GROUP BY o.`id_order`
        ) AS t';
        $directRevenue = (float) Db::getInstance()->getValue($sql);

        $totalRevenue = (float) self::getRevenue($dateFrom, $dateTo, $idHotel);

        return $totalRevenue ? ($directRevenue / $totalRevenue) * 100 : 0;
    }

    public static function getOperatingExpensesForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $useCache = true)
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
            $cacheKey = 'AdminStats::getOperatingExpensesForDiscreteDates'.'_'.(int) $discreteDate['timestamp_from'].'_'.
            (!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
            if (!Cache::isStored($cacheKey) || !$useCache) {
                $sql = 'SELECT
                IFNULL(SUM(
                    CASE
                        WHEN od.`original_wholesale_price` <> "0.000000"
                        THEN od.`original_wholesale_price`
                        WHEN p.`wholesale_price` <> "0.000000"
                        THEN p.`wholesale_price`
                    END
                ), 0)
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hbd.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'order_detail` od
                ON (od.`id_order_detail` = hbd.`id_order_detail`)
                WHERE p.`active` = 1
                AND hbd.`is_refunded` = 0
                AND hbd.`date_from` < "'.pSQL($discreteDate['date_to']).' 00:00:00" AND hbd.`date_to` > "'.pSQL($discreteDate['date_from']).' 00:00:00"'.
                (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');

                $value = Db::getInstance()->getValue($sql);
                Cache::store($cacheKey, $value);
            }

            $result[$discreteDate['timestamp_from']] = Cache::retrieve($cacheKey);
        }

        return $result;
    }

    public static function getGrossOperatingProfitPerAvailableRoomForDiscreteDates($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $roomsRevenues = self::getRoomsRevenueForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $operatingExpenses = self::getOperatingExpensesForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $totalRooms = self::getTotalRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);

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

    public static function getGrossOperatingProfitPerAvailableRoom($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $grossOpProfitPars = self::getGrossOperatingProfitPerAvailableRoomForDiscreteDates($dateFrom, $dateTo, $idHotel);

        if (!count($grossOpProfitPars)) {
            return false;
        }

        $sumGrossOpProfitPars = 0;
        $countGrossOpProfitPars = 0;
        foreach ($grossOpProfitPars as $grossOpProfitPar) {
            if ($grossOpProfitPar != 0) {
                $sumGrossOpProfitPars += $grossOpProfitPar;
                $countGrossOpProfitPars++;
            }
        }

        return $countGrossOpProfitPars ? $sumGrossOpProfitPars / $countGrossOpProfitPars : 0;
    }
}
