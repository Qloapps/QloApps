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

function update_order_messages()
{
    $step = 3000;
    $count_messages = Db::getInstance()->getValue('SELECT count(id_message) FROM '._DB_PREFIX_.'message');
    $nb_loop = $start = 0;
    $pattern = '<br|&[a-zA-Z]{1,8};';
    if ($count_messages > 0) {
        $nb_loop = ceil($count_messages / $step);
    }
    for ($i = 0; $i < $nb_loop; $i++) {
        $sql = 'SELECT id_message, message FROM `'._DB_PREFIX_.'message` WHERE message REGEXP \''.pSQL($pattern, true).'\' LIMIT '.(int)$start.', '.(int)$step;
        $start = intval(($i+1) * $step);
        if ($messages = Db::getInstance()->query($sql)) {
            while ($message = Db::getInstance()->nextRow($messages)) {
                if (is_array($message)) {
                    Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'message`
					SET message = \''.pSQL(preg_replace('/'.$pattern.'/', '', Tools::htmlentitiesDecodeUTF8(br2nl($message['message'])))).'\'
					WHERE id_message = '.(int)$message['id_message']);
                }
            }
        }
    }
    $nb_loop = $start = 0;
    if ($count_messages > 0) {
        $nb_loop = ceil($count_messages / $step);
    }
    for ($i = 0; $i < $nb_loop; $i++) {
        $sql = 'SELECT id_customer_message, message FROM `'._DB_PREFIX_.'customer_message` WHERE message REGEXP \''.pSQL($pattern, true).'\' LIMIT '.(int)$start.', '.(int)$step;
        $start = intval(($i+1) * $step);
        if ($messages = Db::getInstance()->query($sql)) {
            while ($message = Db::getInstance()->nextRow($messages)) {
                if (is_array($message)) {
                    Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'customer_message`
					SET message = \''.pSQL(preg_replace('/'.$pattern.'/', '', Tools::htmlentitiesDecodeUTF8(str_replace('&amp;', '&', $message['message'])))).'\'
					WHERE id_customer_message = '.(int)$message['id_customer_message']);
                }
            }
        }
    }
}

function br2nl($str)
{
    return str_replace(array('<br>', '<br />', '<br/>'), "\n", $str);
}
