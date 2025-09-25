<?php
/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class InstallModelDatabase extends InstallAbstractModel
{
    /**
     * Check database configuration and try a connection
     *
     * @param string $server
     * @param string $database
     * @param string $login
     * @param string $password
     * @param string $prefix
     * @param string $engine
     * @param bool $clear
     * @return array List of errors
     */
    public function testDatabaseSettings($server, $database, $login, $password, $prefix, $clear = false)
    {
        $errors = array();

        // Check if fields are correctly typed
        if (!$server || !Validate::isUrl($server)) {
            $errors[] = $this->language->l('Server name is not valid');
        }

        if (!$database) {
            $errors[] = $this->language->l('You must enter a database name');
        }

        if (!$login) {
            $errors[] = $this->language->l('You must enter a database login');
        }

        if ($prefix && !Validate::isTablePrefix($prefix)) {
            $errors[] = $this->language->l('Tables prefix is invalid');
        }

        if (!$errors) {
            $dbtype = ' ('.Db::getClass().')';
            // Try to connect to database
            switch (Db::checkConnection($server, $login, $password, $database, true)) {
                case 0:
                    if (!Db::checkEncoding($server, $login, $password)) {
                        $errors[] = $this->language->l('Cannot convert database data to utf-8').$dbtype;
                    }

                    // Check if a table with same prefix already exists
                    if (!$clear && Db::hasTableWithSamePrefix($server, $login, $password, $database, $prefix)) {
                        $errors[] = $this->language->l('At least one table with same prefix was already found, please change your prefix or drop your database');
                    }
                    if (!Db::checkAutoIncrement($server, $login, $password)) {
                        $errors[] = $this->language->l('The values of auto_increment increment and offset must be set to 1');
                    }
                    if (($create_error = Db::checkCreatePrivilege($server, $login, $password, $database, $prefix)) !== true) {
                        $errors[] = $this->language->l(sprintf('Your database login does not have the privileges to create table on the database "%s". Ask your hosting provider:', $database));
                        if ($create_error != false) {
                            $errors[] = $create_error;
                        }
                    }
                    break;

                case 1:
                    $errors[] = $this->language->l('Database Server is not found. Please verify the login, password and server fields').$dbtype;
                    break;

                case 2:
                    $error = $this->language->l('Connection to MySQL server succeeded, but database "%s" not found', $database).$dbtype;
                    if ($this->createDatabase($server, $database, $login, $password, true)) {
                        $error .= '<p>'.sprintf('<input type="button" value="%s" class="button" id="btCreateDB">', $this->language->l('Attempt to create the database automatically')).'</p>
						<script type="text/javascript">bindCreateDB();</script>';
                    }
                    $errors[] = $error;
                    break;
            }
        }

        return $errors;
    }

    public function createDatabase($server, $database, $login, $password, $dropit = false)
    {
        $class = Db::getClass();
        return call_user_func(array($class, 'createDatabase'), $server, $login, $password, $database, $dropit);
    }

    public function getBestEngine($server, $database, $login, $password)
    {
        $class = Db::getClass();
        $instance = new $class($server, $login, $password, $database, true);
        $engine = $instance->getBestEngine();
        unset($instance);
        return $engine;
    }
}
