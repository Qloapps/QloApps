<?php
namespace Braintree;

/**
 * Braintree Xml parser and generator
 * PHP version 5
 * superclass for Braintree XML parsing and generation
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
final class Xml
{
    /**
     * @ignore
     */
    protected function  __construct()
    {

    }

    /**
     *
     * @param string $xml
     * @return array
     */
    public static function buildArrayFromXml($xml)
    {
        return Xml\Parser::arrayFromXml($xml);
    }

    /**
     *
     * @param array $array
     * @return string
     */
    public static function buildXmlFromArray($array)
    {
        return Xml\Generator::arrayToXml($array);
    }
}
class_alias('Braintree\Xml', 'Braintree_Xml');
