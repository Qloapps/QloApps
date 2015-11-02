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

class Datas
{
    private static $instance = null;
    protected static $available_args = array(
        'step' => array(
            'name' => 'step',
            'default' => 'all',
            'validate' => 'isGenericName',
            'help' => 'all / database,fixtures,theme,modules,addons_modules',
        ),
        'language' => array(
            'default' => 'en',
            'validate' => 'isLanguageIsoCode',
            'alias' => 'l',
            'help' => 'language iso code',
        ),
        'all_languages' => array(
            'default' => '0',
            'validate' => 'isInt',
            'alias' => 'l',
            'help' => 'install all available languages',
        ),
        'timezone' => array(
            'default' => 'Europe/Paris',
            'alias' => 't',
        ),
        'base_uri' => array(
            'name' => 'base_uri',
            'validate' => 'isUrl',
            'default' => '/',
        ),
        'http_host' => array(
            'name' => 'domain',
            'validate' => 'isGenericName',
            'default' => 'localhost',
        ),
        'database_server' => array(
            'name' => 'db_server',
            'default' => 'localhost',
            'validate' => 'isGenericName',
            'alias' => 'h',
        ),
        'database_login' => array(
            'name' => 'db_user',
            'alias' => 'u',
            'default' => 'root',
            'validate' => 'isGenericName',
        ),
        'database_password' => array(
            'name' => 'db_password',
            'alias' => 'p',
            'default' => '',
        ),
        'database_name' => array(
            'name' => 'db_name',
            'alias' => 'd',
            'default' => 'prestashop',
            'validate' => 'isGenericName',
        ),
        'database_clear' => array(
            'name' => 'db_clear',
            'default' => '1',
            'validate' => 'isInt',
            'help' => 'Drop existing tables'
        ),
        'database_create' => array(
            'name' => 'db_create',
            'default' => '0',
            'validate' => 'isInt',
            'help' => 'Create the database if not exist'
        ),
        'database_prefix' => array(
            'name' => 'prefix',
            'default' => 'ps_',
            'validate' => 'isGenericName',
        ),
        'database_engine' => array(
            'name' => 'engine',
            'validate' => 'isMySQLEngine',
            'default' => 'InnoDB',
            'help' => 'InnoDB/MyISAM',
        ),
        'shop_name' => array(
            'name' => 'name',
            'validate' => 'isGenericName',
            'default' => 'PrestaShop',
        ),
        'shop_activity'    => array(
            'name' => 'activity',
            'default' => 0,
            'validate' => 'isInt',
        ),
        'shop_country' => array(
            'name' => 'country',
            'validate' => 'isLanguageIsoCode',
            'default' => 'fr',
            ),
        'admin_firstname' => array(
            'name' => 'firstname',
            'validate' => 'isName',
            'default' => 'John',
        ),
        'admin_lastname'    => array(
            'name' => 'lastname',
            'validate' => 'isName',
            'default' => 'Doe',
        ),
        'admin_password' => array(
            'name' => 'password',
            'validate' => 'isPasswd',
            'default' => '0123456789',
        ),
        'admin_email' => array(
            'name' => 'email',
            'validate' => 'isEmail',
            'default' => 'pub@prestashop.com'
        ),
        'show_license' => array(
            'name' => 'license',
            'default' => 0,
            'help' => 'show PrestaShop license'
        ),
        'newsletter' => array(
            'name' => 'newsletter',
            'default' => 1,
            'help' => 'get news from PrestaShop',
        ),
        'send_email' => array(
            'name' => 'send_email',
            'default' => 1,
            'help' => 'send an email to the administrator after installation',
        ),
    );

    protected $datas = array();

    public function __get($key)
    {
        if (isset($this->datas[$key])) {
            return $this->datas[$key];
        }

        return false;
    }

    public function __set($key, $value)
    {
        $this->datas[$key] = $value;
    }

    public static function getInstance()
    {
        if (Datas::$instance === null) {
            Datas::$instance = new Datas();
        }
        return Datas::$instance;
    }

    public static function getArgs()
    {
        return Datas::$available_args;
    }

    public function getAndCheckArgs($argv)
    {
        if (!$argv) {
            return false;
        }

        $args_ok = array();
        foreach ($argv as $arg) {
            if (!preg_match('/^--([^=\'"><|`]+)(?:=([^=><|`]+)|(?!license))/i', trim($arg), $res)) {
                continue;
            }

            if ($res[1] == 'license' && !isset($res[2])) {
                $res[2] = 1;
            } elseif (!isset($res[2])) {
                continue;
            }

            $args_ok[$res[1]] = $res[2];
        }

        $errors = array();
        foreach (Datas::getArgs() as $key => $row) {
            if (isset($row['name'])) {
                $name = $row['name'];
            } else {
                $name = $key;
            }
            if (!isset($args_ok[$name])) {
                if (!isset($row['default'])) {
                    $errors[] = 'Field '.$row['name'].' is empty';
                } else {
                    $this->$key = $row['default'];
                }
            } elseif (isset($row['validate']) && !call_user_func(array('Validate', $row['validate']), $args_ok[$name])) {
                $errors[] = 'Field '.$key.' is not valid';
            } else {
                $this->$key = $args_ok[$name];
            }
        }

        return count($errors) ? $errors : true;
    }
}
