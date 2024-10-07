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

function generate_new_cookie_key()
{
    $_SETTINGS_FILE = 'config/settings.inc.php';
    $root_dir = realpath(__DIR__.'/../../../../../../');

    require_once $root_dir.'/tools/random_compat/lib/random.php';
    $key = \Defuse\Crypto\Key::createNewRandomKey();
    $new_cookie_key = $key->saveToAsciiSafeString();

    $fd = fopen($root_dir.'/'.$_SETTINGS_FILE, 'a');
    fwrite($fd, "define('_NEW_COOKIE_KEY_', '$new_cookie_key');" . PHP_EOL);
    fclose($fd);

    return true;
}