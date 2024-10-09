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

function add_new_tabs_160()
{
    $tabs = array(
        array(
            'className' => 'AdminNormalProducts',
            'name' => 'Manage Service Products',
            'id_parent' => (int) Tab::getIdFromClassName('AdminCatalog'),
            'position_after' => 'AdminProducts'
        ),
        array(
            'className' => 'AdminCategories',
            'name' => 'Categories',
            'id_parent' => (int) Tab::getIdFromClassName('AdminCatalog'),
            'position_after' => 'AdminNormalProducts'
        ),
        array(
            'className' => 'AdminModulesCatalog',
            'name' => 'Modules Catalog',
            'id_parent' => (int) Tab::getIdFromClassName('AdminParentModules'),
            'position_after' => 'AdminModules'
        )
    );


    foreach($tabs as $tab) {
        $objTab = new Tab();
        $objTab->active = 1;
        $objTab->class_name = $tab['className'];
        $objTab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $objTab->name[$lang['id_lang']] = $tab['name'];
        }

        $objTab->id_parent = (int)$tab['id_parent'];

        if ($objTab->add()) {
            //Set position Tab
            if (Validate::isLoadedObject(
                $objTabForPosition = Tab::getInstanceFromClassName($tab['position_after'])
            )) {
                $objTab->updatePosition(0, ($objTabForPosition->position + 1));
            }
        }
    }

    return true;
}