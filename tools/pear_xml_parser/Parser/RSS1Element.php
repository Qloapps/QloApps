<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RSS1 Element class for XML_Feed_Parser
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
 * @version    CVS: $Id: RSS1Element.php 6844 2011-06-03 14:46:51Z dMetzger $
 * @link       http://pear.php.net/package/XML_Feed_Parser/
 */

/*
 * This class provides support for RSS 1.0 entries. It will usually be called by
 * XML_Feed_Parser_RSS1 with which it shares many methods.
 *
 * @author    James Stewart <james@jystewart.net>
 * @version    Release: @package_version@
 * @package XML_Feed_Parser
 */
class XML_Feed_Parser_RSS1Element extends XML_Feed_Parser_RSS1
{
    /**
     * This will be a reference to the parent object for when we want
     * to use a 'fallback' rule 
     * @var XML_Feed_Parser_RSS1
     */
    protected $parent;

    /**
     * Our specific element map 
     * @var array
     */
    protected $map = array(
        'id' => array('Id'),
        'title' => array('Text'),
        'link' => array('Link'),
        'description' => array('Text'), # or dc:description
        'category' => array('Category'),
        'rights' => array('Text'), # dc:rights
        'creator' => array('Text'), # dc:creator
        'publisher' => array('Text'), # dc:publisher
        'contributor' => array('Text'), # dc:contributor
        'date' => array('Date'), # dc:date
        'content' => array('Content')
        );

    /**
     * Here we map some elements to their atom equivalents. This is going to be
     * quite tricky to pull off effectively (and some users' methods may vary)
     * but is worth trying. The key is the atom version, the value is RSS1.
     * @var array
     */
    protected $compatMap = array(
        'content' => array('content'),
        'updated' => array('lastBuildDate'),
        'published' => array('date'),
        'subtitle' => array('description'),
        'updated' => array('date'),
        'author' => array('creator'),
        'contributor' => array('contributor')
    );

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

    /**
     * If an rdf:about attribute is specified, return it as an ID
     *
     * There is no established way of showing an ID for an RSS1 entry. We will 
     * simulate it using the rdf:about attribute of the entry element. This cannot
     * be relied upon for unique IDs but may prove useful.
     *
     * @return  string|false
     */
    function getId()
    {
        if ($this->model->attributes->getNamedItem('about')) {
            return $this->model->attributes->getNamedItem('about')->nodeValue;
        }
        return false;
    }

    /**
     * How RSS1 should support for enclosures is not clear. For now we will return
     * false.
     *
     * @return  false
     */
    function getEnclosure()
    {
        return false;
    }
}

?>
