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

function reorderpositions()
{
    $res = true;
    $ps_lang_default = Db::getInstance()->getValue('SELECT value
		FROM `'._DB_PREFIX_.'configuration`
		WHERE name="PS_LANG_DEFAULT"');
    /* Clean products positions */
    $cat = Db::getInstance()->executeS('SELECT id_category FROM `'._DB_PREFIX_.'category`');
    if ($cat) {
        foreach ($cat as $categ) {
            $id_category = $categ['id_category'];
            $result = Db::getInstance()->executeS('
				SELECT `id_product`
				FROM `'._DB_PREFIX_.'category_product`
				WHERE `id_category` = '.$id_category.'
				ORDER BY `position`');
            $sizeof = sizeof($result);
            for ($i = 0; $i < $sizeof; $i++) {
                $res &= Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'category_product`
					SET `position` = '.$i.'
					WHERE `id_category` = '.$id_category.'
					AND `id_product` = '.(int)($result[$i]['id_product']));
            }
        }
    }
    
    $cat_parent = Db::getInstance()->executeS('SELECT DISTINCT c.id_parent FROM `'._DB_PREFIX_.'category` c WHERE id_category != 1');
    foreach ($cat_parent as $parent) {
        $result = Db::getInstance()->executeS('
							SELECT DISTINCT c.*, cl.*
							FROM `'._DB_PREFIX_.'category` c
							LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.$ps_lang_default.')
							WHERE c.id_parent = '.(int)($parent['id_parent']).'
							ORDER BY name ASC');
        foreach ($result as $i => $categ) {
            Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'category`
			SET `position` = '.(int)($i).'
			WHERE `id_parent` = '.(int)($categ['id_parent']).'
			AND `id_category` = '.(int)($categ['id_category']));
        }
        
        $result = Db::getInstance()->executeS('
							SELECT DISTINCT c.*, cl.*
							FROM `'._DB_PREFIX_.'category` c
							LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`)
							WHERE c.id_parent = '.(int)($parent['id_parent']).'
							ORDER BY name ASC');
        
        // Remove number from category name
        foreach ($result as $i => $categ) {
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`)
			SET `name` = \''.preg_replace('/^[0-9]+\./', '', $categ['name']).'\'
			WHERE c.id_category = '.(int)($categ['id_category']).' AND id_lang = \''.(int)($categ['id_lang']).'\'');
        }
    }
    
    /* Clean CMS positions */
    $cms_cat = Db::getInstance()->executeS('SELECT id_cms_category FROM `'._DB_PREFIX_.'cms_category` WHERE active=1');
    if ($cms_cat) {
        foreach ($cms_cat as $i => $categ) {
            $id_category_parent = $categ['id_cms_category'];
            $result &= Db::getInstance()->executeS('
				SELECT `id_cms_category`
				FROM `'._DB_PREFIX_.'cms_category`
				WHERE `id_parent` = '.(int)$id_category_parent.'
				ORDER BY `position`');
            $sizeof = sizeof($result);
            for ($i = 0; $i < $sizeof; ++$i) {
                $sql = 'UPDATE `'._DB_PREFIX_.'cms_category`
						SET `position` = '.(int)$i.'
						WHERE `id_parent` = '.(int)$id_category_parent.'
						AND `id_cms_category` = '.(int)$result[$i]['id_cms_category'];
                $res &= Db::getInstance()->execute($sql);
            }
        }
    }
}
