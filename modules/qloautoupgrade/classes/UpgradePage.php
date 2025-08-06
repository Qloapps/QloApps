<?php

/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade;

use PrestaShop\Module\AutoUpgrade\Twig\Block\RollbackForm;
use PrestaShop\Module\AutoUpgrade\Twig\Block\UpgradeButtonBlock;
use PrestaShop\Module\AutoUpgrade\Twig\Block\UpgradeChecklist;
use PrestaShop\Module\AutoUpgrade\Twig\Form\BackupOptionsForm;
use PrestaShop\Module\AutoUpgrade\Twig\Form\FormRenderer;
use PrestaShop\Module\AutoUpgrade\Twig\Form\UpgradeOptionsForm;
use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfiguration;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\Translator;
use Twig_Environment;

/**
 * Constructs the upgrade page.
 */
class UpgradePage
{
    const TRANSLATION_DOMAIN = 'Modules.Autoupgrade.Admin';

    /**
     * @var string
     */
    private $moduleDir;

    /**
     * @var string
     */
    private $templatesDir = '/views/templates';

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var UpgradeConfiguration
     */
    private $config;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var UpgradeSelfCheck
     */
    private $upgradeSelfCheck;

    /**
     * @var string
     */
    private $autoupgradePath;

    /**
     * @var Upgrader
     */
    private $upgrader;

    /**
     * @var string
     */
    private $prodRootPath;

    /**
     * @var string
     */
    private $adminPath;

    /**
     * @var string
     */
    private $currentIndex;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $installVersion;

    /**
     * @var bool
     */
    private $manualMode;

    /**
     * @var string
     */
    private $backupName;

    /**
     * @var string
     */
    private $downloadPath;

    /**
     * @var BackupFinder
     */
    private $backupFinder;

    public function __construct(
        UpgradeConfiguration $config,
        Twig_Environment $twig,
        Translator $translator,
        UpgradeSelfCheck $upgradeSelfCheck,
        Upgrader $upgrader,
        BackupFinder $backupFinder,
        $autoupgradePath,
        $prodRootPath,
        $adminPath,
        $currentIndex,
        $token,
        $installVersion,
        $manualMode,
        $backupName,
        $downloadPath
    ) {
        $this->moduleDir = realpath(__DIR__ . '/../');
        $this->config = $config;
        $this->translator = $translator;
        $this->upgrader = $upgrader;
        $this->upgradeSelfCheck = $upgradeSelfCheck;
        $this->autoupgradePath = $autoupgradePath;
        $this->prodRootPath = $prodRootPath;
        $this->adminPath = $adminPath;
        $this->currentIndex = $currentIndex;
        $this->token = $token;
        $this->installVersion = $installVersion;
        $this->manualMode = $manualMode;
        $this->backupName = $backupName;
        $this->twig = $twig;
        $this->downloadPath = $downloadPath;
        $this->backupFinder = $backupFinder;
    }

    /**
     * Renders the page.
     *
     * @return string HTML
     */
    public function display($ajaxResult)
    {
        $twig = $this->twig;
        $translationDomain = self::TRANSLATION_DOMAIN;

        // $errMessageData = $this->getErrorMessage();
        // if (!empty($errMessageData)) {
        //     return $twig
        //         ->render('@ModuleAutoUpgrade:error.twig', $errMessageData);
        // }
        $templateData = array(
            'psBaseUri' => __PS_BASE_URI__,
            'translationDomain' => $translationDomain,
            'jsParams' => $this->getJsParams($ajaxResult),
            'currentConfig' => $this->getChecklistBlock(),
            'upgradeButtonBlock' => $this->getUpgradeButtonBlock(),
            'rollbackForm' => $this->getRollbackForm(),
            'backupOptions' => $this->getBackupOptionsForm(),
            'upgradeOptions' => $this->getUpgradeOptionsForm(),
            'currentIndex' => $this->currentIndex,
            'token' => $this->token,
        );

        return $twig->render('@ModuleAutoUpgrade/main.twig', $templateData);
    }

    /**
     * @return string HTML
     */
    private function getChecklistBlock()
    {
        return (new UpgradeChecklist(
            $this->twig,
            $this->upgradeSelfCheck,
            $this->prodRootPath,
            $this->adminPath,
            $this->autoupgradePath,
            $this->currentIndex,
            $this->token
        ))->render();
    }

