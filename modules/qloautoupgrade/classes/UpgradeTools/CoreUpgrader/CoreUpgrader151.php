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

namespace PrestaShop\Module\AutoUpgrade\UpgradeTools\CoreUpgrader;

use PrestaShop\Module\AutoUpgrade\Tools14;
use PrestaShop\Module\AutoUpgrade\UpgradeContainer;
use PrestaShop\Module\AutoUpgrade\UpgradeException;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\FilesystemAdapter;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\SettingsFileWriter;

class CoreUpgrader151 extends CoreUpgrader
{
    /**
     * Complete path to the settings.inc.php
     *
     * @var string
     */
    private $pathToSettingsFile;

    /**
     * Generate a new settings file.
     */
    public function writeNewSettings()
    {
        if (!defined('_PS_CACHE_ENABLED_')) {
            define('_PS_CACHE_ENABLED_', '0');
        }
        $caches = array('CacheMemcache', 'CacheApc', 'CacheFs', 'CacheMemcached',
            'CacheXcache', );

        $datas = array(
            '_DB_SERVER_' => _DB_SERVER_,
            '_DB_NAME_' => _DB_NAME_,
            '_DB_USER_' => _DB_USER_,
            '_DB_PASSWD_' => _DB_PASSWD_,
            '_DB_PREFIX_' => _DB_PREFIX_,
            '_MYSQL_ENGINE_' => _MYSQL_ENGINE_,
            '_PS_CACHING_SYSTEM_' => ((defined('_PS_CACHING_SYSTEM_') && in_array(_PS_CACHING_SYSTEM_, $caches)) ? _PS_CACHING_SYSTEM_ : 'CacheMemcache'),
            '_PS_CACHE_ENABLED_' => _PS_CACHE_ENABLED_,
            '_COOKIE_KEY_' => _COOKIE_KEY_,
            '_COOKIE_IV_' => _COOKIE_IV_,
            '_PS_CREATION_DATE_' => defined('_PS_CREATION_DATE_') ? _PS_CREATION_DATE_ : date('Y-m-d'),
            '_PS_VERSION_' => $this->destinationUpgradeVersion,
            '_QLOAPPS_VERSION_' => $this->destinationQloUpgradeVersion,
            '_PS_DIRECTORY_' => __PS_BASE_URI__,
        );

        if (defined('_RIJNDAEL_KEY_') && defined('_RIJNDAEL_IV_')) {
            $datas['_RIJNDAEL_KEY_'] = _RIJNDAEL_KEY_;
            $datas['_RIJNDAEL_IV_'] = _RIJNDAEL_IV_;
        } elseif (function_exists('openssl_encrypt')) {
            $datas['_RIJNDAEL_KEY_'] = Tools14::passwdGen(32);
            $datas['_RIJNDAEL_IV_'] = base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-128-CBC')));
        } elseif (function_exists('mcrypt_encrypt')) {
            $datas['_RIJNDAEL_KEY_'] = Tools14::passwdGen(mcrypt_get_key_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
            $datas['_RIJNDAEL_IV_'] = base64_encode(mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND));
        }
        $writer = new SettingsFileWriter($this->container->getTranslator());
        $writer->writeSettingsFile($this->pathToSettingsFile, $datas);
        $this->logger->debug($this->container->getTranslator()->trans('Settings file updated', array(), 'Modules.Autoupgrade.Admin'));
    }

    // protected function initConstants()
    // {
    //     parent::initConstants();
    //     $this->pathToSettingsFile = $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/config/settings.inc.php';
    //     // Kept for potential BC with old PS versions
    //     define('SETTINGS_FILE', $this->pathToSettingsFile);
    // }

    // protected function getPreUpgradeVersion()
    // {
    //     if (!file_exists($this->pathToSettingsFile)) {
    //         throw new UpgradeException($this->container->getTranslator()->trans('The config/settings.inc.php file was not found.', array(), 'Modules.Autoupgrade.Admin'));
    //     }
    //     include_once $this->pathToSettingsFile;

