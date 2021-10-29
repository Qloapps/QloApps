<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class representing feed-level data for an RSS2 feed
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
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/XML_Feed_Parser/
 */

/**
 * This class handles RSS2 feeds.
 * 
 * @author    James Stewart <james@jystewart.net>
 * @version    Release: @package_version@
 * @package XML_Feed_Parser
 */
class XML_Feed_Parser_RSS2 extends XML_Feed_Parser_Type
{
    /**
     * The URI of the RelaxNG schema used to (optionally) validate the feed
     * @var string
     */
    protected $relax = 'rss20.rng';

    /**
     * We're likely to use XPath, so let's keep it global
     * @var DOMXPath
     */
    protected $xpath;

    /**
     * The feed type we are parsing
     * @var string
     */
    public $version = 'RSS 2.0';

    /**
     * The class used to represent individual items
     * @var string
     */     
    protected $itemClass = 'XML_Feed_Parser_RSS2Element';
    
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
        'ttl' => array('Text'),
        'pubDate' => array('Date'),
        'lastBuildDate' => array('Date'),
        'title' => array('Text'),
        'link' => array('Link'),
        'description' => array('Text'),
        'language' => array('Text'),
        'copyright' => array('Text'),
        'managingEditor' => array('Text'),
        'webMaster' => array('Text'),
        'category' => array('Text'),
        'generator' => array('Text'),
        'docs' => array('Text'),
        'ttl' => array('Text'),
        'image' => array('Image'),
        'skipDays' => array('skipDays'),
        'skipHours' => array('skipHours'));

    /**
     * Here we map some elements to their atom equivalents. This is going to be
     * quite tricky to pull off effectively (and some users' methods may vary)
     * but is worth trying. The key is the atom version, the value is RSS2.
     * @var array
     */
    protected $compatMap = array(
        'title' => array('title'),
        'rights' => array('copyright'),
        'updated' => array('lastBuildDate'),
        'subtitle' => array('description'),
        'date' => array('pubDate'),
        'author' => array('managingEditor'));

    protected $namespaces = array(
        'dc' => 'http://purl.org/rss/1.0/modules/dc/',
        'content' => 'http://purl.org/rss/1.0/modules/content/');

    /**
     * Our constructor does nothing more than its parent.
     * 
     * @param    DOMDocument    $xml    A DOM object representing the feed
     * @param    bool (optional) $string    Whether or not to validate this feed
     */
    function __construct(DOMDocument $model, $strict = false)
    {
        $this->model = $model;
        $this->setSanitizer(new XML_Feed_Parser_Unsafe_Sanitizer());
        if ($strict) {
            if (! $this->relaxNGValidate()) {
                throw new XML_Feed_Parser_Exception('Failed required validation');
            }
        }

        $this->xpath = new DOMXPath($this->model);
        foreach ($this->namespaces as $key => $value) {
            $this->xpath->registerNamespace($key, $value);
        }
        $this->numberEntries = $this->count('item');
    }

    /**
     * Retrieves an entry by ID, if the ID is specified with the guid element
     *
     * This is not really something that will work with RSS2 as it does not have
     * clear restrictions on the global uniqueness of IDs. But we can emulate
     * it by allowing access based on the 'guid' element. If DOMXPath::evaluate
     * is available, we also use that to store a reference to the entry in the array
     * used by getEntryByOffset so that method does not have to seek out the entry
     * if it's requested that way.
     *
     * @param    string    $id    any valid ID.
     * @return    XML_Feed_Parser_RSS2Element
     */
    function getEntryById($id)
    {
        if (isset($this->idMappings[$id])) {
            return $this->entries[$this->idMappings[$id]];
        }

        $entries = $this->xpath->query("//item[guid='$id']");
        if ($entries->length > 0) {
            $entry = new XML_Feed_Parser_RSS2Element($entries->item(0), $this);
            if (in_array('evaluate', get_class_methods($this->xpath))) {
                $offset = $this->xpath->evaluate("count(preceding-sibling::item)", $entries->item(0));
                $this->entries[$offset] = $entry;
            }
            $this->idMappings[$id] = $entry;
            return $entry;
        }        
    }

    /**
     * Get a category from the element
     *
     * The category element is a simple text construct which can occur any number
     * of times. We allow access by offset or access to an array of results.
     *
     * @param    string    $call    for compatibility with our overloading
     * @param   array $arguments - arg 0 is the offset, arg 1 is whether to return as array
     * @return  string|array|false
     */
    function getCategory($call, $arguments = array())
    {
        $categories = $this->model->getElementsByTagName('category');
        $offset = empty($arguments[0]) ? 0 : $arguments[0];
        $array = empty($arguments[1]) ? false : true;
        if ($categories->length <= $offset) {
            return false;
        }
        if ($array) {
            $list = array();
            foreach ($categories as $category) {
                array_push($list, $category->nodeValue);
            }
            return $list;
        }
        return $categories->item($offset)->nodeValue;
    }

    /**
     * Get details of the image associated with the feed.
     *
     * @return  array|false an array simply containing the child elements
     */
    protected function getImage()
    {
        $images = $this->xpath->query("//image");
        if ($images->length > 0) {
            $image = $images->item(0);
            $title = $image->getElementsByTagName('title');
            $link = $image->getElementsByTagName('link');
            $url = $image->getElementsByTagName('url');
            if (!$title->length || !$link->length || !$url->length) {
                return false;
            }
            $desc = $image->getElementsByTagName('description');
            $description = $desc->length ? $desc->item(0)->nodeValue : false;
            $heigh = $image->getElementsByTagName('height'); 
            $height = $heigh->length ? $heigh->item(0)->nodeValue : false;
            $widt = $image->getElementsByTagName('width'); 
            $width = $widt->length ? $widt->item(0)->nodeValue : false;
            return array(
                'title' => $title->item(0)->nodeValue,
                'link' => $link->item(0)->nodeValue,
                'url' => $url->item(0)->nodeValue,
                'description' => $description,
                'height' => $height,
                'width' => $width);
        }
        return false;
    }

    /**
     * The textinput element is little used, but in the interests of
     * completeness...
     *
     * @return  array|false
     */
    function getTextInput()
    {
        $inputs = $this->model->getElementsByTagName('input');
        if ($inputs->length > 0) {
            $input = $inputs->item(0);
            return array(
                'title' => $input->getElementsByTagName('title')->item(0)->value,
                'description' => 
                    $input->getElementsByTagName('description')->item(0)->value,
                'name' => $input->getElementsByTagName('name')->item(0)->value,
                'link' => $input->getElementsByTagName('link')->item(0)->value);
        }
        return false;
    }

    /**
     * Utility function for getSkipDays and getSkipHours
     *
     * This is a general function used by both getSkipDays and getSkipHours. It simply
     * returns an array of the values of the children of the appropriate tag.
     *
     * @param   string      $tagName    The tag name (getSkipDays or getSkipHours)
     * @return  array|false
     */
    protected function getSkips($tagName)
    {
        $hours = $this->model->getElementsByTagName($tagName);
        if ($hours->length == 0) {
            return false;
        }
        $skipHours = array();
        foreach($hours->item(0)->childNodes as $hour) {
            if ($hour instanceof DOMElement) {
                array_push($skipHours, $hour->nodeValue);
            }
        }
        return $skipHours;
    }

    /**
     * Retrieve skipHours data
     *
     * The skiphours element provides a list of hours on which this feed should
     * not be checked. We return an array of those hours (integers, 24 hour clock)
     *
     * @return  array
     */    
    function getSkipHours()
    {
        return $this->getSkips('skipHours');
    }

    /**
     * Retrieve skipDays data
     *
     * The skipdays element provides a list of days on which this feed should
     * not be checked. We return an array of those days.
     *
     * @return  array
     */
    function getSkipDays()
    {
        return $this->getSkips('skipDays');
    }

    /**
     * Return content of the little-used 'cloud' element
     *
     * The cloud element is rarely used. It is designed to provide some details
     * of a location to update the feed.
     *
     * @return  array   an array of the attributes of the element
     */
    function getCloud()
    {
        $cloud = $this->model->getElementsByTagName('cloud');
        if ($cloud->length == 0) {
            return false;
        }
        $cloudData = array();
        foreach ($cloud->item(0)->attributes as $attribute) {
            $cloudData[$attribute->name] = $attribute->value;
        }
        return $cloudData;
    }
    
    /**
     * Get link URL
     *
     * In RSS2 a link is a text element but in order to ensure that we resolve
     * URLs properly we have a special function for them. We maintain the 
     * parameter used by the atom getLink method, though we only use the offset
     * parameter.
     *
     * @param   int     $offset The position of the link within the feed. Starts from 0
     * @param   string  $attribute  The attribute of the link element required
     * @param   array   $params An array of other parameters. Not used.
     * @return  string
     */
    function getLink($offset, $attribute = 'href', $params = array())
    {
        $xPath = new DOMXPath($this->model);
        $links = $xPath->query('//link');

        if ($links->length <= $offset) {
            return false;
        }
        $link = $links->item($offset);
        return $this->addBase($link->nodeValue, $link);
    }
}

?>
