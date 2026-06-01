<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/



class PasswordHashingCore
{
    /** @var array Available password hashing methods. */
    private $passwordHashMethods = array();

    /**
     * Check whether the stored hash matches the primary hashing method.
     *
     * @param string $passwd Plain-text password to validate.
     * @param string $hash Stored password hash.
     * @param string $staticSalt Static salt used by legacy hash methods.
     *
     * @return bool
     */
    public function isPrimaryHash($passwd, $hash, $staticSalt = _COOKIE_KEY_)
    {
        if (!count($this->passwordHashMethods)) {
            $this->initPasswordHashMethods();
        }

        $closure = reset($this->passwordHashMethods);

        return $closure['verify']($passwd, $hash, $staticSalt);
    }

    /**
     * Validate a password against all supported hashing methods.
     *
     * @param string $passwd Plain-text password to validate.
     * @param string $hash Stored password hash.
     * @param string $staticSalt Static salt used by legacy hash methods.
     *
     * @return bool
     */
    public function validateHash($passwd, $hash, $staticSalt = _COOKIE_KEY_)
    {
        if (!count($this->passwordHashMethods)) {
            $this->initPasswordHashMethods();
        }
        
        foreach ($this->passwordHashMethods as $closure) {
            if ($closure['verify']($passwd, $hash, $staticSalt)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hash a plain-text password with the primary hashing method.
     *
     * @param string $plaintextPassword Password to hash.
     * @param string $staticSalt Static salt reserved for legacy hash methods.
     *
     * @return string
     */
    public function passwordHash($plaintextPassword, $staticSalt = _COOKIE_KEY_)
    {
        if (!count($this->passwordHashMethods)) {
            $this->initPasswordHashMethods();
        }

        $closure = reset($this->passwordHashMethods);

        return $closure['hash']($plaintextPassword, $staticSalt, $closure['option']);
    }

    /**
     * Initialize the supported password hashing methods.
     *
     * @return void
     */
    private function initPasswordHashMethods()
    {
        $this->passwordHashMethods = array(
            'bcrypt' => array(
                'option' => array(),
                'hash' => function ($passwd, $staticSalt, $option) {
                    return password_hash($passwd, PASSWORD_BCRYPT);
                },
                'verify' => function ($passwd, $hash, $staticSalt) {
                    return password_verify($passwd, $hash);
                },
            ),
            'md5' => array(
                'option' => array(),
                'hash' => function ($passwd, $staticSalt, $option) {
                    return md5($staticSalt.$passwd);
                },
                'verify' => function ($passwd, $hash, $staticSalt) {
                    return md5($staticSalt.$passwd) === $hash;
                },
            ),
        );
    }
}
