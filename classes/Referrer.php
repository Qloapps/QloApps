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

class ReferrerCore extends ObjectModel
{
    public $id_shop;
    public $name;
    public $passwd;

    public $http_referer_regexp;
    public $http_referer_like;
    public $request_uri_regexp;
    public $request_uri_like;
    public $http_referer_regexp_not;
    public $http_referer_like_not;
    public $request_uri_regexp_not;
    public $request_uri_like_not;

    public $base_fee;
    public $percent_fee;
    public $click_fee;

    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'referrer',
        'primary' => 'id_referrer',
        'fields' => array(
            'name' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
            'passwd' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'size' => 32),
            'http_referer_regexp' =>        array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 64),
            'request_uri_regexp' =>        array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 64),
            'http_referer_like' =>            array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 64),
            'request_uri_like' =>            array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 64),
            'http_referer_regexp_not' =>    array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'request_uri_regexp_not' =>    array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'http_referer_like_not' =>        array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'request_uri_like_not' =>        array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'base_fee' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'percent_fee' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPercentage'),
            'click_fee' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'date_add' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    protected static $_join = '(r.http_referer_like IS NULL OR r.http_referer_like = \'\' OR cs.http_referer LIKE r.http_referer_like)
			AND (r.request_uri_like IS NULL OR r.request_uri_like = \'\' OR cs.request_uri LIKE r.request_uri_like)
			AND (r.http_referer_like_not IS NULL OR r.http_referer_like_not = \'\' OR cs.http_referer NOT LIKE r.http_referer_like_not)
			AND (r.request_uri_like_not IS NULL OR r.request_uri_like_not = \'\' OR cs.request_uri NOT LIKE r.request_uri_like_not)
			AND (r.http_referer_regexp IS NULL OR r.http_referer_regexp = \'\' OR cs.http_referer REGEXP r.http_referer_regexp)
			AND (r.request_uri_regexp IS NULL OR r.request_uri_regexp = \'\' OR cs.request_uri REGEXP r.request_uri_regexp)
			AND (r.http_referer_regexp_not IS NULL OR r.http_referer_regexp_not = \'\' OR cs.http_referer NOT REGEXP r.http_referer_regexp_not)
			AND (r.request_uri_regexp_not IS NULL OR r.request_uri_regexp_not = \'\' OR cs.request_uri NOT REGEXP r.request_uri_regexp_not)';

    public function add($autodate = true, $null_values = false)
    {
        if (!($result = parent::add($autodate, $null_values))) {
            return false;
        }
        Referrer::refreshCache(array(array('id_referrer' => $this->id)));
        Referrer::refreshIndex(array(array('id_referrer' => $this->id)));
        return $result;
    }

    public static function cacheNewSource($id_connections_source)
    {
        if (!$id_connections_source) {
            return;
        }

        $sql = 'INSERT INTO '._DB_PREFIX_.'referrer_cache (id_referrer, id_connections_source) (
					SELECT id_referrer, id_connections_source
					FROM '._DB_PREFIX_.'referrer r
					LEFT JOIN '._DB_PREFIX_.'connections_source cs ON ('.self::$_join.')
					WHERE id_connections_source = '.(int)$id_connections_source.'
				)';
        Db::getInstance()->execute($sql);
    }

    /**
     * Get list of referrers connections of a customer
     *
     * @param int $id_customer
     */
    public static function getReferrers($id_customer)
    {
        $sql = 'SELECT DISTINCT c.date_add, r.name, s.name AS shop_name
				FROM '._DB_PREFIX_.'guest g
				LEFT JOIN '._DB_PREFIX_.'connections c ON c.id_guest = g.id_guest
				LEFT JOIN '._DB_PREFIX_.'connections_source cs ON c.id_connections = cs.id_connections
				LEFT JOIN '._DB_PREFIX_.'referrer r ON ('.self::$_join.')
				LEFT JOIN '._DB_PREFIX_.'shop s ON s.id_shop = c.id_shop
				WHERE g.id_customer = '.(int)$id_customer.'
					AND r.name IS NOT NULL
				ORDER BY c.date_add DESC';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Get some statistics on visitors connection for current referrer
     *
     * @param int $id_product
     * @param int $employee
     */
    public function getStatsVisits($id_product, $employee)
    {
        $join = $where = '';
        if ($id_product) {
            $join = 'LEFT JOIN `'._DB_PREFIX_.'page` p ON cp.`id_page` = p.`id_page`
					 LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`';
            $where = ' AND pt.`name` = \'product\'
					  AND p.`id_object` = '.(int)$id_product;
        }

        $sql = 'SELECT COUNT(DISTINCT cs.id_connections_source) AS visits,
			COUNT(DISTINCT cs.id_connections) as visitors,
			COUNT(DISTINCT c.id_guest) as uniqs,
			COUNT(DISTINCT cp.time_start) as pages
			FROM '._DB_PREFIX_.'referrer_cache rc
			LEFT JOIN '._DB_PREFIX_.'referrer r ON rc.id_referrer = r.id_referrer
			LEFT JOIN '._DB_PREFIX_.'referrer_shop rs ON r.id_referrer = rs.id_referrer
			LEFT JOIN '._DB_PREFIX_.'connections_source cs ON rc.id_connections_source = cs.id_connections_source
			LEFT JOIN '._DB_PREFIX_.'connections c ON cs.id_connections = c.id_connections
			LEFT JOIN '._DB_PREFIX_.'connections_page cp ON cp.id_connections = c.id_connections
			'.$join.'
			WHERE 1'.
            ((isset($employee->stats_date_from) && isset($employee->stats_date_to))? ' AND cs.date_add BETWEEN \''.pSQL($employee->stats_date_from).' 00:00:00\' AND \''.pSQL($employee->stats_date_to).' 23:59:59\'' : '').
            Shop::addSqlRestriction(false, 'rs').
            Shop::addSqlRestriction(false, 'c').
            ' AND rc.id_referrer = '.(int)$this->id.
            $where;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    /**
     * Get some statistics on customers registrations for current referrer
     *
     * @param int $id_product
     * @param int $employee
     */
    public function getRegistrations($id_product, $employee)
    {
        $join = $where = '';
        if ($id_product) {
            $join = 'LEFT JOIN '._DB_PREFIX_.'connections_page cp ON cp.id_connections = c.id_connections
					 LEFT JOIN `'._DB_PREFIX_.'page` p ON cp.`id_page` = p.`id_page`
					 LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`';
            $where = ' AND pt.`name` = \'product\'
					  AND p.`id_object` = '.(int)$id_product;
        }

        $sql = 'SELECT COUNT(DISTINCT cu.id_customer) AS registrations
				FROM '._DB_PREFIX_.'referrer_cache rc
				LEFT JOIN '._DB_PREFIX_.'referrer_shop rs ON rc.id_referrer = rs.id_referrer
				LEFT JOIN '._DB_PREFIX_.'connections_source cs ON rc.id_connections_source = cs.id_connections_source
				LEFT JOIN '._DB_PREFIX_.'connections c ON cs.id_connections = c.id_connections
				LEFT JOIN '._DB_PREFIX_.'guest g ON g.id_guest = c.id_guest
				LEFT JOIN '._DB_PREFIX_.'customer cu ON cu.id_customer = g.id_customer
				'.$join.'
				WHERE cu.date_add BETWEEN '.ModuleGraph::getDateBetween($employee).'
					'.Shop::addSqlRestriction(false, 'rs').'
					'.Shop::addSqlRestriction(false, 'c').'
					'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'cu').'
					AND cu.date_add > cs.date_add
					AND rc.id_referrer = '.(int)$this->id
                    .$where;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        return (int)$result['registrations'];
    }

    /**
     * Get some statistics on orders for current referrer
     *
     * @param int $id_product
     * @param int $employee
     */
    public function getStatsSales($id_product, $employee)
    {
        $join = $where = '';
        if ($id_product) {
            $join =    'LEFT JOIN '._DB_PREFIX_.'order_detail od ON oo.id_order = od.id_order';
            $where = ' AND od.product_id = '.(int)$id_product;
        }

        $sql = 'SELECT oo.id_order
				FROM '._DB_PREFIX_.'referrer_cache rc
				LEFT JOIN '._DB_PREFIX_.'referrer_shop rs ON rc.id_referrer = rs.id_referrer
				INNER JOIN '._DB_PREFIX_.'connections_source cs ON rc.id_connections_source = cs.id_connections_source
				INNER JOIN '._DB_PREFIX_.'connections c ON cs.id_connections = c.id_connections
				INNER JOIN '._DB_PREFIX_.'guest g ON g.id_guest = c.id_guest
				LEFT JOIN '._DB_PREFIX_.'orders oo ON oo.id_customer = g.id_customer
				'.$join.'
				WHERE oo.invoice_date BETWEEN '.ModuleGraph::getDateBetween($employee).'
					'.Shop::addSqlRestriction(false, 'rs').'
					'.Shop::addSqlRestriction(false, 'c').'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'oo').'
					AND oo.date_add > cs.date_add
					AND rc.id_referrer = '.(int)$this->id.'
					AND oo.valid = 1'
                    .$where;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $implode = array();
        foreach ($result as $row) {
            if ((int)$row['id_order']) {
                $implode[] = (int)$row['id_order'];
            }
        }

        if ($implode) {
            $sql = 'SELECT COUNT(id_order) AS orders, SUM(total_paid_real / conversion_rate) AS sales
					FROM '._DB_PREFIX_.'orders
					WHERE id_order IN ('.implode($implode, ',').')
						'.Shop::addSqlRestriction(Shop::SHARE_ORDER).'
						AND valid = 1';
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        } else {
            return array('orders' => 0, 'sales' => 0);
        }
    }

    /**
     * Refresh cache data of referrer statistics in referrer_shop table
     *
     * @param array $referrers
     * @param int $employee
     * @return true
     */
    public static function refreshCache($referrers = null, $employee = null)
    {
        if (!$referrers || !is_array($referrers)) {
            $referrers = Db::getInstance()->executeS('SELECT id_referrer FROM '._DB_PREFIX_.'referrer');
        }
        foreach ($referrers as $row) {
            $referrer = new Referrer($row['id_referrer']);
            foreach (Shop::getShops(true, null, true) as $shop_id) {
                if (!$referrer->isAssociatedToShop($shop_id)) {
                    continue;
                }

                $stats_visits = $referrer->getStatsVisits(null, $employee);
                $registrations = $referrer->getRegistrations(null, $employee);
                $stats_sales = $referrer->getStatsSales(null, $employee);

                Db::getInstance()->update('referrer_shop', array(
                    'cache_visitors' => (int)$stats_visits['uniqs'],
                    'cache_visits' => (int)$stats_visits['visits'],
                    'cache_pages' => (int)$stats_visits['pages'],
                    'cache_registrations' => (int)$registrations,
                    'cache_orders' => (int)$stats_sales['orders'],
                    'cache_sales' => number_format($stats_sales['sales'], 2, '.', ''),
                    'cache_reg_rate' => $stats_visits['uniqs'] ? $registrations / $stats_visits['uniqs'] : 0,
                    'cache_order_rate' => $stats_visits['uniqs'] ? $stats_sales['orders'] / $stats_visits['uniqs'] : 0,
                ), 'id_referrer = '.(int)$referrer->id.' AND id_shop = '.(int)$shop_id);
            }
        }

        Configuration::updateValue('PS_REFERRERS_CACHE_LIKE', ModuleGraph::getDateBetween($employee));
        Configuration::updateValue('PS_REFERRERS_CACHE_DATE', date('Y-m-d H:i:s'));
        return true;
    }

    /**
     * Cache liaison between connections_source data and referrers data
     *
     * @param array $referrers
     */
    public static function refreshIndex($referrers = null)
    {
        if (!$referrers || !is_array($referrers)) {
            Db::getInstance()->execute('TRUNCATE '._DB_PREFIX_.'referrer_cache');
            Db::getInstance()->execute('
			INSERT INTO '._DB_PREFIX_.'referrer_cache (id_referrer, id_connections_source) (
				SELECT id_referrer, id_connections_source
				FROM '._DB_PREFIX_.'referrer r
				LEFT JOIN '._DB_PREFIX_.'connections_source cs ON ('.self::$_join.')
			)');
        } else {
            foreach ($referrers as $row) {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'referrer_cache WHERE id_referrer = '.(int)$row['id_referrer']);
                Db::getInstance()->execute('
				INSERT INTO '._DB_PREFIX_.'referrer_cache (id_referrer, id_connections_source) (
					SELECT id_referrer, id_connections_source
					FROM '._DB_PREFIX_.'referrer r
					LEFT JOIN '._DB_PREFIX_.'connections_source cs ON ('.self::$_join.')
					WHERE id_referrer = '.(int)$row['id_referrer'].'
					AND id_connections_source IS NOT NULL
				)');
            }
        }
    }

    public static function getAjaxProduct($id_referrer, $id_product, $employee = null)
    {
        $context = Context::getContext();
        $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'));
        $currency = Currency::getCurrencyInstance(Configuration::get('PS_CURRENCY_DEFAULT'));
        $referrer = new Referrer($id_referrer);
        $stats_visits = $referrer->getStatsVisits($id_product, $employee);
        $registrations = $referrer->getRegistrations($id_product, $employee);
        $stats_sales = $referrer->getStatsSales($id_product, $employee);

        // If it's a product and it has no visits nor orders
        if ((int)$id_product && !$stats_visits['visits'] && !$stats_sales['orders']) {
            exit;
        }

        $json_array = array(
            'id_product' => (int)$product->id,
            'product_name' => htmlspecialchars($product->name),
            'uniqs' => (int)$stats_visits['uniqs'],
            'visitors' => (int)$stats_visits['visitors'],
            'visits' => (int)$stats_visits['visits'],
            'pages' => (int)$stats_visits['pages'],
            'registrations' => (int)$registrations,
            'orders' => (int)$stats_sales['orders'],
            'sales' => Tools::displayPrice($stats_sales['sales'], $currency),
            'cart' => Tools::displayPrice(((int)$stats_sales['orders'] ? $stats_sales['sales'] / (int)$stats_sales['orders'] : 0), $currency),
            'reg_rate' => number_format((int)$stats_visits['uniqs'] ? (int)$registrations / (int)$stats_visits['uniqs'] : 0, 4, '.', ''),
            'order_rate' => number_format((int)$stats_visits['uniqs'] ? (int)$stats_sales['orders'] / (int)$stats_visits['uniqs'] : 0, 4, '.', ''),
            'click_fee' => Tools::displayPrice((int)$stats_visits['visits'] * $referrer->click_fee, $currency),
            'base_fee' => Tools::displayPrice($stats_sales['orders'] * $referrer->base_fee, $currency),
            'percent_fee' => Tools::displayPrice($stats_sales['sales'] * $referrer->percent_fee / 100, $currency),
        );

        die('['.json_encode($json_array).']');
    }
}
