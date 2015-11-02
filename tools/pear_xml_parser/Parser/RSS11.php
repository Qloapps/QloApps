<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RSS1.1 class for XML_Feed_Parser
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
 * @version    CVS: $Id: RSS11.php 6844 2011-06-03 14:46:51Z dMetzger $
 * @link       http://pear.php.net/package/XML_Feed_Parser/
 */

/**
 * This class handles RSS1.1 feeds. RSS1.1 is documented at:
 * http://inamidst.com/rss1.1/
 * 
 * @author    James Stewart <james@jystewart.net>
 * @version    Release: @package_version@
 * @package XML_Feed_Parser
 * @todo    Support for RDF:List
 * @todo    Ensure xml:lang is accessible to users
 */
class XML_Feed_Parser_RSS11 extends XML_Feed_Parser_Type
{
    /**
     * The URI of the RelaxNG schema used to (optionally) validate the feed 
     * @var string
     */
    protected $relax = 'rss11.rng';

    /**
     * We're likely to use XPath, so let's keep it global
     * @var DOMXPath
     */
    protected $xpath;

    /**
     * The feed type we are parsing 
     * @var string
     */
    public $version = 'RSS 1.0';

    /**
     * The class used to represent individual items 
     * @var string
     */
    protected $itemClass = 'XML_Feed_Parser_RSS1Element';
    
    /**
     * The element containing entries 
     * @var string
     */
    protected $itemElement = 'item';

    /**
     * Here we map those elements we're not going to handle individually
     * to the constructs they are. The optional second parameter in the array
     * tells the parser whether to 'fall back' (not apt. at the feed level) or
     * fail if the element is missing. If the parameter is not set, the function
     * will simply return false and leave it to the client to decide what to do.
     * @var array
     */
    protected $map = array(
        'title' => array('Text'),
        'link' => array('Text'),
        'description' => array('Text'),
        'image' => array('Image'),
        'updatePeriod' => array('Text'),
        'updateFrequency' => array('Text'),
        'updateBase' => array('Date'),
        'rights' => array('Text'), # dc:rights
        'description' => array('Text'), # dc:description
        'creator' => array('Text'), # dc:creator
        'publisher' => array('Text'), # dc:publisher
        'contributor' => array('Text'), # dc:contributor
        'date' => array('Date') # dc:contributor
        );

    /**
     * Here we map some elements to their atom equivalents. This is going to be
     * quite tricky to pull off effectively (and some users' methods may vary)
     * but is worth trying. The key is the atom version, the value is RSS2.
     * @var array
     */
    protected $compatMap = array(
        'title' => array('title'),
        'link' => array('link'),
        'subtitle' => array('description'),
        'author' => array('creator'),
        'updated' => array('date'));

    /**
     * We will be working with multiple namespaces and it is useful to 
     * keep them together. We will retain support for some common RSS1.0 modules
     * @var array
     */
    protected $namespaces = array(
        'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'rss' => 'http://purl.org/net/rss1.1#',
        'dc' => 'http://purl.org/rss/1.0/modules/dc/',
        'content' => 'http://purl.org/rss/1.0/modules/content/',
        'sy' => 'http://web.resource.org/rss/1.0/modules/syndication/');

    /**
     * Our constructor does nothing more than its parent.
     * 
     * @param    DOMDocument    $xml    A DOM object representing the feed
     * @param    bool (optional) $string    Whether or not to validate this feed
     */
    function __construct(DOMDocument $model, $strict = false)
    {
        $this->model = $model;

        if ($strict) {
            if (! $this->relaxNGValidate()) {
                throw new XML_Feed_Parser_Exception('Failed required validation');
            }
        }

        $this->xpath = new DOMXPath($model);
        foreach ($this->namespaces as $key => $value) {
            $this->xpath->registerNamespace($key, $value);
        }            
        $this->numberEntries = $this->count('item');
    }

