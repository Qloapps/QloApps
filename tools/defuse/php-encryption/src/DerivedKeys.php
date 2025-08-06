<?php

namespace Defuse\Crypto;

final class DerivedKeys
{
    private $akey = null;
    private $ekey = null;

    /**
     * Returns the authentication key.
     */
    public function getAuthenticationKey()
    {
        return $this->akey;
    }

    /**
     * Returns the encryption key.
     */
    public function getEncryptionKey()
    {
        return $this->ekey;
    }

    /**
     * Constructor for DerivedKeys.
     *
     * @param string $akey
     * @param string $ekey
     */
    public function __construct($akey, $ekey)
    {
        $this->akey = $akey;
        $this->ekey = $ekey;
    }
}
