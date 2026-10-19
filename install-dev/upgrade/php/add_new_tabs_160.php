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