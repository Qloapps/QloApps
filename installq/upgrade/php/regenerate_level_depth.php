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

/**
 * Regenerate the entire category tree level_depth
 */
function regenerate_level_depth()
{
    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'category` SET `level_depth` = 0 WHERE `id_category` = 1');
    regenerate_children_categories(1, 0);
}

/**
 * Recursively regenerate the level_depth of this category's children
 *
 * @param int $id_category
 * @param int $level_depth
 */
function regenerate_children_categories($id_category, $level_depth)
{
    $categories = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category` WHERE `id_parent` = '.(int)$id_category);
    if (!$categories) {
        return;
    }
    $new_depth = (int)$level_depth + 1;
    $cat_ids = "";
    foreach ($categories as $category) {
        $cat_ids .= (string)$category['id_category'].',';
        regenerate_children_categories($category['id_category'], $new_depth);
    }
    $cat_ids = substr($cat_ids, 0, -1);

    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'category` SET `level_depth` = '.(int)$new_depth.' WHERE `id_category` IN ('.$cat_ids.')');
}
