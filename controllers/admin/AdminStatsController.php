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
                WHERE os.logable = 1'. (($date_from && $date_to) ? ' AND `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"' : '').' GROUP BY LEFT(`invoice_date`, 10) HAVING 1 '.HotelBranchInformation::addHotelRestriction($id_hotel)
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
                WHERE os.logable = 1'. (($date_from && $date_to) ? ' AND `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"' : '').' GROUP BY LEFT(`invoice_date`, 7) HAVING 1 '.HotelBranchInformation::addHotelRestriction($id_hotel)
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
                WHERE os.logable = 1'. (($date_from && $date_to) ? ' AND `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"' : '').' HAVING 1 '.HotelBranchInformation::addHotelRestriction($id_hotel)
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
        return round(100 * $distinct_products / AdminStatsController::getTotalRoomTypes()).'%';
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
		SELECT SUM(IF(c.id_gender IS NOT NULL, 1, 0)) as total, SUM(IF(type = 0, 1, 0)) as male, SUM(IF(type = 1, 1, 0)) as female, SUM(IF(type = 2, 1, 0)) as neutral
		FROM `'._DB_PREFIX_.'customer` c
		LEFT JOIN `'._DB_PREFIX_.'gender` g ON c.id_gender = g.id_gender
		WHERE c.active = 1 AND c.deleted = 0 '.Shop::addSqlRestriction());

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

    public static function getPurchases($date_from, $date_to, $granularity = false, $id_hotel = 0)
    {
        if ($granularity == 'day') {
            $purchases = array();
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT
				LEFT(`invoice_date`, 10) as date,
				SUM(od.`product_quantity` * IF(
					od.`purchase_supplier_price` > 0,
					od.`purchase_supplier_price`,
					(od.`original_product_price` / `conversion_rate`) * '.(int)Configuration::get('CONF_AVERAGE_PRODUCT_MARGIN').' / 100
				)) as total_purchase_price,
                (
                    SELECT hbd.`id_hotel`
                    FROM`'._DB_PREFIX_.'htl_booking_detail` hbd
                    WHERE hbd.`id_order` = o.`id_order` LIMIT 1
                ) AS id_hotel
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1'.'
			GROUP BY LEFT(`invoice_date`, 10) HAVING 1 '.HotelBranchInformation::addHotelRestriction($id_hotel));

            foreach ($result as $row) {
                $purchases[strtotime($row['date'])] = $row['total_purchase_price'];
            }

            return $purchases;
        } else {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                'SELECT SUM(od.`product_quantity` * IF(
                    od.`purchase_supplier_price` > 0,
                    od.`purchase_supplier_price`,
                    (od.`original_product_price` / `conversion_rate`) * '.(int)Configuration::get('CONF_AVERAGE_PRODUCT_MARGIN').' / 100
                )),
                (
                    SELECT hbd.`id_hotel`
                    FROM`'._DB_PREFIX_.'htl_booking_detail` hbd
                    WHERE hbd.`id_order` = o.`id_order` LIMIT 1
                ) AS id_hotel
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
                WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1 HAVING 1 '.HotelBranchInformation::addHotelRestriction($id_hotel)
            );
        }
    }

    public static function getExpenses($date_from, $date_to, $granularity = false, $id_hotel = 0)
    {
        $expenses = ($granularity == 'day' ? array() : 0);

        $orders = Db::getInstance()->ExecuteS('
		SELECT
			LEFT(`invoice_date`, 10) as date,
            total_paid_tax_incl / o.conversion_rate as total_paid_tax_incl,
			total_shipping_tax_excl / o.conversion_rate as total_shipping_tax_excl,
			o.module,
			ad.id_country,
			o.id_currency,
			c.id_reference as carrier_reference,
            (
                SELECT hbd.`id_hotel`
                FROM`'._DB_PREFIX_.'htl_booking_detail` hbd
                WHERE hbd.`id_order` = o.`id_order` LIMIT 1
            ) AS id_hotel
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'address` ad ON o.id_address_delivery = ad.id_address
		LEFT JOIN `'._DB_PREFIX_.'carrier` c ON o.id_carrier = c.id_carrier
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1 HAVING 1'.HotelBranchInformation::addHotelRestriction($id_hotel));

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

    public static function getBestSellingRoomType($dateFrom, $dateTo, $idHotel = null)
    {
        $sql = 'SELECT p.`id_product`,
        (
            SELECT IFNULL(SUM(ROUND((DATEDIFF(LEAST(hbd.`date_to`, "'.pSQL($dateTo).'"), GREATEST(hbd.`date_from`, "'.pSQL($dateFrom).'")) / DATEDIFF(hbd.`date_to`, hbd.`date_from`)) * hbd.`total_price_tax_excl`, 2)), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o
            ON (o.`id_order` = hbd.`id_order`)
            WHERE hbd.`id_product` = p.`id_product` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($dateFrom).'" AND hbd.`date_from` < "'.pSQL($dateTo).'"
        ) AS totalRevenue
        FROM `'._DB_PREFIX_.'product` p
        INNER JOIN `'._DB_PREFIX_.'htl_room_type` hrt
        ON (hrt.`id_product` = p.`id_product`)
        WHERE p.`active` = 1 AND p.`booking_product` = 1'.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hrt') : '').'
        HAVING totalRevenue > 0
        ORDER BY totalRevenue DESC';

        return Db::getInstance()->getValue($sql);
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
                $nbDaysConversionRate = Validate::isUnsignedInt(Configuration::get('PS_KPI_CONVERSION_RATE_NB_DAYS')) ? Configuration::get('PS_KPI_CONVERSION_RATE_NB_DAYS') : 30;

                $visitors = AdminStatsController::getVisits(
                    true,
                    date('Y-m-d', strtotime('-'.($nbDaysConversionRate + 1).' day')),
                    date('Y-m-d', strtotime('+1 day')),
                    false /*'day'*/
                );

                $orders = AdminStatsController::getOrders(
                    date('Y-m-d', strtotime('-'.($nbDaysConversionRate + 1).' day')),
                    date('Y-m-d', strtotime('-1 day')),
                    false /*'day'*/
                );

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
                $value = AdminStatsController::getDisabledRoomTypes(0);
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
                $daysForAvgOrderVal = Configuration::get('PS_ORDER_KPI_AVG_ORDER_VALUE_NB_DAYS');

                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                SELECT
                    COUNT(o.`id_order`) as orders,
                    SUM(o.`total_paid_tax_excl` / o.`conversion_rate`) as total_paid_tax_excl,
                    (
                        SELECT hbd.`id_hotel`
                        FROM`'._DB_PREFIX_.'htl_booking_detail` hbd
                        WHERE hbd.`id_order` = o.`id_order` LIMIT 1
                    ) AS id_hotel
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = o.`current_state`
                WHERE o.`invoice_date` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-'.($daysForAvgOrderVal + 1).' day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59" AND os.`logable` = 1 HAVING 1 '.HotelBranchInformation::addHotelRestriction(false));
                $value = Tools::displayPrice($row['orders'] ? $row['total_paid_tax_excl'] / $row['orders'] : 0, $currency).' ('.$this->l('tax excl.').')';

                break;

            case 'netprofit_visit':
                $daysForProfitPerVisitor = Configuration::get('PS_ORDER_KPI_PER_VISITOR_PROFIT_NB_DAYS');

                $date_from = date('Y-m-d', strtotime('-'.($daysForProfitPerVisitor + 1).' day'));
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
                if (!($id_category = AdminStatsController::getBestCategory(date('Y-m-d', strtotime('-1 month')), date('Y-m-d', strtotime('+1 month'))))) {
                    $value = $this->l('No category', null, null, false);
                } else {
                    $category = new Category($id_category, $this->context->language->id);
                    $value = $category->name;
                }

                break;

            case 'best_selling_room_type':
                $nbDaysBestSelling = Validate::isUnsignedInt(Configuration::get('PS_KPI_BEST_SELLING_ROOM_TYPE_NB_DAYS')) ? Configuration::get('PS_KPI_BEST_SELLING_ROOM_TYPE_NB_DAYS') : 30;

                if (!($idProduct = AdminStatsController::getBestSellingRoomType(
                    date('Y-m-d', strtotime('-'.($nbDaysBestSelling + 1).' day')),
                    date('Y-m-d', strtotime('-1 day')),
                    0
                ))) {
                    $value = $this->l('--', null, null, false);
                } else {
                    $objProduct = new Product($idProduct, false, $this->context->language->id);
                    $value = $objProduct->name;
                }

                break;

            case 'total_rooms':
                $value = AdminStatsController::getTotalRooms(0);

                break;

            case 'occupied_rooms':
                $value = AdminStatsController::getOccupiedRooms(0);

                break;

            case 'vacant_rooms':
                $totalAvailableRooms = AdminStatsController::getAvailableRoomsForDiscreteDates(date('Y-m-d'), null, 0);
                $totalAvailableRooms = $totalAvailableRooms[strtotime(date('Y-m-d'))];
                $totalOccupiedRooms = AdminStatsController::getOccupiedRooms(0);
                $value = $totalAvailableRooms - $totalOccupiedRooms;

                break;

            case 'booked_rooms':
                $value = AdminStatsController::getBookedRooms(0);

                break;

            case 'disabled_rooms':
                $value = AdminStatsController::getDisabledRoomsForDiscreteDates(date('Y-m-d'), null, 0);
                $value = $value[strtotime(date('Y-m-d'))];

                break;

            case 'online_bookable_rooms':
                $value = AdminStatsController::getAvailableRoomsForDiscreteDates(date('Y-m-d'), null, 0, 1);
                $value = $value[strtotime(date('Y-m-d'))];

                break;

            case 'offline_bookable_rooms':
                $value = AdminStatsController::getAvailableRoomsForDiscreteDates(date('Y-m-d'), null, 0);
                $value = $value[strtotime(date('Y-m-d'))];

                break;

            case 'total_frequent_customers':
                $nbOrdersFrequentCustomers = Configuration::get('PS_KPI_FREQUENT_CUSTOMER_NB_ORDERS');

                $value = AdminStatsController::getTotalFrequentCustomers($nbOrdersFrequentCustomers, 0);

                break;

            case 'revenue_per_available_customer':
                $nbDaysRevPac = Configuration::get('PS_KPI_REVPAC_NB_DAYS');

                $value = AdminStatsController::getRevenuePerAvailableCustomer(
                    date('Y-m-d', strtotime('-'.($nbDaysRevPac + 1).' day')),
                    date('Y-m-d', strtotime('-1 day')),
                    0
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
                $totalSales = AdminStatsController::getTotalSales('', '', false, false, 1);
                if ($totalSales > 0) {
                    $value = Tools::displayPrice($totalSales, $currency);
                } else {
                    $value = Tools::displayPrice(0, $currency);
                }
                break;
            case 'today_arrivals':
                $dateToday = date('Y-m-d');
                $value = 0;
                if ($arrivalsData = AdminStatsController::getArrivalsByDate($dateToday)) {
                    $value = $arrivalsData['total_arrivals'];
                }
                break;
            case 'today_departures':
                $dateToday = date('Y-m-d');
                $value = 0;
                if ($departureData = AdminStatsController::getDeparturesByDate($dateToday)) {
                    $value = $departureData['total_departures'];
                }
                break;
            case 'today_stay_over':
                $dateToday = date('Y-m-d');
                $value = AdminStatsController::getStayOversByDate($dateToday);
                break;
            case 'total_due_amount':
                $dateToday = date('Y-m-d');
                $dueAmount = AdminStatsController::getTotalDueAmount('', '', false, 1);
                if ($dueAmount > 0) {
                    $value = Tools::displayPrice($dueAmount, $currency);
                } else {
                    $value = Tools::displayPrice(0, $currency);
                }
                break;
            case 'average_lead_time':
                $dateToday = date('Y-m-d');
                $value = Tools::ps_round(AdminStatsController::getAverageLeadTime(), 2);
                break;
            case 'average_guest_in_booking':
                $dateToday = date('Y-m-d');
                $value = AdminStatsController::getAverageGuestsPerBooking();
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

    public static function getTotalRooms($idHotel = null)
    {
        $sql = 'SELECT COUNT(hri.`id`)
        FROM `'._DB_PREFIX_.'htl_room_information` hri
        INNER JOIN `'._DB_PREFIX_.'product` p
        ON (p.`id_product` = hri.`id_product`)
        WHERE p.`booking_product` = 1 '.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '');

        return Db::getInstance()->getValue($sql);
    }

    public static function getOccupancyData($dateFrom, $dateTo, $idsHotel = false)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

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

        // Occupied rooms are booked rooms that are not refunded in the date range
        $countOccupied = 0;
        $occupiedRooms = Db::getInstance()->executeS(
            'SELECT DISTINCT hbd.`id_room`
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri
            ON (hri.`id` = hbd.`id_room`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
            ON (p.`id_product` = hri.`id_product`)
            WHERE p.`active` = 1
            AND hbd.`is_refunded` = 0
            AND hbd.`date_from` < "'.pSQL($dateTo).' 00:00:00" AND hbd.`date_to` > "'.pSQL($dateFrom).' 00:00:00"'.
            HotelBranchInformation::addHotelRestriction($idsHotel, 'hbd')
        );

        if ($occupiedRooms) {
            $occupiedRooms = array_column($occupiedRooms, 'id_room');
            $occupancyData['count_occupied'] = count($occupiedRooms);
        } else {
            $occupancyData['count_occupied'] = 0;
        }

        // Unavailable rooms are rooms that are not booked for the date range and in the inactive status
        $countUnavailable = Db::getInstance()->getValue(
            'SELECT COUNT(hri.`id`)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            LEFT JOIN `'._DB_PREFIX_.'htl_branch_info` hbi
            ON (hbi.`id` = hri.`id_hotel`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
            ON (p.`id_product` = hri.`id_product`)
            WHERE p.`active` = 1 '.
            ($occupiedRooms ? ' AND hri.`id` NOT IN ('.implode(',', $occupiedRooms).')' : '').
            ' AND hri.`id_status` = '.(int) HotelRoomInformation::STATUS_INACTIVE.
            HotelBranchInformation::addHotelRestriction($idsHotel, 'hbi', 'id')
        );
        $occupancyData['count_unavailable'] = $countUnavailable;

        // Available rooms are rooms that are not booked for the date range and in the temporary inactive for the date range
        $countDisabled = Db::getInstance()->getValue(
            'SELECT IFNULL(COUNT(hri.`id`), 0)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            LEFT JOIN `'._DB_PREFIX_.'htl_room_disable_dates` hrdd
            ON (hrdd.`id_room` = hri.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
            ON (p.`id_product` = hri.`id_product`)
            WHERE hri.`id_status` = '.(int) HotelRoomInformation::STATUS_TEMPORARY_INACTIVE.
            ($occupiedRooms ? ' AND hri.`id` NOT IN ('.implode(',', $occupiedRooms).')' : '').
            ' AND ("'.pSQL($dateTo).'" > hrdd.`date_from` AND "'.pSQL($dateFrom).'" < hrdd.`date_to`)
            AND p.`active` = 1'.
            (!is_null($idsHotel) ? HotelBranchInformation::addHotelRestriction($idsHotel, 'hri') : '')
        );
        $occupancyData['count_unavailable'] += $countDisabled;

        $occupancyData['count_available'] = $occupancyData['count_total'] - $occupancyData['count_occupied'] - $occupancyData['count_unavailable'];

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

    // Average Daily Rate is the average income per paid occupied room in a given time period
    public static function getAverageDailyRate($dateFrom, $dateTo, $idHotel = null)
    {
        $roomsRevenueByDates = self::getRoomsRevenueForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $occupiedRoomByDates = self::getOccupiedRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);

        $totalOccupiedRooms = array_sum($occupiedRoomByDates);
        $totalRoomsRevenues = array_sum($roomsRevenueByDates);

        return $totalRoomsRevenues ? ($totalRoomsRevenues / $totalOccupiedRooms) : 0;
    }

    public static function getCancellationRate($dateFrom, $dateTo, $idHotel = false)
    {
        $totalBookings = Db::getInstance()->getValue(
            'SELECT COUNT(hbd.`id`) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`date_add` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );

        $cancelledBookings = Db::getInstance()->getValue(
            'SELECT COUNT(hbd.`id`) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`is_refunded` = 1 AND hbd.`date_add` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"'.
            HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
        );

        return $totalBookings ? (($cancelledBookings / $totalBookings) * 100) : 0;
    }

    public static function getRevenue($dateFrom, $dateTo, $idHotel = false, $orderSource = '')
    {
        $sql = 'SELECT SUM(total_paid_tax_excl - refunded_amount)
        FROM (SELECT o.`total_paid_tax_excl` / o.`conversion_rate` AS total_paid_tax_excl,
        (SELECT IFNULL(SUM(orr.`refunded_amount`), 0) FROM`'._DB_PREFIX_.'order_return` orr WHERE orr.`id_order` = o.`id_order`) AS refunded_amount,
        (SELECT hbd.`id_hotel` FROM`'._DB_PREFIX_.'htl_booking_detail` hbd WHERE hbd.`id_order` = o.`id_order` LIMIT 1) AS id_hotel
        FROM `'._DB_PREFIX_.'orders` o

        WHERE o.`valid` = 1 AND o.`invoice_date` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"';

        if ($orderSource) {
            $sql .= ' AND o.`source` = "'.pSQL($orderSource).'"';
        }

        $sql .= ' HAVING 1 '.HotelBranchInformation::addHotelRestriction($idHotel).') as t';

        $result = Db::getInstance()->getValue($sql);

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
        $idLang = Context::getContext()->language->id;
        return Db::getInstance()->executeS(
            'SELECT *, osl.`name` AS `state_name`, os.`color` AS `state_color`, o.`date_add` AS `date_add`, o.`date_upd` AS `date_upd`
            FROM `'._DB_PREFIX_.'orders` o
            LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (o.`current_state` = os.`id_order_state`)
            LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl
            ON (osl.`id_order_state` = o.`current_state` AND osl.`id_lang` = '.(int) $idLang.')
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
        $sql = 'SELECT hbd.*, o.`with_occupancy`, CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
        DATEDIFF(hbd.`date_to`, hbd.`date_from`) AS los
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = hbd.`id_customer`)
        WHERE hbd.`is_refunded` = 0 AND hbd.`date_from` = "'.pSQL($date).' 00:00:00"
        AND hbd.`id_status` != '.(int) HotelBookingDetail::STATUS_CHECKED_IN.'
        AND hbd.`id_status` != '.(int) HotelBookingDetail::STATUS_CHECKED_OUT.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    // Departures: Guest is going to depart from the hotel today
    public static function getDeparturesInfoByDate($date, $idHotel = null)
    {
        $sql = 'SELECT hbd.*, o.`with_occupancy`, CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
        DATEDIFF(hbd.`date_to`, hbd.`date_from`) AS los
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = hbd.`id_customer`)
        WHERE hbd.`is_refunded` = 0 AND hbd.`date_to` = "'.pSQL($date).' 00:00:00"
        AND hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_IN.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    // In-House or Stay Over: The guest is not expected to check out today and will remain at least one more night.
    public static function getInHousesInfo($idHotel = null)
    {
        $sql = 'SELECT hbd.*, o.`with_occupancy`, CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
        DATEDIFF(hbd.`date_to`, hbd.`date_from`) AS los
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = hbd.`id_customer`)
        WHERE hbd.`is_refunded` = 0
        AND (hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_IN.'
        OR (hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_OUT.' AND hbd.`check_out` > "'.pSQL(date('Y-m-d')).' 00:00:00"))
        AND hbd.`date_to` != "'.pSQL(date('Y-m-d')).' 00:00:00"'.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public static function getNewBookingsInfoByDate($date, $idHotel = null)
    {
        $sql = 'SELECT hbd.`id_customer`, CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name, COUNT(hbd.`id`) AS total_rooms,
        SUM(hbd.`adults` + hbd.`children`) AS total_guests, hbd.`id_hotel`, hbd.`hotel_name`, hbd.`id_order`, o.`with_occupancy`,
        o.`total_paid_tax_excl`, o.`id_currency`, osl.`name` AS `state_name`, os.`color` AS `state_color`
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
        LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (o.`current_state` = os.`id_order_state`)
        LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl
        ON (osl.`id_order_state` = o.`current_state` AND osl.`id_lang` = '.(int) Context::getContext()->language->id.')
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = hbd.`id_customer`)
        WHERE hbd.`date_add` BETWEEN "'.pSQL($date).' 00:00:00" AND "'.pSQL($date).' 23:59:59"'.'
        AND o.`current_state` NOT IN ('.implode(',', array((int) Configuration::get('PS_OS_CANCELED'), (int) Configuration::get('PS_OS_REFUND'))).')'.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
        GROUP BY hbd.`id_order`';

        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public static function getCancellationsInfoByDate($date, $idHotel = null)
    {
        $sql = 'SELECT orr.`id_order_return`, orr.`id_customer`, hbd.`room_num`, hbd.`id_product`, hbd.`room_type_name`,
        o.`with_occupancy`, CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name, hbd.`id_hotel`,
        hbd.`hotel_name`, SUM(hbd.`adults` + hbd.`children`) AS total_guests,
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

    public static function getTotalOccupiedRooms($dateFrom, $dateTo, $idHotel = null)
    {
        $sql = 'SELECT COUNT(hbd.`id_room`)
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        WHERE hbd.`is_refunded` = 0
        AND hbd.`date_from` <= "'.pSQL($dateTo).' 00:00:00" AND hbd.`date_to` > "'.pSQL($dateFrom).' 00:00:00"'.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');

        $result = Db::getInstance()->getValue($sql);

        return $result ? $result : 0;
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
                $sql = 'SELECT IFNULL(SUM((hbd.`total_price_tax_excl` / o.`conversion_rate`) / DATEDIFF(hbd.`date_to`, hbd.`date_from`)), 0)
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hbd.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
                WHERE p.`active` = 1
                AND o.`valid` = 1
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
                        INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hbd.`id_product`)
                        WHERE hbd.`id_order` = o.`id_order`
                        AND p.`active` = 1 AND hbd.`is_refunded` = 0 AND o.`valid` = 1'.
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
                    ) AS total_parts,
                    (
                        SELECT COUNT(*)
                        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                        INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hbd.`id_product`)
                        WHERE hbd.`id_order` = o.`id_order`
                        AND hbd.`date_from` <= "'.pSQL($discreteDate['date_from']).' 00:00:00" AND hbd.`date_to` >= "'.pSQL($discreteDate['date_to']).' 00:00:00"
                        AND p.`active` = 1 AND hbd.`is_refunded` = 0 AND o.`valid` = 1'.
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
    public static function getAvailableRoomsForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $showAtFront = null, $useCache = true)
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
            (is_null($showAtFront) ? 'null_' : (int) $showAtFront).(!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
            if (!Cache::isStored($cacheKey) || !$useCache) {
                $sql = 'SELECT (num_total - num_booked - num_inactive - num_temporarily_inactive) AS num_available
                FROM (
                    SELECT (
                        SELECT IFNULL(COUNT(hri.`id`), 0)
                        FROM `'._DB_PREFIX_.'htl_room_information` hri
                        LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                        WHERE p.`active` = 1'.
                        (!is_null($showAtFront) ? ' AND p.`show_at_front` = '.(int) $showAtFront : '').
                        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '').'
                    ) AS num_total,
                    (
                        SELECT COUNT(DISTINCT hbd.`id_room`)
                        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                        LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri
                        ON (hri.`id` = hbd.`id_room`)
                        LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                        WHERE p.`active` = 1'.
                        (!is_null($showAtFront) ? ' AND p.`show_at_front` = '.(int) $showAtFront : '').'
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
                        (!is_null($showAtFront) ? ' AND p.`show_at_front` = '.(int) $showAtFront : '').
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
                        (!is_null($showAtFront) ? ' AND p.`show_at_front` = '.(int) $showAtFront : '').
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

    /**
     * Returns the number of rooms that have been checked-in.
     */
    public static function getOccupiedRooms($idHotel = null)
    {
        $sql = 'SELECT COUNT(DISTINCT hbd.`id_room`)
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri
        ON (hri.`id` = hbd.`id_room`)
        LEFT JOIN `'._DB_PREFIX_.'product` p
        ON (p.`id_product` = hri.`id_product`)
        WHERE p.`active` = 1
        AND hbd.`is_refunded` = 0
        AND hbd.`is_cancelled` = 0
        AND hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_IN.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');
        $result = Db::getInstance()->getValue($sql);

        return $result;
    }

    /**
     * Returns the number of rooms that have been booked but not checked-in yet.
     */
    public static function getBookedRooms($idHotel = null)
    {
        $dateToday = date('Y-m-d');

        $sql = 'SELECT COUNT(DISTINCT hbd.`id_room`)
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        WHERE hbd.`is_refunded` = 0
        AND hbd.`is_cancelled` = 0
        AND hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_ALLOTED.'
        AND hbd.`date_from` = "'.pSQL($dateToday).' 00:00:00"'.
        (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');
        $result = Db::getInstance()->getValue($sql);

        return $result;
    }

    public static function getTotalFrequentCustomers($nbOrders = 5, $idHotel = null)
    {
        $sql = 'SELECT COUNT(t.`id_customer`)
        FROM (
            SELECT o.`id_customer`, COUNT(o.`id_order`) AS nb_orders,
            (
                SELECT hbd.`id_hotel`
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                WHERE hbd.`id_order` = o.`id_order` LIMIT 1
            ) AS id_hotel
            FROM `'._DB_PREFIX_.'orders` o
            WHERE o.`valid` = 1
            GROUP BY o.`id_customer`
            HAVING 1 '.(!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel) : '').'
        ) AS t
        WHERE t.`nb_orders` >= '.(int) $nbOrders;
        $result = Db::getInstance()->getValue($sql);

        return $result;
    }

    public static function getRevenuePerAvailableCustomer($dateFrom, $dateTo, $idHotel = null)
    {
        $totalRevenue = self::getRevenue($dateFrom, $dateTo, $idHotel);

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
        $maxDateAdd = date('Y-m-d H:i:s', strtotime('-'.$nbDaysNewCustomers.' day'));
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

    // Occupancy rate is the percentage of occupied rooms in your hotel at a given time
    public static function getAverageOccupancyRate($dateFrom, $dateTo, $idHotel = null)
    {
        $occupiedRooms = self::getOccupiedRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $allRooms = self::getTotalRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);

        $totalOccupiedRooms = array_sum($occupiedRooms);
        $totalRooms = array_sum($allRooms);

        return $totalRooms ? ($totalOccupiedRooms / $totalRooms) * 100 : 0;
    }

    // Revenue Per Available Room is the average income on all rooms in a given time period
    // RevPAR relates only to room revenue
    public static function getRevenuePerAvailableRoom($dateFrom, $dateTo, $idHotel = null)
    {
        $roomsRevenueByDates = self::getRoomsRevenueForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $allRoomsByDate = self::getTotalRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);

        $totalRoomsRevenues = array_sum($roomsRevenueByDates);
        $totalRooms = array_sum($allRoomsByDate);

        return $totalRooms ? ($totalRoomsRevenues / $totalRooms) : 0;
    }

    // TrevPAR appears very similar to RevPAR
    // RevPAR relates only to room revenue But TrevPAR is for the total(all types services) revenue against your guest rooms.
    public static function getTotalRevenuePerAvailableRoom($dateFrom, $dateTo, $idHotel = null)
    {
        $totalRevenueByDate = self::getTotalRevenueForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $allRoomsByDate = self::getTotalRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);

        $totalRevenues = array_sum($totalRevenueByDate);
        $totalRooms = array_sum($allRoomsByDate);

        return $totalRooms ? ($totalRevenues / $totalRooms) : 0;
    }

    // ALOS referes to the average number of nights guests stay at your property over a given time
    // Formula: [number of room nights booked for a given time / number of reservations for the same time]
    public static function getAverageLengthOfStay($dateFrom, $dateTo, $idHotel = null)
    {
        $nightsBookedByDate = self::getRoomNightsData($dateFrom, $dateTo, $idHotel);

        $totalNightsBooked = array_sum($nightsBookedByDate);
        $totalOccupiedRoom = self::getTotalOccupiedRooms($dateFrom, $dateTo, $idHotel);

        return $totalOccupiedRoom ? ($totalNightsBooked / $totalOccupiedRoom) : 0;
    }

    public static function getDirectRevenueRatio($dateFrom, $dateTo, $idHotel = null)
    {
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        // Direct revenue will be revenue from all the channels
        $totalRevenue = (float) self::getRevenue($dateFrom, $dateTo, $idHotel);

        // Direct revenue will be revenue from this website only
        $directRevenue = (float) self::getRevenue($dateFrom, $dateTo, $idHotel, Configuration::get('PS_SHOP_DOMAIN'));

        return $totalRevenue ? (($directRevenue / $totalRevenue) * 100) : 0;
    }

    public static function getOperatingExpensesForDiscreteDates($dateFrom, $dateTo = null, $idHotel = null, $useCache = true, $onlyRooms = 0)
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
                // sql for rooms expenses
                $roomsSql = 'SELECT
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

                $roomsExpenses = Db::getInstance()->getValue($roomsSql);

                // sql for services expenses
                $servicesExpenses = 0;
                if (!$onlyRooms) {
                    $servicesSql = 'SELECT
                    IFNULL(SUM(
                        CASE
                            WHEN od.`original_wholesale_price` <> "0.000000"
                            THEN od.`original_wholesale_price`
                            WHEN p.`wholesale_price` <> "0.000000"
                            THEN p.`wholesale_price`
                        END
                    ), 0)
                    FROM `'._DB_PREFIX_.'htl_room_type_service_product_order_detail` rtspod
                    LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                    ON (rtspod.`id_htl_booking_detail` = hbd.`id`)
                    LEFT JOIN `'._DB_PREFIX_.'product` p
                    ON (p.`id_product` = hbd.`id_product`)
                    LEFT JOIN `'._DB_PREFIX_.'order_detail` od
                    ON (od.`id_order_detail` = rtspod.`id_order_detail`)
                    WHERE p.`active` = 1
                    AND hbd.`is_refunded` = 0
                    AND hbd.`date_from` < "'.pSQL($discreteDate['date_to']).' 00:00:00" AND hbd.`date_to` > "'.pSQL($discreteDate['date_from']).' 00:00:00"'.
                    (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');

                    $servicesExpenses = Db::getInstance()->getValue($servicesSql);
                }

                Cache::store($cacheKey, ($roomsExpenses + $servicesExpenses));
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
        $totalRevenueByDate = self::getTotalRevenueForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $operatingExpensesByDate = self::getOperatingExpensesForDiscreteDates($dateFrom, $dateTo, $idHotel);
        $allRoomsByDate = self::getTotalRoomsForDiscreteDates($dateFrom, $dateTo, $idHotel);

        $totalRevenues = array_sum($totalRevenueByDate);
        $totalExpenses = array_sum($operatingExpensesByDate);
        $totalRooms = array_sum($allRoomsByDate);

        $totalProfit = $totalRevenues - $totalExpenses;

        return $totalProfit ? ($totalProfit / $totalRooms) : 0;
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
            $hotelsData[$idHotel] = self::getOccupiedRoomsForDiscreteDates(
                $dateFrom,
                $dateTo,
                $idHotel,
                $useCache
            );
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

    /**
     * $dow: day of week, 1 = SUN
     */
    public static function getOccupiedRoomsForDayOfTheWeek($dow, $dateFrom, $dateTo = null, $idHotel = null, $useCache = true)
    {
        $dateTo = !$dateTo ? date('Y-m-d', strtotime('+1 day', strtotime($dateFrom))) : $dateTo;
        $dateToNext = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));

        $result = 0;
        $cacheKey = 'AdminStats::getOccupiedRoomsForDayOfTheWeek'.'_'.(int) $dow.(int) strtotime($dateFrom).
        (int) strtotime($dateTo).(!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
        if (!Cache::isStored($cacheKey) || !$useCache) {
            $sql = 'SELECT SUM((full_weeks) + IF('.(int) $dow.' = dow_date_from || ('.(int) $dow.' > dow_date_from AND (dow_date_from + los - 1) > '.(int) $dow.') || (dow_date_from > '.(int) $dow.' AND ((dow_date_from + los - 1) - 7) >= '.(int) $dow.'), 1, 0)) AS total_occupied
            FROM (
                SELECT DAYOFWEEK(`date_from_final`) AS dow_date_from,
                DAYOFWEEK(`date_to_final`) AS dow_date_to,
                DATEDIFF(`date_to_final`, `date_from_final`) as los,
                IF(('.(int) $dow.' >= DAYOFWEEK(`date_from_final`)), ((DATEDIFF(`date_to_final`, `date_from_final`) -('.(int) $dow.' + 1 - DAYOFWEEK(`date_from_final`))) DIV 7), ((DATEDIFF(`date_to_final`, `date_from_final`) - (7 - DAYOFWEEK(`date_from_final`) + '.(int) $dow.')) DIV 7)) AS full_weeks
                FROM (
                    SELECT IF(DATEDIFF(hbd.`date_from`, \''.pSQL($dateFrom).'\') < 0, \''.pSQL($dateFrom).'\', hbd.`date_from`) AS date_from_final, IF(DATEDIFF(\''.pSQL($dateTo).'\', hbd.`date_to`) < 0, \''.pSQL($dateToNext).'\', hbd.`date_to`) AS date_to_final
                    FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                    WHERE hbd.`is_refunded` = 0
                    AND hbd.`date_from` <= \''.pSQL($dateTo).'\' AND hbd.`date_to` > \''.pSQL($dateFrom).'\''.
                    (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
                ) AS t
            ) AS t1';

            $value = (int) Db::getInstance()->getValue($sql);

            Cache::store($cacheKey, $value);

            $result = Cache::retrieve($cacheKey);
        }

        return $result;
    }

    public static function getOccupiedRoomsForDaysOfTheWeek($dateFrom, $dateTo = null, $idHotel = null, $useCache = true, $average = false)
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
            $hotelsData[$idHotel] = array();
            // 1 = SUN
            for ($i = 1; $i <= 7; $i++) {
                $hotelsData[$idHotel][$i] = self::getOccupiedRoomsForDayOfTheWeek(
                    $i,
                    $dateFrom,
                    $dateTo,
                    $idHotel,
                    $useCache
                );
            }
        }

        // calculate sums
        $result = array();
        foreach ($hotelsData as $hotelData) {
            foreach ($hotelData as $dayOfWeek => $value) {
                if (!array_key_exists($dayOfWeek, $result)) {
                    $result[$dayOfWeek] = $value;
                } else {
                    $result[$dayOfWeek] += $value;
                }
            }
        }

        // calculate averages
        if ($average) {
            $totalHotels = count($idsHotel);
            if ($totalHotels > 1) {
                foreach ($result as $timestamp => &$value) {
                    $value = $value / $totalHotels;
                }
            }
        }

        return $result;
    }

    public static function getLengthOfStayRatio($losMinimum, $dateFrom, $dateTo, $idHotel = null, $useCache = true, $losMaximum = false)
    {
        $result = 0;
        $cacheKey = 'AdminStats::getLengthOfStayRatio'.'_'.(int) $losMinimum.(int) $losMaximum.
        (int) strtotime($dateFrom).(int) strtotime($dateTo).(!is_array($idHotel) ? (int) $idHotel : implode('_', $idHotel));
        if (!Cache::isStored($cacheKey) || !$useCache) {
            $sql = 'SELECT COUNT(hbd.`id`) AS total
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'product` p
            ON (p.`id_product` = hbd.`id_product`)
            WHERE p.`active` = 1
            AND hbd.`is_refunded` = 0
            AND hbd.`date_from` <= "'.pSQL($dateTo).' 00:00:00" AND hbd.`date_to` > "'.pSQL($dateFrom).' 00:00:00"'.
            (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '');

            $total = Db::getInstance()->getValue($sql);

            $sql = 'SELECT COUNT(los)
            FROM (
                SELECT DATEDIFF(hbd.`date_to`, hbd.`date_from`) AS los
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hbd.`id_product`)
                WHERE p.`active` = 1
                AND hbd.`is_refunded` = 0
                AND hbd.`date_from` <= "'.pSQL($dateTo).' 00:00:00" AND hbd.`date_to` > "'.pSQL($dateFrom).' 00:00:00"'.
                (!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '').'
            ) AS t
            WHERE los >= '.(int) $losMinimum.' AND los <= '.(int) ($losMaximum ? $losMaximum : $losMinimum);

            $fraction = Db::getInstance()->getValue($sql);

            $ratio = array('fraction' => $fraction, 'total' => $total);
            Cache::store($cacheKey, $ratio);

            $result = Cache::retrieve($cacheKey);
        }

        return $result;
    }

    public static function getLengthOfStayInfo($days, $dateFrom, $dateTo, $idHotel = null, $useCache = true)
    {
        $idsHotel = array();
        if (is_int($idHotel)) {
            $idsHotel[] = $idHotel;
        } else {
            $idsHotel = $idHotel;
        }

        // collect data
        $hotelsData = array();
        foreach ($idsHotel as $idHotel) {
            $hotelsData[$idHotel] = array();
            foreach ($days as $key => $day) {
                $hotelsData[$idHotel][$key] = self::getLengthOfStayRatio(
                    $day[0],
                    $dateFrom,
                    $dateTo,
                    $idHotel,
                    $useCache,
                    $day[1]
                );
            }
        }

        // calculate sums
        $result = array();
        foreach ($hotelsData as $hotelData) {
            foreach ($days as $key => $day) {
                if (!array_key_exists($key, $result)) {
                    $result[$key]['fraction'] = $hotelData[$key]['fraction'];
                    $result[$key]['total'] = $hotelData[$key]['total'];
                } else {
                    $result[$key]['fraction'] += $hotelData[$key]['fraction'];
                    $result[$key]['total'] += $hotelData[$key]['total'];
                }
            }
        }

        foreach ($result as $key => $ratio) {
            $result[$key] = array(
                'rooms_occupied' => $ratio['fraction'],
                'percent' => $ratio['total'] ? ($ratio['fraction'] / $ratio['total'] * 100) : 0
            );
        }

        return $result;
    }

    // Get to totalo due amount in the orders
    public static function getTotalDueAmount($dateFrom = '', $dateTo = '', $idHotel = false, $useTax = 0)
    {
        $objHotelBooking = new HotelBookingDetail();
        $invalidOrderStates = $objHotelBooking->getOrderStatusToFreeBookedRoom();

        $sql = 'SELECT (' . ($useTax ? 'SUM(o.`total_paid_tax_incl` / o.`conversion_rate`)' : 'SUM(o.`total_paid_tax_excl` / o.`conversion_rate`)') . ' - SUM(o.`total_paid_real` / o.`conversion_rate`)),
        (
            SELECT hbd.`id_hotel`
            FROM`'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`id_order` = o.`id_order` LIMIT 1
        ) AS id_hotel
        FROM `'._DB_PREFIX_.'orders` o
        LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
        WHERE 1 ' . ($invalidOrderStates ? ' AND o.`current_state` NOT IN ('.implode(',', $invalidOrderStates).')' : '') . (($dateFrom && $dateTo) ? ' AND o.`date_add` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"' : '').' HAVING 1 '.HotelBranchInformation::addHotelRestriction($idHotel);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    // Booking or Reservation Lead Time is the period of time (most typically measured in calendar days) between when a guest makes the reservation and the actual check-in/arrival date.
    public static function getAverageLeadTime($dateFrom = '', $dateTo = '', $idHotel = false)
    {
        $sql = 'SELECT (SUM(DATEDIFF(hbd.`date_from`, hbd.`date_add`)) / COUNT(hbd.`id`)) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        WHERE hbd.`is_refunded` = 0 AND hbd.`is_back_order` = 0'
        .(($dateFrom && $dateTo) ? ' AND hbd.`date_add` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"' : '')
        .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    // This function is used to calculate the average number of guests per booking
    /**
     * This function is used to calculate the average number of guests per booking
     * @param string $dateFrom : Date from in date range of booking creation
     * @param string $dateTo : Date to in date range of booking creation
     * @param boolean $idHotel:  : Send id hotel if want to get for specific hotel
     * @return array: of avg_adults and avg_children
     */
    public static function getAverageGuestsPerBooking($dateFrom = '', $dateTo = '', $idHotel = false)
    {
        $sql = 'SELECT (SUM(hbd.`adults`) / COUNT(hbd.`id`)) as avg_adults, (SUM(hbd.`children`) / COUNT(hbd.`id`)) as avg_children FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        WHERE hbd.`is_refunded` = 0 AND hbd.`is_back_order` = 0'
        .(($dateFrom && $dateTo) ? ' AND hbd.`date_add` BETWEEN "'.pSQL($dateFrom).' 00:00:00" AND "'.pSQL($dateTo).' 23:59:59"' : '')
        .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd');

        if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return $result[0];
        } else {
            return array ('avg_adults' => 0, 'avg_children' => 0);
        }
    }
}
