<?php

namespace Defuse\Crypto;

use Defuse\Crypto\Exception as Ex;

final class File
{
    /**
     * Encrypts the input file, saving the ciphertext to the output file.
     *
     * @param string $inputFilename
     * @param string $outputFilename
     * @param Key    $key
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\IOException
     */
    public static function encryptFile($inputFilename, $outputFilename, Key $key)
    {
        self::encryptFileInternal(
            $inputFilename,
            $outputFilename,
            KeyOrPassword::createFromKey($key)
        );
    }

    /**
     * Encrypts a file with a password, using a slow key derivation function to
     * make password cracking more expensive.
     *
     * @param string $inputFilename
     * @param string $outputFilename
     * @param string $password
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\IOException
     */
    public static function encryptFileWithPassword($inputFilename, $outputFilename, $password)
    {
        self::encryptFileInternal(
            $inputFilename,
            $outputFilename,
            KeyOrPassword::createFromPassword($password)
        );
    }

    /**
     * Decrypts the input file, saving the plaintext to the output file.
     *
     * @param string $inputFilename
     * @param string $outputFilename
     * @param Key    $key
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\IOException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     */
    public static function decryptFile($inputFilename, $outputFilename, Key $key)
    {
        self::decryptFileInternal(
            $inputFilename,
            $outputFilename,
            KeyOrPassword::createFromKey($key)
        );
    }

    /**
     * Decrypts a file with a password, using a slow key derivation function to
     * make password cracking more expensive.
     *
     * @param string $inputFilename
     * @param string $outputFilename
     * @param string $password
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\IOException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     */
    public static function decryptFileWithPassword($inputFilename, $outputFilename, $password)
    {
        self::decryptFileInternal(
            $inputFilename,
            $outputFilename,
            KeyOrPassword::createFromPassword($password)
        );
    }

    /**
     * Takes two resource handles and encrypts the contents of the first,
     * writing the ciphertext into the second.
     *
     * @param resource $inputHandle
     * @param resource $outputHandle
     * @param Key      $key
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     */
    public static function encryptResource($inputHandle, $outputHandle, Key $key)
    {
        self::encryptResourceInternal(
            $inputHandle,
            $outputHandle,
            KeyOrPassword::createFromKey($key)
        );
    }

    /**
     * Encrypts the contents of one resource handle into another with a
     * password, using a slow key derivation function to make password cracking
     * more expensive.
     *
     * @param resource $inputHandle
     * @param resource $outputHandle
     * @param string   $password
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\IOException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     */
    public static function encryptResourceWithPassword($inputHandle, $outputHandle, $password)
    {
        self::encryptResourceInternal(
            $inputHandle,
            $outputHandle,
            KeyOrPassword::createFromPassword($password)
        );
    }

    /**
     * Takes two resource handles and decrypts the contents of the first,
     * writing the plaintext into the second.
     *
     * @param resource $inputHandle
     * @param resource $outputHandle
     * @param Key      $key
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\IOException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     */
    public static function decryptResource($inputHandle, $outputHandle, Key $key)
    {
        self::decryptResourceInternal(
            $inputHandle,
            $outputHandle,
            KeyOrPassword::createFromKey($key)
        );
    }

    /**
     * Decrypts the contents of one resource into another with a password, using
     * a slow key derivation function to make password cracking more expensive.
     *
     * @param resource $inputHandle
     * @param resource $outputHandle
     * @param string   $password
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\IOException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     */
    public static function decryptResourceWithPassword($inputHandle, $outputHandle, $password)
    {
        self::decryptResourceInternal(
            $inputHandle,
            $outputHandle,
            KeyOrPassword::createFromPassword($password)
        );
    }

