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

function generate_ntree()
{
    $categories = Db::getInstance()->executeS('SELECT id_category, id_parent FROM '._DB_PREFIX_.'category ORDER BY id_parent ASC, position ASC');
    $categoriesArray = array();
    if (is_array($categories)) {
        foreach ($categories as $category) {
            $categoriesArray[(int)$category['id_parent']]['subcategories'][(int)$category['id_category']] = 1;
        }
    }
    $n = 1;
    generate_ntree_subTree($categoriesArray, 1, $n);
}

function generate_ntree_subTree(&$categories, $id_category, &$n)
{
    $left = (int)$n++;
    if (isset($categories[(int)$id_category]['subcategories'])) {
        foreach (array_keys($categories[(int)$id_category]['subcategories']) as $id_subcategory) {
            generate_ntree_subTree($categories, (int)$id_subcategory, $n);
        }
    }
    $right = (int)$n++;

    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'category
		SET nleft = '.(int)$left.', nright = '.(int)$right.'
		WHERE id_category = '.(int)$id_category.' LIMIT 1');
}
