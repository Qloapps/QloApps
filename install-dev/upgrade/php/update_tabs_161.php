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

function update_tabs_161()
{
    $remove_tabs = array(
        'AdminParentStats',
        'AdminSearchEngines',
        'AdminReferrers',
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

    // remove id parent of stats tab
    $idAdminStats = get_tab_id('AdminStats');
    $position = Db::getInstance()->getValue('SELECT MAX(`position`) FROM `'._DB_PREFIX_.'tab` WHERE `id_parent` = 0');
    Db::getInstance()->update('tab', array('id_parent' => 0, 'position' => ($position + 1)), '`id_tab` = '.$idAdminStats);
}

function get_tab_id($class_name)
{
    static $cache = array();

    if (!isset($cache[$class_name])) {
        $cache[$class_name] = Db::getInstance()->getValue('SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = \''.pSQL($class_name).'\'');
    }
    return $cache[$class_name];
}