<?php

namespace Defuse\Crypto;

use Defuse\Crypto\Exception as Ex;

/*
 * We're using static class inheritance to get access to protected methods
 * inside Crypto. To make it easy to know where the method we're calling can be
 * found, within this file, prefix calls with `Crypto::` or `RuntimeTests::`,
 * and don't use `self::`.
 */

class RuntimeTests extends Crypto
{
    /**
     * Runs the runtime tests.
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @return void
     */
    public static function runtimeTest()
    {
        // 0: Tests haven't been run yet.
        // 1: Tests have passed.
        // 2: Tests are running right now.
        // 3: Tests have failed.
        static $test_state = 0;

        if ($test_state === 1 || $test_state === 2) {
            return;
        }

        if ($test_state === 3) {
            /* If an intermittent problem caused a test to fail previously, we
             * want that to be indicated to the user with every call to this
             * library. This way, if the user first does something they really
             * don't care about, and just ignores all exceptions, they won't get
             * screwed when they then start to use the library for something
             * they do care about. */
            throw new Ex\EnvironmentIsBrokenException('Tests failed previously.');
        }

        try {
            $test_state = 2;

            Core::ensureFunctionExists('openssl_get_cipher_methods');
            if (\in_array(Core::CIPHER_METHOD, \openssl_get_cipher_methods()) === false) {
                throw new Ex\EnvironmentIsBrokenException(
                    'Cipher method not supported. This is normally caused by an outdated ' .
                    'version of OpenSSL (and/or OpenSSL compiled for FIPS compliance). ' .
                    'Please upgrade to a newer version of OpenSSL that supports ' .
                    Core::CIPHER_METHOD . ' to use this library.'
                );
            }

            RuntimeTests::AESTestVector();
            RuntimeTests::HMACTestVector();
            RuntimeTests::HKDFTestVector();

            RuntimeTests::testEncryptDecrypt();
            Core::ensureTrue(Core::ourStrlen(Key::createNewRandomKey()->getRawBytes()) === Core::KEY_BYTE_SIZE);

            Core::ensureTrue(Core::ENCRYPTION_INFO_STRING !== Core::AUTHENTICATION_INFO_STRING);
        } catch (Ex\EnvironmentIsBrokenException $ex) {
            // Do this, otherwise it will stay in the "tests are running" state.
            $test_state = 3;
            throw $ex;
        }

        // Change this to '0' make the tests always re-run (for benchmarking).
        $test_state = 1;
    }

    /**
     * High-level tests of Crypto operations.
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @return void
     */
    private static function testEncryptDecrypt()
    {
        $key  = Key::createNewRandomKey();
        $data = "EnCrYpT EvErYThInG\x00\x00";

        // Make sure encrypting then decrypting doesn't change the message.
        $ciphertext = Crypto::encrypt($data, $key, true);
        try {
            $decrypted = Crypto::decrypt($ciphertext, $key, true);
        } catch (Ex\WrongKeyOrModifiedCiphertextException $ex) {
            // It's important to catch this and change it into a
            // Ex\EnvironmentIsBrokenException, otherwise a test failure could trick
            // the user into thinking it's just an invalid ciphertext!
            throw new Ex\EnvironmentIsBrokenException();
        }
        Core::ensureTrue($decrypted === $data);

        // Modifying the ciphertext: Appending a string.
        try {
            Crypto::decrypt($ciphertext . 'a', $key, true);
            throw new Ex\EnvironmentIsBrokenException();
        } catch (Ex\WrongKeyOrModifiedCiphertextException $e) { /* expected */
        }

        // Modifying the ciphertext: Changing an HMAC byte.
        $indices_to_change = [
            0, // The header.
            Core::HEADER_VERSION_SIZE + 1, // the salt
            Core::HEADER_VERSION_SIZE + Core::SALT_BYTE_SIZE + 1, // the IV
            Core::HEADER_VERSION_SIZE + Core::SALT_BYTE_SIZE + Core::BLOCK_BYTE_SIZE + 1, // the ciphertext
        ];

        foreach ($indices_to_change as $index) {
            try {
                $ciphertext[$index] = \chr((\ord($ciphertext[$index]) + 1) % 256);
                Crypto::decrypt($ciphertext, $key, true);
                throw new Ex\EnvironmentIsBrokenException();
            } catch (Ex\WrongKeyOrModifiedCiphertextException $e) { /* expected */
            }
        }

        // Decrypting with the wrong key.
        $key        = Key::createNewRandomKey();
        $data       = 'abcdef';
        $ciphertext = Crypto::encrypt($data, $key, true);
        $wrong_key  = Key::createNewRandomKey();
        try {
            Crypto::decrypt($ciphertext, $wrong_key, true);
            throw new Ex\EnvironmentIsBrokenException();
        } catch (Ex\WrongKeyOrModifiedCiphertextException $e) { /* expected */
        }

        // Ciphertext too small.
        $key        = Key::createNewRandomKey();
        $ciphertext = \str_repeat('A', Core::MINIMUM_CIPHERTEXT_SIZE - 1);
        try {
            Crypto::decrypt($ciphertext, $key, true);
            throw new Ex\EnvironmentIsBrokenException();
        } catch (Ex\WrongKeyOrModifiedCiphertextException $e) { /* expected */
        }
    }

