<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RSS0.9 Element class for XML_Feed_Parser
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
 * @license    http://www.gnu.org/copyleft/lesser.html  GNU LGPL 2.1
 * @version    CVS: $Id: RSS09Element.php 6844 2011-06-03 14:46:51Z dMetzger $
 * @link       http://pear.php.net/package/XML_Feed_Parser/
 */

/*
 * This class provides support for RSS 0.9 entries. It will usually be called by
 * XML_Feed_Parser_RSS09 with which it shares many methods.
 *
 * @author    James Stewart <james@jystewart.net>
 * @version    Release: @package_version@
 * @package XML_Feed_Parser
 */
class XML_Feed_Parser_RSS09Element extends XML_Feed_Parser_RSS09
{
    /**
     * This will be a reference to the parent object for when we want
     * to use a 'fallback' rule 
     * @var XML_Feed_Parser_RSS09
     */
    protected $parent;

    /**
     * Our specific element map 
     * @var array
     */
    protected $map = array(
        'title' => array('Text'),
        'link' => array('Link'));

    /**
     * Store useful information for later.
     *
     * @param   DOMElement  $element - this item as a DOM element
     * @param   XML_Feed_Parser_RSS1 $parent - the feed of which this is a member
     */
    function __construct(DOMElement $element, $parent, $xmlBase = '')
    {
        $this->model = $element;
        $this->parent = $parent;
    }
}

?>