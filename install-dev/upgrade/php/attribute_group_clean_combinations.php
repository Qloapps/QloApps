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

function attribute_group_clean_combinations()
{
    $attributeCombinations = Db::getInstance()->executeS('SELECT
		pac.`id_attribute`, pa.`id_product_attribute`
		FROM `'._DB_PREFIX_.'product_attribute` pa
		LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
			ON (pa.`id_product_attribute` = pac.`id_product_attribute`)');
    $toRemove = array();
    foreach ($attributeCombinations as $attributeCombination) {
        if ((int)($attributeCombination['id_attribute']) == 0) {
            $toRemove[] = (int)($attributeCombination['id_product_attribute']);
        }
    }

    if (!empty($toRemove)) {
        $res = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attribute`
			WHERE `id_product_attribute` IN ('.implode(', ', $toRemove).')');
        return $res;
    }
    return true;
}
