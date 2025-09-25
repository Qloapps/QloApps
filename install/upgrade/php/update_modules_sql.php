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

function update_modules_sql()
{
    $id_blocklink = Db::getInstance()->getValue('SELECT id_module FROM  `'._DB_PREFIX_.'module` WHERE name = "blocklink"');
    if ($id_blocklink) {
        Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'blocklink_lang` ADD PRIMARY KEY (`id_link`, `id_lang`);');
    }
    $id_productcomments = Db::getInstance()->getValue('SELECT id_module FROM  `'._DB_PREFIX_.'module` WHERE name = "productcomments"');
    if ($id_productcomments) {
        Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'product_comment_grade` ADD PRIMARY KEY (`id_product_comment`, `id_product_comment_criterion`);');
        Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'product_comment_criterion` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_product_comment_criterion`, `id_lang`);');
        Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'product_comment_criterion_product` ADD PRIMARY KEY(`id_product`, `id_product_comment_criterion`);');
    }
}
