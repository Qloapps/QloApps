<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Key gateway class for XML_Feed_Parser package
 *
 * PHP versions 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   XML
 * @package    XML_Feed_Parser
 * @author     James Stewart <james@jystewart.net>
 * @copyright  2005 James Stewart <james@jystewart.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  GNU LGPL
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/XML_Feed_Parser/
 */

/**
 * XML_Feed_Parser_Type is an abstract class required by all of our
 * feed types. It makes sense to load it here to keep the other files
 * clean.
 */
require_once 'XML/Feed/Parser/Type.php';

/**
 * We will throw exceptions when errors occur.
 */
require_once 'XML/Feed/Parser/Exception.php';

/**
 * This is the core of the XML_Feed_Parser package. It identifies feed types 
 * and abstracts access to them. It is an iterator, allowing for easy access 
 * to the entire feed.
 *
 * @author  James Stewart <james@jystewart.net>
 * @version Release: @package_version@
 * @package XML_Feed_Parser
 */
class XML_Feed_Parser2 extends XML_Feed_Parser
{
    /**
     * A variant of XML_Feed_Parser which does no work in the constructor.
     *
     * @see initialize()
     */
    function __construct()
    {
        $this->model = new DOMDocument;
    }


}