    /**
     * Encrypts a file with either a key or a password.
     *
     * @param string        $inputFilename
     * @param string        $outputFilename
     * @param KeyOrPassword $secret
     *
     * @throws Ex\CryptoException
     * @throws Ex\IOException
     */
    private static function encryptFileInternal($inputFilename, $outputFilename, KeyOrPassword $secret)
    {
        /* Open the input file. */
        $if = @\fopen($inputFilename, 'rb');
        if ($if === false) {
            throw new Ex\IOException(
                'Cannot open input file for encrypting: ' .
                self::getLastErrorMessage()
            );
        }
        if (\is_callable('\\stream_set_read_buffer')) {
            /* This call can fail, but the only consequence is performance. */
            \stream_set_read_buffer($if, 0);
        }

        /* Open the output file. */
        $of = @\fopen($outputFilename, 'wb');
        if ($of === false) {
            \fclose($if);
            throw new Ex\IOException(
                'Cannot open output file for encrypting: ' .
                self::getLastErrorMessage()
            );
        }
        if (\is_callable('\\stream_set_write_buffer')) {
            /* This call can fail, but the only consequence is performance. */
            \stream_set_write_buffer($of, 0);
        }

        /* Perform the encryption. */
        try {
            self::encryptResourceInternal($if, $of, $secret);
        } catch (Ex\CryptoException $ex) {
            \fclose($if);
            \fclose($of);
            throw $ex;
        }

        /* Close the input file. */
        if (\fclose($if) === false) {
            \fclose($of);
            throw new Ex\IOException(
                'Cannot close input file after encrypting'
            );
        }

        /* Close the output file. */
        if (\fclose($of) === false) {
            throw new Ex\IOException(
                'Cannot close output file after encrypting'
            );
        }
    }

    /**
     * Decrypts a file with either a key or a password.
     *
     * @param string        $inputFilename
     * @param string        $outputFilename
     * @param KeyOrPassword $secret
     *
     * @throws Ex\CryptoException
     * @throws Ex\IOException
     */
    private static function decryptFileInternal($inputFilename, $outputFilename, KeyOrPassword $secret)
    {
        /* Open the input file. */
        $if = @\fopen($inputFilename, 'rb');
        if ($if === false) {
            throw new Ex\IOException(
                'Cannot open input file for decrypting: ' .
                self::getLastErrorMessage()
            );
        }
        
        if (\is_callable('\\stream_set_read_buffer')) {
            /* This call can fail, but the only consequence is performance. */
            \stream_set_read_buffer($if, 0);
        }

        /* Open the output file. */
        $of = @\fopen($outputFilename, 'wb');
        if ($of === false) {
            \fclose($if);
            throw new Ex\IOException(
                'Cannot open output file for decrypting: ' .
                self::getLastErrorMessage()
            );
        }
        
        if (\is_callable('\\stream_set_write_buffer')) {
            /* This call can fail, but the only consequence is performance. */
            \stream_set_write_buffer($of, 0);
        }

        /* Perform the decryption. */
        try {
            self::decryptResourceInternal($if, $of, $secret);
        } catch (Ex\CryptoException $ex) {
            \fclose($if);
            \fclose($of);
            throw $ex;
        }

        /* Close the input file. */
        if (\fclose($if) === false) {
            \fclose($of);
            throw new Ex\IOException(
                'Cannot close input file after decrypting'
            );
        }

        /* Close the output file. */
        if (\fclose($of) === false) {
            throw new Ex\IOException(
                'Cannot close output file after decrypting'
            );
        }
    }