    //     // return _PS_VERSION_;
    //     return _QLOAPPS_VERSION_;
    // }

    // protected function upgradeLanguage($lang)
    // {
    //     require_once _PS_TOOL_DIR_ . 'tar/Archive_Tar.php';
    //     $lang_pack = json_decode(Tools14::file_get_contents('http' . (extension_loaded('openssl')
    //                     ? 's' : '') . '://www.prestashop.com/download/lang_packs/get_language_pack.php?version=' . $this->destinationUpgradeVersion . '&iso_lang=' . $lang['iso_code']));

    //     if (!$lang_pack) {
    //         return;
    //     }

    //     if ($content = Tools14::file_get_contents('http' . (extension_loaded('openssl')
    //                 ? 's' : '') . '://translations.prestashop.com/download/lang_packs/gzip/' . $lang_pack->version . '/' . $lang['iso_code'] . '.gzip')) {
    //         $file = _PS_TRANSLATIONS_DIR_ . $lang['iso_code'] . '.gzip';
    //         if ((bool) file_put_contents($file, $content)) {
    //             $gz = new \Archive_Tar($file, 'gz');
    //             $files_list = $gz->listContent();
    //             if (!$this->container->getUpgradeConfiguration()->shouldKeepMails()) {
    //                 $files_listing = array();
    //                 foreach ($files_list as $i => $file) {
    //                     if (preg_match('/^mails\/' . $lang['iso_code'] . '\/.*/', $file['filename'])) {
    //                         unset($files_list[$i]);
    //                     }
    //                 }
    //                 foreach ($files_list as $file) {
    //                     if (isset($file['filename']) && is_string($file['filename'])) {
    //                         $files_listing[] = $file['filename'];
    //                     }
    //                 }
    //                 if (is_array($files_listing)) {
    //                     $gz->extractList($files_listing, _PS_TRANSLATIONS_DIR_ . '../', '');
    //                 }
    //             } else {
    //                 $gz->extract(_PS_TRANSLATIONS_DIR_ . '../', false);
    //             }
    //         }
    //     }
    // }

    // protected function loadEntityInterface()
    // {
    //     require_once _PS_ROOT_DIR_ . '/Core/Foundation/Database/Core_Foundation_Database_EntityInterface.php';
    // }

    // protected function runCoreCacheClean()
    // {
    //     parent::runCoreCacheClean();

    //     // delete cache filesystem if activated
    //     if (defined('_PS_CACHE_ENABLED_') && false != _PS_CACHE_ENABLED_) {
    //         $depth = (int) $this->db->getValue('SELECT value
	// 			FROM ' . _DB_PREFIX_ . 'configuration
	// 			WHERE name = "PS_CACHEFS_DIRECTORY_DEPTH"');
    //         if ($depth) {
    //             if (!defined('_PS_CACHEFS_DIRECTORY_')) {
    //                 define('_PS_CACHEFS_DIRECTORY_', $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/cache/cachefs/');
    //             }
    //             FilesystemAdapter::deleteDirectory(_PS_CACHEFS_DIRECTORY_, false);
    //             if (class_exists('CacheFs', false)) {
    //                 $this->createCacheFsDirectories((int) $depth);
    //             }
    //         }
    //     }
    // }

    // private function createCacheFsDirectories($level_depth, $directory = false)
    // {
    //     if (!$directory) {
    //         if (!defined('_PS_CACHEFS_DIRECTORY_')) {
    //             define('_PS_CACHEFS_DIRECTORY_', $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/cache/cachefs/');
    //         }
    //         $directory = _PS_CACHEFS_DIRECTORY_;
    //     }
    //     $chars = '0123456789abcdef';
    //     for ($i = 0; $i < strlen($chars); ++$i) {
    //         $new_dir = $directory . $chars[$i] . '/';
    //         if (mkdir($new_dir, 0775) && chmod($new_dir, 0775) && $level_depth - 1 > 0) {
    //             $this->createCacheFsDirectories($level_depth - 1, $new_dir);
    //         }
    //     }
    // }
}
