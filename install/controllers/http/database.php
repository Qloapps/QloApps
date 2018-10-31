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
 * Step 3 : configure database and email connection
 */
class InstallControllerHttpDatabase extends InstallControllerHttp
{
    /**
     * @var InstallModelDatabase
     */
    public $model_database;

    /**
     * @var InstallModelMail
     */
    public $model_mail;

    public function init()
    {
        require_once _PS_INSTALL_MODELS_PATH_.'database.php';
        $this->model_database = new InstallModelDatabase();
    }

    /**
     * @see InstallAbstractModel::processNextStep()
     */
    public function processNextStep()
    {
        // Save database config
        $this->session->database_server = trim(Tools::getValue('dbServer'));
        $this->session->database_name = trim(Tools::getValue('dbName'));
        $this->session->database_login = trim(Tools::getValue('dbLogin'));
        $this->session->database_password = trim(Tools::getValue('dbPassword'));
        $this->session->database_prefix = trim(Tools::getValue('db_prefix'));
        $this->session->database_clear = Tools::getValue('database_clear');

        $this->session->rewrite_engine = Tools::getValue('rewrite_engine');
    }

    /**
     * Database configuration must be valid to validate this step
     *
     * @see InstallAbstractModel::validate()
     */
    public function validate()
    {
        $this->errors = $this->model_database->testDatabaseSettings(
            $this->session->database_server,
            $this->session->database_name,
            $this->session->database_login,
            $this->session->database_password,
            $this->session->database_prefix,
            // We do not want to validate table prefix if we are already in install process
            ($this->session->step == 'process') ? true : $this->session->database_clear
        );
        if (count($this->errors)) {
            return false;
        }

        if (!isset($this->session->database_engine)) {
            $this->session->database_engine = $this->model_database->getBestEngine($this->session->database_server, $this->session->database_name, $this->session->database_login, $this->session->database_password);
        }
        return true;
    }

    public function process()
    {
        if (Tools::getValue('checkDb')) {
            $this->processCheckDb();
        } elseif (Tools::getValue('createDb')) {
            $this->processCreateDb();
        }
    }

    /**
     * Check if a connection to database is possible with these data
     */
    public function processCheckDb()
    {
        $server = Tools::getValue('dbServer');
        $database = Tools::getValue('dbName');
        $login = Tools::getValue('dbLogin');
        $password = Tools::getValue('dbPassword');
        $prefix = Tools::getValue('db_prefix');
        $clear = Tools::getValue('clear');

        $errors = $this->model_database->testDatabaseSettings($server, $database, $login, $password, $prefix, $clear);

        $this->ajaxJsonAnswer(
            (count($errors)) ? false : true,
            (count($errors)) ? implode('<br />', $errors) : $this->l('Database is connected')
        );
    }

    /**
     * Attempt to create the database
     */
    public function processCreateDb()
    {
        $server = Tools::getValue('dbServer');
        $database = Tools::getValue('dbName');
        $login = Tools::getValue('dbLogin');
        $password = Tools::getValue('dbPassword');

        $success = $this->model_database->createDatabase($server, $database, $login, $password);

        $this->ajaxJsonAnswer(
            $success,
            $success ? $this->l('Database is created') : $this->l('Cannot create the database automatically')
        );
    }

    /**
     * @see InstallAbstractModel::display()
     */
    public function display()
    {
        if (!$this->session->database_server) {
            if (file_exists(_PS_ROOT_DIR_.'/config/settings.inc.php')) {
                @include_once _PS_ROOT_DIR_.'/config/settings.inc.php';
                $this->database_server = _DB_SERVER_;
                $this->database_name = _DB_NAME_;
                $this->database_login = _DB_USER_;
                $this->database_password = _DB_PASSWD_;
                $this->database_engine = _MYSQL_ENGINE_;
                $this->database_prefix = _DB_PREFIX_;
            } else {
                $this->database_server = 'localhost';
                $this->database_name = 'qloapps';
                $this->database_login = 'root';
                $this->database_password = '';
                $this->database_engine = 'InnoDB';
                $this->database_prefix = 'ps_';
            }

            $this->database_clear = true;
            $this->use_smtp = false;
            $this->smtp_encryption = 'off';
            $this->smtp_port = 25;
        } else {
            $this->database_server = $this->session->database_server;
            $this->database_name = $this->session->database_name;
            $this->database_login = $this->session->database_login;
            $this->database_password = $this->session->database_password;
            $this->database_engine = $this->session->database_engine;
            $this->database_prefix = $this->session->database_prefix;
            $this->database_clear = $this->session->database_clear;

            $this->use_smtp = $this->session->use_smtp;
            $this->smtp_encryption = $this->session->smtp_encryption;
            $this->smtp_port = $this->session->smtp_port;
        }

        $this->displayTemplate('database');
    }
}
