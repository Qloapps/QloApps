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
 * ImportModule class, ImportModule.php
 * Import module management
 * @category classes
 *
 */

abstract class ImportModuleCore extends Module
{
    protected $_link = null;

    public $server;

    public $user;

    public $passwd;

    public $database;

    /** @var string Prefix database */
    public $prefix;


    public function __destruct()
    {
        if ($this->_link) {
            @mysql_close($this->_link);
        }
    }

    protected function initDatabaseConnection()
    {
        if ($this->_link != null) {
            return $this->_link;
        }
        if ($this->_link = mysql_connect($this->server, $this->user, $this->passwd, true)) {
            if (!mysql_select_db($this->database, $this->_link)) {
                die(Tools::displayError('The database selection cannot be made.'));
            }
            if (!mysql_query('SET NAMES \'utf8\'', $this->_link)) {
                die(Tools::displayError('Fatal error: no UTF-8 support. Please check your server configuration.'));
            }
        } else {
            die(Tools::displayError('Link to database cannot be established.'));
        }
        return $this->_link;
    }

    public function ExecuteS($query)
    {
        $this->initDatabaseConnection();
        $result = mysql_query($query, $this->_link);
        $result_array = array();
        if ($result !== true) {
            while ($row = mysql_fetch_assoc($result)) {
                $result_array[] = $row;
            }
        }
        return $result_array;
    }

    public function Execute($query)
    {
        $this->initDatabaseConnection();
        return mysql_query($query, $this->_link);
    }

    public function getValue($query)
    {
        $this->initDatabaseConnection();
        $result = $this->executeS($query);
        if (!count($result)) {
            return 0;
        } else {
            return array_shift($result[0]);
        }
    }

    public static function getImportModulesOnDisk()
    {
        $modules = Module::getModulesOnDisk(true);
        foreach ($modules as $key => $module) {
            if (!isset($module->parent_class) || $module->parent_class != 'ImportModule') {
                unset($modules[$key]);
            }
        }
        return $modules;
    }

    abstract public function getDefaultIdLang();
}
