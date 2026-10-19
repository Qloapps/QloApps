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

function move_hotel_categoryes_to_location()
{
    if ($idLocationCategory = Configuration::get('PS_LOCATIONS_CATEGORY')) {

        $sql = 'SELECT hbi.`id`, hbi.`id_category`
        FROM `'._DB_PREFIX_.'htl_branch_info` hbi';
        $hotels = Db::getInstance()->executeS($sql);
        $categoriesToUpdate = array();
        foreach ($hotels as $hotel) {
            $objCategory = new Category($hotel['id_category']);
            $categories = $objCategory->getParentsCategories();
            foreach ($categories as $category) {
                if ($category['level_depth'] == 2
                    && $category['id_category'] != $idLocationCategory
                    && $category['id_parent'] != $idLocationCategory
                ) {
                    if (!in_array($category['id_category'], $categoriesToUpdate)) {
                        $categoriesToUpdate[] = $category['id_category'];
                    }
                }
            }
        }

        // update country categories id_parent
        db::getInstance()->update('category',
            array('id_parent' => $idLocationCategory),
            'id_category IN ('.implode(', ', $categoriesToUpdate).')'
        );

        // regenerate category tree
        Category::regenerateEntireNtree();
        $objCategory = new Category();
        $objCategory->recalculateLevelDepth($idLocationCategory);

        return true;
    } else {
        return false;
    }
}