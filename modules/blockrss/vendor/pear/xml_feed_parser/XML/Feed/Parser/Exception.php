<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Keeps the exception class for XML_Feed_Parser.
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
 * We are extending PEAR_Exception
 */
require_once 'PEAR/Exception.php';

/**
 * XML_Feed_Parser_Exception is a simple extension of PEAR_Exception, existing
 * to help with identification of the source of exceptions.
 *
 * @author  James Stewart <james@jystewart.net>
 * @version Release: @package_version@
 * @package XML_Feed_Parser
 */ 
class XML_Feed_Parser_Exception extends PEAR_Exception
{

}

