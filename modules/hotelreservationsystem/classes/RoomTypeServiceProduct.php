<?php
/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class RoomTypeServiceProduct extends ObjectModel
{
    /** @var int id_product */
    public $id_product;

    public $position;

    /** @var int id_hotel or id_room_type */
    public $id_element;

    /** @var int define element type hotel or room type (refer RoomTypeServiceProduct class for constants) */
    public $element_type;

    const WK_ELEMENT_TYPE_HOTEL = 1;
    const WK_ELEMENT_TYPE_ROOM_TYPE = 2;

    const WK_NUM_RESULTS = 4;

    public static $definition = array(
        'table' => 'htl_room_type_service_product',
        'primary' => 'id_room_type_service_product',
        'fields' => array(
            'id_product' =>     array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'position' =>       array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_element' =>     array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'element_type' =>   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId')
        )
    );

    public static function deleteRoomProductLink($idProduct, $elementType = 0)
    {
        $where = '`id_product`='.(int)$idProduct;

        if ($elementType) {
            $where .= ' AND `element_type`='.(int)$elementType;
        }

        return Db::getInstance()->delete(
            'htl_room_type_service_product',
            $where
        );
    }

    public function addRoomProductLink($idProduct, $values, $elementType)
    {
        if(!is_array($values)) {
            $values = (array)$values;
        }

        $rowData = array();
        foreach($values as $value) {
            $rowData[] = array(
                'id_product' => $idProduct,
                'position' => self::getHigherPosition(),
                'id_element' => $value,
                'element_type' => $elementType
            );
        }

        return Db::getInstance()->insert($this->def['table'], $rowData);
    }

    public function getAssociatedHotelsAndRoomType($idProduct, $formated = true) {
        $rows = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_service_product` AS rsp
            WHERE `id_product` = '.(int)$idProduct
        );

        if ($formated) {
            $response = array('hotels' => array(), 'room_types' => array());
            foreach($rows as $row) {
                $key = $row['element_type'] == self::WK_ELEMENT_TYPE_HOTEL ? 'hotels' : 'room_types';
                $response[$key][] = $row['id_element'];
            }
            return $response;
        }
        return $rows;
    }

    public function getProductsForRoomType($idProductRoomType)
    {
        $sql = 'SELECT `id_room_type_service_product`, `id_product`, `position` FROM `'._DB_PREFIX_.'htl_room_type_service_product`
            WHERE `element_type` = '.self::WK_ELEMENT_TYPE_ROOM_TYPE.' AND `id_element` = '.(int)$idProductRoomType.'
            ORDER BY `position` ASC';

        return Db::getInstance()->executeS($sql);
    }

    public function isRoomTypeLinkedWithProduct($idProductRoomType, $idServiceProduct)
    {
        $sql = 'SELECT `id_room_type_service_product` FROM  `'._DB_PREFIX_.'htl_room_type_service_product`
            WHERE `id_product` = '.(int)$idServiceProduct.' AND `id_element` = '.(int)$idProductRoomType.'
            AND `element_type` = '.self::WK_ELEMENT_TYPE_ROOM_TYPE;

        return Db::getInstance()->getValue($sql);
    }

    public static function getAutoAddServices($idProduct, $dateFrom = null, $dateTo = null, $priceAdditionType = null, $useTax = null)
    {
        if (Product::isBookingProduct($idProduct)) {
            $sql = 'SELECT p.`id_product` FROM  `'._DB_PREFIX_.'htl_room_type_service_product` rsp
            INNER JOIN `'._DB_PREFIX_.'product` p ON (rsp.`id_product` = p.`id_product` AND p.`auto_add_to_cart` = 1)
            WHERE p.`active` = 1 AND `id_element` = '.(int)$idProduct.' AND `element_type` = '.self::WK_ELEMENT_TYPE_ROOM_TYPE;
            if (!is_null($priceAdditionType)) {
                $sql .= ' AND p.`price_addition_type` = '.$priceAdditionType;
            }
            if ($services = Db::getInstance()->executeS($sql)) {
                $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
                foreach($services as &$service) {
                    $service['price'] = $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$service['id_product'],
                        (int)$idProduct,
                        1,
                        $dateFrom,
                        $dateTo,
                        $useTax
                    );
                }
                return $services;
            }
        }

        return false;
    }

    public function getServiceProductsData($idProductRoomType, $p = 1, $n = 0, $front = false, $available_for_order = 2, $auto_add_to_cart = 0, $subCategory = false, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $objProduct = new Product($idProductRoomType);
        if ($serviceProducts = $objProduct->getProductServiceProducts(
            $idLang,
            $p,
            $n,
            $front,
            $available_for_order,
            $auto_add_to_cart,
            false,
            true,
            $subCategory
        )) {
            $objHotelRoomType = new HotelRoomType();
            $serviceProducts = Product::getProductsProperties($idLang, $serviceProducts);
            $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
            foreach($serviceProducts as &$serviceProduct) {
                $serviceProduct['price_tax_exc'] = $objRoomTypeServiceProductPrice->getServicePrice(
                    (int)$serviceProduct['id_product'],
                    (int)$idProductRoomType,
                    1,
                    null,
                    null,
                    false
                );

                $serviceProduct['price_tax_incl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                    (int)$serviceProduct['id_product'],
                    (int)$idProductRoomType,
                    1,
                    null,
                    null,
                    true
                );
                $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;
                $serviceProduct['price_without_reduction'] = $objRoomTypeServiceProductPrice->getServicePrice(
                    (int)$serviceProduct['id_product'],
                    (int)$idProductRoomType,
                    1,
                    null,
                    null,
                    $useTax
                );
                $serviceProduct['images'] = Image::getImages((int)Context::getContext()->language->id, $serviceProduct['id_product']);
            }
        }

        return $serviceProducts;
    }

    public function getServiceProductsGroupByCategory($idProduct, $p = 1, $n = 0, $front = false, $available_for_order = 2, $auto_add_to_cart = 0, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $objProduct = new Product($idProduct);
        if ($serviceProductsCategories = $objProduct->getAvailableServiceProductsCategories($idLang, 1)) {
            foreach ($serviceProductsCategories as $key => $category) {
                if ($products = $this->getServiceProductsData($idProduct, $p, $n, $front, $available_for_order, $auto_add_to_cart, $category['id_category'], $idLang)) {
                    $serviceProductsCategories[$key]['products'] = $products;
                } else {
                    unset($serviceProductsCategories[$key]);
                }
            }
        }
        return $serviceProductsCategories;
    }

    public static function getHigherPosition()
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_room_type_service_product`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function cleanPositions($idProductRoomType)
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_room_type_service_product` SET `position` = @i:=@i+1
            WHERE `element_type` = '.self::WK_ELEMENT_TYPE_ROOM_TYPE.' AND `id_element` = '.(int)$idProductRoomType.'
            ORDER BY `position` ASC';

        return Db::getInstance()->execute($sql);
    }

    /**
     * This function will change position on drag and drop
     *
     * @param int $idSlider
     * @param int $idImage
     * @param int $toRowIndex
     * @param int $idPosition
     * @return boolean
     */
    public static function changePositions($idProduct, $idElement, $toRowIndex, $idPosition)
    {
        if ($toRowIndex >= $idPosition) {
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'htl_room_type_service_product` SET `position` = position -1
            WHERE  `id_product` != '.(int) $idProduct.' AND `id_element` ='.(int) $idElement .' AND `element_type` = '.self::WK_ELEMENT_TYPE_ROOM_TYPE.'
            AND `position`  <= '.(int) ($toRowIndex). ' AND `position` >= ' .(int) $idPosition);

            return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'htl_room_type_service_product` SET `position` = '.(int) ($toRowIndex).'
            WHERE  `id_product` = '.(int) $idProduct.' AND `id_element` ='.(int) $idElement .' AND `element_type` = '.self::WK_ELEMENT_TYPE_ROOM_TYPE);
        } elseif ($toRowIndex < $idPosition) {
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'htl_room_type_service_product` SET `position` = position +1
            WHERE  `id_product` != '.(int) $idProduct.' AND `id_element` ='.(int) $idElement .' AND `element_type` = '.self::WK_ELEMENT_TYPE_ROOM_TYPE.'
            AND `position`  >= '. (int) $toRowIndex. ' AND `position` <= ' .(int) $idPosition);

            return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'htl_room_type_service_product` SET `position` = '.$toRowIndex.'
            WHERE  `id_product` = '.(int) $idProduct.' AND `id_element` ='.(int) $idElement .' AND `element_type` = '.self::WK_ELEMENT_TYPE_ROOM_TYPE);
        }
    }
}