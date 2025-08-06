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
 * Represents quantities available
 * It is either synchronized with Stock or manualy set by the seller
 *
 * @since 1.5.0
 */
class StockAvailableCore extends ObjectModel
{
    /** @var int identifier of the current product */
    public $id_product;

    /** @var int identifier of product attribute if necessary */
    public $id_product_attribute;

    /** @var int the shop associated to the current product and corresponding quantity */
    public $id_shop;

    /** @var int the group shop associated to the current product and corresponding quantity */
    public $id_shop_group;

    /** @var int the quantity available for sale */
    public $quantity = 0;

    /** @var bool determine if the available stock value depends on physical stock */
    public $depends_on_stock = false;

    /** @var bool determine if a product is out of stock - it was previously in Product class */
    public $out_of_stock = false;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'stock_available',
        'primary' => 'id_stock_available',
        'fields' => array(
            'id_product' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop_group' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'quantity' =>                array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'depends_on_stock' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'out_of_stock' =>            array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
        ),
    );

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = array(
        'fields' => array(
            'id_product' => array('xlink_resource' => 'products'),
            'id_product_attribute' => array('xlink_resource' => 'combinations'),
            'id_shop' => array('xlink_resource' => 'shops'),
            'id_shop_group' => array('xlink_resource' => 'shop_groups'),
        ),
        'hidden_fields' => array(
        ),
        'objectMethods' => array(
            'add' => 'addWs',
            'update' => 'updateWs',
        ),
    );

    /**
     * For a given {id_product, id_product_attribute and id_shop}, gets the stock available id associated
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $id_shop Optional
     * @return int
     */

    public function updateWs()
    {
        if ($this->depends_on_stock) {
            return WebserviceRequest::getInstance()->setError(500, Tools::displayError('You cannot update the available stock when it depends on stock.'), 133);
        }
        return $this->update();
    }

    public static function getStockAvailableIdByProductId($id_product, $id_product_attribute = null, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('id_stock_available');
        $query->from('stock_available');
        $query->where('id_product = '.(int)$id_product);

        if ($id_product_attribute !== null) {
            $query->where('id_product_attribute = '.(int)$id_product_attribute);
        }

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given id_product, synchronizes StockAvailable::quantity with Stock::usable_quantity
     *
     * @param int $id_product
     */
    public static function synchronize($id_product, $order_id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        //if product is pack sync recursivly product in pack
        if (Pack::isPack($id_product)) {
            if (Validate::isLoadedObject($product = new Product((int)$id_product))) {
                if ($product->pack_stock_type == 1 || $product->pack_stock_type == 2 || ($product->pack_stock_type == 3 && Configuration::get('PS_PACK_STOCK_TYPE') > 0)) {
                    $products_pack = Pack::getItems($id_product, (int)Configuration::get('PS_LANG_DEFAULT'));
                    foreach ($products_pack as $product_pack) {
                        StockAvailable::synchronize($product_pack->id, $order_id_shop);
                    }
                }
            } else {
                return false;
            }
        }

        // gets warehouse ids grouped by shops
        $ids_warehouse = Warehouse::getWarehousesGroupedByShops();
        if ($order_id_shop !== null) {
            $order_warehouses = array();
            $wh = Warehouse::getWarehouses(false, (int)$order_id_shop);
            foreach ($wh as $warehouse) {
                $order_warehouses[] = $warehouse['id_warehouse'];
            }
        }

        // gets all product attributes ids
        $ids_product_attribute = array();
        foreach (Product::getProductAttributesIds($id_product) as $id_product_attribute) {
            $ids_product_attribute[] = $id_product_attribute['id_product_attribute'];
        }

        // Allow to order the product when out of stock?
        $out_of_stock = StockAvailable::outOfStock($id_product);

        $manager = StockManagerFactory::getManager();
        // loops on $ids_warehouse to synchronize quantities
        foreach ($ids_warehouse as $id_shop => $warehouses) {
            // first, checks if the product depends on stock for the given shop $id_shop
            if (StockAvailable::dependsOnStock($id_product, $id_shop)) {
                // init quantity
                $product_quantity = 0;

                // if it's a simple product
                if (empty($ids_product_attribute)) {
                    $allowed_warehouse_for_product = WareHouse::getProductWarehouseList((int)$id_product, 0, (int)$id_shop);
                    $allowed_warehouse_for_product_clean = array();
                    foreach ($allowed_warehouse_for_product as $warehouse) {
                        $allowed_warehouse_for_product_clean[] = (int)$warehouse['id_warehouse'];
                    }
                    $allowed_warehouse_for_product_clean = array_intersect($allowed_warehouse_for_product_clean, $warehouses);
                    if ($order_id_shop != null && !count(array_intersect($allowed_warehouse_for_product_clean, $order_warehouses))) {
                        continue;
                    }

                    $product_quantity = $manager->getProductRealQuantities($id_product, null, $allowed_warehouse_for_product_clean, true);

                    Hook::exec('actionUpdateQuantity',
                                    array(
                                        'id_product' => $id_product,
                                        'id_product_attribute' => 0,
                                        'quantity' => $product_quantity,
                                        'id_shop' => $id_shop
                                        )
                    );
                }
                // else this product has attributes, hence loops on $ids_product_attribute
                else {
                    foreach ($ids_product_attribute as $id_product_attribute) {
                        $allowed_warehouse_for_combination = WareHouse::getProductWarehouseList((int)$id_product, (int)$id_product_attribute, (int)$id_shop);
                        $allowed_warehouse_for_combination_clean = array();
                        foreach ($allowed_warehouse_for_combination as $warehouse) {
                            $allowed_warehouse_for_combination_clean[] = (int)$warehouse['id_warehouse'];
                        }
                        $allowed_warehouse_for_combination_clean = array_intersect($allowed_warehouse_for_combination_clean, $warehouses);
                        if (($order_id_shop != null && !count(array_intersect($allowed_warehouse_for_combination_clean, $order_warehouses))) ||
                            !count($allowed_warehouse_for_combination_clean)
                        ) {
                            continue;
                        }

                        $quantity = $manager->getProductRealQuantities($id_product, $id_product_attribute, $allowed_warehouse_for_combination_clean, true);

                        $query = new DbQuery();
                        $query->select('COUNT(*)');
                        $query->from('stock_available');
                        $query->where('id_product = '.(int)$id_product.' AND id_product_attribute = '.(int)$id_product_attribute.
                            StockAvailable::addSqlShopRestriction(null, $id_shop));

                        if ((int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query)) {
                            $query = array(
                                'table' => 'stock_available',
                                'data' => array('quantity' => $quantity),
                                'where' => 'id_product = '.(int)$id_product.' AND id_product_attribute = '.(int)$id_product_attribute.
                                StockAvailable::addSqlShopRestriction(null, $id_shop)
                            );
                            Db::getInstance()->update($query['table'], $query['data'], $query['where']);
                        } else {
                            $query = array(
                                'table' => 'stock_available',
                                'data' => array(
                                    'quantity' => $quantity,
                                    'depends_on_stock' => 1,
                                    'out_of_stock' => $out_of_stock,
                                    'id_product' => (int)$id_product,
                                    'id_product_attribute' => (int)$id_product_attribute,
                                )
                            );
                            StockAvailable::addSqlShopParams($query['data'], $id_shop);
                            Db::getInstance()->insert($query['table'], $query['data']);
                        }

                        $product_quantity += $quantity;

                        Hook::exec('actionUpdateQuantity',
                                    array(
                                        'id_product' => $id_product,
                                        'id_product_attribute' => $id_product_attribute,
                                        'quantity' => $quantity,
                                        'id_shop' => $id_shop
                                    )
                        );
                    }
                }
                // updates
                // if $id_product has attributes, it also updates the sum for all attributes
                if (($order_id_shop != null && array_intersect($warehouses, $order_warehouses)) || $order_id_shop == null) {
                    $query = array(
                        'table' => 'stock_available',
                        'data' => array('quantity' => $product_quantity),
                        'where' => 'id_product = '.(int)$id_product.' AND id_product_attribute = 0'.
                        StockAvailable::addSqlShopRestriction(null, $id_shop)
                    );
                    Db::getInstance()->update($query['table'], $query['data'], $query['where']);
                }
            }
        }
        // In case there are no warehouses, removes product from StockAvailable
        if (count($ids_warehouse) == 0 && StockAvailable::dependsOnStock((int)$id_product)) {
            Db::getInstance()->update('stock_available', array('quantity' => 0 ), 'id_product = '.(int)$id_product);
        }

        Cache::clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$id_product.'*');
    }

    /**
     * For a given id_product, sets if stock available depends on stock
     *
     * @param int $id_product
     * @param int $depends_on_stock Optional : true by default
     * @param int $id_shop Optional : gets context by default
     */
    public static function setProductDependsOnStock($id_product, $depends_on_stock = true, $id_shop = null, $id_product_attribute = 0)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $existing_id = StockAvailable::getStockAvailableIdByProductId((int)$id_product, (int)$id_product_attribute, $id_shop);
        if ($existing_id > 0) {
            Db::getInstance()->update('stock_available', array(
                'depends_on_stock' => (int)$depends_on_stock
            ), 'id_stock_available = '.(int)$existing_id);
        } else {
            $params = array(
                'depends_on_stock' => (int)$depends_on_stock,
                'id_product' => (int)$id_product,
                'id_product_attribute' => (int)$id_product_attribute
            );

            StockAvailable::addSqlShopParams($params, $id_shop);

            Db::getInstance()->insert('stock_available', $params);
        }

        // depends on stock.. hence synchronizes
        if ($depends_on_stock) {
            StockAvailable::synchronize($id_product);
        }
    }

    /**
     * For a given id_product, sets if product is available out of stocks
     *
     * @param int $id_product
     * @param int $out_of_stock Optional false by default
     * @param int $id_shop Optional gets context by default
     */
    public static function setProductOutOfStock($id_product, $out_of_stock = false, $id_shop = null, $id_product_attribute = 0)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $existing_id = (int)StockAvailable::getStockAvailableIdByProductId((int)$id_product, (int)$id_product_attribute, $id_shop);

        if ($existing_id > 0) {
            Db::getInstance()->update(
                'stock_available',
                array('out_of_stock' => (int)$out_of_stock),
                'id_product = '.(int)$id_product.
                (($id_product_attribute) ? ' AND id_product_attribute = '.(int)$id_product_attribute : '').
                StockAvailable::addSqlShopRestriction(null, $id_shop)
            );
        } else {
            $params = array(
                'out_of_stock' => (int)$out_of_stock,
                'id_product' => (int)$id_product,
                'id_product_attribute' => (int)$id_product_attribute
            );

            StockAvailable::addSqlShopParams($params, $id_shop);
            Db::getInstance()->insert('stock_available', $params, false, true, Db::ON_DUPLICATE_KEY);
        }
    }

    /**
     * For a given id_product and id_product_attribute, gets its stock available
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $id_shop Optional : gets context by default
     * @return int Quantity
     */
    public static function getQuantityAvailableByProduct($id_product = null, $id_product_attribute = null, $id_shop = null)
    {
        // if null, it's a product without attributes
        if ($id_product_attribute === null) {
            $id_product_attribute = 0;
        }

        $key = 'StockAvailable::getQuantityAvailableByProduct_'.(int)$id_product.'-'.(int)$id_product_attribute.'-'.(int)$id_shop;
        if (!Cache::isStored($key)) {
            $query = new DbQuery();
            $query->select('SUM(quantity)');
            $query->from('stock_available');

            // if null, it's a product without attributes
            if ($id_product !== null) {
                $query->where('id_product = '.(int)$id_product);
            }

            $query->where('id_product_attribute = '.(int)$id_product_attribute);
            $query = StockAvailable::addSqlShopRestriction($query, $id_shop);
            $result = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            Cache::store($key, $result);
            return $result;
        }

        return Cache::retrieve($key);
    }

    /**
     * Upgrades total_quantity_available after having saved
     * @see ObjectModel::add()
     */
    public function add($autodate = true, $null_values = false)
    {
        if (!$result = parent::add($autodate, $null_values)) {
            return false;
        }

        $result &= $this->postSave();
        return $result;
    }

    /**
     * Upgrades total_quantity_available after having update
     * @see ObjectModel::update()
     */
    public function update($null_values = false)
    {
        if (!$result = parent::update($null_values)) {
            return false;
        }

        $result &= $this->postSave();
        return $result;
    }

    /**
     * Upgrades total_quantity_available after having saved
     * @see StockAvailableCore::update()
     * @see StockAvailableCore::add()
     */
    public function postSave()
    {
        if ($this->id_product_attribute == 0) {
            return true;
        }

        $id_shop = (Shop::getContext() != Shop::CONTEXT_GROUP && $this->id_shop ? $this->id_shop : null);

        if (!Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) {
            $combination = new Combination((int)$this->id_product_attribute);
            if ($colors = $combination->getColorsAttributes()) {
                $product = new Product((int)$this->id_product);
                foreach ($colors as $color) {
                    if ($product->isColorUnavailable((int)$color['id_attribute'], (int)$this->id_shop)) {
                        Tools::clearColorListCache($product->id);
                        break;
                    }
                }
            }
        }

        $total_quantity = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT SUM(quantity) as quantity
			FROM '._DB_PREFIX_.'stock_available
			WHERE id_product = '.(int)$this->id_product.'
			AND id_product_attribute <> 0 '.
            StockAvailable::addSqlShopRestriction(null, $id_shop)
        );
        $this->setQuantity($this->id_product, 0, $total_quantity, $id_shop);

        return true;
    }

    /**
     * For a given id_product and id_product_attribute updates the quantity available
     * If $avoid_parent_pack_update is true, then packs containing the given product won't be updated
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $delta_quantity The delta quantity to update
     * @param int $id_shop Optional
     */
    public static function updateQuantity($id_product, $id_product_attribute, $delta_quantity, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }
        $product = new Product((int)$id_product);
        if (!Validate::isLoadedObject($product)) {
            return false;
        }

        $stockManager = Adapter_ServiceLocator::get('Core_Business_Stock_StockManager');
        $stockManager->updateQuantity($product, $id_product_attribute, $delta_quantity, $id_shop);
        return true;
    }

    /**
     * For a given id_product and id_product_attribute sets the quantity available
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $delta_quantity The delta quantity to update
     * @param int $id_shop Optional
     */
    public static function setQuantity($id_product, $id_product_attribute, $quantity, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $context = Context::getContext();

        // if there is no $id_shop, gets the context one
        if ($id_shop === null && Shop::getContext() != Shop::CONTEXT_GROUP) {
            $id_shop = (int)$context->shop->id;
        }

        $depends_on_stock = StockAvailable::dependsOnStock($id_product);

        //Try to set available quantity if product does not depend on physical stock
        if (!$depends_on_stock) {
            $id_stock_available = (int)StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);
            if ($id_stock_available) {
                $stock_available = new StockAvailable($id_stock_available);
                $stock_available->quantity = (int)$quantity;
                $stock_available->update();
            } else {
                $out_of_stock = StockAvailable::outOfStock($id_product, $id_shop);
                $stock_available = new StockAvailable();
                $stock_available->out_of_stock = (int)$out_of_stock;
                $stock_available->id_product = (int)$id_product;
                $stock_available->id_product_attribute = (int)$id_product_attribute;
                $stock_available->quantity = (int)$quantity;

                if ($id_shop === null) {
                    $shop_group = Shop::getContextShopGroup();
                } else {
                    $shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$id_shop));
                }

                // if quantities are shared between shops of the group
                if ($shop_group->share_stock) {
                    $stock_available->id_shop = 0;
                    $stock_available->id_shop_group = (int)$shop_group->id;
                } else {
                    $stock_available->id_shop = (int)$id_shop;
                    $stock_available->id_shop_group = 0;
                }
                $stock_available->add();
            }

            Hook::exec('actionUpdateQuantity',
                array(
                    'id_product' => $id_product,
                    'id_product_attribute' => $id_product_attribute,
                    'quantity' => $stock_available->quantity
                )
            );
        }

        Cache::clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$id_product.'*');
    }

    /**
     * Removes a given product from the stock available
     *
     * @param int $id_product
     * @param int|null $id_product_attribute Optional
     * @param Shop|null $shop Shop id or shop object Optional
     *
     * @return bool
     */
    public static function removeProductFromStockAvailable($id_product, $id_product_attribute = null, $shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        if (Shop::getContext() == SHOP::CONTEXT_SHOP) {
            if (Shop::getContextShopGroup()->share_stock == 1) {
                $pa_sql = '';
                if ($id_product_attribute !== null) {
                    $pa_sql = '_attribute';
                    $id_product_attribute_sql = $id_product_attribute;
                } else {
                    $id_product_attribute_sql = $id_product;
                }

                if ((int)Db::getInstance()->getValue('SELECT COUNT(*)
						FROM '._DB_PREFIX_.'product'.$pa_sql.'_shop
						WHERE id_product'.$pa_sql.'='.(int)$id_product_attribute_sql.'
							AND id_shop IN ('.implode(',', array_map('intval', Shop::getContextListShopID(SHOP::SHARE_STOCK))).')')) {
                    return true;
                }
            }
        }

        $res = Db::getInstance()->execute('
		DELETE FROM '._DB_PREFIX_.'stock_available
		WHERE id_product = '.(int)$id_product.
        ($id_product_attribute ? ' AND id_product_attribute = '.(int)$id_product_attribute : '').
        StockAvailable::addSqlShopRestriction(null, $shop));

        if ($id_product_attribute) {
            if ($shop === null || !Validate::isLoadedObject($shop)) {
                $shop_datas = array();
                StockAvailable::addSqlShopParams($shop_datas);
                $id_shop = (int)$shop_datas['id_shop'];
            } else {
                $id_shop = (int)$shop->id;
            }

            $stock_available = new StockAvailable();
            $stock_available->id_product = (int)$id_product;
            $stock_available->id_product_attribute = (int)$id_product_attribute;
            $stock_available->id_shop = (int)$id_shop;
            $stock_available->postSave();
        }

        Cache::clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$id_product.'*');

        return $res;
    }

    /**
     * Removes all product quantities from all a group of shops
     * If stocks are shared, remoe all old available quantities for all shops of the group
     * Else remove all available quantities for the current group
     *
     * @param ShopGroup $shop_group the ShopGroup object
     */
    public static function resetProductFromStockAvailableByShopGroup(ShopGroup $shop_group)
    {
        if ($shop_group->share_stock) {
            $shop_list = Shop::getShops(false, $shop_group->id, true);
        }

        if (count($shop_list) > 0) {
            $id_shops_list = implode(', ', $shop_list);
            return Db::getInstance()->update('stock_available', array('quantity' => 0), 'id_shop IN ('.$id_shops_list.')');
        } else {
            return Db::getInstance()->update('stock_available', array('quantity' => 0), 'id_shop_group = '.$shop_group->id);
        }
    }

    /**
     * For a given product, tells if it depends on the physical (usable) stock
     *
     * @param int $id_product
     * @param int $id_shop Optional : gets context if null @see Context::getContext()
     * @return bool : depends on stock @see $depends_on_stock
     */
    public static function dependsOnStock($id_product, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('depends_on_stock');
        $query->from('stock_available');
        $query->where('id_product = '.(int)$id_product);
        $query->where('id_product_attribute = 0');

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given product, get its "out of stock" flag
     *
     * @param int $id_product
     * @param int $id_shop Optional : gets context if null @see Context::getContext()
     * @return bool : depends on stock @see $depends_on_stock
     */
    public static function outOfStock($id_product, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('out_of_stock');
        $query->from('stock_available');
        $query->where('id_product = '.(int)$id_product);
        $query->where('id_product_attribute = 0');

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * Add an sql restriction for shops fields - specific to StockAvailable
     *
     * @param DbQuery|string|null $sql   Reference to the query object
     * @param Shop|int|null       $shop  Optional : The shop ID
     * @param string|null         $alias Optional : The current table alias
     *
     * @return string|DbQuery DbQuery object or the sql restriction string
     */
    public static function addSqlShopRestriction($sql = null, $shop = null, $alias = null)
    {
        $context = Context::getContext();

        if (!empty($alias)) {
            $alias .= '.';
        }

        // if there is no $id_shop, gets the context one
        // get shop group too
        if ($shop === null || $shop === $context->shop->id) {
            if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shop_group = Shop::getContextShopGroup();
            } else {
                $shop_group = $context->shop->getGroup();
            }
            $shop = $context->shop;
        } elseif (is_object($shop)) {
            /** @var Shop $shop */
            $shop_group = $shop->getGroup();
        } else {
            $shop = new Shop($shop);
            $shop_group = $shop->getGroup();
        }

        // if quantities are shared between shops of the group
        if ($shop_group->share_stock) {
            if (is_object($sql)) {
                $sql->where(pSQL($alias).'id_shop_group = '.(int)$shop_group->id);
                $sql->where(pSQL($alias).'id_shop = 0');
            } else {
                $sql = ' AND '.pSQL($alias).'id_shop_group = '.(int)$shop_group->id.' ';
                $sql .= ' AND '.pSQL($alias).'id_shop = 0 ';
            }
        } else {
            if (is_object($sql)) {
                $sql->where(pSQL($alias).'id_shop = '.(int)$shop->id);
                $sql->where(pSQL($alias).'id_shop_group = 0');
            } else {
                $sql = ' AND '.pSQL($alias).'id_shop = '.(int)$shop->id.' ';
                $sql .= ' AND '.pSQL($alias).'id_shop_group = 0 ';
            }
        }

        return $sql;
    }

    /**
     * Add sql params for shops fields - specific to StockAvailable
     *
     * @param array $params Reference to the params array
     * @param int $id_shop Optional : The shop ID
     *
     */
    public static function addSqlShopParams(&$params, $id_shop = null)
    {
        $context = Context::getContext();
        $group_ok = false;

        // if there is no $id_shop, gets the context one
        // get shop group too
        if ($id_shop === null) {
            if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shop_group = Shop::getContextShopGroup();
            } else {
                $shop_group = $context->shop->getGroup();
                $id_shop = $context->shop->id;
            }
        } else {
            $shop = new Shop($id_shop);
            $shop_group = $shop->getGroup();
        }

        // if quantities are shared between shops of the group
        if ($shop_group->share_stock) {
            $params['id_shop_group'] = (int)$shop_group->id;
            $params['id_shop'] = 0;

            $group_ok = true;
        } else {
            $params['id_shop_group'] = 0;
        }

        // if no group specific restriction, set simple shop restriction
        if (!$group_ok) {
            $params['id_shop'] = (int)$id_shop;
        }
    }

    /**
     * Copies stock available content table
     *
     * @param int $src_shop_id
     * @param int $dst_shop_id
     * @return bool
     */
    public static function copyStockAvailableFromShopToShop($src_shop_id, $dst_shop_id)
    {
        if (!$src_shop_id || !$dst_shop_id) {
            return false;
        }

        $query = '
			INSERT INTO '._DB_PREFIX_.'stock_available
			(
				id_product,
				id_product_attribute,
				id_shop,
				id_shop_group,
				quantity,
				depends_on_stock,
				out_of_stock
			)
			(
				SELECT id_product, id_product_attribute, '.(int)$dst_shop_id.', 0, quantity, depends_on_stock, out_of_stock
				FROM '._DB_PREFIX_.'stock_available
				WHERE id_shop = '.(int)$src_shop_id.
            ')';

        return Db::getInstance()->execute($query);
    }
}
