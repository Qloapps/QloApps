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

function add_order_state($conf_name, $name, $invoice, $send_email, $color, $unremovable, $logable, $delivery, $template = null)
{
    $res = true;
    $name_lang = array();
    $template_lang = array();
    foreach (explode('|', $name) as $item) {
        $temp = explode(':', $item);
        $name_lang[$temp[0]] = $temp[1];
    }

    if ($template) {
        foreach (explode('|', $template) as $item) {
            $temp = explode(':', $item);
            $template_lang[$temp[0]] = $temp[1];
        }
    }

    $res &= Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'order_state` (`invoice`, `send_email`, `color`, `unremovable`, `logable`, `delivery`)
		VALUES ('.(int)$invoice.', '.(int)$send_email.', "'.$color.'", '.(int)$unremovable.', '.(int)$logable.', '.(int)$delivery.')');

    $id_order_state = Db::getInstance()->getValue('
		SELECT MAX(`id_order_state`)
		FROM `'._DB_PREFIX_.'order_state`');

    $languages = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'lang`');
    foreach ($languages as $lang) {
        $iso_code = $lang['iso_code'];
        $iso_code_name = isset($name_lang[$iso_code])?$iso_code:'en';
        $iso_code_template = isset($template_lang[$iso_code])?$iso_code:'en';
        $name = isset($name_lang[$iso_code]) ? $name_lang[$iso_code] : $name_lang['en'];
        $template = isset($template_lang[$iso_code]) ? $template_lang[$iso_code] : '';

        $res &= Db::getInstance()->execute('
		INSERT IGNORE INTO `'._DB_PREFIX_.'order_state_lang` (`id_lang`, `id_order_state`, `name`, `template`)
		VALUES ('.(int)$lang['id_lang'].', '.(int)$id_order_state.', "'. $name .'", "'. $template .'")
		');
    }

    $exist = Db::getInstance()->getValue('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \''.pSQL($conf_name).'\'');
    if ($exist) {
        $res &= Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)$id_order_state.'" WHERE `name` LIKE \''.pSQL($conf_name).'\'');
    } else {
        $res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (name, value) VALUES ("'.pSQL($conf_name).'", "'.(int)$id_order_state.'"');
    }
}
