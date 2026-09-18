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

function add_new_tab($className, $name, $id_parent, $returnId = false, $parentTab = null, $module = '')
{
    if (!is_null($parentTab) && !empty($parentTab) && strtolower(trim($parentTab)) !== 'null') {
        $id_parent = (int)Db::getInstance()->getValue('SELECT `id_tab` FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \''.pSQL($parentTab).'\'');
    }

    $array = array();
    foreach (explode('|', $name) as $item) {
        $temp = explode(':', $item);
        $array[$temp[0]] = $temp[1];
    }

    if (!(int)Db::getInstance()->getValue('SELECT count(id_tab) FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \''.pSQL($className).'\' ')) {
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'tab` (`id_parent`, `class_name`, `module`, `position`) VALUES ('.(int)$id_parent.', \''.pSQL($className).'\', \''.pSQL($module).'\',
									(SELECT IFNULL(MAX(t.position),0)+ 1 FROM `'._DB_PREFIX_.'tab` t WHERE t.id_parent = '.(int)$id_parent.'))');
    }

    $languages = Db::getInstance()->executeS('SELECT id_lang, iso_code FROM `'._DB_PREFIX_.'lang`');
    foreach ($languages as $lang) {
        Db::getInstance()->execute('
		INSERT IGNORE INTO `'._DB_PREFIX_.'tab_lang` (`id_lang`, `id_tab`, `name`)
		VALUES ('.(int)$lang['id_lang'].', (
				SELECT `id_tab`
				FROM `'._DB_PREFIX_.'tab`
				WHERE `class_name` = \''.pSQL($className).'\' LIMIT 0,1
			), \''.pSQL(isset($array[$lang['iso_code']]) ? $array[$lang['iso_code']] : $array['en']).'\')
		');
    }

    Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`)
								(SELECT `id_profile`, (
								SELECT `id_tab`
								FROM `'._DB_PREFIX_.'tab`
								WHERE `class_name` = \''.pSQL($className).'\' LIMIT 0,1
								), 1, 1, 1, 1 FROM `'._DB_PREFIX_.'profile` )');

    if ($returnId && strtolower(trim($returnId)) !== 'false') {
        return (int)Db::getInstance()->getValue('SELECT `id_tab`
								FROM `'._DB_PREFIX_.'tab`
								WHERE `class_name` = \''.pSQL($className).'\'');
    }
}
