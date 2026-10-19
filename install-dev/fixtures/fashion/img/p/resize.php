<?php

include('../../../../../config/config.inc.php');
ini_set('max_execution_time', 7200);
ini_set('memory_limit', '512M');

$types = ImageType::getImagesTypes('products');
$files = scandir(dirname(__FILE__));
foreach ($files as $file) {
    if (preg_match('/^Fotolia_([0-9]+)_X\.jpg$/i', $file, $match)) {
        foreach ($types as $type) {
            if (!file_exists($match[1].'-'.$type['name'].'.jpg')) {
                ImageManager::resize($file, $match[1].'-'.$type['name'].'.jpg', $type['width'], $type['height'], 'jpg', true);
            }
        }
        //if (!file_exists($match[1].'.jpg'))
        {
            //copy($file, $match[1].'.jpg');
            ImageManager::resize($file, $match[1].'.jpg', 800, 800, 'jpg', true);
        }
    }
}
