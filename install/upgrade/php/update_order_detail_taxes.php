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

function update_order_detail_taxes()
{
    $order_detail_taxes = Db::getInstance()->executeS('
	SELECT `id_order_detail`, `tax_name`, `tax_rate` FROM `'._DB_PREFIX_.'order_detail`
	');
    $id_lang_list = Db::getInstance()->executeS('SELECT id_lang FROM `'._DB_PREFIX_.'lang`');

    foreach ($order_detail_taxes as $order_detail_tax) {
        if ($order_detail_tax['tax_rate'] == '0.000') {
            continue;
        }

        $alternative_tax_name = 'Tax '.$order_detail_tax['tax_rate'];
        $create_tax = true;
        $id_tax = (int)Db::getInstance()->getValue('SELECT t.`id_tax`
			FROM `'._DB_PREFIX_.'tax` t
			LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (tl.id_tax = t.id_tax)
			WHERE tl.`name` = \''.pSQL($order_detail_tax['tax_name']).'\' ');
        $id_tax_alt = (int)Db::getInstance()->getValue('SELECT t.`id_tax`
			FROM `'._DB_PREFIX_.'tax` t
			LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (tl.id_tax = t.id_tax)
			WHERE tl.`name` = \''.pSQL($alternative_tax_name).'\' ');
            
        if ($id_tax || $id_tax_alt) {
            $create_tax = !(bool)Db::getInstance()->getValue('SELECT count(*)
				FROM `'._DB_PREFIX_.'tax`
				WHERE id_tax = '. (int)$id_tax .'
					AND rate = "'.pSql($order_detail_tax['tax_rate']).'"
			');
        }

        if ($create_tax) {
            $tax_name = (isset($order_detail_tax['tax_name']) ? $order_detail_tax['tax_name'] : $alternative_tax_name);

            Db::getInstance()->execute(
            'INSERT INTO `'._DB_PREFIX_.'tax` (`rate`, `active`, `deleted`)
			VALUES (\''.(float)$order_detail_tax['tax_rate'].'\', 0, 1)'
            );

            $id_tax = Db::getInstance()->Insert_ID();
            foreach ($id_lang_list as $id_lang) {
                Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'tax_lang` (`id_tax`, `id_lang`, `name`)
				VALUES ('.(int)$id_tax.','.(int)$id_lang['id_lang'].',\''.pSQL($tax_name).'\')
				');
            }
        }

        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'order_detail_tax` (`id_order_detail`, `id_tax`)
		VALUES ('.(int)$order_detail_tax['id_order_detail'].','.$id_tax.')
		');
    }
}
