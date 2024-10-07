<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

/**
 * updates existing feature images from png to jpg for Qlo 1.5.0
 *
 * @return void
 */
function change_feature_image_to_jpg()
{
    $featuresFilePath = _PS_IMG_DIR_.'rf/';
    $files = scandir($featuresFilePath);
    foreach ($files as $file) {
        if ($file[0] === '.') {
            continue;
        }

        if (strpos($file, '.png')) {
            if(ImageManager::resize($featuresFilePath.$file, $featuresFilePath.explode('.', $file)[0].'.jpg')) {
                @unlink($featuresFilePath.$file);

            }
        }
    }
    return true;
}