    /**
     * @return string HTML
     */
    private function getUpgradeButtonBlock()
    {
        return (new UpgradeButtonBlock(
            $this->twig,
            $this->translator,
            $this->config,
            $this->upgrader,
            $this->upgradeSelfCheck,
            $this->downloadPath,
            $this->token,
            $this->manualMode
        ))->render();
    }

    /**
     * @return string
     */
    private function getRollbackForm()
    {
        return (new RollbackForm($this->twig, $this->backupFinder))
            ->render();
    }

    /**
     * @return string
     */
    private function getBackupOptionsForm()
    {
        $formRenderer = new FormRenderer($this->config, $this->twig, $this->translator);

        return (new BackupOptionsForm($this->translator, $formRenderer))
            ->render();
    }

    /**
     * @return string
     */
    private function getUpgradeOptionsForm()
    {
        $formRenderer = new FormRenderer($this->config, $this->twig, $this->translator);

        return (new UpgradeOptionsForm($this->translator, $formRenderer))
            ->render();
    }

    /**
     * @return array|null
     */
    private function getErrorMessage()
    {
        $translator = $this->translator;

        // PrestaShop demo mode
        if (defined('_PS_MODE_DEMO_') && true == _PS_MODE_DEMO_) {
            return array(
                'message' => $translator->trans('This functionality has been disabled.', array(), self::TRANSLATION_DOMAIN),
            );
        }

        if (!file_exists($this->autoupgradePath . DIRECTORY_SEPARATOR . 'ajax-upgradetab.php')) {
            return array(
                'showWarningIcon' => true,
                'message' => $translator->trans(
                    '[TECHNICAL ERROR] ajax-upgradetab.php is missing. Please reinstall or reset the module.',
                    array(),
                    self::TRANSLATION_DOMAIN
                ),
            );
        }

        return array();
    }

