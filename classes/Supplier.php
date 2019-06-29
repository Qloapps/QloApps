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

class SupplierCore extends ObjectModel
{
    public $id;

    /** @var int supplier ID */
    public $id_supplier;

    /** @var string Name */
    public $name;

    /** @var string A short description for the discount */
    public $description;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var string Friendly URL */
    public $link_rewrite;

    /** @var string Meta title */
    public $meta_title;

    /** @var string Meta keywords */
    public $meta_keywords;

    /** @var string Meta description */
    public $meta_description;

    /** @var bool active */
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'supplier',
        'primary' => 'id_supplier',
        'multilang' => true,
        'fields' => array(
            'name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64),
            'active' =>            array('type' => self::TYPE_BOOL),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            /* Lang fields */
            'description' =>        array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'meta_title' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
            'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'link_rewrite' => array('sqlId' => 'link_rewrite'),
        ),
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);

        $this->link_rewrite = $this->getLink();
        $this->image_dir = _PS_SUPP_IMG_DIR_;
    }

    public function getLink()
    {
        return Tools::link_rewrite($this->name);
    }

    /**
     * Return suppliers
     *
     * @return array Suppliers
     */
    public static function getSuppliers($get_nb_products = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_groups = false)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }
        if (!Group::isFeatureActive()) {
            $all_groups = true;
        }

        $query = new DbQuery();
        $query->select('s.*, sl.`description`');
        $query->from('supplier', 's');
        $query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$id_lang);
        $query->join(Shop::addSqlAssociation('supplier', 's'));
        if ($active) {
            $query->where('s.`active` = 1');
        }
        $query->orderBy(' s.`name` ASC');
        $query->limit($n, ($p - 1) * $n);
        $query->groupBy('s.id_supplier');

        $suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if ($suppliers === false) {
            return false;
        }
        if ($get_nb_products) {
            $sql_groups = '';
            if (!$all_groups) {
                $groups = FrontController::getCurrentCustomerGroups();
                $sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
            }

            $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT  ps.`id_supplier`, COUNT(DISTINCT ps.`id_product`) as nb_products
					FROM `'._DB_PREFIX_.'product_supplier` ps
					JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'supplier` as m ON (m.`id_supplier`= p.`id_supplier`)
					WHERE ps.id_product_attribute = 0'.
                    ($active ? ' AND product_shop.`active` = 1' : '').
                    ' AND product_shop.`visibility` NOT IN ("none")'.
                    ($all_groups ? '' :'
					AND ps.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)').'
					GROUP BY ps.`id_supplier`'
                );

            $counts = array();
            foreach ($results as $result) {
                $counts[(int)$result['id_supplier']] = (int)$result['nb_products'];
            }

            if (count($counts) && is_array($suppliers)) {
                foreach ($suppliers as $key => $supplier) {
                    if (isset($counts[(int)$supplier['id_supplier']])) {
                        $suppliers[$key]['nb_products'] = $counts[(int)$supplier['id_supplier']];
                    } else {
                        $suppliers[$key]['nb_products'] = 0;
                    }
                }
            }
        }

        $nb_suppliers = count($suppliers);
        $rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS');
        for ($i = 0; $i < $nb_suppliers; $i++) {
            $suppliers[$i]['link_rewrite'] = ($rewrite_settings ? Tools::link_rewrite($suppliers[$i]['name']) : 0);
        }
        return $suppliers;
    }

    /**
     * Return name from id
     *
     * @param int $id_supplier Supplier ID
     * @return string name
     */
    protected static $cache_name = array();
    public static function getNameById($id_supplier)
    {
        if (!isset(self::$cache_name[$id_supplier])) {
            self::$cache_name[$id_supplier] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `name` FROM `'._DB_PREFIX_.'supplier` WHERE `id_supplier` = '.(int)$id_supplier);
        }
        return self::$cache_name[$id_supplier];
    }

    public static function getIdByName($name)
    {
        $result = Db::getInstance()->getRow('
		SELECT `id_supplier`
		FROM `'._DB_PREFIX_.'supplier`
		WHERE `name` = \''.pSQL($name).'\'');

        if (isset($result['id_supplier'])) {
            return (int)$result['id_supplier'];
        }

        return false;
    }

    public static function getProducts($id_supplier, $id_lang, $p, $n,
        $order_by = null, $order_way = null, $get_total = false, $active = true, $active_category = true)
    {
        $context = Context::getContext();
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if ($p < 1) {
            $p = 1;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'name';
        }
        if (empty($order_way)) {
            $order_way = 'ASC';
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = 'WHERE cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
        }

        /* Return only the number of products */
        if ($get_total) {
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(DISTINCT ps.`id_product`)
			FROM `'._DB_PREFIX_.'product_supplier` ps
			JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
			'.Shop::addSqlAssociation('product', 'p').'
			WHERE ps.`id_supplier` = '.(int)$id_supplier.'
			AND ps.id_product_attribute = 0
			'.($active ? ' AND product_shop.`active` = 1' : '').'
			'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
			AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_product` cp
				'.(Group::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.`id_category` = cg.`id_category`)' : '').'
				'.($active_category ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
				'.$sql_groups.'
			)');
        }

        $nb_days_new_product = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

        if (strpos('.', $order_by) > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
        }
        $alias = '';
        if (in_array($order_by, array('price', 'date_add', 'date_upd'))) {
            $alias = 'product_shop.';
        } elseif ($order_by == 'id_product') {
            $alias = 'p.';
        } elseif ($order_by == 'manufacturer_name') {
            $order_by = 'name';
            $alias = 'm.';
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock,
					IFNULL(stock.quantity, 0) as quantity,
					pl.`description`,
					pl.`description_short`,
					pl.`link_rewrite`,
					pl.`meta_description`,
					pl.`meta_keywords`,
					pl.`meta_title`,
					pl.`name`,
					image_shop.`id_image` id_image,
					il.`legend`,
					s.`name` AS supplier_name,
					DATEDIFF(p.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00", INTERVAL '.($nb_days_new_product).' DAY)) > 0 AS new,
					m.`name` AS manufacturer_name'.(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute' : '').'
				 FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.id_product = p.id_product
					AND ps.id_product_attribute = 0) '.
                (Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON s.`id_supplier` = p.`id_supplier`
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				'.Product::sqlStock('p', 0);

        if (Group::isFeatureActive() || $active_category) {
            $sql .= 'JOIN `'._DB_PREFIX_.'category_product` cp ON (p.id_product = cp.id_product)';
            if (Group::isFeatureActive()) {
                $sql .= 'JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.`id_category` = cg.`id_category` AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')';
            }
            if ($active_category) {
                $sql .= 'JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1';
            }
        }

        $sql .= '
				WHERE ps.`id_supplier` = '.(int)$id_supplier.'
					'.($active ? ' AND product_shop.`active` = 1' : '').'
					'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				GROUP BY ps.id_product
				ORDER BY '.$alias.pSQL($order_by).' '.pSQL($order_way).'
				LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }

        return Product::getProductsProperties($id_lang, $result);
    }

    public function getProductsLite($id_lang)
    {
        $context = Context::getContext();
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        $sql = '
			SELECT p.`id_product`,
				   pl.`name`
			FROM `'._DB_PREFIX_.'product` p
			'.Shop::addSqlAssociation('product', 'p').'
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
				p.`id_product` = pl.`id_product`
				AND pl.`id_lang` = '.(int)$id_lang.'
			)
			INNER JOIN `'._DB_PREFIX_.'product_supplier` ps ON (
				ps.`id_product` = p.`id_product`
				AND ps.`id_supplier` = '.(int)$this->id.'
			)
			'.($front ? ' WHERE product_shop.`visibility` IN ("both", "catalog")' : '').'
			GROUP BY p.`id_product`';

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        return $res;
    }

    /*
    * Tells if a supplier exists
    *
    * @param $id_supplier Supplier id
    * @return bool
    */
    public static function supplierExists($id_supplier)
    {
        $query = new DbQuery();
        $query->select('id_supplier');
        $query->from('supplier');
        $query->where('id_supplier = '.(int)$id_supplier);
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        return ($res > 0);
    }

    /**
     * @see ObjectModel::delete()
     */
    public function delete()
    {
        if (parent::delete()) {
            CartRule::cleanProductRuleIntegrity('suppliers', $this->id);
            return $this->deleteImage();
        }
    }

    /**
     * Gets product informations
     *
     * @since 1.5.0
     * @param int $id_supplier
     * @param int $id_product
     * @param int $id_product_attribute
     * @return array
     */
    public static function getProductInformationsBySupplier($id_supplier, $id_product, $id_product_attribute = 0)
    {
        $query = new DbQuery();
        $query->select('product_supplier_reference, product_supplier_price_te, id_currency');
        $query->from('product_supplier');
        $query->where('id_supplier = '.(int)$id_supplier);
        $query->where('id_product = '.(int)$id_product);
        $query->where('id_product_attribute = '.(int)$id_product_attribute);
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (count($res)) {
            return $res[0];
        }
    }
}
