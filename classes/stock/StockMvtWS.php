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
 * Webservice entity for stock movements
 * @since 1.5.0
 */
class StockMvtWSCore extends ObjectModelCore
{
    public $id;

    /**
     * @var string The creation date of the movement
     */
    public $date_add;

    /**
     * @var int The employee id, responsible of the movement
     */
    public $id_employee;

    /**
     * @var string The first name of the employee responsible of the movement
     */
    public $employee_firstname;

    /**
     * @var string The last name of the employee responsible of the movement
     */
    public $employee_lastname;

    /**
     * @var int The stock id on wtich the movement is applied
     */
    public $id_stock;

    /**
     * @var int the quantity of product with is moved
     */
    public $physical_quantity;

    /**
     * @var int id of the movement reason assoiated to the movement
     */
    public $id_stock_mvt_reason;

    /**
     * @var int Used when the movement is due to a customer order
     */
    public $id_order = null;

    /**
     * @var int detrmine if the movement is a positive or negative operation
     */
    public $sign;

    /**
     * @var int Used when the movement is due to a supplier order
     */
    public $id_supply_order = null;

    /**
     * @var float Last value of the weighted-average method
     */
    public $last_wa = null;

    /**
     * @var float Current value of the weighted-average method
     */
    public $current_wa = null;

    /**
     * @var float The unit price without tax of the product associated to the movement
     */
    public $price_te;

    /**
     * @var int Refers to an other id_stock_mvt : used for LIFO/FIFO implementation in StockManager
     */
    public $referer;

    /**
     * @var int id_product (@see Stock::id_product)
     */
    public $id_product;

    /**
     * @var int id_product_attribute (@see Stock::id_product_attribute)
     */
    public $id_product_attribute;

    /**
     * @var int id_warehouse (@see Stock::id_warehouse)
     */
    public $id_warehouse;

    /**
     * @var int id_currency (@see Warehouse::id_currency)
     */
    public $id_currency;

    /**
     * @var string management_type (@see Warehouse::management_type)
     */
    public $management_type;

    /*
     * @var string : Name of the product (@see Product::getProductName)
     */
    public $product_name;

    /**
     * @var string EAN13 of the product (@see Stock::product_ean13)
     */
    public $ean13;

    /**
     * @var string UPC of the product (@see Stock::product_upc)
     */
    public $upc;

    /**
     * @var string Reference of the product (@see Stock::product_reference)
     */
    public $reference;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'stock_mvt',
        'primary' => 'id_stock_mvt',
        'fields' => array(
            'id_employee' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'employee_firstname' =>    array('type' => self::TYPE_STRING, 'validate' => 'isName'),
            'employee_lastname' =>        array('type' => self::TYPE_STRING, 'validate' => 'isName'),
            'id_stock' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'physical_quantity' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_stock_mvt_reason' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_supply_order' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'sign' =>                    array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'last_wa' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'current_wa' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'price_te' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'referer' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
        ),
    );

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = array(
        'fields' => array(
            'id_product' => array('xlink_resource' => 'products'),
            'id_product_attribute' => array('xlink_resource' => 'combinations'),
            'id_warehouse' => array('xlink_resource' => 'warehouses'),
            'id_currency' => array('xlink_resource' => 'currencies'),
            'management_type' => array(),
            'id_employee' => array('xlink_resource' => 'employees'),
            'id_stock' => array('xlink_resource' => 'stocks'),
            'id_stock_mvt_reason' => array('xlink_resource' => 'stock_movement_reasons'),
            'id_order' => array('xlink_resource' => 'orders'),
            'id_supply_order' => array('xlink_resource' => 'supply_orders'),
            'product_name' => array('getter' => 'getWSProductName', 'i18n' => true),
            'ean13' => array(),
            'upc' => array(),
            'reference' => array(),
        ),
        'hidden_fields' => array(
            'referer',
            'employee_firstname',
            'employee_lastname',
        ),
    );

    /**
     * Associations tables for attributes that require different tables than stated in ObjectModel::definition
     * @var Array
     */
    protected $tables_assoc = array(
        'id_product' => array('table' => 's'),
        'id_product_attribute' => array('table' => 's'),
        'id_warehouse' => array('table' => 's'),
        'id_currency' => array('table' => 's'),
        'management_type' => array('table' => 'w'),
        'ean13' => array('table' => 's'),
        'upc' => array('table' => 's'),
        'reference' => array('table' => 's'),
    );

    /**
     * @see ObjectModel
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        // calls parent
        parent::__construct($id, $id_lang, $id_shop);

        if ((int)$this->id != 0) {
            $res = $this->getWebserviceObjectList(null, (' AND '.$this->def['primary'].' = '.(int)$this->id), null, null, true);
            if (isset($res[0])) {
                foreach ($this->tables_assoc as $key => $param) {
                    $this->{$key} = $res[0][$key];
                }
            }
        }
    }

    /**
     * @see ObjectModel::getWebserviceObjectList()
     * Added $full for this specific object
     */
    public function getWebserviceObjectList($join, $filter, $sort, $limit, $full = false)
    {
        $query = 'SELECT DISTINCT main.'.$this->def['primary'].' ';

        if ($full) {
            $query .= ', s.id_product, s.id_product_attribute, s.id_warehouse, w.id_currency, w.management_type,
					   s.ean13, s.upc, s.reference ';
        }

        $old_filter = $filter;
        if ($filter) {
            foreach ($this->tables_assoc as $key => $value) {
                $filter = str_replace('main.`'.$key.'`', $value['table'].'.`'.$key.'`', $filter);
            }
        }

        $query .= 'FROM '._DB_PREFIX_.$this->def['table'].' as main ';

        if ($filter !== $old_filter || $full) {
            $query .= 'LEFT JOIN '._DB_PREFIX_.'stock s ON (s.id_stock = main.id_stock) ';
            $query .= 'LEFT JOIN '._DB_PREFIX_.'warehouse w ON (w.id_warehouse = s.id_warehouse) ';
            $query .= 'LEFT JOIN '._DB_PREFIX_.'currency c ON (c.id_currency = w.id_currency) ';
        }

        if ($join) {
            $query .= $join;
        }

        $query .= 'WHERE 1 ';

        if ($filter) {
            $query .= $filter.' ';
        }

        if ($sort) {
            $query .= $sort.' ';
        }

        if ($limit) {
            $query .= $limit.' ';
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * Webservice : getter for the product name
     */
    public function getWSProductName()
    {
        $res = array();
        foreach (Language::getIDs(true) as $id_lang) {
            $res[$id_lang] = Product::getProductName($this->id_product, $this->id_product_attribute, $id_lang);
        }

        return $res;
    }
}
