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
     * @throws \TypeError
     *
     * @return string
     */
    public static function encrypt($plaintext, $key, $raw_binary = false)
    {
        if (!\is_string($plaintext)) {
            throw new \TypeError(
                'String expected for argument 1. ' . \ucfirst(\gettype($plaintext)) . ' given instead.'
            );
        }
        if (!($key instanceof Key)) {
            throw new \TypeError(
                'Key expected for argument 2. ' . \ucfirst(\gettype($key)) . ' given instead.'
            );
        }
        if (!\is_bool($raw_binary)) {
            throw new \TypeError(
                'Boolean expected for argument 3. ' . \ucfirst(\gettype($raw_binary)) . ' given instead.'
            );
        }
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
     * @throws \TypeError
     *
     * @return string
     */
    public static function encryptWithPassword(
        $plaintext,
        #[\SensitiveParameter]
        $password,
        $raw_binary = false
    )
    {
        if (!\is_string($plaintext)) {
            throw new \TypeError(
                'String expected for argument 1. ' . \ucfirst(\gettype($plaintext)) . ' given instead.'
            );
        }
        if (!\is_string($password)) {
            throw new \TypeError(
                'String expected for argument 2. ' . \ucfirst(\gettype($password)) . ' given instead.'
            );
        }
        if (!\is_bool($raw_binary)) {
            throw new \TypeError(
                'Boolean expected for argument 3. ' . \ucfirst(\gettype($raw_binary)) . ' given instead.'
            );
        }
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
     * @throws \TypeError
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     *
     * @return string
     */
    public static function decrypt($ciphertext, $key, $raw_binary = false)
    {
        if (!\is_string($ciphertext)) {
            throw new \TypeError(
                'String expected for argument 1. ' . \ucfirst(\gettype($ciphertext)) . ' given instead.'
            );
        }
        if (!($key instanceof Key)) {
            throw new \TypeError(
                'Key expected for argument 2. ' . \ucfirst(\gettype($key)) . ' given instead.'
            );
        }
        if (!\is_bool($raw_binary)) {
            throw new \TypeError(
                'Boolean expected for argument 3. ' . \ucfirst(\gettype($raw_binary)) . ' given instead.'
            );
        }
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
     * @throws \TypeError
     *
     * @return string
     */
    public static function decryptWithPassword(
        $ciphertext,
        #[\SensitiveParameter]
        $password,
        $raw_binary = false
    )
    {
        if (!\is_string($ciphertext)) {
            throw new \TypeError(
                'String expected for argument 1. ' . \ucfirst(\gettype($ciphertext)) . ' given instead.'
            );
        }
        if (!\is_string($password)) {
            throw new \TypeError(
                'String expected for argument 2. ' . \ucfirst(\gettype($password)) . ' given instead.'
            );
        }
        if (!\is_bool($raw_binary)) {
            throw new \TypeError(
                'Boolean expected for argument 3. ' . \ucfirst(\gettype($raw_binary)) . ' given instead.'
            );
        }
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
     * @throws \TypeError
     *
     * @return string
     */
    public static function legacyDecrypt(
        $ciphertext,
        #[\SensitiveParameter]
        $key
    )
    {
        if (!\is_string($ciphertext)) {
            throw new \TypeError(
                'String expected for argument 1. ' . \ucfirst(\gettype($ciphertext)) . ' given instead.'
            );
        }
        if (!\is_string($key)) {
            throw new \TypeError(
                'String expected for argument 2. ' . \ucfirst(\gettype($key)) . ' given instead.'
            );
        }

        RuntimeTests::runtimeTest();

        // Extract the HMAC from the front of the ciphertext.
        if (Core::ourStrlen($ciphertext) <= Core::LEGACY_MAC_BYTE_SIZE) {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Ciphertext is too short.'
            );
        }
        /**
         * @var string
         */
        $hmac = Core::ourSubstr($ciphertext, 0, Core::LEGACY_MAC_BYTE_SIZE);
        Core::ensureTrue(\is_string($hmac));
        /**
         * @var string
         */
        $messageCiphertext = Core::ourSubstr($ciphertext, Core::LEGACY_MAC_BYTE_SIZE);
        Core::ensureTrue(\is_string($messageCiphertext));

        // Regenerate the same authentication sub-key.
        $akey = Core::HKDF(
            Core::LEGACY_HASH_FUNCTION_NAME,
            $key,
            Core::LEGACY_KEY_BYTE_SIZE,
            Core::LEGACY_AUTHENTICATION_INFO_STRING,
            null
        );

        if (self::verifyHMAC($hmac, $messageCiphertext, $akey)) {
            // Regenerate the same encryption sub-key.
            $ekey = Core::HKDF(
                Core::LEGACY_HASH_FUNCTION_NAME,
                $key,
                Core::LEGACY_KEY_BYTE_SIZE,
                Core::LEGACY_ENCRYPTION_INFO_STRING,
                null
            );

            // Extract the IV from the ciphertext.
            if (Core::ourStrlen($messageCiphertext) <= Core::LEGACY_BLOCK_BYTE_SIZE) {
                throw new Ex\WrongKeyOrModifiedCiphertextException(
                    'Ciphertext is too short.'
                );
            }
            /**
             * @var string
             */
            $iv = Core::ourSubstr($messageCiphertext, 0, Core::LEGACY_BLOCK_BYTE_SIZE);
            Core::ensureTrue(\is_string($iv));

            /**
             * @var string
             */
            $actualCiphertext = Core::ourSubstr($messageCiphertext, Core::LEGACY_BLOCK_BYTE_SIZE);
            Core::ensureTrue(\is_string($actualCiphertext));

            // Do the decryption.
            $plaintext = self::plainDecrypt($actualCiphertext, $ekey, $iv, Core::LEGACY_CIPHER_METHOD);
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
        /** @var string $header */
        $header = Core::ourSubstr($ciphertext, 0, Core::HEADER_VERSION_SIZE);
        if ($header !== Core::CURRENT_VERSION) {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Bad version header.'
            );
        }

        // Get the salt.
        /** @var string $salt */
        $salt = Core::ourSubstr(
            $ciphertext,
            Core::HEADER_VERSION_SIZE,
            Core::SALT_BYTE_SIZE
        );
        Core::ensureTrue(\is_string($salt));

        // Get the IV.
        /** @var string $iv */
        $iv = Core::ourSubstr(
            $ciphertext,
            Core::HEADER_VERSION_SIZE + Core::SALT_BYTE_SIZE,
            Core::BLOCK_BYTE_SIZE
        );
        Core::ensureTrue(\is_string($iv));

        // Get the HMAC.
        /** @var string $hmac */
        $hmac = Core::ourSubstr(
            $ciphertext,
            Core::ourStrlen($ciphertext) - Core::MAC_BYTE_SIZE,
            Core::MAC_BYTE_SIZE
        );
        Core::ensureTrue(\is_string($hmac));

        // Get the actual encrypted ciphertext.
        /** @var string $encrypted */
        $encrypted = Core::ourSubstr(
            $ciphertext,
            Core::HEADER_VERSION_SIZE + Core::SALT_BYTE_SIZE +
                Core::BLOCK_BYTE_SIZE,
            Core::ourStrlen($ciphertext) - Core::MAC_BYTE_SIZE - Core::SALT_BYTE_SIZE -
                Core::BLOCK_BYTE_SIZE - Core::HEADER_VERSION_SIZE
        );
        Core::ensureTrue(\is_string($encrypted));

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
    protected static function plainEncrypt(
        $plaintext,
        #[\SensitiveParameter]
        $key,
        #[\SensitiveParameter]
        $iv
    )
    {
        Core::ensureConstantExists('OPENSSL_RAW_DATA');
        Core::ensureFunctionExists('openssl_encrypt');
        /** @var string $ciphertext */
        $ciphertext = \openssl_encrypt(
            $plaintext,
            Core::CIPHER_METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        Core::ensureTrue(\is_string($ciphertext), 'openssl_encrypt() failed');

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
    protected static function plainDecrypt(
        $ciphertext,
        #[\SensitiveParameter]
        $key,
        #[\SensitiveParameter]
        $iv,
        $cipherMethod
    )
    {
        Core::ensureConstantExists('OPENSSL_RAW_DATA');
        Core::ensureFunctionExists('openssl_decrypt');

        /** @var string $plaintext */
        $plaintext = \openssl_decrypt(
            $ciphertext,
            $cipherMethod,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        Core::ensureTrue(\is_string($plaintext), 'openssl_decrypt() failed.');

        return $plaintext;
    }

    /**
     * Verifies an HMAC without leaking information through side-channels.
     *
     * @param string $expected_hmac
     * @param string $message
     * @param string $key
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return bool
     */
    protected static function verifyHMAC(
        $expected_hmac,
        $message,
        #[\SensitiveParameter]
        $key
    )
    {
        $message_hmac = \hash_hmac(Core::HASH_FUNCTION_NAME, $message, $key, true);
        return Core::hashEquals($message_hmac, $expected_hmac);
    }
}
