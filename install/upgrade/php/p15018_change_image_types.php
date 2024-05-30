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

function p15018_change_image_types()
{
    $replace_types = array(
        'products' => array(
            'small' => array('small_default', '98', '98'),
            'medium' => array('medium_default', '125', '125'),
            'large' => array('large_default', '458', '458'),
            'thickbox' => array('thickbox_default', '800', '800'),
            'home' => array('home_default', '270', '270')
        ),
        'others' => array(
            'category' => array('category_default', '870', '217'),
            'large_scene' => array('scene_default', '520', '189'),
            'thumb_scene' => array('m_scene_default', '161', '58')
        )
    );

    $new_types = array(
        'products' => array(
            'small' => array('cart_default', '80', '80')
        )
    );

    foreach ($new_types as $type => $type_array) {
        foreach ($type_array as $old_type => $new_type) {
            if (is_array($new_type) && count($new_type)) {
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'image_type` (
					SELECT NULL, "'.$new_type[0].'", "'.$new_type[1].'", "'.$new_type[2].'", products, categories, manufacturers, suppliers, scenes, stores
					FROM `'._DB_PREFIX_.'image_type` WHERE name = "'.$old_type.'" LIMIT 1)');
            }
        }
    }

    $option = (bool)Db::getInstance()->getValue('SELECT id_theme FROM `'._DB_PREFIX_.'theme` WHERE directory != "default" AND directory != "prestashop"');
        
    // If there is another theme than the default one, duplicate
    if ($option) {
        foreach ($replace_types as $type => $type_array) {
            foreach ($type_array as $old_type => $new_type) {
                if (is_array($new_type) && count($new_type)) {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'image_type` (
					SELECT NULL, "'.$new_type[0].'", "'.$new_type[1].'", "'.$new_type[2].'", products, categories, manufacturers, suppliers, scenes, stores
					FROM `'._DB_PREFIX_.'image_type` WHERE name = "'.$old_type.'" LIMIT 1)');
                }
    // But if there is only the default one, we can update de names
    else {
        foreach ($replace_types as $type => $type_array) {
            foreach ($type_array as $old_type => $new_type) {
                if (is_array($new_type) && count($new_type)) {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image_type` SET name = "'.$new_type[0].'" WHERE name = "'.$old_type.'"');
                }
            }
        }
    }
            }
        }
    }

    // If there is less than 500 images, copy to the new format (if there is more, the merchant will have to click "regenerate thumbnails")
    $result = Db::getInstance()->executeS('SELECT id_image, id_product FROM `'._DB_PREFIX_.'image`');
    if (Db::getInstance()->numRows() < 500) {
        if (!defined('_PS_ROOT_DIR_')) {
            define('_PS_ROOT_DIR_', realpath(INSTALL_PATH.'/../'));
        }
        foreach ($result as $row) {
            if (file_exists(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'p'.DIRECTORY_SEPARATOR.$row['id_product'].'-'.$row['id_image'].'.jpg')) {
                foreach ($replace_types['products'] as $old_type => $new_type) {
                    if (is_array($new_type) && count($new_type)) {
                        p15018_copy_or_rename(
                            _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'p'.DIRECTORY_SEPARATOR.$row['id_product'].'-'.$row['id_image'].'-'.$old_type.'.jpg',
                            _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'p'.DIRECTORY_SEPARATOR.$row['id_product'].'-'.$row['id_image'].'-'.$new_type[0].'.jpg',
                            $option
                        );
                    }
                }
            }
            $folder = implode(DIRECTORY_SEPARATOR, str_split((string)$row['id_image'])).DIRECTORY_SEPARATOR;
            if (file_exists(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'p'.DIRECTORY_SEPARATOR.$folder.$row['id_image'].'.jpg')) {
                foreach ($replace_types['products'] as $old_type => $new_type) {
                    if (is_array($new_type) && count($new_type)) {
                        p15018_copy_or_rename(
                            _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'p'.DIRECTORY_SEPARATOR.$folder.$row['id_image'].'-'.$old_type.'.jpg',
                            _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'p'.DIRECTORY_SEPARATOR.$folder.$row['id_image'].'-'.$new_type[0].'.jpg',
                            $option
                        );
                    }
                }
            }
        }
        
        // Then the other entities (if there is less than 500 products, that should not be a problem)
        $directories = array('p', 'c', 'm', 's', 'su', 'scenes', 'scenes'.DIRECTORY_SEPARATOR.'thumbs', 'st');
        foreach ($directories as $directory) {
            foreach (scandir(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$directory) as $file) {
                if (!preg_match('/^([0-9]+|[a-z]{2}-default)\-[a-z_-]+\.jpg$/i', $file)) {
                    continue;
                }
                foreach ($replace_types as $type => $type_array) {
                    foreach ($type_array as $old_type => $new_type) {
                        if (preg_match('/^([0-9]+|[a-z]{2}-default)\-'.$old_type.'\.jpg$/i', $file, $matches) && is_array($new_type) && count($new_type)) {
                            p15018_copy_or_rename(
                                _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.$matches[1].'-'.$old_type.'.jpg',
                                _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.$matches[1].'-'.$new_type[0].'.jpg',
                                $option
                            );
                        }
                    }
                }
            }
        }
    }

    return true;
}

function p15018_copy_or_rename($from, $to, $option)
{
    if ($option) {
        @copy($from, $to);
    } else {
        @rename($from, $to);
    }
}
