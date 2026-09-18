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

function fix_unique_specific_price()
{
    $result = Db::getInstance()->executeS('
	SELECT MIN(id_specific_price) id_specific_price
	FROM '._DB_PREFIX_.'specific_price
	GROUP BY `id_product`, `id_shop`, `id_currency`, `id_country`, `id_group`, `from_quantity`, `from`, `to`');
    if (!$result || !count($result)) {
        return true;
    } // return tru if there is not any specific price in the database
        
    $sql = '';
    foreach ($result as $row) {
        $sql .= (int)$row['id_specific_price'].',';
    }
    $sql = rtrim($sql, ',');

    return Db::getInstance()->execute('
	DELETE FROM '._DB_PREFIX_.'specific_price
	WHERE id_specific_price NOT IN ('.$sql.')');
}