    /**
     * Attempts to identify an element by ID given by the rdf:about attribute
     *
     * This is not really something that will work with RSS1.1 as it does not have
     * clear restrictions on the global uniqueness of IDs. We will employ the
     * _very_ hit and miss method of selecting entries based on the rdf:about
     * attribute. Please note that this is even more hit and miss with RSS1.1 than
     * with RSS1.0 since RSS1.1 does not require the rdf:about attribute for items.
     *
     * @param    string    $id    any valid ID.
     * @return    XML_Feed_Parser_RSS1Element
     */
    function getEntryById($id)
    {
        if (isset($this->idMappings[$id])) {
            return $this->entries[$this->idMappings[$id]];
        }

        $entries = $this->xpath->query("//rss:item[@rdf:about='$id']");
        if ($entries->length > 0) {
            $classname = $this->itemClass;
            $entry = new $classname($entries->item(0), $this);
            return $entry;
        }
        return false;
    }

    /**
     * Get details of the image associated with the feed.
     *
     * @return  array|false an array simply containing the child elements
     */
    protected function getImage()
    {
        $images = $this->model->getElementsByTagName('image');
        if ($images->length > 0) {
            $image = $images->item(0);
            $details = array();
            if ($image->hasChildNodes()) {
                $details = array(
                    'title' => $image->getElementsByTagName('title')->item(0)->value,
                    'url' => $image->getElementsByTagName('url')->item(0)->value);
                if ($image->getElementsByTagName('link')->length > 0) {
                    $details['link'] = 
                        $image->getElementsByTagName('link')->item(0)->value;
                }
            } else {
                $details = array('title' => false,
                    'link' => false,
                    'url' => $image->attributes->getNamedItem('resource')->nodeValue);
            }
            $details = array_merge($details, 
                array('description' => false, 'height' => false, 'width' => false));
            if (! empty($details)) {
                return $details;
            }
        }
        return false;
    }

    /**
     * The textinput element is little used, but in the interests of
     * completeness we will support it.
     *
     * @return  array|false
     */
    protected function getTextInput()
    {
        $inputs = $this->model->getElementsByTagName('textinput');
        if ($inputs->length > 0) {
            $input = $inputs->item(0);
            $results = array();
            $results['title'] = isset(
                $input->getElementsByTagName('title')->item(0)->value) ? 
                $input->getElementsByTagName('title')->item(0)->value : null;
            $results['description'] = isset(
                $input->getElementsByTagName('description')->item(0)->value) ? 
                $input->getElementsByTagName('description')->item(0)->value : null;
            $results['name'] = isset(
                $input->getElementsByTagName('name')->item(0)->value) ? 
                $input->getElementsByTagName('name')->item(0)->value : null;
            $results['link'] = isset(
                   $input->getElementsByTagName('link')->item(0)->value) ? 
                   $input->getElementsByTagName('link')->item(0)->value : null;
            if (empty($results['link']) and 
                $input->attributes->getNamedItem('resource')) {
                $results['link'] = $input->attributes->getNamedItem('resource')->nodeValue;
            }
            if (! empty($results)) {
                return $results;
            }
        }
        return false;
    }

    /**
     * Attempts to discern authorship
     *
     * Dublin Core provides the dc:creator, dc:contributor, and dc:publisher
     * elements for defining authorship in RSS1. We will try each of those in
     * turn in order to simulate the atom author element and will return it
     * as text.
     *
     * @return  array|false
     */
    function getAuthor()
    {
        $options = array('creator', 'contributor', 'publisher');
        foreach ($options as $element) {
            $test = $this->model->getElementsByTagName($element);
            if ($test->length > 0) {
                return $test->item(0)->value;
            }
        }
        return false;
    }
    
    /**
     * Retrieve a link
     *
     * In RSS1 a link is a text element but in order to ensure that we resolve
     * URLs properly we have a special function for them.
     *
     * @return  string
     */
    function getLink($offset = 0, $attribute = 'href', $params = false)
    {
        $links = $this->model->getElementsByTagName('link');
        if ($links->length <= $offset) {
            return false;
        }
        $link = $links->item($offset);
        return $this->addBase($link->nodeValue, $link);
    }
}

?>
