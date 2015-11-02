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

function ps1607_language_code_update()
{
    if (defined('_PS_VERSION_')) {
        $langs = Db::getInstance()->executeS('SELECT `id_lang`, `iso_code`, `language_code` FROM `'._DB_PREFIX_.'lang`');
        if (is_array($langs) && $langs) {
            foreach ($langs as $lang) {
                if (Tools::strlen($lang['language_code']) == 2) {
                    $result = Tools::jsonDecode(Tools::file_get_contents('https://www.prestashop.com/download/lang_packs/get_language_pack.php?version='._PS_VERSION_.'&iso_lang='.Tools::strtolower($lang['iso_code'])));
                    if ($result && !isset($result->error) && Tools::strlen($result->language_code) > 2) {
                        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'lang` SET `language_code` = \''.pSQL($result->language_code).'\' WHERE `id_lang` = '.(int)$lang['id_lang']).' LIMIT 1';
                    }
                }
            }
        }
    }
    return true;
}