    /**
     * @param string $ajaxResult Json encoded response data
     *
     * @return array
     */
    private function getJsParams($ajaxResult)
    {
        $translationDomain = self::TRANSLATION_DOMAIN;
        // relative admin dir
        $adminDir = trim(str_replace($this->prodRootPath, '', $this->adminPath), DIRECTORY_SEPARATOR);

        $translator = $this->translator;

        $jsParams = array(
            'manualMode' => (bool) $this->manualMode,
            '_PS_MODE_DEV_' => (defined('_PS_MODE_DEV_') && true == _PS_MODE_DEV_),
            'PS_AUTOUP_BACKUP' => (bool) $this->config->get('PS_AUTOUP_BACKUP'),
            'adminDir' => $adminDir,
            'adminUrl' => __PS_BASE_URI__ . $adminDir,
            'token' => $this->token,
            'txtError' => $this->_getJsErrorMsgs(),
            'firstTimeParams' => json_decode($ajaxResult),
            'ajaxUpgradeTabExists' => file_exists($this->autoupgradePath . DIRECTORY_SEPARATOR . 'ajax-upgradetab.php'),
            'currentIndex' => $this->currentIndex,
            'tab' => 'AdminSelfUpgrade',
            'channel' => $this->config->get('channel'),
            'translation' => array(
                'confirmDeleteBackup' => $translator->trans('Are you sure you want to delete this backup?', array(), $translationDomain),
                'delete' => $translator->trans('Delete', array(), 'Admin.Actions'),
                'updateInProgress' => $translator->trans('An update is currently in progress... Click "OK" to abort.', array(), $translationDomain),
                'upgradingPrestaShop' => $translator->trans('Upgrading PrestaShop', array(), $translationDomain),
                'upgradeComplete' => $translator->trans('Upgrade complete', array(), $translationDomain),
                'upgradeCompleteWithWarnings' => $translator->trans('Upgrade complete, but warning notifications has been found.', array(), $translationDomain),
                'todoList' => array(
                    $translator->trans('Cookies have changed, you will need to log in again once you refreshed the page', array(), $translationDomain),
                    $translator->trans('Modules zips have been updated, open modules and services tab to finish module updation', array(), $translationDomain),
                    $translator->trans('Javascript and CSS files have changed, please clear your browser cache with CTRL-F5', array(), $translationDomain),
                    $translator->trans('Please check that your front-office theme is functional (try to create an account, place an order...)', array(), $translationDomain),
                    $translator->trans('Product images do not appear in the front-office? Try regenerating the thumbnails in Preferences > Images', array(), $translationDomain),
                    $translator->trans('Do not forget to reactivate your shop once you have checked everything!', array(), $translationDomain),
                ),
                'todoListTitle' => $translator->trans('ToDo list:', array(), $translationDomain),
                'startingRestore' => $translator->trans('Starting restoration...', array(), $translationDomain),
                'restoreComplete' => $translator->trans('Restoration complete.', array(), $translationDomain),
                'cannotDownloadFile' => $translator->trans('Your server cannot download the file. Please upload it first by ftp in your admin/autoupgrade directory', array(), $translationDomain),
                'jsonParseErrorForAction' => $translator->trans('Javascript error (parseJSON) detected for action ', array(), $translationDomain),
                'manuallyGoToButton' => $translator->trans('Manually go to %s button', array(), $translationDomain),
                'endOfProcess' => $translator->trans('End of process', array(), $translationDomain),
                'processCancelledCheckForRestore' => $translator->trans('Operation canceled. Checking for restoration...', array(), $translationDomain),
                'confirmRestoreBackup' => $translator->trans('Do you want to restore %s?', array($this->backupName), $translationDomain),
                'processCancelledWithError' => $translator->trans('Operation canceled. An error happened.', array(), $translationDomain),
                'missingAjaxUpgradeTab' => $translator->trans('[TECHNICAL ERROR] ajax-upgradetab.php is missing. Please reinstall the module.', array(), $translationDomain),
                'clickToRefreshAndUseNewConfiguration' => $translator->trans('Click to refresh the page and use the new configuration', array(), $translationDomain),
                'errorDetectedDuring' => $translator->trans('Error detected during', array(), $translationDomain),
                'downloadTimeout' => $translator->trans('The request exceeded the max_time_limit. Please change your server configuration.', array(), $translationDomain),
                'seeOrHideList' => $translator->trans('See or hide the list', array(), $translationDomain),
                'coreFiles' => $translator->trans('Core file(s)', array(), $translationDomain),
                'mailFiles' => $translator->trans('Mail file(s)', array(), $translationDomain),
                'translationFiles' => $translator->trans('Translation file(s)', array(), $translationDomain),
                'linkAndMd5CannotBeEmpty' => $translator->trans('Link and MD5 hash cannot be empty', array(), $translationDomain),
                'needToEnterArchiveVersionNumber' => $translator->trans('You need to enter the version number associated with the archive.', array(), $translationDomain),
                'noArchiveSelected' => $translator->trans('No archive has been selected.', array(), $translationDomain),
                'needToEnterDirectoryVersionNumber' => $translator->trans('You need to enter the version number associated with the directory.', array(), $translationDomain),
                'confirmSkipBackup' => $translator->trans('Please confirm that you want to skip the backup.', array(), $translationDomain),
                'confirmPreserveFileOptions' => $translator->trans('Please confirm that you want to preserve file options.', array(), $translationDomain),
                'lessOptions' => $translator->trans('Less options', array(), $translationDomain),
                'moreOptions' => $translator->trans('More options (Expert mode)', array(), $translationDomain),
                'filesWillBeDeleted' => $translator->trans('These files will be deleted', array(), $translationDomain),
                'filesWillBeReplaced' => $translator->trans('These files will be replaced', array(), $translationDomain),
            ),
        );

        return $jsParams;
    }

