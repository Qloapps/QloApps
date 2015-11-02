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
 * Represents the products kept in warehouses
 *
 * @since 1.5.0
 */
class StockCore extends ObjectModel
{
    /** @var int identifier of the warehouse */
    public $id_warehouse;

    /** @var int identifier of the product */
    public $id_product;

    /** @var int identifier of the product attribute if necessary */
    public $id_product_attribute;

    /** @var string Product reference */
    public $reference;

    /** @var int Product EAN13 */
    public $ean13;

    /** @var string UPC */
    public $upc;

    /** @var int the physical quantity in stock for the current product in the current warehouse */
    public $physical_quantity;

    /** @var int the usable quantity (for sale) of the current physical quantity */
    public $usable_quantity;

    /** @var int the unit price without tax forthe current product */
    public $price_te;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'stock',
        'primary' => 'id_stock',
        'fields' => array(
            'id_warehouse' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'reference' =>                array('type' => self::TYPE_STRING, 'validate' => 'isReference'),
            'ean13' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isEan13'),
            'upc' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isUpc'),
            'physical_quantity' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'usable_quantity' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'price_te' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
        ),
    );

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = array(
        'fields' => array(
            'id_warehouse' => array('xlink_resource' => 'warehouses'),
            'id_product' => array('xlink_resource' => 'products'),
            'id_product_attribute' => array('xlink_resource' => 'combinations'),
            'real_quantity' => array('getter' => 'getWsRealQuantity', 'setter' => false),
        ),
        'hidden_fields' => array(
        ),
    );

    /**
     * @see ObjectModel::update()
     */
    public function update($null_values = false)
    {
        $this->getProductInformations();

        return parent::update($null_values);
    }

    /**
     * @see ObjectModel::add()
     */
    public function add($autodate = true, $null_values = false)
    {
        $this->getProductInformations();

        return parent::add($autodate, $null_values);
    }

    /**
     * Gets reference, ean13 and upc of the current product
     * Stores it in stock for stock_mvt integrity and history purposes
     */
    protected function getProductInformations()
    {
        // if combinations
        if ((int)$this->id_product_attribute > 0) {
            $query = new DbQuery();
            $query->select('reference, ean13, upc');
            $query->from('product_attribute');
            $query->where('id_product = '.(int)$this->id_product);
            $query->where('id_product_attribute = '.(int)$this->id_product_attribute);
            $rows = Db::getInstance()->executeS($query);

            if (!is_array($rows)) {
                return;
            }

            foreach ($rows as $row) {
                $this->reference = $row['reference'];
                $this->ean13 = $row['ean13'];
                $this->upc = $row['upc'];
            }
        } else {
            // else, simple product

            $product = new Product((int)$this->id_product);
            if (Validate::isLoadedObject($product)) {
                $this->reference = $product->reference;
                $this->ean13 = $product->ean13;
                $this->upc = $product->upc;
            }
        }
    }

    /**
     * Webservice : used to get the real quantity of a product
     */
    public function getWsRealQuantity()
    {
        $manager = StockManagerFactory::getManager();
        $quantity = $manager->getProductRealQuantities($this->id_product, $this->id_product_attribute, $this->id_warehouse, true);
        return $quantity;
    }

    public static function deleteStockByIds($id_product = null, $id_product_attribute = null)
    {
        if (!$id_product || !$id_product_attribute) {
            return false;
        }

        return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'stock WHERE `id_product` = '.(int)$id_product.' AND `id_product_attribute` = '.(int)$id_product_attribute);
    }

    public static function productIsPresentInStock($id_product = 0, $id_product_attribute = 0, $id_warehouse = 0)
    {
        if (!(int)$id_product && !is_int($id_product_attribute) && !(int)$id_warehouse) {
            return false;
        }

        $result = Db::getInstance()->executeS('SELECT `id_stock` FROM '._DB_PREFIX_.'stock
			WHERE `id_warehouse` = '.(int)$id_warehouse.' AND `id_product` = '.(int)$id_product.((int)$id_product_attribute ? ' AND `id_product_attribute` = '.$id_product_attribute : ''));

        return (is_array($result) && !empty($result) ? true : false);
    }
}
