<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Handles PrestaShop webservice key creation and deletion for the QGHC module.
 * Separated from the main class to keep module class free of business logic.
 */
class QghcWebserviceSetup
{
    const WS_ACCOUNT_DESCRIPTION = 'Google Hotel Configuration Key';

    /** Set true only around this class's own delete() calls so the module's protection
     *  guard (QloGoogleHotelConnector::guardWebserviceKeyObject()) lets them through —
     *  every other caller must still be blocked. */
    public static $bypassGuard = false;

    /**
     * Creates a PS webservice key for QGHC, granted full permissions on every
     * webservice resource (the Channel Manager needs broader access, not just
     * ghc_api). Skips creation when a valid key already exists.
     *
     * @return bool
     */
    public static function createKey()
    {
        $existingKey = (string) Db::getInstance()->getValue(
            'SELECT `key` FROM `' . _DB_PREFIX_ . 'webservice_account`
             WHERE `description` = \'' . pSQL(self::WS_ACCOUNT_DESCRIPTION) . '\''
        );
        if ($existingKey) {
            return true;
        }

        return (bool) self::createKeyRow();
    }

    /**
     * Deletes the existing QGHC key (if any) and creates a fresh one — unlike createKey(),
     * always generates a new key even when one already exists.
     *
     * @return string|false New key on success, false on failure.
     */
    public static function regenerateKey()
    {
        if (!self::deleteKey()) {
            return false;
        }
        return self::createKeyRow();
    }

    /**
     * Creates the webservice_account row, grants full permissions on every resource, and
     * enables PS_WEBSERVICE if needed. Shared by createKey() and regenerateKey().
     *
     * @return string|false New key on success, false on failure.
     */
    private static function createKeyRow()
    {
        $key = Tools::strtoupper(Tools::passwdGen(32));

        $wsAccount              = new WebserviceKey();
        $wsAccount->key         = $key;
        $wsAccount->description = self::WS_ACCOUNT_DESCRIPTION;
        $wsAccount->active      = 1;

        if (!$wsAccount->save()) {
            return false;
        }

        Configuration::updateValue(QloGoogleHotelConnector::CONFIG_WS_KEY, $key);

        $resources = WebserviceRequest::getResources();
        // ghc_api can be missing here during install due to hook-timing; ensure it anyway.
        if (!isset($resources['ghc_api'])) {
            $resources['ghc_api'] = array('description' => 'Google Hotel Connector API');
        }

        foreach ($resources as $resourceName => $resourceDef) {
            $forbidden = isset($resourceDef['forbidden_method']) ? $resourceDef['forbidden_method'] : array();
            foreach (array('GET', 'POST', 'PUT', 'DELETE', 'HEAD') as $method) {
                if (in_array($method, $forbidden, true)) {
                    continue;
                }
                Db::getInstance()->insert(
                    'webservice_permission',
                    array(
                        'resource'              => pSQL($resourceName),
                        'method'                => $method,
                        'id_webservice_account' => (int) $wsAccount->id,
                    ),
                    false,
                    true,
                    Db::INSERT_IGNORE
                );
            }
        }

        if (!Configuration::get('PS_WEBSERVICE')) {
            Configuration::updateValue('PS_WEBSERVICE', 1);
        }

        return $key;
    }

    /**
     * Returns true when the given key exists in the PS webservice accounts table.
     *
     * @param string $key
     * @return bool
     */
    public static function keyExists($key)
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'webservice_account`
             WHERE `key` = \'' . pSQL($key) . '\''
        );
    }

    /**
     * Returns the active QGHC webservice key, or empty string if none exists.
     *
     * @return string
     */
    public static function getKey()
    {
        $key = (string) Configuration::get(QloGoogleHotelConnector::CONFIG_WS_KEY);
        if (!$key) {
            $key = (string) Db::getInstance()->getValue(
                'SELECT `key` FROM `' . _DB_PREFIX_ . 'webservice_account`
                 WHERE `description` = \'' . pSQL(self::WS_ACCOUNT_DESCRIPTION) . '\' AND `active` = 1'
            );
        }
        return $key;
    }

    /**
     * Deletes the QGHC webservice account from PrestaShop.
     *
     * @return bool
     */
    public static function deleteKey()
    {
        $key = (string) Configuration::get(QloGoogleHotelConnector::CONFIG_WS_KEY);
        if (!$key) {
            $key = (string) Db::getInstance()->getValue(
                'SELECT `key` FROM `' . _DB_PREFIX_ . 'webservice_account`
                 WHERE `description` = \'' . pSQL(self::WS_ACCOUNT_DESCRIPTION) . '\''
            );
        }
        if (!$key) {
            return true;
        }

        $idAccount = (int) Db::getInstance()->getValue(
            'SELECT `id_webservice_account` FROM `' . _DB_PREFIX_ . 'webservice_account`
             WHERE `key` = \'' . pSQL($key) . '\''
        );

        if ($idAccount) {
            $wsAccount = new WebserviceKey($idAccount);
            self::$bypassGuard = true;
            try {
                $deleted = $wsAccount->delete();
            } catch (Exception $e) {
                self::$bypassGuard = false;
                throw $e;
            }
            self::$bypassGuard = false;
            if (!$deleted) {
                return false;
            }
        }

        return true;
    }
}
