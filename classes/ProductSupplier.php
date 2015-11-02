<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class ProductSupplierCore extends ObjectModel
{
    /**
     * @var int product ID
     * */
    public $id_product;

    /**
     * @var int product attribute ID
     * */
    public $id_product_attribute;

    /**
     * @var int the supplier ID
     * */
    public $id_supplier;

    /**
     * @var string The supplier reference of the product
     * */
    public $product_supplier_reference;

    /**
     * @var int the currency ID for unit price tax excluded
     * */
    public $id_currency;

    /**
     * @var string The unit price tax excluded of the product
     * */
    public $product_supplier_price_te;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_supplier',
        'primary' => 'id_product_supplier',
        'fields' => array(
            'product_supplier_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 32),
            'id_product' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_supplier' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'product_supplier_price_te' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'id_currency' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = array(
        'objectsNodeName' => 'product_suppliers',
        'objectNodeName' => 'product_supplier',
        'fields' => array(
            'id_product' => array('xlink_resource' => 'products'),
            'id_product_attribute' => array('xlink_resource' => 'combinations'),
            'id_supplier' => array('xlink_resource' => 'suppliers'),
            'id_currency' => array('xlink_resource' => 'currencies'),
        ),
    );

    /**
     * @see ObjectModel::delete()
     */
    public function delete()
    {
        $res = parent::delete();

        if ($res && $this->id_product_attribute == 0) {
            $items = ProductSupplier::getSupplierCollection($this->id_product, false);
            foreach ($items as $item) {
                /** @var ProductSupplier $item */
                if ($item->id_product_attribute > 0) {
                    $item->delete();
                }
            }
        }

        return $res;
    }

    /**
     * For a given product and supplier, gets the product supplier reference
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $id_supplier
     * @return string
     */
    public static function getProductSupplierReference($id_product, $id_product_attribute, $id_supplier)
    {
        // build query
        $query = new DbQuery();
        $query->select('ps.product_supplier_reference');
        $query->from('product_supplier', 'ps');
        $query->where('ps.id_product = '.(int)$id_product.'
			AND ps.id_product_attribute = '.(int)$id_product_attribute.'
			AND ps.id_supplier = '.(int)$id_supplier
        );

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given product and supplier, gets the product supplier unit price
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $id_supplier
     * @param bool $with_currency Optional
     * @return array
     */
    public static function getProductSupplierPrice($id_product, $id_product_attribute, $id_supplier, $with_currency = false)
    {
        // build query
        $query = new DbQuery();
        $query->select('ps.product_supplier_price_te');
        if ($with_currency) {
            $query->select('ps.id_currency');
        }
        $query->from('product_supplier', 'ps');
        $query->where('ps.id_product = '.(int)$id_product.'
			AND ps.id_product_attribute = '.(int)$id_product_attribute.'
			AND ps.id_supplier = '.(int)$id_supplier
        );

        if (!$with_currency) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        }

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if (isset($res[0])) {
            return $res[0];
        }

        return $res;
    }

    /**
     * For a given product and supplier, gets corresponding ProductSupplier ID
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $id_supplier
     * @return array
     */
    public static function getIdByProductAndSupplier($id_product, $id_product_attribute, $id_supplier)
    {
        // build query
        $query = new DbQuery();
        $query->select('ps.id_product_supplier');
        $query->from('product_supplier', 'ps');
        $query->where('ps.id_product = '.(int)$id_product.'
			AND ps.id_product_attribute = '.(int)$id_product_attribute.'
			AND ps.id_supplier = '.(int)$id_supplier
        );

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given product, retrieves its suppliers
     *
     * @param int $id_product
     * @param int $group_by_supplier
     * @return PrestaShopCollection Collection of ProductSupplier
     */
    public static function getSupplierCollection($id_product, $group_by_supplier = true)
    {
        $suppliers = new PrestaShopCollection('ProductSupplier');
        $suppliers->where('id_product', '=', (int)$id_product);

        if ($group_by_supplier) {
            $suppliers->groupBy('id_supplier');
        }

        return $suppliers;
    }

    /**
     * For a given Supplier, Product, returns the purchased price
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param bool $converted_price Optional
     * @return Array keys: price_te, id_currency
     */
    public static function getProductPrice($id_supplier, $id_product, $id_product_attribute = 0, $converted_price = false)
    {
        if (is_null($id_supplier) || is_null($id_product)) {
            return;
        }

        $query = new DbQuery();
        $query->select('product_supplier_price_te as price_te, id_currency');
        $query->from('product_supplier');
        $query->where('id_product = '.(int)$id_product.' AND id_product_attribute = '.(int)$id_product_attribute);
        $query->where('id_supplier = '.(int)$id_supplier);

        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        if ($converted_price) {
            return Tools::convertPrice($row['price_te'], $row['id_currency']);
        }

        return $row['price_te'];
    }
}