    /**
     * Test HKDF against test vectors.
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @return void
     */
    private static function HKDFTestVector()
    {
        // HKDF test vectors from RFC 5869

        // Test Case 1
        $ikm    = \str_repeat("\x0b", 22);
        $salt   = Encoding::hexToBin('000102030405060708090a0b0c');
        $info   = Encoding::hexToBin('f0f1f2f3f4f5f6f7f8f9');
        $length = 42;
        $okm    = Encoding::hexToBin(
            '3cb25f25faacd57a90434f64d0362f2a' .
            '2d2d0a90cf1a5a4c5db02d56ecc4c5bf' .
            '34007208d5b887185865'
        );
        $computed_okm = Core::HKDF('sha256', $ikm, $length, $info, $salt);
        Core::ensureTrue($computed_okm === $okm);

        // Test Case 7
        $ikm    = \str_repeat("\x0c", 22);
        $length = 42;
        $okm    = Encoding::hexToBin(
            '2c91117204d745f3500d636a62f64f0a' .
            'b3bae548aa53d423b0d1f27ebba6f5e5' .
            '673a081d70cce7acfc48'
        );
        $computed_okm = Core::HKDF('sha1', $ikm, $length, '', null);
        Core::ensureTrue($computed_okm === $okm);
    }

    /**
     * Test HMAC against test vectors.
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @return void
     */
    private static function HMACTestVector()
    {
        // HMAC test vector From RFC 4231 (Test Case 1)
        $key     = \str_repeat("\x0b", 20);
        $data    = 'Hi There';
        $correct = 'b0344c61d8db38535ca8afceaf0bf12b881dc200c9833da726e9376c2e32cff7';
        Core::ensureTrue(
            \hash_hmac(Core::HASH_FUNCTION_NAME, $data, $key) === $correct
        );
    }

    /**
     * Test AES against test vectors.
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @return void
     */
    private static function AESTestVector()
    {
        // AES CTR mode test vector from NIST SP 800-38A
        $key = Encoding::hexToBin(
            '603deb1015ca71be2b73aef0857d7781' .
            '1f352c073b6108d72d9810a30914dff4'
        );
        $iv        = Encoding::hexToBin('f0f1f2f3f4f5f6f7f8f9fafbfcfdfeff');
        $plaintext = Encoding::hexToBin(
            '6bc1bee22e409f96e93d7e117393172a' .
            'ae2d8a571e03ac9c9eb76fac45af8e51' .
            '30c81c46a35ce411e5fbc1191a0a52ef' .
            'f69f2445df4f9b17ad2b417be66c3710'
        );
        $ciphertext = Encoding::hexToBin(
            '601ec313775789a5b7a7f504bbf3d228' .
            'f443e3ca4d62b59aca84e990cacaf5c5' .
            '2b0930daa23de94ce87017ba2d84988d' .
            'dfc9c58db67aada613c2dd08457941a6'
        );

        $computed_ciphertext = Crypto::plainEncrypt($plaintext, $key, $iv);
        Core::ensureTrue($computed_ciphertext === $ciphertext);

        $computed_plaintext = Crypto::plainDecrypt($ciphertext, $key, $iv, Core::CIPHER_METHOD);
        Core::ensureTrue($computed_plaintext === $plaintext);
    }
}
