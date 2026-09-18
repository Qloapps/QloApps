<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function customization_field_multishop_lang()
{
    $shops = Db::getInstance()->executeS('
		SELECT `id_shop`
		FROM `'._DB_PREFIX_.'shop`
		WHERE `id_shop` != 1
		');

    $customization_field_lang = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'customization_field_lang`
		');

    foreach ($customization_field_lang as $value) {
        $data = array();
        $customization_lang = array(
            'id_customization_field' => $value['id_customization_field'],
            'id_lang' => $value['id_lang'],
            'name' => pSQL($value['name'])
            );
        foreach ($shops as $shop) {
            $customization_lang['id_shop'] = $shop['id_shop'];
            $data[] = $customization_lang;
        }
        Db::getInstance()->insert('customization_field_lang', $data);
    }
}
