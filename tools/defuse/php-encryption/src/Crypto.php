<?php

namespace Defuse\Crypto;

use Defuse\Crypto\Exception as Ex;

class Crypto
{
    /**
     * Encrypts a string with a Key.
     *
     * @param string $plaintext
     * @param Key    $key
     * @param bool   $raw_binary
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return string
     */
    public static function encrypt($plaintext, Key $key, $raw_binary = false)
    {
        return self::encryptInternal(
            $plaintext,
            KeyOrPassword::createFromKey($key),
            $raw_binary
        );
    }

    /**
     * Encrypts a string with a password, using a slow key derivation function
     * to make password cracking more expensive.
     *
     * @param string $plaintext
     * @param string $password
     * @param bool   $raw_binary
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return string
     */
    public static function encryptWithPassword($plaintext, $password, $raw_binary = false)
    {
        return self::encryptInternal(
            $plaintext,
            KeyOrPassword::createFromPassword($password),
            $raw_binary
        );
    }

    /**
     * Decrypts a ciphertext to a string with a Key.
     *
     * @param string $ciphertext
     * @param Key    $key
     * @param bool   $raw_binary
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     *
     * @return string
     */
    public static function decrypt($ciphertext, Key $key, $raw_binary = false)
    {
        return self::decryptInternal(
            $ciphertext,
            KeyOrPassword::createFromKey($key),
            $raw_binary
        );
    }

    /**
     * Decrypts a ciphertext to a string with a password, using a slow key
     * derivation function to make password cracking more expensive.
     *
     * @param string $ciphertext
     * @param string $password
     * @param bool   $raw_binary
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     *
     * @return string
     */
    public static function decryptWithPassword($ciphertext, $password, $raw_binary = false)
    {
        return self::decryptInternal(
            $ciphertext,
            KeyOrPassword::createFromPassword($password),
            $raw_binary
        );
    }

    /**
     * Decrypts a legacy ciphertext produced by version 1 of this library.
     *
     * @param string $ciphertext
     * @param string $key
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     *
     * @return string
     */
    public static function legacyDecrypt($ciphertext, $key)
    {
        RuntimeTests::runtimeTest();

        // Extract the HMAC from the front of the ciphertext.
        if (Core::ourStrlen($ciphertext) <= Core::LEGACY_MAC_BYTE_SIZE) {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Ciphertext is too short.'
            );
        }
        $hmac = Core::ourSubstr($ciphertext, 0, Core::LEGACY_MAC_BYTE_SIZE);
        if ($hmac === false) {
            throw new Ex\EnvironmentIsBrokenException();
        }
        $ciphertext = Core::ourSubstr($ciphertext, Core::LEGACY_MAC_BYTE_SIZE);
        if ($ciphertext === false) {
            throw new Ex\EnvironmentIsBrokenException();
        }

        // Regenerate the same authentication sub-key.
        $akey = Core::HKDF(
            Core::LEGACY_HASH_FUNCTION_NAME,
            $key,
            Core::LEGACY_KEY_BYTE_SIZE,
            Core::LEGACY_AUTHENTICATION_INFO_STRING,
            null
        );

