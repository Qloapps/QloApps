<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class representing entries in an RSS2 feed.
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
 * @version    CVS: $Id: RSS2Element.php 6844 2011-06-03 14:46:51Z dMetzger $
 * @link       http://pear.php.net/package/XML_Feed_Parser/
 */

/**
 * This class provides support for RSS 2.0 entries. It will usually be 
 * called by XML_Feed_Parser_RSS2 with which it shares many methods.
 *
 * @author    James Stewart <james@jystewart.net>
 * @version    Release: @package_version@
 * @package XML_Feed_Parser
 */
class XML_Feed_Parser_RSS2Element extends XML_Feed_Parser_RSS2
{
    /**
     * This will be a reference to the parent object for when we want
     * to use a 'fallback' rule
     * @var XML_Feed_Parser_RSS2
     */
    protected $parent;

    /**
     * Our specific element map 
     * @var array
     */
    protected $map = array(
        'title' => array('Text'),
        'guid' => array('Guid'),
        'description' => array('Text'),
        'author' => array('Text'),
        'comments' => array('Text'),
        'enclosure' => array('Enclosure'),
        'pubDate' => array('Date'),
        'source' => array('Source'),
        'link' => array('Text'),
        'content' => array('Content'));

    /**
     * Here we map some elements to their atom equivalents. This is going to be
     * quite tricky to pull off effectively (and some users' methods may vary)
     * but is worth trying. The key is the atom version, the value is RSS2.
     * @var array
     */
    protected $compatMap = array(
        'id' => array('guid'),
        'updated' => array('lastBuildDate'),
        'published' => array('pubdate'),
        'guidislink' => array('guid', 'ispermalink'),
        'summary' => array('description'));

    /**
     * Store useful information for later.
     *
     * @param   DOMElement  $element - this item as a DOM element
     * @param   XML_Feed_Parser_RSS2    $parent - the feed of which this is a member
     */
    function __construct(DOMElement $element, $parent, $xmlBase = '')
    {
        $this->model = $element;
        $this->parent = $parent;
    }

    /**
     * Get the value of the guid element, if specified
     *
     * guid is the closest RSS2 has to atom's ID. It is usually but not always a
     * URI. The one attribute that RSS2 can posess is 'ispermalink' which specifies
     * whether the guid is itself dereferencable. Use of guid is not obligatory,
     * but is advisable. To get the guid you would call $item->id() (for atom
     * compatibility) or $item->guid(). To check if this guid is a permalink call
     * $item->guid("ispermalink").
     *
     * @param   string  $method - the method name being called
     * @param   array   $params - parameters required
     * @return  string  the guid or value of ispermalink
     */
    protected function getGuid($method, $params)
    {
        $attribute = (isset($params[0]) and $params[0] == 'ispermalink') ? 
            true : false;
        $tag = $this->model->getElementsByTagName('guid');
        if ($tag->length > 0) {
            if ($attribute) {
                if ($tag->hasAttribute("ispermalink")) {
                    return $tag->getAttribute("ispermalink");
                }
            }
            return $tag->item(0)->nodeValue;
        }
        return false;
    }

    /**
     * Access details of file enclosures
     *
     * The RSS2 spec is ambiguous as to whether an enclosure element must be
     * unique in a given entry. For now we will assume it needn't, and allow
     * for an offset.
     *
     * @param   string $method - the method being called
     * @param   array   $parameters - we expect the first of these to be our offset
     * @return  array|false
     */
    protected function getEnclosure($method, $parameters)
    {
        $encs = $this->model->getElementsByTagName('enclosure');
        $offset = isset($parameters[0]) ? $parameters[0] : 0;
        if ($encs->length > $offset) {
            try {
                if (! $encs->item($offset)->hasAttribute('url')) {
                    return false;
                }
                $attrs = $encs->item($offset)->attributes;
                return array(
                    'url' => $attrs->getNamedItem('url')->value,
                    'length' => $attrs->getNamedItem('length')->value,
                    'type' => $attrs->getNamedItem('type')->value);
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Get the entry source if specified
     *
     * source is an optional sub-element of item. Like atom:source it tells
     * us about where the entry came from (eg. if it's been copied from another
     * feed). It is not a rich source of metadata in the same way as atom:source
     * and while it would be good to maintain compatibility by returning an
     * XML_Feed_Parser_RSS2 element, it makes a lot more sense to return an array.
     *
     * @return array|false
     */
    protected function getSource()
    {
        $get = $this->model->getElementsByTagName('source');
        if ($get->length) {
            $source = $get->item(0);
            $array = array(
                'content' => $source->nodeValue);
            foreach ($source->attributes as $attribute) {
                $array[$attribute->name] = $attribute->value;
            }
            return $array;
        }
        return false;
    }
}

?>