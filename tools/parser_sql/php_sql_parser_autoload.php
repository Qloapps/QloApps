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

require_once __DIR__.'/PHPSQLParser.php';

function classAutoLoader($class)
{
    // check if php sql parser file is called
    if(0 !== strpos($class, 'PHPSQLParser')) {
        return;
    }

    $className = end(explode('\\', $class));
    if(class_exists($className)) {
        return;
    }

    $class = str_replace('PHPSQLParser\\', '', $class);

    // create path for required file
    $classFile = __DIR__.'/'.str_replace('\\', '/', $class).'.php';

    if (!file_exists($classFile) || !is_file($classFile)) {
        return;
    }

    require $classFile;
}

spl_autoload_register('classAutoLoader');