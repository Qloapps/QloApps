<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class GamificationTools
{
    public static function parseMetaData($content)
    {
        $meta_data = array(
            'PREFIX_' => _DB_PREFIX_,
            );
        //replace define
        $content = str_replace(array_keys($meta_data), array_values($meta_data), $content);
        
        //replace meta data
        $content = preg_replace_callback('#\{config\}([a-zA-Z0-9_-]*)\{/config\}#', create_function('$matches', 'return Configuration::get($matches[1]);'), $content);
        $content = preg_replace_callback('#\{link\}(.*)\{/link\}#', create_function('$matches', 'return Context::getContext()->link->getAdminLink($matches[1]);'), $content);
        $content = preg_replace_callback('#\{employee\}(.*)\{/employee\}#', create_function('$matches', 'return Context::getContext()->employee->$matches[1];'), $content);
        $content = preg_replace_callback('#\{language\}(.*)\{/language\}#', create_function('$matches', 'return Context::getContext()->language->$matches[1];'), $content);
        $content = preg_replace_callback('#\{country\}(.*)\{/country\}#', create_function('$matches', 'return Context::getContext()->country->$matches[1];'), $content);
        
        return $content;
    }
}
