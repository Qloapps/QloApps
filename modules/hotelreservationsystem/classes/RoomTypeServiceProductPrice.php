<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class RoomTypeServiceProductPrice extends ObjectModel
{
    /** @var int id_product */
    public $id_product;

    /** @var float price for specific room type */
    public $price;

    public $id_tax_rules_group;

    /** @var int id_hotel or id_room_type */
    public $id_element;

    /** @var int define element type hotel or room type (refer RoomTypeServiceProduct clas for constants) */
    public $element_type;

    // public $id_room_type_service_product;

    public static $definition = array(
        'table' => 'htl_room_type_service_product_price',
        'primary' => 'id_room_type_service_product_price',
        'fields' => array(
            // 'id_room_type_service_product' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'price' =>          array('type' => self::TYPE_FLOAT),
            'id_tax_rules_group' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_element' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'element_type' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId')
        )
    );

    public static function deleteRoomProductPrices($idProduct, $elementType = 0, $idElement = 0)
    {
        $where = '`id_product`='.(int)$idProduct;

        if ($elementType) {
            $where .= ' AND `element_type`='.(int)$elementType;
        }

        if ($idElement) {
            $where .= ' AND `id_element` = '.(int) $idElement;
        }

        return Db::getInstance()->delete(
            'htl_room_type_service_product_price',
            $where
        );
    }

    public static function getProductRoomTypePriceAndTax($idProduct, $idElement, $elementType)
    {
        $cache_key = 'RoomTypeServiceProductPrice::getProductRoomTypePriceAndTax'.$idProduct.'_'.$idElement.'_'.$elementType;
        if (!Cache::isStored($cache_key)) {
            $objServiceProduct = new Product((int)$idProduct);
            if ($result = Db::getInstance()->getRow('
                SELECT spp.`price`, spp.`id_tax_rules_group`, p.`auto_add_to_cart`, p.`price_addition_type`
                FROM `'._DB_PREFIX_.'product` p
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_service_product` sp
                ON (sp.`id_product` = p.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_service_product_price` spp
                ON (spp.`id_product` = sp.`id_product` AND spp.`id_element` = sp.`id_element` AND spp.`element_type` = sp.`element_type`)
                WHERE p.`id_product`='.(int)$idProduct.
                ' AND sp.`id_element`='.(int)$idElement.
                ' AND sp.`element_type`='.(int)$elementType)
            ) {
                if ($result['auto_add_to_cart'] && $result['price_addition_type'] == Product::PRICE_ADDITION_TYPE_WITH_ROOM) {
                    // if service is auto add to cart and added in room price, we need to find room type tax rule group
                    if ($elementType == RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE) {
                        $result['id_tax_rules_group'] = Product::getIdTaxRulesGroupByIdProduct((int)$idElement);
                    }
                }
            } elseif ($objServiceProduct->auto_add_to_cart
                && $elementType == RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                && $objServiceProduct->price_addition_type == Product::PRICE_ADDITION_TYPE_WITH_ROOM
            ) {
                $result = array();
                $result['auto_add_to_cart'] = 1;
                $result['price_addition_type'] = Product::PRICE_ADDITION_TYPE_WITH_ROOM;
                $result['id_tax_rules_group'] = Product::getIdTaxRulesGroupByIdProduct((int)$idElement);
            }

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    public function getProductRoomTypeLinkPriceInfo($idProduct, $idElement, $elementType)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_service_product_price`
            WHERE `id_product`='.(int)$idProduct.
            ' AND `id_element`='.(int)$idElement.
            ' AND `element_type`='.(int)$elementType
        );
    }

    public static function getPrice(
        $idProduct,
        $idHotel,
        $idProductOption = null,
        $useTax = null,
        $quantity = 1,
        $useReduc = true
    ) {
        $idHotelAddress = Cart::getIdAddressForTaxCalculation($idProduct, $idHotel);
        $price =  Product::getPriceStatic(
            $idProduct,
            $useTax,
            $idProductOption,
            6,
            null,
            false,
            $useReduc,
            $quantity,
            false,
            null,
            null,
            $idHotelAddress
        );

        return $price * (int)$quantity;
    }
}

