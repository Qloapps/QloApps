<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
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