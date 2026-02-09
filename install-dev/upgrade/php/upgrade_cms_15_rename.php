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

function upgrade_cms_15_rename()
{
    $res = true;
    $db = Db::getInstance();

    $res &= $db->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'_cms_shop"');
    if ($res) {
        $res &= $db->execute('RENAME TABLE `'._DB_PREFIX_.'_cms_shop` to `'._DB_PREFIX_.'cms_shop`');
        // in case the script upgrade_cms_15.php have set a wrong table name, it's empty
        $res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cms_shop` (id_shop, id_cms)
			(SELECT 1, id_cms FROM '._DB_PREFIX_.'cms)');

        // cms_block table is blockcms module dependant. Don't update table that does not exists
        $table_cms_block_exists = $db->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'cms"');
        if (!$table_cms_block_exists) {
            return $res;
        }
        $res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'cms`
			ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT "1" AFTER `id_cms`');
    }

    return $res;
}
