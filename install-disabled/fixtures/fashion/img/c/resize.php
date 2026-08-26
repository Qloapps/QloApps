<?php

include('../../../../../config/config.inc.php');
ini_set('max_execution_time', 7200);
ini_set('memory_limit', '512M');

$types = ImageType::getImagesTypes('categories');
$files = scandir(dirname(__FILE__));
foreach ($files as $file) {
    if (preg_match('/^([a-z0-9-_]+)\.jpg$/i', $file, $match) && !preg_match('/default\.jpg$/i', $file)) {
        foreach ($types as $type) {
            if (!file_exists($match[1].'-'.$type['name'].'.jpg')) {
                ImageManager::resize($file, $match[1].'-'.$type['name'].'.jpg', $type['width'], $type['height'], 'jpg', true);
            }
        }
    }
}
