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