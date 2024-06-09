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
 * Migrate BO tabs for 1.5 (new reorganization of BO)
 */
function migrate_tabs_15()
{
    include_once(_PS_INSTALL_PATH_.'upgrade/php/add_new_tab.php');

    // ===== Remove deleted tabs =====
    $remove_tabs = array(
        'AdminAliases',
        'AdminContact',
        'AdminDb',
        'AdminGenerator',
        'AdminPdf',
        'AdminSubDomains',
        'AdminStatsConf',
    );

    $ids = array();
    foreach ($remove_tabs as $tab) {
        if ($id = get_tab_id($tab)) {
            $ids[] = $id;
        }
    }

    if ($ids) {
        Db::getInstance()->delete('tab', 'id_tab IN ('.implode(', ', $ids).')');
        Db::getInstance()->delete('tab_lang', 'id_tab IN ('.implode(', ', $ids).')');
    }

    // ===== Create new parent tabs =====
    $parent = array(
        'AdminCatalog' =>            get_tab_id('AdminCatalog'),
        'AdminParentOrders' =>        add_new_tab('AdminParentOrders', 'en:Orders|fr:Commandes|es:Pedidos|de:Bestellungen|it:Ordini', 0, true),
        'AdminParentCustomer' =>    add_new_tab('AdminParentCustomer', 'en:Customers|fr:Clients|es:Clientes|de:Kunden|it:Clienti', 0, true),
        'AdminPriceRule' =>        add_new_tab('AdminPriceRule', 'en:Price rules|fr:Promotions|es:Price rules|de:Price rules|it:Price rules', 0, true),
        'AdminParentShipping' =>    add_new_tab('AdminParentShipping', 'en:Shipping|fr:Transport|es:Transporte|de:Versandkosten|it:Spedizione', 0, true),
        'AdminParentLocalization' =>add_new_tab('AdminParentLocalization', 'en:Localization|fr:Localisation|es:Ubicación|de:Lokalisierung|it:Localizzazione', 0, true),
        'AdminParentModules' =>    add_new_tab('AdminParentModules', 'en:Modules|fr:Modules|es:Módulos|de:Module|it:Moduli', 0, true),
        'AdminParentPreferences' => add_new_tab('AdminParentPreferences', 'en:Preferences|fr:Préférences|es:Preferencias|de:Voreinstellungen|it:Preferenze', 0, true),
        'AdminTools' =>                get_tab_id('AdminTools'),
        'AdminAdmin' =>            add_new_tab('AdminAdmin', 'en:Administration|fr:Administration|es:Administration|de:Administration|it:Administration', 0, true),
        'AdminParentStats' =>        add_new_tab('AdminParentStats', 'en:Stats|fr:Stats|es:Estadísticas|de:Statistik|it:Stat', 0, true),
        'AdminParentShop' =>        add_new_tab('AdminParentShop', 'en:Shops|fr:Boutiques|es:Shops|de:Shops|it:Shops', 0, true),
        'AdminStock' =>                get_tab_id('AdminStock'),
    );

    // ===== Move tabs from old parents to new parents =====
    $move_association = array(
        'AdminParentOrders' => 'AdminOrders',
        'AdminParentCustomer' => 'AdminCustomers',
        'AdminParentShipping' => 'AdminShipping',
        'AdminParentLocalization' => 'AdminLocalization',
        'AdminParentModules' => 'AdminModules',
        'AdminParentPreferences' => 'AdminPreferences',
        'AdminAdmin' => 'AdminEmployees',
        'AdminParentStats' => 'AdminStats',
        'AdminParentShop' => 'AdminShop',
    );

    foreach ($move_association as $to => $from) {
        if (empty($parent[$to])) {
            continue;
        }

        $id_parent = get_tab_id($from);
        if ($id_parent) {
            Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'tab
				SET id_parent = '.$parent[$to].'
				WHERE id_parent = '.$id_parent.'
					OR id_tab = '.$id_parent.'
			');
        }
    }

    // ===== Move tabs to their new parents =====
    $move_to = array(
        'AdminContacts' => 'AdminParentCustomer',
        'AdminCustomerThreads' => 'AdminParentCustomer',
        'AdminCurrencies' => 'AdminParentLocalization',
        'AdminTaxes' => 'AdminParentLocalization',
        'AdminTaxRulesGroup' => 'AdminParentLocalization',
        'AdminLanguages' => 'AdminParentLocalization',
        'AdminTranslations' => 'AdminParentLocalization',
        'AdminZones' => 'AdminParentLocalization',
        'AdminCountries' => 'AdminParentLocalization',
        'AdminStates' => 'AdminParentLocalization',
        'AdminCartRules' => 'AdminPriceRule',
        'AdminSpecificPriceRule' => 'AdminPriceRule',
        'AdminQuickAccesses' => 'AdminAdmin',
        'AdminPayment' => 'AdminParentModules',
        'AdminCmsContent' => 'AdminParentPreferences',
        'AdminStores' => 'AdminParentPreferences',
        'AdminEmails' => 'AdminTools',
        'AdminPerformance' => 'AdminTools',
        'AdminAccountingConfiguration' => 'AdminTools',
        'AdminAccountingRegisteredNumber' => 'AdminTools',
        'AdminAccountingExport' => 'AdminStats',
    );

    foreach ($move_to as $from => $to) {
        if (empty($parent[$to])) {
            continue;
        }

        $id_tab = get_tab_id($from);
        if ($id_tab) {
            Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'tab
				SET id_parent = '.$parent[$to].'
				WHERE id_tab = '.$id_tab.'
			');
        }
    }

    // ===== Remove AdminThemes from Modules parent =====
    $id_tab_theme = Db::getInstance()->getValue(
        'SELECT id_tab FROM '._DB_PREFIX_.'tab
		WHERE class_name = \'AdminThemes\'
			AND id_parent = '.$parent['AdminParentModules'].'
	');

    if ($id_tab_theme) {
        Db::getInstance()->delete('tab', 'id_tab = '.$id_tab_theme);
    }

    // ===== Create new tabs (but not parents this time) =====
    add_new_tab('AdminOrderPreferences', 'en:Orders|fr:Commandes|es:Pedidos|de:Bestellungen|it:Ordini', $parent['AdminParentPreferences']);
    add_new_tab('AdminCustomerPreferences', 'en:Customers|fr:Clients|es:Clientes|de:Kunden|it:Clienti', $parent['AdminParentPreferences']);
    add_new_tab('AdminMaintenance', 'en:Maintenance|fr:Maintenance|es:Maintenance|de:Maintenance|it:Maintenance', $parent['AdminParentPreferences']);
    add_new_tab('AdminAdminPreferences', 'en:Preferences|fr:Préférences|es:Preferencias|de:Voreinstellungen|it:Preferenze', $parent['AdminAdmin']);

    // ===== Sort parent tabs =====
    $position = 0;
    foreach ($parent as $id) {
        Db::getInstance()->update('tab', array('position' => $position++), 'id_tab = '.(int)$id);
    }

    $sql = 'SELECT id_tab FROM '._DB_PREFIX_.'tab
			WHERE id_tab NOT IN ('.implode(', ', $parent).')
				AND id_parent = 0';
    
    $id_tabs = Db::getInstance()->executeS($sql);
    if (is_array($id_tabs) && count($id_tabs)) {
        foreach (Db::getInstance()->executeS($sql) as $row) {
            Db::getInstance()->update('tab', array('position' => $position++), 'id_tab = '.$row['id_tab']);
        }
    }
}

function get_tab_id($class_name)
{
    static $cache = array();

    if (!isset($cache[$class_name])) {
        $cache[$class_name] = Db::getInstance()->getValue('SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = \''.pSQL($class_name).'\'');
    }
    return $cache[$class_name];
}

/* DO NOT REMOVE THIS FUNCTION !
function get_tab_langs($classname)
{
    $parent_xml = simplexml_load_file(_PS_INSTALL_DATA_PATH_.'xml/tab.xml');
    $result = $parent_xml->xpath('entities/tab[class_name=\''.$classname.'\']');
    $id = (string)$result[0]['id'];
    foreach (array('en', 'fr', 'es', 'de', 'it') as $iso)
    {
        $xml = simplexml_load_file(_PS_INSTALL_LANGS_PATH_.$iso.'/data/tab.xml');
        $result = $xml->xpath('tab[@id=\''.$id.'\']');
        $values[$iso] = (string)$result[0]['name'];
    }

    $return = '';
    foreach ($values as $iso => $lang)
        $return .= $iso.':'.$lang.'|';
    return utf8_decode(rtrim($return, '|'));
}
*/
