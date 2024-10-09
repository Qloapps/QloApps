<?php
/**
* 2010-2023 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
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