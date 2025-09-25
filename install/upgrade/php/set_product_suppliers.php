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

function set_product_suppliers()
{
    $ps_currency_default = Db::getInstance()->getValue('SELECT value
	FROM `'._DB_PREFIX_.'configuration` WHERE name="PS_CURRENCY_DEFAULT"');

    //Get all products with positive quantity
    $resource = Db::getInstance()->query('
		SELECT id_supplier, id_product, supplier_reference, wholesale_price
		FROM `'._DB_PREFIX_.'product`
		WHERE `id_supplier` > 0
	');

    while ($row = Db::getInstance()->nextRow($resource)) {
        //Set default supplier for product
        Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'product_supplier`
			(`id_product`, `id_product_attribute`, `id_supplier`,
				`product_supplier_reference`, `product_supplier_price_te`,
				`id_currency`)
			VALUES
			("'.(int)$row['id_product'].'", "0", "'.(int)$row['id_supplier'].'",
			"'.pSQL($row['supplier_reference']).'", "'.(int)$row['wholesale_price'].'",
				"'.(int)$ps_currency_default.'")
		');

        //Try to get product attribues
        $attributes = Db::getInstance()->executeS('
			SELECT id_product_attribute, supplier_reference, wholesale_price
			FROM `'._DB_PREFIX_.'product_attribute`
			WHERE `id_product` = '.(int)$row['id_product']
        );

        //Add each attribute to stock_available
        foreach ($attributes as $attribute) {
            // set supplier for attribute
            Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'product_supplier`
				(`id_product`, `id_product_attribute`,
				`id_supplier`, `product_supplier_reference`,
				`product_supplier_price_te`, `id_currency`)
				VALUES
				("'.(int)$row['id_product'].'", "'.(int)$attribute['id_product_attribute'].'",
				"'.(int)$row['id_supplier'].'", "'.pSQL($attribute['supplier_reference']).'",
				"'.(int)$attribute['wholesale_price'].'", "'.(int)$ps_currency_default.'")
			');
        }
    }
}
