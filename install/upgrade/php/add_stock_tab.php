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

require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'add_new_tab.php');

function add_stock_tab()
{
    // Patch for the 1.0.1 sql update
    Db::getInstance()->query('
		DELETE
		FROM `'._DB_PREFIX_.'tab`
		WHERE id_parent = 1
		AND class_name = "AdminStocks"');

    // Create new tabs
    $id_parent = add_new_tab(
        'AdminStock',
        'en:Stock|fr:Stock|es:Stock|de:Stock|it:Stock',
        0,
        true);

    add_new_tab(
        'AdminWarehouses',
        'en:Warehouses|fr:Entrepôts|es:Warehouses|de:Warehouses|it:Warehouses',
        $id_parent);

    add_new_tab(
        'AdminStockManagement',
        'en:Stock Management|fr:Gestion du stock|es:Stock Management|de:Stock Management|it:Stock Management',
        $id_parent);

    add_new_tab(
        'AdminStockMvt',
        'en:Stock Movement|fr:Mouvements de Stock|es:Stock Movement|de:Stock Movement|it:Stock Movement',
        $id_parent);

    add_new_tab(
        'AdminStockInstantState',
        'en:Stock instant state|fr:Etat instantané du stock|es:Stock instant state|de:Stock instant state|it:Stock instant state',
        $id_parent);

    add_new_tab(
        'AdminStockCover',
        'en:Stock cover|fr:Couverture du stock|es:Stock cover|de:Stock cover|it:Stock cover',
        $id_parent);

    add_new_tab(
        'AdminSupplyOrders',
        'en:Supply orders|fr:Commandes fournisseurs|es:Supply orders|de:Supply orders|it:Supply orders',
        $id_parent);

    add_new_tab(
        'AdminStockConfiguration',
        'en:Configuration|fr:Configuration|es:Configuration|de:Configuration|it:Configuration',
        $id_parent);
}
