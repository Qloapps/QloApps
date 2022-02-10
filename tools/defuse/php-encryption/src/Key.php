<?php

namespace Defuse\Crypto;

use Defuse\Crypto\Exception as Ex;

final class Key
{
    const KEY_CURRENT_VERSION = "\xDE\xF0\x00\x00";
    const KEY_BYTE_SIZE       = 32;

    private $key_bytes = null;

    /**
     * Creates new random key.
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return Key
     */
    public static function createNewRandomKey()
    {
        return new Key(Core::secureRandom(self::KEY_BYTE_SIZE));
    }

    /**
     * Loads a Key from its encoded form.
     *
     * @param string $saved_key_string
     *
     * @throws Ex\BadFormatException
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return Key
     */
    public static function loadFromAsciiSafeString($saved_key_string)
    {
        $key_bytes = Encoding::loadBytesFromChecksummedAsciiSafeString(self::KEY_CURRENT_VERSION, $saved_key_string);
        return new Key($key_bytes);
    }

    /**
     * Encodes the Key into a string of printable ASCII characters.
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return string
     */
    public function saveToAsciiSafeString()
    {
        return Encoding::saveBytesToChecksummedAsciiSafeString(
            self::KEY_CURRENT_VERSION,
            $this->key_bytes
        );
    }

    /**
     * Gets the raw bytes of the key.
     *
     * @return string
     */
    public function getRawBytes()
    {
        return $this->key_bytes;
    }

    /**
     * Constructs a new Key object from a string of raw bytes.
     *
     * @param string $bytes
     *
     * @throws Ex\EnvironmentIsBrokenException
     */
    private function __construct($bytes)
    {
        if (Core::ourStrlen($bytes) !== self::KEY_BYTE_SIZE) {
            throw new Ex\EnvironmentIsBrokenException(
                'Bad key length.'
            );
        }
        $this->key_bytes = $bytes;
    }

}
