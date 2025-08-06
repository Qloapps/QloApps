<?php

/*
 * 2007-2018 PrestaShop
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
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade\Parameters;

/**
 * File names where upgrade temporary content is stored.
 */
class UpgradeFileNames
{
    /**
     * configFilename contains all configuration specific to the autoupgrade module.
     *
     * @var string
     */
    const CONFIG_FILENAME = 'config.var';

    /**
     * during upgradeFiles process,
     * this files contains the list of queries left to upgrade in a serialized array.
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const QUERIES_TO_UPGRADE_LIST = 'queriesToUpgrade.list';

    /**
     * during upgradeFiles process,
     * this files contains the list of files left to upgrade in a serialized array.
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const FILES_TO_UPGRADE_LIST = 'filesToUpgrade.list';

    /**
     * during upgradeModules process,
     * this files contains the list of modules left to upgrade in a serialized array.
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const MODULES_TO_UPGRADE_LIST = 'modulesToUpgrade.list';

    /**
     * during upgradeModules process,
     * this files contains the list of modules left to upgrade in a serialized array.
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const MODULES_TO_INSTALL_LIST = 'modulesToInstall.list';

    /**
     * during upgradeModules process,
     * this files contains the list of modules left to upgrade in a serialized array.
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const MODULES_TO_REMOVE_LIST = 'modulesToRemove.list';

    /**
     * during upgradeFiles process,
     * this files contains the list of files left to upgrade in a serialized array.
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const FILES_DIFF_LIST = 'filesDiff.list';

    /**
     * during backupFiles process,
     * this files contains the list of files left to save in a serialized array.
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const FILES_TO_BACKUP_LIST = 'filesToBackup.list';

    /**
     * during backupDb process,
     * this files contains the list of tables left to save in a serialized array.
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const DB_TABLES_TO_BACKUP_LIST = 'tablesToBackup.list';

    /**
     * during restoreDb process,
     * this file contains a serialized array of queries which left to execute for restoring database
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const QUERIES_TO_RESTORE_LIST = 'queryToRestore.list';
    const DB_TABLES_TO_CLEAN_LIST = 'tableToClean.list';

    /**
     * during restoreFiles process,
     * this file contains difference between queryToRestore and queries present in a backupFiles archive
     * (this file is deleted in init() method if you reload the page).
     *
     * @var string
     */
    const FILES_TO_REMOVE_LIST = 'filesToRemove.list';

    /**
     * during restoreFiles process,
     * contains list of files present in backupFiles archive.
     *
     * @var string
     */
    const FILES_FROM_ARCHIVE_LIST = 'filesFromArchive.list';

    /**
     * mailCustomList contains list of mails files which are customized,
     * relative to original files for the current PrestaShop version.
     *
     * @var string
     */
    const MAILS_CUSTOM_LIST = 'mails-custom.list';

    /**
     * tradCustomList contains list of translation files which are customized,
     * relative to original files for the current PrestaShop version.
     *
     * @var string
     */
    const TRANSLATION_FILES_CUSTOM_LIST = 'translations-custom.list';

    /**
     * tmp_files contains an array of filename which will be removed
     * at the beginning of the upgrade process.
     *
     * @var array
     */
    public static $tmp_files = array(
        'QUERIES_TO_UPGRADE_LIST', // used ?
        'MODULES_TO_UPGRADE_LIST',
        'MODULES_TO_INSTALL_LIST',
        'MODULES_TO_REMOVE_LIST',
        'FILES_TO_UPGRADE_LIST',
        'FILES_DIFF_LIST',
        'FILES_TO_BACKUP_LIST',
        'DB_TABLES_TO_BACKUP_LIST',
        'QUERIES_TO_RESTORE_LIST',
        'DB_TABLES_TO_CLEAN_LIST',
        'FILES_TO_REMOVE_LIST',
        'FILES_FROM_ARCHIVE_LIST',
        'MAILS_CUSTOM_LIST',
        'TRANSLATION_FILES_CUSTOM_LIST',
    );
}
