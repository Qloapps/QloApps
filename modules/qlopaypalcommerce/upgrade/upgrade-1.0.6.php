<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/afl-3.0.php
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
* @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_6($module)
{
    $objUpgrade = new UpgradeQloPaypalCommerce106($module);

    return $objUpgrade->initUpgrade();
}

class UpgradeQloPaypalCommerce106
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function initUpgrade()
    {
        return $this->migrateCredentialsToModeSpecificKeys();
    }

    /**
     * The module used to store one single credential set (WK_PAYPAL_COMMERCE_MERCHANT_ID/
     * _EMAIL/_CLIENT_ID/_CLIENT_SECRET); it now keeps separate sandbox and live credential
     * sets. An existing site's real credentials are sitting under the old keys - copy them
     * into whichever mode (WK_PAYPAL_COMMERCE_PAYMENT_MODE) was actually active, since that's
     * the only one we know is genuinely a sandbox vs. live value; the unused mode is left
     * empty rather than guessed. Never overwrites a new key that's already set.
     */
    public function migrateCredentialsToModeSpecificKeys()
    {
        $oldMerchantId = Configuration::get('WK_PAYPAL_COMMERCE_MERCHANT_ID');

        if (!$oldMerchantId) {
            return true;
        }

        $mode = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
        $prefix = ($mode == QloPaypalCommerce::WK_PAYPAL_COMMERCE_PAYMENT_MODE_PRODUCTION) ? 'LIVE' : 'SANDBOX';

        if (!Configuration::get('WK_PAYPAL_COMMERCE_'.$prefix.'_MERCHANT_ID')) {
            Configuration::updateValue('WK_PAYPAL_COMMERCE_'.$prefix.'_MERCHANT_ID', $oldMerchantId);
            Configuration::updateValue('WK_PAYPAL_COMMERCE_'.$prefix.'_EMAIL', Configuration::get('WK_PAYPAL_COMMERCE_EMAIL'));
            Configuration::updateValue('WK_PAYPAL_COMMERCE_'.$prefix.'_CLIENT_ID', Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID'));
            Configuration::updateValue('WK_PAYPAL_COMMERCE_'.$prefix.'_CLIENT_SECRET', Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_SECRET'));
        }

        Configuration::deleteByName('WK_PAYPAL_COMMERCE_MERCHANT_ID');
        Configuration::deleteByName('WK_PAYPAL_COMMERCE_EMAIL');
        Configuration::deleteByName('WK_PAYPAL_COMMERCE_CLIENT_ID');
        Configuration::deleteByName('WK_PAYPAL_COMMERCE_CLIENT_SECRET');

        return true;
    }
}
