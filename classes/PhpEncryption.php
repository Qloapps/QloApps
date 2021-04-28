<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;

/**
 * Class PhpEncryptionCore for openSSL 1.0.1+.
 */
class PhpEncryptionCore
{
    const ENGINE = 'PhpEncryptionEngine';
    const LEGACY_ENGINE = 'PhpEncryptionLegacyEngine';

    private static $engine;

    /**
     * PhpEncryptionCore constructor.
     *
     * @param string $hexString A string that only contains hexadecimal characters
     *                          Bother upper and lower case are allowed
     */
    public function __construct($hexString)
    {
        $engineClass = self::resolveEngineToUse();
        self::$engine = new $engineClass($hexString);
    }

    /**
     * Encrypt the plaintext.
     *
     * @param string $plaintext Plaintext
     *
     * @return string Cipher text
     */
    public function encrypt($plaintext)
    {
        return self::$engine->encrypt($plaintext);
    }

    /**
     * Decrypt the cipher text.
     *
     * @param string $cipherText Cipher text
     *
     * @return bool|string Plaintext
     *                     `false` if unable to decrypt
     *
     * @throws Exception
     */
    public function decrypt($cipherText)
    {
        return self::$engine->decrypt($cipherText);
    }

    /**
     * @param $header
     * @param $bytes
     *
     * @return string
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function saveBytesToChecksummedAsciiSafeString($header, $bytes)
    {
        $engine = self::resolveEngineToUse();

        return $engine::saveBytesToChecksummedAsciiSafeString($header, $bytes);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public static function createNewRandomKey()
    {
        $engine = self::resolveEngineToUse();

        try {
            $randomKey = $engine::createNewRandomKey();
        } catch (EnvironmentIsBrokenException $exception) {
            $buf = $engine::randomCompat();
            $randomKey = $engine::saveToAsciiSafeString($buf);
        }

        return $randomKey;
    }

    /**
     * Choose which engine use regarding the OpenSSL cipher methods available.
     */
    public static function resolveEngineToUse()
    {
        if (false === in_array(\Defuse\Crypto\Core::CIPHER_METHOD, openssl_get_cipher_methods())) {
            return self::LEGACY_ENGINE;
        }

        return self::ENGINE;
    }
}