    /**
     * @return array
     */
    private function _getJsErrorMsgs()
    {
        $translationDomain = self::TRANSLATION_DOMAIN;
        $translator = $this->translator;
        $ret = array(
            0 => $translator->trans('Required field', array(), $translationDomain),
            1 => $translator->trans('Too long!', array(), $translationDomain),
            2 => $translator->trans('Fields are different!', array(), $translationDomain),
            3 => $translator->trans('This email address is wrong!', array(), $translationDomain),
            4 => $translator->trans('Impossible to send the email!', array(), $translationDomain),
            5 => $translator->trans('Cannot create settings file, if /app/config/parameters.php exists, please give the public write permissions to this file, else please create a file named parameters.php in config directory.', array(), $translationDomain),
            6 => $translator->trans('Cannot write settings file, please create a file named settings.inc.php in the "config" directory.', array(), $translationDomain),
            7 => $translator->trans('Impossible to upload the file!', array(), $translationDomain),
            8 => $translator->trans('Data integrity is not valid. Hack attempt?', array(), $translationDomain),
            9 => $translator->trans('Impossible to read the content of a MySQL content file.', array(), $translationDomain),
            10 => $translator->trans('Cannot access a MySQL content file.', array(), $translationDomain),
            11 => $translator->trans('Error while inserting data in the database:', array(), $translationDomain),
            12 => $translator->trans('The password is incorrect (must be alphanumeric string with at least 8 characters)', array(), 'Install'),
            14 => $translator->trans('At least one table with same prefix was already found, please change your prefix or drop your database', array(), 'Install'),
            15 => $translator->trans('This is not a valid file name.', array(), $translationDomain),
            16 => $translator->trans('This is not a valid image file.', array(), $translationDomain),
            17 => $translator->trans('Error while creating the /app/config/parameters.php file.', array(), $translationDomain),
            18 => $translator->trans('Error:', array(), $translationDomain),
            19 => $translator->trans('This PrestaShop database already exists. Please revalidate your authentication information to the database.', array(), $translationDomain),
            22 => $translator->trans('An error occurred while resizing the picture.', array(), $translationDomain),
            23 => $translator->trans('Database connection is available!', array(), $translationDomain),
            24 => $translator->trans('Database Server is available but database is not found', array(), $translationDomain),
            25 => $translator->trans('Database Server is not found. Please verify the login, password and server fields.', array(), $translationDomain),
            26 => $translator->trans('An error occurred while sending email, please verify your parameters.', array(), $translationDomain),
            // Upgrader
            27 => $translator->trans('This installer is too old.', array(), $translationDomain),
            28 => $translator->trans('You already have the %s version.', array($this->installVersion), $translationDomain),
            29 => $translator->trans('There is no older version. Did you delete or rename the app/config/parameters.php file?', array(), $translationDomain),
            30 => $translator->trans('The app/config/parameters.php file was not found. Did you delete or rename this file?', array(), $translationDomain),
            31 => $translator->trans('Cannot find the SQL upgrade files. Please verify that the /install/upgrade/sql folder is not empty.', array(), $translationDomain),
            32 => $translator->trans('No upgrade is possible.', array(), $translationDomain),
            33 => $translator->trans('Error while loading SQL upgrade file.', array(), $translationDomain),
            34 => $translator->trans('Error while inserting content into the database', array(), $translationDomain),
            35 => $translator->trans('Unfortunately,', array(), $translationDomain),
            36 => $translator->trans('SQL errors have occurred.', array(), $translationDomain),
            37 => $translator->trans('The config/defines.inc.php file was not found. Where did you move it?', array(), $translationDomain),
            // End of upgrader
            38 => $translator->trans('Impossible to write the image /img/logo.jpg. If this image already exists, please delete it.', array(), $translationDomain),
            39 => $translator->trans('The uploaded file exceeds the upload_max_filesize directive in php.ini', array(), $translationDomain),
            40 => $translator->trans('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', array(), $translationDomain),
            41 => $translator->trans('The uploaded file was only partially uploaded', array(), $translationDomain),
            42 => $translator->trans('No file was uploaded.', array(), $translationDomain),
            43 => $translator->trans('Missing a temporary folder', array(), $translationDomain),
            44 => $translator->trans('Failed to write file to disk', array(), $translationDomain),
            45 => $translator->trans('File upload stopped by extension', array(), $translationDomain),
            46 => $translator->trans('Cannot convert your database\'s data to utf-8.', array(), $translationDomain),
            47 => $translator->trans('Invalid shop name', array(), 'Install'),
            48 => $translator->trans('Your firstname contains some invalid characters', array(), 'Install'),
            49 => $translator->trans('Your lastname contains some invalid characters', array(), $translationDomain),
            50 => $translator->trans('Your database server does not support the utf-8 charset.', array(), 'Install'),
            51 => $translator->trans('Your MySQL server does not support this engine, please use another one like MyISAM', array(), $translationDomain),
            52 => $translator->trans('The file /img/logo.jpg is not writable, please CHMOD 755 this file or CHMOD 777', array(), $translationDomain),
            53 => $translator->trans('Invalid catalog mode', array(), $translationDomain),
            999 => $translator->trans('No error code available', array(), $translationDomain),
        );

        return $ret;
    }
}
