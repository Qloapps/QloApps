<?php
/*
* 2007-2014 PrestaShop
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
* /ersions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function p16012_pack_rework()
{
    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`id_configuration`, `name`, `value`, `date_add`, `date_upd`) VALUES (NULL, "PS_PACK_STOCK_TYPE", "0", NOW(), NOW())');
    $all_product_in_pack = Db::getInstance()->ExecuteS('SELECT `id_product_item` FROM '._DB_PREFIX_.'pack GROUP BY `id_product_item`');
    foreach ($all_product_in_pack as $value) {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'pack
		 	SET `id_product_attribute_item` = '.(getDefaultAttribute($value['id_product_item']) ? getDefaultAttribute($value['id_product_item']).' ' : '0 ').'
		 	WHERE `id_product_item` = '.$value['id_product_item']);
    }

    $all_product_pack = Db::getInstance()->ExecuteS('SELECT `id_product_pack` FROM '._DB_PREFIX_.'pack GROUP BY `id_product_pack`');
    foreach ($all_product_pack as $value) {
        $work_with_stock = 1;
        $lang = Db::getInstance()->ExecuteS('SELECT value FROM '._DB_PREFIX_.'configuration WHERE `id_shop` = NULL AND `id_shop_group` = NULL AND `name` = "PS_LANG_DEFAULT"');
        $products = getItems($value['id_product_pack']);
        foreach ($products as $product) {
            if ($product != 1) {
                $work_with_stock = 0;
                break;
            }
        }
        if ($work_with_stock) {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET `pack_stock_type` = 1 WHERE `id_product` = '.(int)$value['id_product_pack']);
        }
    }
}

function getDefaultAttribute($id_product)
{
    static $combinations = array();

    if (!isset($combinations[$id_product])) {
        $combinations[$id_product] = array();
    }
    if (isset($combinations[$id_product]['default'])) {
        return $combinations[$id_product]['default'];
    }

    $sql = 'SELECT id_product_attribute
			FROM '._DB_PREFIX_.'product_attribute
			WHERE default_on = 1 AND id_product = '.(int)$id_product;
    $result = Db::getInstance()->getValue($sql);

    $combinations[$id_product]['default'] = $result ? $result : ($result = Db::getInstance()->getValue('SELECT id_product_attribute
			FROM '._DB_PREFIX_.'product_attribute
			WHERE id_product = '.(int)$id_product));
    return $result;
}

function getItems($id_product)
{
    $result = Db::getInstance()->executeS('SELECT id_product_item, quantity FROM '._DB_PREFIX_.'pack where id_product_pack = '.(int)$id_product);
    $array_result = array();
    foreach ($result as $row) {
        $p = Db::getInstance()->executeS('SELECT `advanced_stock_management` FROM '._DB_PREFIX_.'product WHERE `id_product` = '.(int)$row['id_product_item']);
        $array_result[] = $p[0]['advanced_stock_management'];
    }
    return $array_result;
}