    /**
     * Encrypts a resource with either a key or a password.
     *
     * @param resource      $inputHandle
     * @param resource      $outputHandle
     * @param KeyOrPassword $secret
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\IOException
     */
    private static function encryptResourceInternal($inputHandle, $outputHandle, KeyOrPassword $secret)
    {
        if (! \is_resource($inputHandle)) {
            throw new Ex\IOException(
                'Input handle must be a resource!'
            );
        }
        if (! \is_resource($outputHandle)) {
            throw new Ex\IOException(
                'Output handle must be a resource!'
            );
        }

        $inputStat = \fstat($inputHandle);
        $inputSize = $inputStat['size'];

        $file_salt = Core::secureRandom(Core::SALT_BYTE_SIZE);
        $keys = $secret->deriveKeys($file_salt);
        $ekey = $keys->getEncryptionKey();
        $akey = $keys->getAuthenticationKey();

        $ivsize = Core::BLOCK_BYTE_SIZE;
        $iv     = Core::secureRandom($ivsize);

        /* Initialize a streaming HMAC state. */
        $hmac = \hash_init(Core::HASH_FUNCTION_NAME, HASH_HMAC, $akey);
        if ($hmac === false) {
            throw new Ex\EnvironmentIsBrokenException(
                'Cannot initialize a hash context'
            );
        }

        /* Write the header, salt, and IV. */
        self::writeBytes(
            $outputHandle,
            Core::CURRENT_VERSION . $file_salt . $iv,
            Core::HEADER_VERSION_SIZE + Core::SALT_BYTE_SIZE + $ivsize
        );

        /* Add the header, salt, and IV to the HMAC. */
        \hash_update($hmac, Core::CURRENT_VERSION);
        \hash_update($hmac, $file_salt);
        \hash_update($hmac, $iv);

        /* $thisIv will be incremented after each call to the encryption. */
        $thisIv = $iv;

        /* How many blocks do we encrypt at a time? We increment by this value. */
        $inc = Core::BUFFER_BYTE_SIZE / Core::BLOCK_BYTE_SIZE;

        /* Loop until we reach the end of the input file. */
        $at_file_end = false;
        while (! (\feof($inputHandle) || $at_file_end)) {
            /* Find out if we can read a full buffer, or only a partial one. */
            $pos = \ftell($inputHandle);
            if ($pos === false) {
                throw new Ex\IOException(
                    'Could not get current position in input file during encryption'
                );
            }
            if ($pos + Core::BUFFER_BYTE_SIZE >= $inputSize) {
                /* We're at the end of the file, so we need to break out of the loop. */
                $at_file_end = true;
                $read = self::readBytes(
                    $inputHandle,
                    $inputSize - $pos
                );
            } else {
                $read = self::readBytes(
                    $inputHandle,
                    Core::BUFFER_BYTE_SIZE
                );
            }

            /* Encrypt this buffer. */
            $encrypted = \openssl_encrypt(
                $read,
                Core::CIPHER_METHOD,
                $ekey,
                OPENSSL_RAW_DATA,
                $thisIv
            );

            if ($encrypted === false) {
                throw new Ex\EnvironmentIsBrokenException(
                    'OpenSSL encryption error'
                );
            }

            /* Write this buffer's ciphertext. */
            self::writeBytes($outputHandle, $encrypted, Core::ourStrlen($encrypted));
            /* Add this buffer's ciphertext to the HMAC. */
            \hash_update($hmac, $encrypted);

            /* Increment the counter by the number of blocks in a buffer. */
            $thisIv = Core::incrementCounter($thisIv, $inc);
            /* WARNING: Usually, unless the file is a multiple of the buffer
             * size, $thisIv will contain an incorrect value here on the last
             * iteration of this loop. */
        }

        /* Get the HMAC and append it to the ciphertext. */
        $final_mac = \hash_final($hmac, true);
        self::writeBytes($outputHandle, $final_mac, Core::MAC_BYTE_SIZE);
    }

    /**
     * Decrypts a file-backed resource with either a key or a password.
     *
     * @param resource      $inputHandle
     * @param resource      $outputHandle
     * @param KeyOrPassword $secret
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\IOException
     * @throws Ex\WrongKeyOrModifiedCiphertextException
     */
    public static function decryptResourceInternal($inputHandle, $outputHandle, KeyOrPassword $secret)
    {
        if (! \is_resource($inputHandle)) {
            throw new Ex\IOException(
                'Input handle must be a resource!'
            );
        }
        if (! \is_resource($outputHandle)) {
            throw new Ex\IOException(
                'Output handle must be a resource!'
            );
        }

        /* Make sure the file is big enough for all the reads we need to do. */
        $stat = \fstat($inputHandle);
        if ($stat['size'] < Core::MINIMUM_CIPHERTEXT_SIZE) {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Input file is too small to have been created by this library.'
            );
        }

