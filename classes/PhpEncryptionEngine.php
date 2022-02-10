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
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Encoding;
use Defuse\Crypto\Key;

/**
 * Class PhpEncryption engine for openSSL 1.0.1+.
 */
class PhpEncryptionEngineCore
{
    protected $key;

    /**
     * PhpEncryptionCore constructor.
     *
     * @param string $hexString A string that only contains hexadecimal characters
     *                          Bother upper and lower case are allowed
     */
    public function __construct($hexString)
    {
        $this->key = self::loadFromAsciiSafeString($hexString);
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
        return Crypto::encrypt($plaintext, $this->key);
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
        try {
            $plaintext = Crypto::decrypt($cipherText, $this->key);
        } catch (Exception $e) {
            if ($e instanceof \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException) {
                return false;
            }

            throw $e;
        }

        return $plaintext;
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
        return Encoding::saveBytesToChecksummedAsciiSafeString($header, $bytes);
    }

    /**
     * @return string
     */
    public static function createNewRandomKey()
    {
        $key = Key::createNewRandomKey();

        return $key->saveToAsciiSafeString();
    }

    /**
     * @param $hexString
     *
     * @return Key
     */
    public static function loadFromAsciiSafeString($hexString)
    {
        return Key::loadFromAsciiSafeString($hexString);
    }

    /**
     * @return string
     *
     * @throws Exception
     *
     * @see https://github.com/paragonie/random_compat/blob/v1.4.1/lib/random_bytes_openssl.php
     * @see https://github.com/paragonie/random_compat/blob/v1.4.1/lib/random_bytes_mcrypt.php
     */
    public static function randomCompat()
    {
        $bytes = Key::KEY_BYTE_SIZE;

        $secure = true;
        $buf = openssl_random_pseudo_bytes($bytes, $secure);
        if (
            $buf !== false
            &&
            $secure
            &&
            RandomCompat_strlen($buf) === $bytes
        ) {
            return $buf;
        }

        $buf = @mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
        if (
            $buf !== false
            &&
            RandomCompat_strlen($buf) === $bytes
        ) {
            return $buf;
        }

        throw new Exception(
            'Could not gather sufficient random data'
        );
    }

    /**
     * @param $buf
     *
     * @return string
     */
    public static function saveToAsciiSafeString($buf)
    {
        return Encoding::saveBytesToChecksummedAsciiSafeString(
            Key::KEY_CURRENT_VERSION,
            $buf
        );
    }
}