        if (self::verifyHMAC($hmac, $ciphertext, $akey)) {
            // Regenerate the same encryption sub-key.
            $ekey = Core::HKDF(
                Core::LEGACY_HASH_FUNCTION_NAME,
                $key,
                Core::LEGACY_KEY_BYTE_SIZE,
                Core::LEGACY_ENCRYPTION_INFO_STRING,
                null
            );

            // Extract the IV from the ciphertext.
            if (Core::ourStrlen($ciphertext) <= Core::LEGACY_BLOCK_BYTE_SIZE) {
                throw new Ex\WrongKeyOrModifiedCiphertextException(
                    'Ciphertext is too short.'
                );
            }
            $iv = Core::ourSubstr($ciphertext, 0, Core::LEGACY_BLOCK_BYTE_SIZE);
            if ($iv === false) {
                throw new Ex\EnvironmentIsBrokenException();
            }
            $ciphertext = Core::ourSubstr($ciphertext, Core::LEGACY_BLOCK_BYTE_SIZE);
            if ($ciphertext === false) {
                throw new Ex\EnvironmentIsBrokenException();
            }

            // Do the decryption.
            $plaintext = self::plainDecrypt($ciphertext, $ekey, $iv, Core::LEGACY_CIPHER_METHOD);
            return $plaintext;
        } else {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Integrity check failed.'
            );
        }
    }

    /**
     * Encrypts a string with either a key or a password.
     *
     * @param string        $plaintext
     * @param KeyOrPassword $secret
     * @param bool          $raw_binary
     *
     * @return string
     */
    private static function encryptInternal($plaintext, KeyOrPassword $secret, $raw_binary)
    {
        RuntimeTests::runtimeTest();

        $salt = Core::secureRandom(Core::SALT_BYTE_SIZE);
        $keys = $secret->deriveKeys($salt);
        $ekey = $keys->getEncryptionKey();
        $akey = $keys->getAuthenticationKey();
        $iv     = Core::secureRandom(Core::BLOCK_BYTE_SIZE);

        $ciphertext = Core::CURRENT_VERSION . $salt . $iv . self::plainEncrypt($plaintext, $ekey, $iv);
        $auth       = \hash_hmac(Core::HASH_FUNCTION_NAME, $ciphertext, $akey, true);
        $ciphertext = $ciphertext . $auth;

        if ($raw_binary) {
            return $ciphertext;
        }
        return Encoding::binToHex($ciphertext);
    }

    /**
     * Decrypts a ciphertext to a string with either a key or a password.
     *
     * @param string        $ciphertext
     * @param KeyOrPassword $secret
     * @param bool          $raw_binary
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     *
     * @return string
     */
    private static function decryptInternal($ciphertext, KeyOrPassword $secret, $raw_binary)
    {
        RuntimeTests::runtimeTest();

        if (! $raw_binary) {
            try {
                $ciphertext = Encoding::hexToBin($ciphertext);
            } catch (Ex\BadFormatException $ex) {
                throw new Ex\WrongKeyOrModifiedCiphertextException(
                    'Ciphertext has invalid hex encoding.'
                );
            }
        }

        if (Core::ourStrlen($ciphertext) < Core::MINIMUM_CIPHERTEXT_SIZE) {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Ciphertext is too short.'
            );
        }

        // Get and check the version header.
        $header = Core::ourSubstr($ciphertext, 0, Core::HEADER_VERSION_SIZE);
        if ($header !== Core::CURRENT_VERSION) {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Bad version header.'
            );
        }

        // Get the salt.
        $salt = Core::ourSubstr(
            $ciphertext,
            Core::HEADER_VERSION_SIZE,
            Core::SALT_BYTE_SIZE
        );
        if ($salt === false) {
            throw new Ex\EnvironmentIsBrokenException();
        }

        // Get the IV.
        $iv = Core::ourSubstr(
            $ciphertext,
            Core::HEADER_VERSION_SIZE + Core::SALT_BYTE_SIZE,
            Core::BLOCK_BYTE_SIZE
        );
        if ($iv === false) {
            throw new Ex\EnvironmentIsBrokenException();
        }

        // Get the HMAC.
        $hmac = Core::ourSubstr(
            $ciphertext,
            Core::ourStrlen($ciphertext) - Core::MAC_BYTE_SIZE,
            Core::MAC_BYTE_SIZE
        );
        if ($hmac === false) {
            throw new Ex\EnvironmentIsBrokenException();
        }

        // Get the actual encrypted ciphertext.
        $encrypted = Core::ourSubstr(
            $ciphertext,
            Core::HEADER_VERSION_SIZE + Core::SALT_BYTE_SIZE +
                Core::BLOCK_BYTE_SIZE,
            Core::ourStrlen($ciphertext) - Core::MAC_BYTE_SIZE - Core::SALT_BYTE_SIZE -
                Core::BLOCK_BYTE_SIZE - Core::HEADER_VERSION_SIZE
        );
        if ($encrypted === false) {
            throw new Ex\EnvironmentIsBrokenException();
        }

        // Derive the separate encryption and authentication keys from the key
        // or password, whichever it is.
        $keys = $secret->deriveKeys($salt);

        if (self::verifyHMAC($hmac, $header . $salt . $iv . $encrypted, $keys->getAuthenticationKey())) {
            $plaintext = self::plainDecrypt($encrypted, $keys->getEncryptionKey(), $iv, Core::CIPHER_METHOD);
            return $plaintext;
        } else {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Integrity check failed.'
            );
        }
    }

    /**
     * Raw unauthenticated encryption (insecure on its own).
     *
     * @param string $plaintext
     * @param string $key
     * @param string $iv
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return string
     */
    protected static function plainEncrypt($plaintext, $key, $iv)
    {
        Core::ensureConstantExists('OPENSSL_RAW_DATA');
        Core::ensureFunctionExists('openssl_encrypt');
        $ciphertext = \openssl_encrypt(
            $plaintext,
            Core::CIPHER_METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($ciphertext === false) {
            throw new Ex\EnvironmentIsBrokenException(
                'openssl_encrypt() failed.'
            );
        }

        return $ciphertext;
    }

    /**
     * Raw unauthenticated decryption (insecure on its own).
     *
     * @param string $ciphertext
     * @param string $key
     * @param string $iv
     * @param string $cipherMethod
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return string
     */
    protected static function plainDecrypt($ciphertext, $key, $iv, $cipherMethod)
    {
        Core::ensureConstantExists('OPENSSL_RAW_DATA');
        Core::ensureFunctionExists('openssl_decrypt');
        $plaintext = \openssl_decrypt(
            $ciphertext,
            $cipherMethod,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        if ($plaintext === false) {
            throw new Ex\EnvironmentIsBrokenException(
                'openssl_decrypt() failed.'
            );
        }

        return $plaintext;
    }

    /**
     * Verifies an HMAC without leaking information through side-channels.
     *
     * @param string $correct_hmac
     * @param string $message
     * @param string $key
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return bool
     */
    protected static function verifyHMAC($correct_hmac, $message, $key)
    {
        $message_hmac = \hash_hmac(Core::HASH_FUNCTION_NAME, $message, $key, true);
        return Core::hashEquals($correct_hmac, $message_hmac);
    }
}
