<?php
/** Copyright since 2007 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2007 Webkul IN
*  @license   https://store.webkul.com/license.html
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