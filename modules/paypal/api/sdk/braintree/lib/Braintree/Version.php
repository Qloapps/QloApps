<?php
namespace Braintree;

/**
 * Braintree Library Version
 * stores version information about the Braintree library
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
final class Version
{
    /**
     * class constants
     */
    const MAJOR = 3;
    const MINOR = 10;
    const TINY = 0;

    /**
     * @ignore
     * @access protected
     */
    protected function  __construct()
    {
    }

    /**
     *
     * @return string the current library version
     */
    public static function get()
    {
        return self::MAJOR . '.' . self::MINOR . '.' . self::TINY;
    }
}
class_alias('Braintree\Version', 'Braintree_Version');
