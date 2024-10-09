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

function add_new_categores_160()
{
    $categories = array(
        array(
            'name' => 'Locations',
            'description' => '',
            'id_parent' => Configuration::get('PS_HOME_CATEGORY'),
            'config_key' => 'PS_LOCATIONS_CATEGORY'
        ),
        array(
            'name' => 'Services',
            'description' => '',
            'id_parent' => Configuration::get('PS_HOME_CATEGORY'),
            'config_key' => 'PS_SERVICE_CATEGORY'
        )
    );

    $groupIds = array();
    $objGroup = new Group();
    $dataGroupIds = $objGroup->getGroups(1, $id_shop = false);

    foreach ($dataGroupIds as $key => $value) {
        $groupIds[] = $value['id_group'];
    }
    $res = true;
    foreach($categories as $category) {
        $objCategory = new Category();
        $objCategory->name = array();
        $objCategory->description = array();
        $objCategory->link_rewrite = array();

        foreach (Language::getLanguages(true) as $lang) {
            $objCategory->name[$lang['id_lang']] = $category['name'];
            $objCategory->description[$lang['id_lang']] = $category['description'];
            $objCategory->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($category['name']);
        }
        $objCategory->id_parent = $category['id_parent'];
        $objCategory->groupBox = $groupIds;
        if ($objCategory->save()) {
            $res &= Configuration::updateValue($category['config_key'], $objCategory->id);
        }
    }

    return $res;
}