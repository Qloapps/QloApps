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

    /**
     * This method deletes passed associations and cleans positions of remaining associations
     */
    public static function deleteRoomProductLink($idProduct, $elementType = 0, $idElement = 0)
    {
        $where = '`id_product`='.(int)$idProduct;

        if ($elementType) {
            $where .= ' AND `element_type`='.(int)$elementType;
        }

        if ($idElement) {
            $where .= ' AND `id_element` = '.(int) $idElement;
        }

        // Get the list of elements before deleting associations to clean positions of remaining associations
        $elements = Db::getInstance()->executeS(
            'SELECT rsp.`id_element`, rsp.`element_type`
            FROM `'._DB_PREFIX_.'htl_room_type_service_product` rsp
            WHERE '.$where.'
            GROUP BY rsp.`element_type`, rsp.`id_element`'
        );

        $result = Db::getInstance()->delete(
            'htl_room_type_service_product',
            $where
        );

        // Clean positions of remaining associations after deletion
        $result &= self::cleanPositions($elements);

        return $result;
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
                'position' => self::getHigherPosition($value, $elementType),
                'id_element' => $value,
                'element_type' => $elementType
            );
        }

        return Db::getInstance()->insert($this->def['table'], $rowData);
    }

    public function getAssociatedHotelsAndRoomType($idProduct, $elementType = 0, $idElement = 0, $formated = true) {
        $rows = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_service_product` AS rsp
            WHERE `id_product` = '.(int)$idProduct . ($elementType ? ' AND rsp.`element_type` = ' . (int)$elementType : "" . ($idElement ? ' AND rsp.`id_element` = ' . (int)$idElement : ""))
        );

        if ($formated) {
            $response = array('hotel' => array(), 'room_type' => array());
            foreach($rows as $row) {
                $key = $row['element_type'] == self::WK_ELEMENT_TYPE_HOTEL ? 'hotel' : 'room_type';
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

    public static function getAutoAddServices($idProduct, $dateFrom = null, $dateTo = null, $priceAdditionType = null, $useTax = null, $use_reduc = 1)
    {
        if (Product::isBookingProduct($idProduct)) {
            $context = Context::getContext();
            $front = true;
            if (isset($context->controller->controller_type) && !in_array($context->controller->controller_type, array('front', 'modulefront'))) {
                $front = false;
            }

            $sql = 'SELECT p.`id_product` FROM  `'._DB_PREFIX_.'htl_room_type_service_product` rsp
            INNER JOIN `'._DB_PREFIX_.'product` p ON (rsp.`id_product` = p.`id_product` AND p.`auto_add_to_cart` = 1)
            WHERE p.`active` = 1 AND `id_element` = '.(int)$idProduct.' AND `element_type` = '.self::WK_ELEMENT_TYPE_ROOM_TYPE.
            ($front ? ' AND p.`available_for_order` = 1':'');
            if (!is_null($priceAdditionType)) {
                $sql .= ' AND p.`price_addition_type` = '.$priceAdditionType;
            }
            if ($services = Db::getInstance()->executeS($sql)) {
                foreach($services as &$service) {
                    $service['price'] = Product::getServiceProductPrice(
                        (int)$service['id_product'],
                        0,
                        false,
                        (int)$idProduct,
                        $useTax,
                        1,
                        $dateFrom,
                        $dateTo,
                        false,
                        null,
                        $use_reduc
                    );
                }

                Hook::exec('actionAutoAddServicesModifier', array('services' => &$services, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo));

                return $services;
            }
        }

        return false;
    }

    public function getServiceProductsData($idProductRoomType, $p = 1, $n = 0, $front = false, $available_for_order = 2, $auto_add_to_cart = 0, $subCategory = false, $idLang = false)
    {
        $context = Context::getContext();
        if (!$idLang) {
            $idLang = $context->language->id;
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
            $serviceProducts = Product::getProductsProperties($idLang, $serviceProducts);
            foreach($serviceProducts as &$serviceProduct) {
                $serviceProduct['price_tax_exc'] = Product::getServiceProductPrice(
                    (int)$serviceProduct['id_product'],
                    0,
                    false,
                    (int) $idProductRoomType,
                    false,
                    1,
                    null,
                    null,
                    $context->cart->id
                );

                $serviceProduct['price_tax_incl'] = Product::getServiceProductPrice(
                    (int)$serviceProduct['id_product'],
                    0,
                    false,
                    (int) $idProductRoomType,
                    true,
                    1,
                    null,
                    null,
                    $context->cart->id
                );

                $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;
                $serviceProduct['price_without_reduction'] = Product::getServiceProductPrice(
                    (int)$serviceProduct['id_product'],
                    0,
                    false,
                    (int)$idProductRoomType,
                    $useTax,
                    1,
                    null,
                    null,
                    $context->cart->id,
                    null,
                    false // for price without reduct
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

    public static function getHigherPosition($idElement, $elementType)
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(rsp.`position`)
            FROM `'._DB_PREFIX_.'htl_room_type_service_product` AS rsp
            WHERE rsp.`id_element` = '.(int) $idElement.'
            AND rsp.`element_type` = '.(int) $elementType
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public static function cleanPositions($elements)
    {
        $result = true;
        foreach ($elements as $element) {
            Db::getInstance()->execute('SET @i = -1', false);
            $result &= Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'htl_room_type_service_product` rsp
                SET rsp.`position` = @i:=@i+1
                WHERE rsp.`element_type` = '.(int) $element['element_type'].'
                AND rsp.`id_element` = '.(int) $element['id_element'].'
                ORDER BY rsp.`position` ASC'
            );
        }

        return $result;
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
    public static function updatePosition(
        $idProduct,
        $idElement,
        $newPosition,
        $elementType
    ) {
        if (!$result = Db::getInstance()->executeS(
            'SELECT rsp.`id_product`, rsp.`position`
            FROM `'._DB_PREFIX_.'htl_room_type_service_product` rsp
            WHERE rsp.`id_element` = '.(int) $idElement.'
            AND rsp.`element_type` = '.(int) $elementType.'
            ORDER BY rsp.`position` ASC'
        )) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int) $block['id_product'] == (int) $idProduct) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }

        $way = ($newPosition >= $movedBlock['position']) ? 1 : 0;

        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_room_type_service_product` rsp
            SET rsp.`position` = `position` '.($way ? '- 1' : '+ 1').'
            WHERE rsp.`id_element` = '.(int) $idElement.'
            AND rsp.`element_type` = '.(int) $elementType.'
            AND rsp.`position`'.($way ? '> '.
            (int) $movedBlock['position'].' AND rsp.`position` <= '.(int) $newPosition : '< '.
            (int) $movedBlock['position'].' AND rsp.`position` >= '.(int) $newPosition)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_room_type_service_product` rsp
            SET rsp.`position` = '.(int) $newPosition.'
            WHERE rsp.`id_element` = '.(int) $idElement.'
            AND rsp.`element_type` = '.(int) $elementType.'
            AND rsp.`id_product` = '.(int) $movedBlock['id_product']
        ));
    }
}
