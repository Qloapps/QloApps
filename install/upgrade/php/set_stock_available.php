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

function set_stock_available()
{
    $res = true;
    //Get all products with positive quantity
    $resource = Db::getInstance()->query('
		SELECT quantity, id_product, out_of_stock
		FROM `'._DB_PREFIX_.'product`
		WHERE `active` = 1
	');

    while ($row = Db::getInstance()->nextRow($resource)) {
        $quantity = 0;

        //Try to get product attribues
        $attributes = Db::getInstance()->executeS('
			SELECT quantity, id_product_attribute
			FROM `'._DB_PREFIX_.'product_attribute`
			WHERE `id_product` = '.(int)$row['id_product']
        );

        //Add each attribute to stock_available
        foreach ($attributes as $attribute) {
            // add to global quantity
            $quantity += $attribute['quantity'];

            //add stock available for attributes
            $res &= Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'stock_available`
				(`id_product`, `id_product_attribute`, `id_shop`, `id_group_shop`, `quantity`, `depends_on_stock`, `out_of_stock`)
				VALUES
				("'.(int)$row['id_product'].'", "'.(int)$attribute['id_product_attribute'].'", "1", "0", "'.(int)$attribute['quantity'].'", "0", "'.(int)$row['out_of_stock'].'")
			');
            if (!$res) {
                return array('error' => Db::getInstance()->getNumberError(), 'msg' => '(attributes)'.Db::getInstance()->getMsgError());
            }
        }

        if (count($attributes) == 0) {
            $quantity = (int)$row['quantity'];
        }

        //Add stock available for product;
        $res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'stock_available`
			(`id_product`, `id_product_attribute`, `id_shop`, `id_group_shop`, `quantity`, `depends_on_stock`, `out_of_stock`)
			VALUES
			("'.(int)$row['id_product'].'", "0", "1", "0", "'.(int)$quantity.'", "0", "'.(int)$row['out_of_stock'].'")
		');
        if (!$res) {
            return array('error' => Db::getInstance()->getNumberError(), 'msg' => '(products)'.Db::getInstance()->getMsgError());
        }
    }
    return $res;
}
