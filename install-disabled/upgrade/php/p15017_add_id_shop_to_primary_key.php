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

function p15017_add_id_shop_to_primary_key()
{
    // Drop old indexes
    $old_indexes = array(
        'category_lang_index' => 'category_lang',
        'shipper_lang_index' => 'carrier_lang',
        'product_lang_index' => 'product_lang',
        'id_category_shop' => 'category_shop'
    );
    foreach ($old_indexes as $index => $table) {
        if (Db::getInstance()->executeS('SHOW INDEX FROM `'._DB_PREFIX_.$table.'` WHERE Key_name = "'.$index.'"')) {
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.$table.'` DROP KEY `'.$index.'`');
        }
    }

    // The former primary keys where set on id_object and id_lang. They must now be set on id_shop too.
    foreach (array('product', 'category', 'meta', 'carrier') as $table) {
        if (Db::getInstance()->executeS('SHOW INDEX FROM `'._DB_PREFIX_.$table.'` WHERE Key_name = "PRIMARY"')) {
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.$table.'_lang` DROP PRIMARY KEY');
        }
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.$table.'_lang` ADD PRIMARY KEY (`id_'.$table.'`, `id_shop`, `id_lang`)');
    }
    
    return true;
}