        /* Check the version header. */
        $header = self::readBytes($inputHandle, Core::HEADER_VERSION_SIZE);
        if ($header !== Core::CURRENT_VERSION) {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Bad version header.'
            );
        }

        /* Get the salt. */
        $file_salt = self::readBytes($inputHandle, Core::SALT_BYTE_SIZE);

        /* Get the IV. */
        $ivsize = Core::BLOCK_BYTE_SIZE;
        $iv     = self::readBytes($inputHandle, $ivsize);

        /* Derive the authentication and encryption keys. */
        $keys = $secret->deriveKeys($file_salt);
        $ekey = $keys->getEncryptionKey();
        $akey = $keys->getAuthenticationKey();

        /* We'll store the MAC of each buffer-sized chunk as we verify the
         * actual MAC, so that we can check them again when decrypting. */
        $macs = [];

        /* $thisIv will be incremented after each call to the decryption. */
        $thisIv = $iv;

        /* How many blocks do we encrypt at a time? We increment by this value. */
        $inc = Core::BUFFER_BYTE_SIZE / Core::BLOCK_BYTE_SIZE;

        /* Get the HMAC. */
        if (\fseek($inputHandle, (-1 * Core::MAC_BYTE_SIZE), SEEK_END) === false) {
            throw new Ex\IOException(
                'Cannot seek to beginning of MAC within input file'
            );
        }

        /* Get the position of the last byte in the actual ciphertext. */
        $cipher_end = \ftell($inputHandle);
        if ($cipher_end === false) {
            throw new Ex\IOException(
                'Cannot read input file'
            );
        }
        /* We have the position of the first byte of the HMAC. Go back by one. */
        --$cipher_end;

        /* Read the HMAC. */
        $stored_mac = self::readBytes($inputHandle, Core::MAC_BYTE_SIZE);

        /* Initialize a streaming HMAC state. */
        $hmac = \hash_init(Core::HASH_FUNCTION_NAME, HASH_HMAC, $akey);
        if ($hmac === false) {
            throw new Ex\EnvironmentIsBrokenException(
                'Cannot initialize a hash context'
            );
        }

        /* Reset file pointer to the beginning of the file after the header */
        if (\fseek($inputHandle, Core::HEADER_VERSION_SIZE, SEEK_SET) === false) {
            throw new Ex\IOException(
                'Cannot read seek within input file'
            );
        }

        /* Seek to the start of the actual ciphertext. */
        if (\fseek($inputHandle, Core::SALT_BYTE_SIZE + $ivsize, SEEK_CUR) === false) {
            throw new Ex\IOException(
                'Cannot seek input file to beginning of ciphertext'
            );
        }

        /* PASS #1: Calculating the HMAC. */

        \hash_update($hmac, $header);
        \hash_update($hmac, $file_salt);
        \hash_update($hmac, $iv);
        $hmac2 = \hash_copy($hmac);

        $break = false;
        while (! $break) {
            $pos = \ftell($inputHandle);
            if ($pos === false) {
                throw new Ex\IOException(
                    'Could not get current position in input file during decryption'
                );
            }

            /* Read the next buffer-sized chunk (or less). */
            if ($pos + Core::BUFFER_BYTE_SIZE >= $cipher_end) {
                $break = true;
                $read  = self::readBytes(
                    $inputHandle,
                    $cipher_end - $pos + 1
                );
            } else {
                $read = self::readBytes(
                    $inputHandle,
                    Core::BUFFER_BYTE_SIZE
                );
            }

            /* Update the HMAC. */
            \hash_update($hmac, $read);

            /* Remember this buffer-sized chunk's HMAC. */
            $chunk_mac = \hash_copy($hmac);
            if ($chunk_mac === false) {
                throw new Ex\EnvironmentIsBrokenException(
                    'Cannot duplicate a hash context'
                );
            }
            $macs []= \hash_final($chunk_mac);
        }

        /* Get the final HMAC, which should match the stored one. */
        $final_mac = \hash_final($hmac, true);

        /* Verify the HMAC. */
        if (! Core::hashEquals($final_mac, $stored_mac)) {
            throw new Ex\WrongKeyOrModifiedCiphertextException(
                'Integrity check failed.'
            );
        }

        /* PASS #2: Decrypt and write output. */

        /* Rewind to the start of the actual ciphertext. */
        if (\fseek($inputHandle, Core::SALT_BYTE_SIZE + $ivsize + Core::HEADER_VERSION_SIZE, SEEK_SET) === false) {
            throw new Ex\IOException(
                'Could not move the input file pointer during decryption'
            );
        }

        $at_file_end = false;
        while (! $at_file_end) {
            $pos = \ftell($inputHandle);
            if ($pos === false) {
                throw new Ex\IOException(
                    'Could not get current position in input file during decryption'
                );
            }

            /* Read the next buffer-sized chunk (or less). */
            if ($pos + Core::BUFFER_BYTE_SIZE >= $cipher_end) {
                $at_file_end = true;
                $read   = self::readBytes(
                    $inputHandle,
                    $cipher_end - $pos + 1
                );
            } else {
                $read = self::readBytes(
                    $inputHandle,
                    Core::BUFFER_BYTE_SIZE
                );
            }

            /* Recalculate the MAC (so far) and compare it with the one we
             * remembered from pass #1 to ensure attackers didn't change the
             * ciphertext after MAC verification. */
            \hash_update($hmac2, $read);
            $calc_mac = \hash_copy($hmac2);
            if ($calc_mac === false) {
                throw new Ex\EnvironmentIsBrokenException(
                    'Cannot duplicate a hash context'
                );
            }
            $calc = \hash_final($calc_mac);

            if (empty($macs)) {
                throw new Ex\WrongKeyOrModifiedCiphertextException(
                    'File was modified after MAC verification'
                );
            } elseif (! Core::hashEquals(\array_shift($macs), $calc)) {
                throw new Ex\WrongKeyOrModifiedCiphertextException(
                    'File was modified after MAC verification'
                );
            }

            /* Decrypt this buffer-sized chunk. */
            $decrypted = \openssl_decrypt(
                $read,
                Core::CIPHER_METHOD,
                $ekey,
                OPENSSL_RAW_DATA,
                $thisIv
            );
            if ($decrypted === false) {
                throw new Ex\EnvironmentIsBrokenException(
                    'OpenSSL decryption error'
                );
            }

            /* Write the plaintext to the output file. */
            self::writeBytes(
                $outputHandle,
                $decrypted,
                Core::ourStrlen($decrypted)
            );

            /* Increment the IV by the amount of blocks in a buffer. */
            $thisIv = Core::incrementCounter($thisIv, $inc);
            /* WARNING: Usually, unless the file is a multiple of the buffer
             * size, $thisIv will contain an incorrect value here on the last
             * iteration of this loop. */
        }
    }

    /**
     * Read from a stream; prevent partial reads.
     *
     * @param resource $stream
     * @param int      $num_bytes
     *
     * @throws Ex\IOException
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return string
     */
    public static function readBytes($stream, $num_bytes)
    {
        if ($num_bytes < 0) {
            throw new Ex\EnvironmentIsBrokenException(
                'Tried to read less than 0 bytes'
            );
        } elseif ($num_bytes === 0) {
            return '';
        }
        $buf       = '';
        $remaining = $num_bytes;
        while ($remaining > 0 && ! \feof($stream)) {
            $read = \fread($stream, $remaining);

            if ($read === false) {
                throw new Ex\IOException(
                    'Could not read from the file'
                );
            }
            $buf .= $read;
            $remaining -= Core::ourStrlen($read);
        }
        if (Core::ourStrlen($buf) !== $num_bytes) {
            throw new Ex\IOException(
                'Tried to read past the end of the file'
            );
        }
        return $buf;
    }

    /**
     * Write to a stream; prevents partial writes.
     *
     * @param resource $stream
     * @param string   $buf
     * @param int      $num_bytes
     *
     * @throws Ex\IOException
     *
     * @return string
     */
    public static function writeBytes($stream, $buf, $num_bytes = null)
    {
        $bufSize = Core::ourStrlen($buf);
        if ($num_bytes === null) {
            $num_bytes = $bufSize;
        }
        if ($num_bytes > $bufSize) {
            throw new Ex\IOException(
                'Trying to write more bytes than the buffer contains.'
            );
        }
        if ($num_bytes < 0) {
            throw new Ex\IOException(
                'Tried to write less than 0 bytes'
            );
        }
        $remaining = $num_bytes;
        while ($remaining > 0) {
            $written = \fwrite($stream, $buf, $remaining);
            if ($written === false) {
                throw new Ex\IOException(
                    'Could not write to the file'
                );
            }
            $buf = Core::ourSubstr($buf, $written, null);
            $remaining -= $written;
        }
        return $num_bytes;
    }

    /**
     * Returns the last PHP error's or warning's message string.
     *
     * @return string
     */
    private static function getLastErrorMessage()
    {
        $error = error_get_last();
        if ($error === null) {
            return '[no PHP error]';
        } else {
            return $error['message'];
        }
    }
}
