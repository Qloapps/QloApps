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

function add_attribute_position()
{
    $groups = Db::getInstance()->executeS('
	SELECT DISTINCT `id_attribute_group`
	FROM `'._DB_PREFIX_.'attribute`');
    if (count($groups) && is_array($groups)) {
        foreach ($groups as $group) {
            $attributes = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'attribute`
			WHERE `id_attribute_group` = '. (int)($group['id_attribute_group']));
            $i = 0;
            if (count($attributes) && is_array($attributes)) {
                foreach ($attributes as $attribute) {
                    Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'attribute`
					SET `position` = '.$i++.'
					WHERE `id_attribute` = '.(int)$attribute['id_attribute'].'
					AND `id_attribute_group` = '.(int)$attribute['id_attribute_group']);
                }
            }
        }
    }
}
