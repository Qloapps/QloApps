<?php

namespace Defuse\Crypto;

use Defuse\Crypto\Exception as Ex;

final class KeyOrPassword
{
    const PBKDF2_ITERATIONS    = 100000;
    const SECRET_TYPE_KEY      = 1;
    const SECRET_TYPE_PASSWORD = 2;

    private $secret_type = null;
    private $secret      = null;

    /**
     * Initializes an instance of KeyOrPassword from a key.
     *
     * @param Key $key
     *
     * @return KeyOrPassword
     */
    public static function createFromKey(Key $key)
    {
        return new KeyOrPassword(self::SECRET_TYPE_KEY, $key);
    }

    /**
     * Initializes an instance of KeyOrPassword from a password.
     *
     * @param string $password
     *
     * @return KeyOrPassword
     */
    public static function createFromPassword($password)
    {
        return new KeyOrPassword(self::SECRET_TYPE_PASSWORD, $password);
    }

    /**
     * Derives authentication and encryption keys from the secret, using a slow
     * key derivation function if the secret is a password.
     *
     * @param string $salt
     *
     * @throws Ex\EnvironmentIsBrokenException
     *
     * @return DerivedKeys
     */
    public function deriveKeys($salt)
    {
        if (Core::ourStrlen($salt) !== Core::SALT_BYTE_SIZE) {
            throw new Ex\EnvironmentIsBrokenException('Bad salt.');
        }

        if ($this->secret_type === self::SECRET_TYPE_KEY) {
            $akey = Core::HKDF(
                Core::HASH_FUNCTION_NAME,
                $this->secret->getRawBytes(),
                Core::KEY_BYTE_SIZE,
                Core::AUTHENTICATION_INFO_STRING,
                $salt
            );
            $ekey = Core::HKDF(
                Core::HASH_FUNCTION_NAME,
                $this->secret->getRawBytes(),
                Core::KEY_BYTE_SIZE,
                Core::ENCRYPTION_INFO_STRING,
                $salt
            );
            return new DerivedKeys($akey, $ekey);
        } elseif ($this->secret_type === self::SECRET_TYPE_PASSWORD) {
            /* Our PBKDF2 polyfill is vulnerable to a DoS attack documented in
             * GitHub issue #230. The fix is to pre-hash the password to ensure
             * it is short. We do the prehashing here instead of in pbkdf2() so
             * that pbkdf2() still computes the function as defined by the
             * standard. */
            $prehash = \hash(Core::HASH_FUNCTION_NAME, $this->secret, true);
            $prekey = Core::pbkdf2(
                Core::HASH_FUNCTION_NAME,
                $prehash,
                $salt,
                self::PBKDF2_ITERATIONS,
                Core::KEY_BYTE_SIZE,
                true
            );
            $akey = Core::HKDF(
                Core::HASH_FUNCTION_NAME,
                $prekey,
                Core::KEY_BYTE_SIZE,
                Core::AUTHENTICATION_INFO_STRING,
                $salt
            );
            /* Note the cryptographic re-use of $salt here. */
            $ekey = Core::HKDF(
                Core::HASH_FUNCTION_NAME,
                $prekey,
                Core::KEY_BYTE_SIZE,
                Core::ENCRYPTION_INFO_STRING,
                $salt
            );
            return new DerivedKeys($akey, $ekey);
        } else {
            throw new Ex\EnvironmentIsBrokenException('Bad secret type.');
        }
    }

    /**
     * Constructor for KeyOrPassword.
     *
     * @param int   $secret_type
     * @param mixed $secret      (either a Key or a password string)
     */
    private function __construct($secret_type, $secret)
    {
        $this->secret_type = $secret_type;
        $this->secret = $secret;
    }
}
