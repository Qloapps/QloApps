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

/**
 * This function copy all images located in /install/data/img/* that are missing in previous upgrade
 *  in the matching img dir. This does not modify images that are already present.
 *
 */
function p15014_copy_missing_images_tab_from_installer()
{
    $res = true;
    $DIR_SEP = DIRECTORY_SEPARATOR;
    if (!defined('_PS_ROOT_DIR_')) {
        define('_PS_ROOT_DIR_', realpath(INSTALL_PATH.'/../'));
    }

    $install_dir_path = INSTALL_PATH.$DIR_SEP.'data'.$DIR_SEP.'img';
    $img_dir = scandir($install_dir_path);
    foreach ($img_dir as $dir) {
        if ($dir[0] == '.' || !is_dir($install_dir_path.$DIR_SEP.$dir)) {
            continue;
        }

        $img_subdir = scandir($install_dir_path.$DIR_SEP.$dir);
        foreach ($img_subdir as $img) {
            if ($img[0] == '.') {
                continue;
            }
            if (!file_exists(_PS_ROOT_DIR_.$DIR_SEP.'img'.$DIR_SEP.$dir.$DIR_SEP.$img) && file_exists($install_dir_path.$DIR_SEP.$dir.$DIR_SEP.$img)) {
                $res &= copy($install_dir_path.$DIR_SEP.$dir.$DIR_SEP.$img, _PS_ROOT_DIR_.$DIR_SEP.'img'.$DIR_SEP.$dir.$DIR_SEP.$img);
            }
        }
    }

    return $res;
}
