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

define('_CONTAINS_REQUIRED_FIELD_', 2);

function add_required_customization_field_flag()
{
    if (($result = Db::getInstance()->executeS('SELECT `id_product` FROM `'._DB_PREFIX_.'customization_field` WHERE `required` = 1')) === false) {
        return false;
    }
    if (Db::getInstance()->numRows()) {
        $productIds = array();
        foreach ($result as $row) {
            $productIds[] = (int)($row['id_product']);
        }
        if (!Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET `customizable` = '._CONTAINS_REQUIRED_FIELD_.' WHERE `id_product` IN ('.implode(', ', $productIds).')')) {
            return false;
        }
    }
    return true;
}
