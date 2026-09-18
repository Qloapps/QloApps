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

function clean_tabs_15()
{
    include_once(_PS_INSTALL_PATH_.'upgrade/php/migrate_tabs_15.php');

    $clean_tabs_15 = array(
    9 => array(
        'class_name' => 'AdminCatalog',
        'position' => 0,
        'active' => 1,
        'children' => array(
            21 => array('class_name' => 'AdminProducts', 'position' => 0, 'active' => 1,
            ),
            22 => array('class_name' => 'AdminCategories', 'position' => 1, 'active' => 1,
            ),
            23 => array('class_name' => 'AdminTracking', 'position' => 2, 'active' => 1,
            ),
            24 => array('class_name' => 'AdminAttributesGroups', 'position' => 3, 'active' => 1,
            ),
            25 => array('class_name' => 'AdminFeatures', 'position' => 4, 'active' => 1,
            ),
            26 => array('class_name' => 'AdminManufacturers', 'position' => 5, 'active' => 1,
            ),
            27 => array('class_name' => 'AdminSuppliers', 'position' => 6, 'active' => 1,
            ),
            28 => array('class_name' => 'AdminScenes', 'position' => 7, 'active' => 1,
            ),
            29 => array('class_name' => 'AdminTags', 'position' => 8, 'active' => 1,
            ),
            30 => array('class_name' => 'AdminAttachments', 'position' => 9, 'active' => 1,
            ),
        ),
    ),
    10 => array(
        'class_name' => 'AdminParentOrders',
        'position' => 1,
        'active' => 1,
        'children' => array(
            31 => array('class_name' => 'AdminOrders', 'position' => 0, 'active' => 1,
            ),
            32 => array('class_name' => 'AdminInvoices', 'position' => 1, 'active' => 1,
            ),
            33 => array('class_name' => 'AdminReturn', 'position' => 2, 'active' => 1,
            ),
            34 => array('class_name' => 'AdminDeliverySlip', 'position' => 3, 'active' => 1,
            ),
            35 => array('class_name' => 'AdminSlip', 'position' => 4, 'active' => 1,
            ),
            36 => array('class_name' => 'AdminStatuses', 'position' => 5, 'active' => 1,
            ),
            37 => array('class_name' => 'AdminOrderMessage', 'position' => 6, 'active' => 1,
            ),
        ),
    ),
    11 => array(
        'class_name' => 'AdminParentCustomer',
        'position' => 2,
        'active' => 1,
        'children' => array(
            38 => array('class_name' => 'AdminCustomers', 'position' => 0, 'active' => 1,
            ),
            39 => array('class_name' => 'AdminAddresses', 'position' => 1, 'active' => 1,
            ),
            40 => array('class_name' => 'AdminGroups', 'position' => 2, 'active' => 1,
            ),
            41 => array('class_name' => 'AdminCarts', 'position' => 3, 'active' => 1,
            ),
            42 => array('class_name' => 'AdminCustomerThreads', 'position' => 4, 'active' => 1,
            ),
            43 => array('class_name' => 'AdminContacts', 'position' => 5, 'active' => 1,
            ),
            44 => array('class_name' => 'AdminGenders', 'position' => 6, 'active' => 1,
            ),
            45 => array('class_name' => 'AdminOutstanding', 'position' => 7, 'active' => 0,
            ),
        ),
    ),
    12 => array(
        'class_name' => 'AdminPriceRule',
        'position' => 3,
        'active' => 1,
        'children' => array(
            46 => array('class_name' => 'AdminCartRules', 'position' => 0, 'active' => 1,
            ),
            47 => array('class_name' => 'AdminSpecificPriceRule', 'position' => 1, 'active' => 1,
            ),
        ),
    ),
    13 => array(
        'class_name' => 'AdminParentShipping',
        'position' => 4,
        'active' => 1,
        'children' => array(
            48 => array('class_name' => 'AdminShipping', 'position' => 0, 'active' => 1,
            ),
            49 => array('class_name' => 'AdminCarriers', 'position' => 1, 'active' => 1,
            ),
            50 => array('class_name' => 'AdminRangePrice', 'position' => 2, 'active' => 1,
            ),
            51 => array('class_name' => 'AdminRangeWeight', 'position' => 3, 'active' => 1,
            ),
        ),
    ),
    14 => array(
        'class_name' => 'AdminParentLocalization',
        'position' => 5,
        'active' => 1,
        'children' => array(
            52 => array('class_name' => 'AdminLocalization', 'position' => 0, 'active' => 1,
            ),
            53 => array('class_name' => 'AdminLanguages', 'position' => 1, 'active' => 1,
            ),
            54 => array('class_name' => 'AdminZones', 'position' => 2, 'active' => 1,
            ),
            55 => array('class_name' => 'AdminCountries', 'position' => 3, 'active' => 1,
            ),
            56 => array('class_name' => 'AdminStates', 'position' => 4, 'active' => 1,
            ),
            57 => array('class_name' => 'AdminCurrencies', 'position' => 5, 'active' => 1,
            ),
            58 => array('class_name' => 'AdminTaxes', 'position' => 6, 'active' => 1,
            ),
            59 => array('class_name' => 'AdminTaxRulesGroup', 'position' => 7, 'active' => 1,
            ),
            60 => array('class_name' => 'AdminTranslations', 'position' => 8, 'active' => 1,
            ),
        ),
    ),
    15 => array(
        'class_name' => 'AdminParentModules',
        'position' => 6,
        'active' => 1,
        'children' => array(
            61 => array('class_name' => 'AdminModules', 'position' => 0, 'active' => 1,
            ),
            62 => array('class_name' => 'AdminAddonsCatalog', 'position' => 1, 'active' => 1,
            ),
            63 => array('class_name' => 'AdminModulesPositions', 'position' => 2, 'active' => 1,
            ),
            64 => array('class_name' => 'AdminPayment', 'position' => 3, 'active' => 1,
            ),
        ),
    ),
    16 => array(
        'class_name' => 'AdminParentPreferences',
        'position' => 7,
        'active' => 1,
        'children' => array(
            65 => array('class_name' => 'AdminPreferences', 'position' => 0, 'active' => 1,
            ),
            66 => array('class_name' => 'AdminOrderPreferences', 'position' => 1, 'active' => 1,
            ),
            67 => array('class_name' => 'AdminPPreferences', 'position' => 2, 'active' => 1,
            ),
            68 => array('class_name' => 'AdminCustomerPreferences', 'position' => 3, 'active' => 1,
            ),
            69 => array('class_name' => 'AdminThemes', 'position' => 4, 'active' => 1,
            ),
            70 => array('class_name' => 'AdminMeta', 'position' => 5, 'active' => 1,
            ),
            71 => array('class_name' => 'AdminCmsContent', 'position' => 6, 'active' => 1,
            ),
            72 => array('class_name' => 'AdminImages', 'position' => 7, 'active' => 1,
            ),
            73 => array('class_name' => 'AdminStores', 'position' => 8, 'active' => 1,
            ),
            74 => array('class_name' => 'AdminSearchConf', 'position' => 9, 'active' => 1,
            ),
            75 => array('class_name' => 'AdminMaintenance', 'position' => 10, 'active' => 1,
            ),
            76 => array('class_name' => 'AdminGeolocation', 'position' => 11, 'active' => 1,
            ),
        ),
    ),
    17 => array(
        'class_name' => 'AdminTools',
        'position' => 8,
        'active' => 1,
        'children' => array(
            77 => array('class_name' => 'AdminInformation', 'position' => 0, 'active' => 1,
            ),
            78 => array('class_name' => 'AdminPerformance', 'position' => 1, 'active' => 1,
            ),
            79 => array('class_name' => 'AdminEmails', 'position' => 2, 'active' => 1,
            ),
            80 => array('class_name' => 'AdminShopGroup', 'position' => 3, 'active' => 0,
            ),
            81 => array('class_name' => 'AdminImport', 'position' => 4, 'active' => 1,
            ),
            82 => array('class_name' => 'AdminBackup', 'position' => 5, 'active' => 1,
            ),
            83 => array('class_name' => 'AdminRequestSql', 'position' => 6, 'active' => 1,
            ),
            84 => array('class_name' => 'AdminLogs', 'position' => 7, 'active' => 1,
            ),
            85 => array('class_name' => 'AdminWebservice', 'position' => 8, 'active' => 1,
            ),
        ),
    ),
    18 => array(
        'class_name' => 'AdminAdmin',
        'position' => 9,
        'active' => 1,
        'children' => array(
            86 => array('class_name' => 'AdminAdminPreferences', 'position' => 0, 'active' => 1,
            ),
            87 => array('class_name' => 'AdminQuickAccesses', 'position' => 1, 'active' => 1,
            ),
            88 => array('class_name' => 'AdminEmployees', 'position' => 2, 'active' => 1,
            ),
            89 => array('class_name' => 'AdminProfiles', 'position' => 3, 'active' => 1,
            ),
            90 => array('class_name' => 'AdminAccess', 'position' => 4, 'active' => 1,
            ),
            91 => array('class_name' => 'AdminTabs', 'position' => 5, 'active' => 1,
            ),
        ),
    ),
    19 => array(
        'class_name' => 'AdminParentStats',
        'position' => 10,
        'active' => 1,
        'children' => array(
            92 => array('class_name' => 'AdminStats', 'position' => 0, 'active' => 1,
            ),
            93 => array('class_name' => 'AdminSearchEngines', 'position' => 1, 'active' => 1,
            ),
            94 => array('class_name' => 'AdminReferrers', 'position' => 2, 'active' => 1,
            ),
        ),
    ),
    20 => array(
        'class_name' => 'AdminStock',
        'position' => 11,
        'active' => 1,
        'children' => array(
            95 => array('class_name' => 'AdminWarehouses', 'position' => 0, 'active' => 1,
            ),
            96 => array('class_name' => 'AdminStockManagement', 'position' => 1, 'active' => 1,
            ),
            97 => array('class_name' => 'AdminStockMvt', 'position' => 2, 'active' => 1,
            ),
            98 => array('class_name' => 'AdminStockInstantState', 'position' => 3, 'active' => 1,
            ),
            99 => array('class_name' => 'AdminStockCover', 'position' => 4, 'active' => 1,
            ),
            100 => array('class_name' => 'AdminSupplyOrders', 'position' => 5, 'active' => 1,
            ),
            101 => array('class_name' => 'AdminStockConfiguration', 'position' => 6, 'active' => 1,
            ),
        ),
    ),
);

    //===== step 1 disabled all useless native tabs in 1.5 =====/

    $remove_tabs = array(
        2 => 'AdminAddonsMyAccount', 4 => 'AdminAliases', 5 => 'AdminAppearance', 12 => 'AdminCMSContent',
        13 => 'AdminContact', 16 => 'AdminCounty', 20 => 'AdminDb', 22 => 'AdminDiscounts', 26 => 'AdminGenerator',
        38 => 'AdminMessages', 45 => 'AdminPDF', 63 => 'AdminStatsConf', 67 => 'AdminSubDomains'
        );
    $ids = array();
    foreach ($remove_tabs as $tab) {
        if ($id = get_tab_id($tab)) {
            $ids[] = $id;
        }
    }

    if ($ids) {
        Db::getInstance()->update('tab', array('active' => 0), 'id_tab IN ('.implode(', ', $ids).')');
    }

    //=====================================/


    //===== step 2 move all no native tabs in AdminTools  =====/

    $id_admin_tools = get_tab_id('AdminTools');

    $tab_to_move = get_simple_clean_tab15($clean_tabs_15);

    $ids = array();
    foreach ($tab_to_move as $tab) {
        if ($id = get_tab_id($tab)) {
            $ids[] = $id;
        }
    }

    if ($ids) {
        Db::getInstance()->update('tab', array('id_parent' => $id_admin_tools), 'id_tab NOT IN ('.implode(', ', $ids).') AND `id_parent` <> -1');
    }

    //=====================================/

    //===== step 3 sort all 1.5 tabs  =====/

    updatePositionAndActive15($clean_tabs_15);

    //=====================================/

    //specific case for AdminStockMvt in AdminStock
    
    $id_AdminStockMvt = get_tab_id('AdminStockMvt');
    $id_AdminStock = get_tab_id('AdminStock');
    Db::getInstance()->update('tab', array('id_parent' => $id_AdminStock), 'id_tab ='.$id_AdminStockMvt);
    
    //rename some tabs
    renameTab(get_tab_id('AdminCartRules'), array('fr' => 'Règles paniers', 'es' => 'Reglas de cesta', 'en' => 'Cart Rules', 'de' => 'Warenkorb Preisregein', 'it' => 'Regole Carrello'));
    
    renameTab(get_tab_id('AdminPreferences'), array('fr' => 'Générales', 'es' => 'General', 'en' => 'General', 'de' => 'Allgemein', 'it' => 'Generale'));
    
    renameTab(get_tab_id('AdminThemes'), array('fr' => 'Thèmes', 'es' => 'Temas', 'en' => 'Themes', 'de' => 'Themen', 'it' => 'Temi'));
    
    renameTab(get_tab_id('AdminStores'), array('fr' => 'Coordonnées & magasins', 'es' => 'Contacto y tiendas', 'en' => 'Store Contacts', 'de' => 'Shopadressen', 'it' => 'Contatti e Negozi'));
    
    renameTab(get_tab_id('AdminTools'), array('fr' => 'Paramètres avancés', 'es' => 'Parametros avanzados', 'en' => 'Advanced Parameters', 'de' => 'Erweiterte Parameter', 'it' => 'Parametri Avanzati'));
        
    renameTab(get_tab_id('AdminTools'), array('fr' => 'Paramètres avancés', 'es' => 'Parametros avanzados', 'en' => 'Advanced Parameters', 'de' => 'Erweiterte Parameter', 'it' => 'Parametri Avanzati'));
    
    renameTab(get_tab_id('AdminTabs'), array('fr' => 'Menus', 'es' => 'Pestañas', 'en' => 'Menus', 'de' => 'Tabs', 'it' => 'Tabs'));
}

//==== functions =====/


function get_simple_clean_tab15($clean_tabs_15)
{
    $light_tab = array();
    foreach ($clean_tabs_15 as $tab) {
        $light_tab[] = $tab['class_name'];
        if (isset($tab['children'])) {
            $light_tab = array_merge($light_tab, get_simple_clean_tab15($tab['children']));
        }
    }
    return $light_tab;
}

function updatePositionAndActive15($clean_tabs_15)
{
    foreach ($clean_tabs_15 as $id => $tab) {
        Db::getInstance()->update('tab', array('position' => $tab['position'], 'active' => $tab['active']), '`id_tab`= '.get_tab_id($tab['class_name']));
        if (isset($tab['children'])) {
            updatePositionAndActive15($tab['children']);
        }
    }
}

function renameTab($id_tab, $names)
{
    if (!$id_tab) {
        return;
    }
    $langues = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'lang');

    foreach ($langues as $lang) {
        if (array_key_exists($lang['iso_code'], $names)) {
            Db::getInstance()->update('tab_lang', array('name' => $names[$lang['iso_code']]), '`id_tab`= '.$id_tab.' AND `id_lang` ='.$lang['id_lang']);
        }
    }
}
