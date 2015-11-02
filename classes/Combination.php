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

class CombinationCore extends ObjectModel
{
    public $id_product;

    public $reference;

    public $supplier_reference;

    public $location;

    public $ean13;

    public $upc;

    public $wholesale_price;

    public $price;

    public $unit_price_impact;

    public $ecotax;

    public $minimal_quantity = 1;

    public $quantity;

    public $weight;

    public $default_on;

    public $available_date = '0000-00-00';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_attribute',
        'primary' => 'id_product_attribute',
        'fields' => array(
            'id_product' =>        array('type' => self::TYPE_INT, 'shop' => 'both', 'validate' => 'isUnsignedId', 'required' => true),
            'location' =>            array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64),
            'ean13' =>                array('type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13),
            'upc' =>                array('type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12),
            'quantity' =>            array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 10),
            'reference' =>            array('type' => self::TYPE_STRING, 'size' => 32),
            'supplier_reference' => array('type' => self::TYPE_STRING, 'size' => 32),

            /* Shop fields */
            'wholesale_price' =>    array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'size' => 27),
            'price' =>                array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20),
            'ecotax' =>            array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'size' => 20),
            'weight' =>            array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isFloat'),
            'unit_price_impact' =>    array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20),
            'minimal_quantity' =>    array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId', 'required' => true),
            'default_on' =>        array('type' => self::TYPE_BOOL, 'allow_null' => true, 'shop' => true, 'validate' => 'isBool'),
            'available_date' =>    array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'),
        ),
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'combination',
        'objectsNodeName' => 'combinations',
        'fields' => array(
            'id_product' => array('required' => true, 'xlink_resource'=> 'products'),
        ),
        'associations' => array(
            'product_option_values' => array('resource' => 'product_option_value'),
            'images' => array('resource' => 'image', 'api' => 'images/products'),
        ),
    );

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        // Removes the product from StockAvailable, for the current shop
        StockAvailable::removeProductFromStockAvailable((int)$this->id_product, (int)$this->id);

        if ($specific_prices = SpecificPrice::getByProductId((int)$this->id_product, (int)$this->id)) {
            foreach ($specific_prices as $specific_price) {
                $price = new SpecificPrice((int)$specific_price['id_specific_price']);
                $price->delete();
            }
        }

        if (!$this->hasMultishopEntries() && !$this->deleteAssociations()) {
            return false;
        }

        $this->deleteFromSupplier($this->id_product);
        Product::updateDefaultAttribute($this->id_product);

        return true;
    }

    public function deleteFromSupplier($id_product)
    {
        return Db::getInstance()->delete('product_supplier', 'id_product = '.(int)$id_product
            .' AND id_product_attribute = '.(int)$this->id);
    }

    public function add($autodate = true, $null_values = false)
    {
        if ($this->default_on) {
            $this->default_on = 1;
        } else {
            $this->default_on = null;
        }

        if (!parent::add($autodate, $null_values)) {
            return false;
        }

        $product = new Product((int)$this->id_product);
        if ($product->getType() == Product::PTYPE_VIRTUAL) {
            StockAvailable::setProductOutOfStock((int)$this->id_product, 1, null, (int)$this->id);
        } else {
            StockAvailable::setProductOutOfStock((int)$this->id_product, StockAvailable::outOfStock((int)$this->id_product), null, $this->id);
        }

        SpecificPriceRule::applyAllRules(array((int)$this->id_product));

        Product::updateDefaultAttribute($this->id_product);

        return true;
    }

    public function update($null_values = false)
    {
        if ($this->default_on) {
            $this->default_on = 1;
        } else {
            $this->default_on = null;
        }

        $return = parent::update($null_values);
        Product::updateDefaultAttribute($this->id_product);

        return $return;
    }

    public function deleteAssociations()
    {
        $result = Db::getInstance()->delete('product_attribute_combination', '`id_product_attribute` = '.(int)$this->id);
        $result &= Db::getInstance()->delete('cart_product', '`id_product_attribute` = '.(int)$this->id);
        $result &= Db::getInstance()->delete('product_attribute_image', '`id_product_attribute` = '.(int)$this->id);

        return $result;
    }

    public function setAttributes($ids_attribute)
    {
        $result = $this->deleteAssociations();
        if ($result && !empty($ids_attribute)) {
            $sql_values = array();
            foreach ($ids_attribute as $value) {
                $sql_values[] = '('.(int)$value.', '.(int)$this->id.')';
            }

            $result = Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'product_attribute_combination` (`id_attribute`, `id_product_attribute`)
				VALUES '.implode(',', $sql_values)
            );
        }
        return $result;
    }

    public function setWsProductOptionValues($values)
    {
        $ids_attributes = array();
        foreach ($values as $value) {
            $ids_attributes[] = $value['id'];
        }
        return $this->setAttributes($ids_attributes);
    }

    public function getWsProductOptionValues()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.id_attribute AS id
			FROM `'._DB_PREFIX_.'product_attribute_combination` a
			'.Shop::addSqlAssociation('attribute', 'a').'
			WHERE a.id_product_attribute = '.(int)$this->id);

        return $result;
    }

    public function getWsImages()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.`id_image` as id
			FROM `'._DB_PREFIX_.'product_attribute_image` a
			'.Shop::addSqlAssociation('product_attribute', 'a').'
			WHERE a.`id_product_attribute` = '.(int)$this->id.'
		');
    }

    public function setImages($ids_image)
    {
        if (Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'product_attribute_image`
			WHERE `id_product_attribute` = '.(int)$this->id) === false) {
            return false;
        }

        if (is_array($ids_image) && count($ids_image)) {
            $sql_values = array();

            foreach ($ids_image as $value) {
                $sql_values[] = '('.(int)$this->id.', '.(int)$value.')';
            }

            if (is_array($sql_values) && count($sql_values)) {
                Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'product_attribute_image` (`id_product_attribute`, `id_image`)
					VALUES '.implode(',', $sql_values)
                );
            }
        }
        return true;
    }

    public function setWsImages($values)
    {
        $ids_images = array();
        foreach ($values as $value) {
            $ids_images[] = (int)$value['id'];
        }
        return $this->setImages($ids_images);
    }

    public function getAttributesName($id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT al.*
			FROM '._DB_PREFIX_.'product_attribute_combination pac
			JOIN '._DB_PREFIX_.'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang='.(int)$id_lang.')
			WHERE pac.id_product_attribute='.(int)$this->id);
    }

    /**
     * This method is allow to know if a feature is active
     * @since 1.5.0.1
     * @return bool
     */
    public static function isFeatureActive()
    {
        static $feature_active = null;

        if ($feature_active === null) {
            $feature_active = Configuration::get('PS_COMBINATION_FEATURE_ACTIVE');
        }
        return $feature_active;
    }

    /**
     * This method is allow to know if a Combination entity is currently used
     * @since 1.5.0.1
     * @param $table
     * @param $has_active_column
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $has_active_column = false)
    {
        return parent::isCurrentlyUsed('product_attribute');
    }

    /**
     * For a given product_attribute reference, returns the corresponding id
     *
     * @param int $id_product
     * @param string $reference
     * @return int id
     */
    public static function getIdByReference($id_product, $reference)
    {
        if (empty($reference)) {
            return 0;
        }

        $query = new DbQuery();
        $query->select('pa.id_product_attribute');
        $query->from('product_attribute', 'pa');
        $query->where('pa.reference LIKE \'%'.pSQL($reference).'%\'');
        $query->where('pa.id_product = '.(int)$id_product);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function getColorsAttributes()
    {
        return Db::getInstance()->executeS('
			SELECT a.id_attribute
			FROM '._DB_PREFIX_.'product_attribute_combination pac
			JOIN '._DB_PREFIX_.'attribute a ON (pac.id_attribute = a.id_attribute)
			JOIN '._DB_PREFIX_.'attribute_group ag ON (ag.id_attribute_group = a.id_attribute_group)
			WHERE pac.id_product_attribute='.(int)$this->id.' AND ag.is_color_group = 1
		');
    }

    /**
     * Retrive the price of combination
     *
     * @since 1.5.0
     * @param int $id_product_attribute
     * @return float mixed
     */
    public static function getPrice($id_product_attribute)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT product_attribute_shop.`price`
			FROM `'._DB_PREFIX_.'product_attribute` pa
			'.Shop::addSqlAssociation('product_attribute', 'pa').'
			WHERE pa.`id_product_attribute` = '.(int)$id_product_attribute
        );
    }
}
