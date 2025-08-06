<?php

namespace Defuse\Crypto;

use Defuse\Crypto\Exception as Ex;

final class Encoding
{
    const CHECKSUM_BYTE_SIZE     = 32;
    const CHECKSUM_HASH_ALGO     = 'sha256';
    const SERIALIZE_HEADER_BYTES = 4;

    /**
     * Converts a byte string to a hexadecimal string without leaking
     * information through side channels.
     *
     * @param string $byte_string
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return string
     */
    public static function binToHex($byte_string)
    {
        $hex = '';
        $len = Core::ourStrlen($byte_string);
        for ($i = 0; $i < $len; ++$i) {
            $c = \ord($byte_string[$i]) & 0xf;
            $b = \ord($byte_string[$i]) >> 4;
            $hex .= \pack(
                'CC',
                87 + $b + ((($b - 10) >> 8) & ~38),
                87 + $c + ((($c - 10) >> 8) & ~38)
            );
        }
        return $hex;
    }

    /**
     * Converts a hexadecimal string into a byte string without leaking
     * information through side channels.
     *
     * @param string $hex_string
     *
     * @throws Ex\BadFormatException
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return string
     */
    public static function hexToBin($hex_string)
    {
        $hex_pos = 0;
        $bin     = '';
        $hex_len = Core::ourStrlen($hex_string);
        $state   = 0;
        $c_acc   = 0;

        while ($hex_pos < $hex_len) {
            $c        = \ord($hex_string[$hex_pos]);
            $c_num    = $c ^ 48;
            $c_num0   = ($c_num - 10) >> 8;
            $c_alpha  = ($c & ~32) - 55;
            $c_alpha0 = (($c_alpha - 10) ^ ($c_alpha - 16)) >> 8;
            if (($c_num0 | $c_alpha0) === 0) {
                throw new Ex\BadFormatException(
                    'Encoding::hexToBin() input is not a hex string.'
                );
            }
            $c_val = ($c_num0 & $c_num) | ($c_alpha & $c_alpha0);
            if ($state === 0) {
                $c_acc = $c_val * 16;
            } else {
                $bin .= \pack('C', $c_acc | $c_val);
            }
            $state ^= 1;
            ++$hex_pos;
        }
        return $bin;
    }

    /*
     * SECURITY NOTE ON APPLYING CHECKSUMS TO SECRETS:
     *
     *      The checksum introduces a potential security weakness. For example,
     *      suppose we apply a checksum to a key, and that an adversary has an
     *      exploit against the process containing the key, such that they can
     *      overwrite an arbitrary byte of memory and then cause the checksum to
     *      be verified and learn the result.
     *
     *      In this scenario, the adversary can extract the key one byte at
     *      a time by overwriting it with their guess of its value and then
     *      asking if the checksum matches. If it does, their guess was right.
     *      This kind of attack may be more easy to implement and more reliable
     *      than a remote code execution attack.
     *
     *      This attack also applies to authenticated encryption as a whole, in
     *      the situation where the adversary can overwrite a byte of the key
     *      and then cause a valid ciphertext to be decrypted, and then
     *      determine whether the MAC check passed or failed.
     *
     *      By using the full SHA256 hash instead of truncating it, I'm ensuring
     *      that both ways of going about the attack are equivalently difficult.
     *      A shorter checksum of say 32 bits might be more useful to the
     *      adversary as an oracle in case their writes are coarser grained.
     *
     *      Because the scenario assumes a serious vulnerability, we don't try
     *      to prevent attacks of this style.
     */

    /**
     * INTERNAL USE ONLY: Applies a version header, applies a checksum, and
     * then encodes a byte string into a range of printable ASCII characters.
     *
     * @param string $header
     * @param string $bytes
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return string
     */
    public static function saveBytesToChecksummedAsciiSafeString($header, $bytes)
    {
        // Headers must be a constant length to prevent one type's header from
        // being a prefix of another type's header, leading to ambiguity.
        if (Core::ourStrlen($header) !== self::SERIALIZE_HEADER_BYTES) {
            throw new Ex\EnvironmentIsBrokenException(
                'Header must be ' . self::SERIALIZE_HEADER_BYTES . ' bytes.'
            );
        }

        return Encoding::binToHex(
            $header .
            $bytes .
            \hash(
                self::CHECKSUM_HASH_ALGO,
                $header . $bytes,
                true
            )
        );
    }

    /**
     * INTERNAL USE ONLY: Decodes, verifies the header and checksum, and returns
     * the encoded byte string.
     *
     * @param string $expected_header
     * @param string $string
     *
     * @throws Ex\EnvironmentIsBrokenException
     * @throws Ex\BadFormatException
     *
     * @return string
     */
    public static function loadBytesFromChecksummedAsciiSafeString($expected_header, $string)
    {
        // Headers must be a constant length to prevent one type's header from
        // being a prefix of another type's header, leading to ambiguity.
        if (Core::ourStrlen($expected_header) !== self::SERIALIZE_HEADER_BYTES) {
            throw new Ex\EnvironmentIsBrokenException(
                'Header must be 4 bytes.'
            );
        }

        $bytes = Encoding::hexToBin($string);

        /* Make sure we have enough bytes to get the version header and checksum. */
        if (Core::ourStrlen($bytes) < self::SERIALIZE_HEADER_BYTES + self::CHECKSUM_BYTE_SIZE) {
            throw new Ex\BadFormatException(
                'Encoded data is shorter than expected.'
            );
        }

        /* Grab the version header. */
        $actual_header = Core::ourSubstr($bytes, 0, self::SERIALIZE_HEADER_BYTES);

        if ($actual_header !== $expected_header) {
            throw new Ex\BadFormatException(
                'Invalid header.'
            );
        }

        /* Grab the bytes that are part of the checksum. */
        $checked_bytes = Core::ourSubstr(
            $bytes,
            0,
            Core::ourStrlen($bytes) - self::CHECKSUM_BYTE_SIZE
        );

        /* Grab the included checksum. */
        $checksum_a = Core::ourSubstr(
            $bytes,
            Core::ourStrlen($bytes) - self::CHECKSUM_BYTE_SIZE,
            self::CHECKSUM_BYTE_SIZE
        );

        /* Re-compute the checksum. */
        $checksum_b = \hash(self::CHECKSUM_HASH_ALGO, $checked_bytes, true);

        /* Check if the checksum matches. */
        if (! Core::hashEquals($checksum_a, $checksum_b)) {
            throw new Ex\BadFormatException(
                "Data is corrupted, the checksum doesn't match"
            );
        }

        return Core::ourSubstr(
            $bytes,
            self::SERIALIZE_HEADER_BYTES,
            Core::ourStrlen($bytes) - self::SERIALIZE_HEADER_BYTES - self::CHECKSUM_BYTE_SIZE
        );
    }
}
