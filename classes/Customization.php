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

class CustomizationCore extends ObjectModel
{
    /** @var int */
    public $id_product_attribute;
    /** @var int */
    public $id_address_delivery;
    /** @var int */
    public $id_cart;
    /** @var int */
    public $id_product;
    /** @var int */
    public $quantity;
    /** @var int */
    public $quantity_refunded;
    /** @var int */
    public $quantity_returned;
    /** @var bool */
    public $in_cart;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'customization',
        'primary' => 'id_customization',
        'fields' => array(
            /* Classic fields */
            'id_product_attribute' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_address_delivery' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'quantity' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'quantity_refunded' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'quantity_returned' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'in_cart' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
        ),
    );
    protected $webserviceParameters = array(
        'fields' => array(
            'id_address_delivery' => array(
                'xlink_resource' => array(
                    'resourceName' => 'addresses'
                )
            ),
            'id_cart' => array(
                'xlink_resource' => array(
                    'resourceName' => 'carts'
                )
            ),
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'products'
                )
            ),
        ),
        'associations' => array(
            'customized_data_text_fields' => array('resource' => 'customized_data_text_field', 'virtual_entity' => true, 'fields' => array(
                'id_customization_field' => array('required' => true, 'xlink_resource' => 'product_customization_fields'),
                'value' => array(),
                )
            ),
            'customized_data_images' => array('resource' => 'customized_data_image', 'virtual_entity' => true, 'setter' => false, 'fields' => array(
                'id_customization_field' => array('xlink_resource' => 'product_customization_fields'),
                'value' => array(),
                )
            ),
        ),
    );

    public static function getReturnedCustomizations($id_order)
    {
        if (($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT ore.`id_order_return`, ord.`id_order_detail`, ord.`id_customization`, ord.`product_quantity`
			FROM `'._DB_PREFIX_.'order_return` ore
			INNER JOIN `'._DB_PREFIX_.'order_return_detail` ord ON (ord.`id_order_return` = ore.`id_order_return`)
			WHERE ore.`id_order` = '.(int)($id_order).' AND ord.`id_customization` != 0')) === false) {
            return false;
        }
        $customizations = array();
        foreach ($result as $row) {
            $customizations[(int)($row['id_customization'])] = $row;
        }
        return $customizations;
    }

    public static function getOrderedCustomizations($id_cart)
    {
        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_customization`, `quantity` FROM `'._DB_PREFIX_.'customization` WHERE `id_cart` = '.(int)($id_cart))) {
            return false;
        }
        $customizations = array();
        foreach ($result as $row) {
            $customizations[(int)($row['id_customization'])] = $row;
        }
        return $customizations;
    }

    public static function countCustomizationQuantityByProduct($customizations)
    {
        $total = array();
        foreach ($customizations as $customization) {
            $total[(int)$customization['id_order_detail']] = !isset($total[(int)$customization['id_order_detail']]) ? (int)$customization['quantity'] : $total[(int)$customization['id_order_detail']] + (int)$customization['quantity'];
        }
        return $total;
    }

    public static function getLabel($id_customization, $id_lang, $id_shop = null)
    {
        if (!(int)$id_customization || !(int)$id_lang) {
            return false;
        }
        if (Shop::isFeatureActive() && !(int)$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `name`
		FROM `'._DB_PREFIX_.'customization_field_lang`
		WHERE `id_customization_field` = '.(int)$id_customization.((int)$id_shop ? ' AND cfl.`id_shop` = '.(int)$id_shop : '').'
		AND `id_lang` = '.(int)$id_lang
        );

        return $result;
    }

    public static function retrieveQuantitiesFromIds($ids_customizations)
    {
        $quantities = array();

        $in_values  = '';
        foreach ($ids_customizations as $key => $id_customization) {
            if ($key > 0) {
                $in_values .= ',';
            }
            $in_values .= (int)($id_customization);
        }

        if (!empty($in_values)) {
            $results = Db::getInstance()->executeS(
                            'SELECT `id_customization`, `id_product`, `quantity`, `quantity_refunded`, `quantity_returned`
							 FROM `'._DB_PREFIX_.'customization`
							 WHERE `id_customization` IN ('.$in_values.')');

            foreach ($results as $row) {
                $quantities[$row['id_customization']] = $row;
            }
        }

        return $quantities;
    }

    public static function countQuantityByCart($id_cart)
    {
        $quantity = array();

        $results = Db::getInstance()->executeS('
			SELECT `id_product`, `id_product_attribute`, SUM(`quantity`) AS quantity
			FROM `'._DB_PREFIX_.'customization`
			WHERE `id_cart` = '.(int)$id_cart.'
			GROUP BY `id_cart`, `id_product`, `id_product_attribute`
		');

        foreach ($results as $row) {
            $quantity[$row['id_product']][$row['id_product_attribute']] = $row['quantity'];
        }

        return $quantity;
    }

    /**
     * This method is allow to know if a feature is used or active
     * @since 1.5.0.1
     * @return bool
     */
    public static function isFeatureActive()
    {
        return Configuration::get('PS_CUSTOMIZATION_FEATURE_ACTIVE');
    }

    /**
     * This method is allow to know if a Customization entity is currently used
     * @since 1.5.0.1
     * @param $table
     * @param $has_active_column
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $has_active_column = false)
    {
        return (bool)Db::getInstance()->getValue('
			SELECT `id_customization_field`
			FROM `'._DB_PREFIX_.'customization_field`
		');
    }

    public function getWsCustomizedDataTextFields()
    {
        if (!$results = Db::getInstance()->executeS('
			SELECT id_customization_field, value
			FROM `'._DB_PREFIX_.'customization_field` cf
			LEFT JOIN `'._DB_PREFIX_.'customized_data` cd ON (cf.id_customization_field = cd.index)
			WHERE `id_product` = '.(int)$this->id_product.'
			AND cf.type = 1')) {
            return array();
        }
        return $results;
    }

    public function getWsCustomizedDataImages()
    {
        if (!$results = Db::getInstance()->executeS('
			SELECT id_customization_field, value
			FROM `'._DB_PREFIX_.'customization_field` cf
			LEFT JOIN `'._DB_PREFIX_.'customized_data` cd ON (cf.id_customization_field = cd.index)
			WHERE `id_product` = '.(int)$this->id_product.'
			AND cf.type = 0')) {
            return array();
        }
        return $results;
    }

    public function setWsCustomizedDataTextFields($values)
    {
        $cart = new Cart($this->id_cart);
        if (!Validate::isLoadedObject($cart)) {
            WebserviceRequest::getInstance()->setError(500, Tools::displayError('Could not load cart id='.$this->id_cart), 137);
            return false;
        }
        Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'customized_data`
		WHERE id_customization = '.(int)$this->id.'
		AND type = 1');
        foreach ($values as $value) {
            $query = 'INSERT INTO `'._DB_PREFIX_.'customized_data` (`id_customization`, `type`, `index`, `value`)
				VALUES ('.(int)$this->id.', 1, '.(int)$value['id_customization_field'].', \''.pSQL($value['value']).'\')';

            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }
}
