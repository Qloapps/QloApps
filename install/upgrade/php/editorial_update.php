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

function editorial_update()
{
    /*Table creation*/

    if (Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name`="editorial"')) {
        Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'editorial` (
		`id_editorial` int(10) unsigned NOT NULL auto_increment,
		`body_home_logo_link` varchar(255) NOT NULL,
		PRIMARY KEY (`id_editorial`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');

        Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'editorial_lang` (
		`id_editorial` int(10) unsigned NOT NULL,
		`id_lang` int(10) unsigned NOT NULL,
		`body_title` varchar(255) NOT NULL,
		`body_subheading` varchar(255) NOT NULL,
		`body_paragraph` text NOT NULL,
		`body_logo_subheading` varchar(255) NOT NULL,
		PRIMARY KEY (`id_editorial`, `id_lang`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');

        if (file_exists(dirname(__FILE__).'/../../../modules/editorial/editorial.xml')) {
            $xml = @simplexml_load_file(dirname(__FILE__).'/../../../modules/editorial/editorial.xml');
            if (!$xml) {
                return false;
            }
            Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'editorial`(`id_editorial`, `body_home_logo_link`) VALUES(1, "'.(isset($xml->body->home_logo_link) ? pSQL($xml->body->home_logo_link) : '').'")');

            $languages = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'lang`');
            foreach ($languages as $language) {
                Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'editorial_lang` (`id_editorial`, `id_lang`, `body_title`, `body_subheading`, `body_paragraph`, `body_logo_subheading`)
				VALUES (1, '.(int)($language['id_lang']).',
				"'.(isset($xml->body->{'title_'.$language['id_lang']}) ? pSQL($xml->body->{'title_'.$language['id_lang']}) : '').'",
				"'.(isset($xml->body->{'subheading_'.$language['id_lang']}) ? pSQL($xml->body->{'subheading_'.$language['id_lang']}) : '').'",
				"'.(isset($xml->body->{'paragraph_'.$language['id_lang']}) ? pSQL($xml->body->{'paragraph_'.$language['id_lang']}, true) : '').'",
				"'.(isset($xml->body->{'logo_subheading_'.$language['id_lang']}) ? pSQL($xml->body->{'logo_subheading_'.$language['id_lang']}) : '').'")');
            }

            unlink(dirname(__FILE__).'/../../../modules/editorial/editorial.xml');
        }
    }
}
