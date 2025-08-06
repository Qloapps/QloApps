<?php

/*
* 2007-2016 PrestaShop
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
*    @author PrestaShop SA <contact@prestashop.com>
*    @copyright    2007-2016 PrestaShop SA
*    @version    Release: $Revision: 11834 $
*    @license        http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*    International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\Module\AutoUpgrade\AjaxResponse;
use PrestaShop\Module\AutoUpgrade\BackupFinder;
use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeFileNames;
use PrestaShop\Module\AutoUpgrade\UpgradePage;
use PrestaShop\Module\AutoUpgrade\UpgradeSelfCheck;
use PrestaShop\Module\AutoUpgrade\Tools14;
use PrestaShop\Module\AutoUpgrade\UpgradeContainer;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\FilesystemAdapter;


require_once _PS_ROOT_DIR_ . '/modules/qloautoupgrade/vendor/autoload.php';

class AdminSelfUpgradeController extends AdminController
{

    public $multishop_context;
    public $multishop_context_group = false;
    public $_html = '';
    // used for translations
    public static $l_cache;
    // retrocompatibility
    public $noTabLink = array();
    public $id = -1;

    public $ajax = false;

    public $standalone = true;

    /**
     * Initialized in initPath().
     */
    public $autoupgradePath;
    public $downloadPath;
    public $backupPath;
    public $latestPath;
    public $tmpPath;

    /**
     * autoupgradeDir.
     *
     * @var string directory relative to admin dir
     */
    public $autoupgradeDir = 'qloautoupgrade';
    public $latestRootDir = '';
    public $prodRootDir = '';
    public $adminDir = '';

    public $keepImages;
    public $updateDefaultTheme;
    public $changeToDefaultTheme;
    public $keepMails;
    public $manualMode;
    public $deactivateCustomModule;

    public static $classes14 = array('Cache', 'CacheFS', 'CarrierModule', 'Db', 'FrontController', 'Helper', 'ImportModule',
        'MCached', 'Module', 'ModuleGraph', 'ModuleGraphEngine', 'ModuleGrid', 'ModuleGridEngine',
        'MySQL', 'Order', 'OrderDetail', 'OrderDiscount', 'OrderHistory', 'OrderMessage', 'OrderReturn',
        'OrderReturnState', 'OrderSlip', 'OrderState', 'PDF', 'RangePrice', 'RangeWeight', 'StockMvt',
        'StockMvtReason', 'SubDomain', 'Shop', 'Tax', 'TaxRule', 'TaxRulesGroup', 'WebserviceKey', 'WebserviceRequest', '', );

    public static $maxBackupFileSize = 15728640; // 15 Mo

    public $_fieldsUpgradeOptions = array();
    public $_fieldsBackupOptions = array();

    /**
     * @var UpgradeContainer
     */
    private $upgradeContainer;


    public function __construct()
    {
        parent::__construct();

        $this->init();

        $this->db = Db::getInstance();
        $this->bootstrap = true;

        self::$currentIndex = $_SERVER['SCRIPT_NAME'] . (($controller = Tools14::getValue('controller')) ? '?controller=' . $controller : '');

    }

     /**
     * Init context and dependencies, handles POST and GET
     */
    public function init()
    {
        if (!$this->ajax) {
            parent::init();
        }

        // For later use, let's set up prodRootDir and adminDir
        // This way it will be easier to upgrade a different path if needed
        $this->prodRootDir = _PS_ROOT_DIR_;
        $this->adminDir = realpath(_PS_ADMIN_DIR_);
        $this->upgradeContainer = new UpgradeContainer($this->prodRootDir, $this->adminDir);

        if (!defined('__PS_BASE_URI__')) {
            // _PS_DIRECTORY_ replaces __PS_BASE_URI__ in 1.5
            if (defined('_PS_DIRECTORY_')) {
                define('__PS_BASE_URI__', _PS_DIRECTORY_);
            } else {
                define('__PS_BASE_URI__', realpath(dirname($_SERVER['SCRIPT_NAME'])) . '/../../');
            }
        }
        // from $_POST or $_GET
        $this->action = empty($_REQUEST['action']) ? null : $_REQUEST['action'];
        $this->initPath();
        $this->upgradeContainer->getState()->importFromArray(
            empty($_REQUEST['params']) ? array() : $_REQUEST['params']
        );

        // // If you have defined this somewhere, you know what you do
        // // load options from configuration if we're not in ajax mode
        if (!$this->ajax) {
            $upgrader = $this->upgradeContainer->getUpgrader();
            $this->upgradeContainer->getCookie()->create(
                $this->context->employee->id,
                $this->context->language->iso_code
            );

            $this->upgradeContainer->getState()->initDefault(
                $upgrader,
                $this->upgradeContainer->getProperty(UpgradeContainer::PS_ROOT_PATH),
                $this->upgradeContainer->getProperty(UpgradeContainer::QLO_VERSION));

            if (isset($_GET['refreshCurrentVersion'])) {
                $upgradeConfiguration = $this->upgradeContainer->getUpgradeConfiguration();
                // delete the potential xml files we saved in config/xml (from last release and from current)
                $upgrader->clearXmlMd5File($this->upgradeContainer->getProperty(UpgradeContainer::QLO_VERSION));
                $upgrader->clearXmlMd5File($upgrader->version_num);
                if ($upgradeConfiguration->get('channel') == 'private' && !$upgradeConfiguration->get('private_allow_major')) {
                    $upgrader->checkPSVersion(true, array('private', 'minor'));
                } else {
                    $upgrader->checkPSVersion(true, array('minor'));
                }
                Tools14::redirectAdmin(self::$currentIndex . '&conf=5&token=' . Tools14::getValue('token'));
            }
            // removing temporary files
            $this->upgradeContainer->getFileConfigurationStorage()->cleanAll();
        }

        $this->keepImages = $this->upgradeContainer->getUpgradeConfiguration()->shouldBackupImages();
        $this->updateDefaultTheme = $this->upgradeContainer->getUpgradeConfiguration()->get('PS_AUTOUP_UPDATE_DEFAULT_THEME');
        $this->changeToDefaultTheme = $this->upgradeContainer->getUpgradeConfiguration()->get('PS_AUTOUP_CHANGE_DEFAULT_THEME');
        $this->keepMails = $this->upgradeContainer->getUpgradeConfiguration()->get('PS_AUTOUP_KEEP_MAILS');
        $this->deactivateCustomModule = $this->upgradeContainer->getUpgradeConfiguration()->get('PS_AUTOUP_CUSTOM_MOD_DESACT');
    }

    /**
     * create some required directories if they does not exists.
     */
    public function initPath()
    {
        $this->upgradeContainer->getWorkspace()->createFolders();

        // set autoupgradePath, to be used in backupFiles and backupDb config values
        $this->autoupgradePath = $this->adminDir . DIRECTORY_SEPARATOR . $this->autoupgradeDir;
        $this->backupPath = $this->autoupgradePath . DIRECTORY_SEPARATOR . 'backup';
        $this->downloadPath = $this->autoupgradePath . DIRECTORY_SEPARATOR . 'download';
        $this->latestPath = $this->autoupgradePath . DIRECTORY_SEPARATOR . 'latest';
        $this->tmpPath = $this->autoupgradePath . DIRECTORY_SEPARATOR . 'tmp';
        $this->latestRootDir = $this->latestPath . DIRECTORY_SEPARATOR;

        // if (!file_exists($this->backupPath . DIRECTORY_SEPARATOR . 'index.php')) {
        //     if (!copy(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'index.php', $this->backupPath . DIRECTORY_SEPARATOR . 'index.php')) {
        //         $this->_errors[] = $this->trans('Unable to create file %s', array($this->backupPath . DIRECTORY_SEPARATOR . 'index.php'), 'Modules.Autoupgrade.Admin');
        //     }
        // }

        // $tmp = "order deny,allow\ndeny from all";
        // if (!file_exists($this->backupPath . DIRECTORY_SEPARATOR . '.htaccess')) {
        //     if (!file_put_contents($this->backupPath . DIRECTORY_SEPARATOR . '.htaccess', $tmp)) {
        //         $this->_errors[] = $this->trans('Unable to create file %s', array($this->backupPath . DIRECTORY_SEPARATOR . '.htaccess'), 'Modules.Autoupgrade.Admin');
        //     }
        // }
    }

    public function display()
    {
        $configuration_keys = array(
            'PS_AUTOUP_UPDATE_DEFAULT_THEME' => 1,
            'PS_AUTOUP_CHANGE_DEFAULT_THEME' => 0,
            'PS_AUTOUP_KEEP_MAILS' => 0,
            'PS_AUTOUP_CUSTOM_MOD_DESACT' => 1,
            'PS_AUTOUP_PERFORMANCE' => 1,
        );

        foreach ($configuration_keys as $k => $default_value) {
            if (Configuration::get($k) == '') {
                Configuration::updateValue($k, $default_value);
            }
        }

        // update backup name
        $backupFinder = new BackupFinder($this->backupPath);
        $availableBackups = $backupFinder->getAvailableBackups();
        if (!$this->upgradeContainer->getUpgradeConfiguration()->get('PS_AUTOUP_BACKUP')
            && !empty($availableBackups)
            && !in_array($this->upgradeContainer->getState()->getBackupName(), $availableBackups)
        ) {
            $this->upgradeContainer->getState()->setBackupName(end($availableBackups));
        }

        $upgrader = $this->upgradeContainer->getUpgrader();
        $upgradeSelfCheck = new UpgradeSelfCheck(
            $upgrader,
            $this->prodRootDir,
            $this->adminDir,
            $this->autoupgradePath
        );
        $response = new AjaxResponse($this->upgradeContainer->getState(), $this->upgradeContainer->getLogger());
        $this->_html = (new UpgradePage(
            $this->upgradeContainer->getUpgradeConfiguration(),
            $this->upgradeContainer->getTwig(),
            $this->upgradeContainer->getTranslator(),
            $upgradeSelfCheck,
            $upgrader,
            $backupFinder,
            $this->autoupgradePath,
            $this->prodRootDir,
            $this->adminDir,
            self::$currentIndex,
            $this->token,
            $this->upgradeContainer->getState()->getInstallVersion(),
            $this->manualMode,
            $this->upgradeContainer->getState()->getBackupName(),
            $this->downloadPath
        ))->display(
            $response
                ->setUpgradeConfiguration($this->upgradeContainer->getUpgradeConfiguration())
                ->getJson()
        );

        $this->ajax = true;
        $this->content = $this->_html;

        return parent::display();
    }

    /**
     * function to set configuration fields display.
     */
    private function _setFields()
    {
        $this->_fieldsBackupOptions = array(
            'PS_AUTOUP_BACKUP' => array(
                'title' => $this->trans('Back up my files and database', array(), 'Modules.Autoupgrade.Admin'), 'cast' => 'intval', 'validation' => 'isBool', 'defaultValue' => '1',
                'type' => 'bool', 'desc' => $this->trans('Automatically back up your database and files in order to restore your shop if needed. This is experimental: you should still perform your own manual backup for safety.', array(), 'Modules.Autoupgrade.Admin'),
            ),
            'PS_AUTOUP_KEEP_IMAGES' => array(
                'title' => $this->trans('Back up my images', array(), 'Modules.Autoupgrade.Admin'), 'cast' => 'intval', 'validation' => 'isBool', 'defaultValue' => '1',
                'type' => 'bool', 'desc' => $this->trans('To save time, you can decide not to back your images up. In any case, always make sure you did back them up manually.', array(), 'Modules.Autoupgrade.Admin'),
            ),
        );
        $this->_fieldsUpgradeOptions = array(
            'PS_AUTOUP_PERFORMANCE' => array(
                'title' => $this->trans('Server performance', array(), 'Modules.Autoupgrade.Admin'), 'cast' => 'intval', 'validation' => 'isInt', 'defaultValue' => '1',
                'type' => 'select', 'desc' => $this->trans('Unless you are using a dedicated server, select "Low".', array(), 'Modules.Autoupgrade.Admin') . '<br />' .
                $this->trans('A high value can cause the upgrade to fail if your server is not powerful enough to process the upgrade tasks in a short amount of time.', array(), 'Modules.Autoupgrade.Admin'),
                'choices' => array(1 => $this->trans('Low (recommended)', array(), 'Modules.Autoupgrade.Admin'), 2 => $this->trans('Medium', array(), 'Modules.Autoupgrade.Admin'), 3 => $this->trans('High', array(), 'Modules.Autoupgrade.Admin')),
            ),
            'PS_AUTOUP_CUSTOM_MOD_DESACT' => array(
                'title' => $this->trans('Disable non-native modules', array(), 'Modules.Autoupgrade.Admin'), 'cast' => 'intval', 'validation' => 'isBool',
                'type' => 'bool', 'desc' => $this->trans('As non-native modules can experience some compatibility issues, we recommend to disable them by default.', array(), 'Modules.Autoupgrade.Admin') . '<br />' .
                $this->trans('Keeping them enabled might prevent you from loading the "Modules" page properly after the upgrade.', array(), 'Modules.Autoupgrade.Admin'),
            ),
            'PS_AUTOUP_UPDATE_DEFAULT_THEME' => array(
                'title' => $this->trans('Upgrade the default theme', array(), 'Modules.Autoupgrade.Admin'), 'cast' => 'intval', 'validation' => 'isBool', 'defaultValue' => '1',
                'type' => 'bool', 'desc' => $this->trans('If you customized the default PrestaShop theme in its folder (folder name "classic" in 1.7), enabling this option will lose your modifications.', array(), 'Modules.Autoupgrade.Admin') . '<br />'
                . $this->trans('If you are using your own theme, enabling this option will simply update the default theme files, and your own theme will be safe.', array(), 'Modules.Autoupgrade.Admin'),
            ),
            'PS_AUTOUP_CHANGE_DEFAULT_THEME' => array(
                'title' => $this->trans('Switch to the default theme', array(), 'Modules.Autoupgrade.Admin'), 'cast' => 'intval', 'validation' => 'isBool', 'defaultValue' => '0',
                'type' => 'bool', 'desc' => $this->trans('This will change your theme: your shop will then use the default theme of the version of PrestaShop you are upgrading to.', array(), 'Modules.Autoupgrade.Admin'),
            ),
            'PS_AUTOUP_KEEP_MAILS' => array(
                'title' => $this->trans('Keep the customized email templates', array(), 'Modules.Autoupgrade.Admin'), 'cast' => 'intval', 'validation' => 'isBool',
                'type' => 'bool', 'desc' => $this->trans('This will not upgrade the default PrestaShop e-mails.', array(), 'Modules.Autoupgrade.Admin') . '<br />'
                . $this->trans('If you customized the default PrestaShop e-mail templates, enabling this option will keep your modifications.', array(), 'Modules.Autoupgrade.Admin'),
            ),
        );
    }

    public function postProcess()
    {
        $this->_setFields();

        if (Tools14::isSubmit('putUnderMaintenance')) {
            foreach (Shop::getCompleteListOfShopsID() as $id_shop) {
                Configuration::updateValue('PS_SHOP_ENABLE', 0, false, null, (int) $id_shop);
            }
            Configuration::updateGlobalValue('PS_SHOP_ENABLE', 0);
        }

        if (Tools14::isSubmit('ignorePsRequirements')) {
            Configuration::updateValue('PS_AUTOUP_IGNORE_REQS', 1);
        }

        if (Tools14::isSubmit('ignorePhpOutdated')) {
            Configuration::updateValue('PS_AUTOUP_IGNORE_PHP_UPGRADE', 1);
        }

        if (Tools14::isSubmit('customSubmitAutoUpgrade')) {
            $config_keys = array_keys(array_merge($this->_fieldsUpgradeOptions, $this->_fieldsBackupOptions));
            $config = array();
            foreach ($config_keys as $key) {
                if (isset($_POST[$key])) {
                    $config[$key] = $_POST[$key];
                }
            }
            $UpConfig = $this->upgradeContainer->getUpgradeConfiguration();
            $UpConfig->merge($config);

            if ($this->upgradeContainer->getUpgradeConfigurationStorage()->save($UpConfig, UpgradeFileNames::CONFIG_FILENAME)) {
                Tools14::redirectAdmin(self::$currentIndex . '&conf=6&token=' . Tools14::getValue('token'));
            }
        }

        if (Tools14::isSubmit('deletebackup')) {
            $res = false;
            $name = Tools14::getValue('name');
            $filelist = scandir($this->backupPath);
            foreach ($filelist as $filename) {
                // the following will match file or dir related to the selected backup
                if (!empty($filename) && $filename[0] != '.' && $filename != 'index.php' && $filename != '.htaccess'
                    && preg_match('#^(auto-backupfiles_|)' . preg_quote($name) . '(\.zip|)$#', $filename, $matches)) {
                    if (is_file($this->backupPath . DIRECTORY_SEPARATOR . $filename)) {
                        $res &= unlink($this->backupPath . DIRECTORY_SEPARATOR . $filename);
                    } elseif (!empty($name) && is_dir($this->backupPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR)) {
                        $res = FilesystemAdapter::deleteDirectory($this->backupPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR);
                    }
                }
            }
            if ($res) {
                Tools14::redirectAdmin(self::$currentIndex . '&conf=1&token=' . Tools14::getValue('token'));
            } else {
                $this->_errors[] = $this->trans('Error when trying to delete backups %s', array($name), 'Modules.Autoupgrade.Admin');
            }
        }
        parent::postProcess();
    }

    /**
     * @deprecated
     * Method allowing errors on very old tabs to be displayed.
     * On the next major of this module, use an admin controller and get rid of this.
     *
     * This method is called by functions.php available in the admin root folder.
     */
    public function displayErrors()
    {
        if (empty($this->_errors)) {
            return;
        }
        echo implode(' - ', $this->_errors);
    }

    /**
     * Adapter for trans calls, existing only on PS 1.7.
     * Making them available for PS 1.6 as well.
     *
     * @param string $id
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return (new \PrestaShop\Module\AutoUpgrade\UpgradeTools\Translator(__CLASS__))->trans($id, $parameters, $domain, $locale);
    }
}