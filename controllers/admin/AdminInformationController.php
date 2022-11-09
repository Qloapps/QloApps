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

class AdminInformationControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        $this->show_toolbar = false;
        $this->display = 'view';
        parent::initContent();
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['back']);
    }

    public function renderView()
    {
        $this->initPageHeaderToolbar();

        $hosting_vars = array();
        if (!defined('_PS_HOST_MODE_')) {
            $hosting_vars = array(
                'version' => array(
                    'php' => phpversion(),
                    'server' => $_SERVER['SERVER_SOFTWARE'],
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time')
                ),
                'database' => array(
                    'version' => Db::getInstance()->getVersion(),
                    'server' => _DB_SERVER_,
                    'name' => _DB_NAME_,
                    'user' => _DB_USER_,
                    'prefix' => _DB_PREFIX_,
                    'engine' => _MYSQL_ENGINE_,
                    'driver' => Db::getClass(),
                ),
                'uname' => function_exists('php_uname') ? php_uname('s').' '.php_uname('v').' '.php_uname('m') : '',
                'apache_instaweb' => Tools::apacheModExists('mod_instaweb')
            );
        }

        $shop_vars = array(
            'shop' => array(
                'ps' => _PS_VERSION_,
                'qloappsV' => _QLOAPPS_VERSION_,
                'url' => $this->context->shop->getBaseURL(),
                'theme' => $this->context->shop->theme_name,
            ),
            'mail' => Configuration::get('PS_MAIL_METHOD') == 1,
            'smtp' => array(
                'server' => Configuration::get('PS_MAIL_SERVER'),
                'user' => Configuration::get('PS_MAIL_USER'),
                'password' => Configuration::get('PS_MAIL_PASSWD'),
                'encryption' => Configuration::get('PS_MAIL_SMTP_ENCRYPTION'),
                'port' => Configuration::get('PS_MAIL_SMTP_PORT'),
            ),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        );

        $this->tpl_view_vars = array_merge($this->getTestResult(), array_merge($hosting_vars, $shop_vars));

        return parent::renderView();
    }

    /**
     * get all tests
     *
     * @return array of test results
     */
    public function getTestResult()
    {
        $tests_errors = array(
            'phpversion' => $this->l('The required PHP version is between 5.6 to 7.4.'),
            'upload' => $this->l('Configure your server to allow file uploads.'),
            'system' => $this->l('Configure your server to allow the creation of directories and files with write permissions.'),
            'gd' => $this->l('Enable the GD library on your server.'),
            'mysql_support' => $this->l('Enable the MySQL support on your server.'),
            'config_dir' => $this->l('Set write permissions for the "config" folder.'),
            'cache_dir' => $this->l('Set write permissions for the "cache" folder.'),
            'sitemap' => $this->l('Set write permissions for the "sitemap.xml" file.'),
            'img_dir' => $this->l('Set write permissions for the "img" folder and subfolders.'),
            'log_dir' => $this->l('Set write permissions for the "log" folder and subfolders.'),
            'mails_dir' => $this->l('Set write permissions for the "mails" folder and subfolders.'),
            'module_dir' => $this->l('Set write permissions for the "modules" folder and subfolders.'),
            'theme_lang_dir' => sprintf($this->l('Set the write permissions for the "themes/%s/lang/" folder and subfolders, recursively.'), _THEME_NAME_),
            'theme_pdf_lang_dir' => sprintf($this->l('Set the write permissions for the "themes/%s/pdf/lang/" folder and subfolders, recursively.'), _THEME_NAME_),
            'theme_cache_dir' => sprintf($this->l('Set the write permissions for the "themes/%s/cache/" folder and subfolders, recursively.'), _THEME_NAME_),
            'translations_dir' => $this->l('Set write permissions for the "translations" folder and subfolders.'),
            'customizable_products_dir' => $this->l('Set write permissions for the "upload" folder and subfolders.'),
            'virtual_products_dir' => $this->l('Set write permissions for the "download" folder and subfolders.'),
            'fopen' => $this->l('Allow the PHP fopen() function on your server.'),
            'register_globals' => $this->l('Set PHP "register_globals" option to "Off".'),
            'gz' => $this->l('Enable GZIP compression on your server.'),
            'files' => $this->l('Some QloApps files are missing from your server.'),
            'new_phpversion' => sprintf($this->l('You are using PHP %s version. Soon, the latest PHP version supported by QloApps will be PHP 5.6. To make sure you’re ready for the future, we recommend you to upgrade to PHP 5.6 now!'), phpversion()),
            'pdo_mysql' => $this->l('PDO MySQL extension is not loaded.'),
            'openssl' => $this->l('PHP OpenSSL extension is not loaded.'),
            'curl' => $this->l('PHP Curl extension is not loaded.'),
            'soap' => $this->l('PHP SOAP extension is not loaded.'),
            'simplexml' => $this->l('PHP SimpleXml extension is not loaded.'),
            'memory_limit' => $this->l('In PHP configuration set memory_limit to minimum 128M.'),
            'upload_max_filesize' => $this->l('In PHP configuration set upload_max_filesize to minimum 16M.'),
            'max_execution_time' => $this->l('In PHP configuration set max_execution_time to minimum 500.'),
            'mbstring' => $this->l('PHP Mbstring extension is not enabled.'),
            'magicquotes' => $this->l('PHP magic quotes option is enabled.'),
            'dom' => $this->l('PHP DOM extension is not loaded.'),
        );

        // Functions list to test with 'test_system'
        // Test to execute (function/args): lets uses the default test
        $params_required_results = ConfigurationTest::check(ConfigurationTest::getDefaultTests());

        if (!defined('_PS_HOST_MODE_')) {
            $params_optional_results = ConfigurationTest::check(ConfigurationTest::getDefaultTestsOp());
        }

        $fail_required = in_array('fail', $params_required_results);

        if ($fail_required && $params_required_results['files'] != 'ok') {
            $tmp = ConfigurationTest::test_files(true);
            if (is_array($tmp) && count($tmp)) {
                $tests_errors['files'] = $tests_errors['files'].'<br/>('.implode(', ', $tmp).')';
            }
        }

        $results = array(
            'failRequired' => $fail_required,
            'testsErrors' => $tests_errors,
            'testsRequired' => $params_required_results,
        );

        if (!defined('_PS_HOST_MODE_')) {
            $results = array_merge($results, array(
                'failOptional' => in_array('fail', $params_optional_results),
                'testsOptional' => $params_optional_results,
            ));
        }

        return $results;
    }

    public function displayAjaxCheckFiles()
    {
        $this->file_list = array('missing' => array(), 'updated' => array());
        $xml = @simplexml_load_file(_QLO_API_URL_.'/xml/md5/'._QLOAPPS_VERSION_.'.xml');
        if (!$xml || !isset($xml->ps_root_dir[0])) {
            die(json_encode($this->file_list));
        }

        $this->getListOfUpdatedFiles($xml->ps_root_dir[0]);
        die(json_encode($this->file_list));
    }

    public function getListOfUpdatedFiles(SimpleXMLElement $dir, $path = '')
    {
        $exclude_regexp = '(install(-dev|-new)?|themes|tools|cache|docs|download|img|localization|log|mails|translations|upload|modules|override/(:?.*)index.php$)';
        $admin_dir = basename(_PS_ADMIN_DIR_);

        foreach ($dir->md5file as $file) {
            $filename = preg_replace('#^admin/#', $admin_dir.'/', $path.$file['name']);
            if (preg_match('#^'.$exclude_regexp.'#', $filename)) {
                continue;
            }

            if (!file_exists(_PS_ROOT_DIR_.'/'.$filename)) {
                $this->file_list['missing'][] = $filename;
            } else {
                $md5_local = md5_file(_PS_ROOT_DIR_.'/'.$filename);
                if ($md5_local != (string)$file) {
                    $this->file_list['updated'][] = $filename;
                }
            }
        }

        foreach ($dir->dir as $subdir) {
            $this->getListOfUpdatedFiles($subdir, $path.$subdir['name'].'/');
        }
    }
}
