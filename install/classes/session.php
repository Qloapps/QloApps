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
 * Manage session for install script
 */
class InstallSession
{
    protected static $_instance;
    protected static $_cookie_mode = false;
    protected static $_cookie = false;

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        session_name('install_'.substr(md5($_SERVER['HTTP_HOST']), 0, 12));
        $session_started = session_start();
        if (!($session_started)
        || (!isset($_SESSION['session_mode']) && (isset($_GET['_']) || isset($_POST['submitNext']) || isset($_POST['submitPrevious']) || isset($_POST['language'])))) {
            InstallSession::$_cookie_mode = true;
            InstallSession::$_cookie = new Cookie('ps_install', null, time() + 7200, null, true);
        }
        if ($session_started && !isset($_SESSION['session_mode'])) {
            $_SESSION['session_mode'] = 'session';
            session_write_close();
        }
    }

    public function clean()
    {
        if (InstallSession::$_cookie_mode) {
            InstallSession::$_cookie->logout();
        } else {
            foreach ($_SESSION as $k => $v) {
                unset($_SESSION[$k]);
            }
        }
    }

    public function &__get($varname)
    {
        if (InstallSession::$_cookie_mode) {
            $ref = InstallSession::$_cookie->{$varname};
            if (0 === strncmp($ref, 'serialized_array:', strlen('serialized_array:'))) {
                $ref = unserialize(substr($ref, strlen('serialized_array:')));
            }
        } else {
            if (isset($_SESSION[$varname])) {
                $ref = &$_SESSION[$varname];
            } else {
                $null = null;
                $ref = &$null;
            }
        }
        return $ref;
    }

    public function __set($varname, $value)
    {
        if (InstallSession::$_cookie_mode) {
            if ($varname == 'xml_loader_ids') {
                return;
            }
            if (is_array($value)) {
                $value = 'serialized_array:'.serialize($value);
            }
            InstallSession::$_cookie->{$varname} = $value;
        } else {
            $_SESSION[$varname] = $value;
        }
    }

    public function __isset($varname)
    {
        if (InstallSession::$_cookie_mode) {
            return isset(InstallSession::$_cookie->{$varname});
        } else {
            return isset($_SESSION[$varname]);
        }
    }

    public function __unset($varname)
    {
        if (InstallSession::$_cookie_mode) {
            unset(InstallSession::$_cookie->{$varname});
        } else {
            unset($_SESSION[$varname]);
        }
    }
}
