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
 * StockManagerInterface : defines a way to manage stock
 * @since 1.5.0
 */
interface StockManagerInterface
{
    /**
     * Checks if the StockManager is available
     *
     * @return StockManagerInterface
     */
    public static function isAvailable();

    /**
     * For a given product, adds a given quantity
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param Warehouse $warehouse
     * @param int $quantity
     * @param int $id_stock_movement_reason
     * @param float $price_te
     * @param bool $is_usable
     * @param int $id_supply_order optionnal
     * @return bool
     */
    public function addProduct($id_product, $id_product_attribute, Warehouse $warehouse, $quantity, $id_stock_movement_reason, $price_te, $is_usable = true, $id_supply_order = null);

    /**
     * For a given product, removes a given quantity
     *
     * @param int           $id_product
     * @param int|null      $id_product_attribute
     * @param Warehouse     $warehouse
     * @param int           $quantity
     * @param int           $id_stock_mvt_reason
     * @param bool          $is_usable
     * @param int|null      $id_order
     * @param int           $ignore_pack
     * @param Employee|null $employee
     * @param Stock|null    $stock
     *
     * @return array - empty if an error occurred | details of removed products quantities with corresponding prices otherwise
     */
    public function removeProduct(
        $id_product,
        $id_product_attribute,
        Warehouse $warehouse,
        $quantity,
        $id_stock_movement_reason,
        $is_usable = true,
        $id_order = null,
        $ignore_pack = 0,
        $employee = null,
        Stock $stock = null
    );

    /**
     * For a given product, returns its physical quantity
     * If the given product has combinations and $id_product_attribute is null, returns the sum for all combinations
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param array|int $ids_warehouse optional
     * @param bool $usable false default - in this case we retrieve all physical quantities, otherwise we retrieve physical quantities flagged as usable
     * @return int
     */
    public function getProductPhysicalQuantities($id_product, $id_product_attribute, $ids_warehouse = null, $usable = false);

    /**
     * For a given product, returns its real quantity
     * If the given product has combinations and $id_product_attribute is null, returns the sum for all combinations
     * Real quantity : (physical_qty + supply_orders_qty - client_orders_qty)
     * If $usable is defined, real quantity: usable_qty + supply_orders_qty - client_orders_qty
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param array|int $ids_warehouse optional
     * @param bool $usable false by default
     * @return int
     */
    public function getProductRealQuantities($id_product, $id_product_attribute, $ids_warehouse = null, $usable = false);

    /**
     * For a given product, transfers quantities between two warehouses
     * By default, it manages usable quantities
     * It is also possible to transfer a usable quantity from warehouse 1 in an unusable quantity to warehouse 2
     * It is also possible to transfer a usable quantity from warehouse 1 in an unusable quantity to warehouse 1
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $quantity
     * @param int $warehouse_from
     * @param int $warehouse_to
     * @param bool $usable_from Optional, true by default
     * @param bool $usable_to Optional, true by default
     * @return bool
     */
    public function transferBetweenWarehouses($id_product, $id_product_attribute, $quantity, $warehouse_from, $warehouse_to, $usable_from = true, $usable_to = true);

    /**
     * For a given product, returns the time left before being out of stock.
     * By default, for the given product, it will use sum(quantities removed in all warehouses)
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $coverage
     * @param int $id_warehouse Optional
     * @return int time
     */
    public function getProductCoverage($id_product, $id_product_attribute, $coverage, $id_warehouse = null);

    /**
     * For a given product, returns the number of products sold for the the given coverage period.
     * By default, for the given product, it will use sum(quantities removed in all warehouses)
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $coverage
     * @param int|null $id_warehouse Optional
     * @return int Number of products sold over the coverage period
     */
    public function getProductOutForCoverage($id_product, $id_product_attribute, $coverage, $id_warehouse = null);
}
