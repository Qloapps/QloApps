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

function add_missing_image_key()
{
    $res = true;
    $key_exists = Db::getInstance()->executeS('SHOW INDEX
		FROM `'._DB_PREFIX_.'image`
		WHERE Key_name = \'idx_product_image\'');
    if ($key_exists) {
        $res &= Db::getInstance()->execute('ALTER TABLE
		`'._DB_PREFIX_.'image`
		DROP KEY `idx_product_image`');
    }
    $res &= Db::getInstance()->execute('ALTER TABLE
	`'._DB_PREFIX_.'image`
	ADD UNIQUE `idx_product_image` (`id_image`, `id_product`, `cover`)');

    return $res;
}
