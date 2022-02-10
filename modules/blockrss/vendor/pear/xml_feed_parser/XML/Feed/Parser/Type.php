<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
require_once 'XML/Feed/Parser/Sanitizer.php';

/**
 * Abstract class providing common methods for XML_Feed_Parser feeds.
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
 * This abstract class provides some general methods that are likely to be
 * implemented exactly the same way for all feed types.
 *
 * @package XML_Feed_Parser
 * @author  James Stewart <james@jystewart.net>
 * @version Release: @package_version@
 */
abstract class XML_Feed_Parser_Type
{
    protected $sanitizer;
    /**
     * Where we store our DOM object for this feed 
     * @var DOMDocument
     */
    public $model;

    /**
     * For iteration we'll want a count of the number of entries 
     * @var int
     */
    public $numberEntries;

    /**
     * Where we store our entry objects once instantiated 
     * @var array
     */
    public $entries = array();

    /**
     * Store mappings between entry IDs and their position in the feed
     */
    public $idMappings = array();

    /**
     * Proxy to allow use of element names as method names
     *
     * We are not going to provide methods for every entry type so this
     * function will allow for a lot of mapping. We rely pretty heavily
     * on this to handle our mappings between other feed types and atom.
     *
     * @param   string  $call - the method attempted
     * @param   array   $arguments - arguments to that method
     * @return  mixed
     */
    function __call($call, $arguments = array())
    {
        if (! is_array($arguments)) {
            $arguments = array();
        }

        if (isset($this->compatMap[$call])) {
            $tempMap = $this->compatMap;
            $tempcall = array_pop($tempMap[$call]);
            if (! empty($tempMap)) {
                $arguments = array_merge($arguments, $tempMap[$call]);
            }
            $call = $tempcall;
        }

        /* To be helpful, we allow a case-insensitive search for this method */
        if (! isset($this->map[$call])) {
            foreach (array_keys($this->map) as $key) {
                if (strtoupper($key) == strtoupper($call)) {
                    $call = $key;
                    break;
                }
            }
        }

        if (empty($this->map[$call])) {
            return false;
        }

        $method = 'get' . $this->map[$call][0];
        if ($method == 'getLink') {
            $offset = empty($arguments[0]) ? 0 : $arguments[0];
            $attribute = empty($arguments[1]) ? 'href' : $arguments[1];
            $params = isset($arguments[2]) ? $arguments[2] : array();
            return $this->getLink($offset, $attribute, $params);
        }
        if (method_exists($this, $method)) {
            return $this->$method($call, $arguments);
        }

        return false;
    }

    public function setSanitizer(XML_Feed_Parser_Sanitizer $sanitizer) {
        $this->sanitizer = $sanitizer;

        foreach ($this->entries as $entry) {
            $entry->setSanizier($sanitizer);
        }
    } 
    public function getSanitizer() {
        return $this->sanitizer;
    }

    /**
     * Proxy to allow use of element names as attribute names
     *
     * For many elements variable-style access will be desirable. This function
     * provides for that.
     *
     * @param   string  $value - the variable required
     * @return  mixed
     */
    function __get($value)
    {
        return $this->__call($value, array());
    }

    /**
     * Utility function to help us resolve xml:base values
     *
     * We have other methods which will traverse the DOM and work out the different
     * xml:base declarations we need to be aware of. We then need to combine them.
     * If a declaration starts with a protocol then we restart the string. If it 
     * starts with a / then we add on to the domain name. Otherwise we simply tag 
     * it on to the end.
     *
     * @param   string  $base - the base to add the link to
     * @param   string  $link
     */
    function combineBases($base, $link)
    {
        if (preg_match('/^[A-Za-z]+:\/\//', $link)) {
            return $link;
        } else if (preg_match('/^\//', $link)) {
            /* Extract domain and suffix link to that */
            preg_match('/^([A-Za-z]+:\/\/.*)?\/*/', $base, $results);
            $firstLayer = $results[0];
            return $firstLayer . "/" . $link;
        } else if (preg_match('/^\.\.\//', $base)) {
            /* Step up link to find place to be */
            preg_match('/^((\.\.\/)+)(.*)$/', $link, $bases);
            $suffix = $bases[3];
            $count = preg_match_all('/\.\.\//', $bases[1], $steps);
            $url = explode("/", $base);
            for ($i = 0; $i <= $count; $i++) {
                array_pop($url);
            }
            return implode("/", $url) . "/" . $suffix;
        } else if (preg_match('/^(?!\/$)/', $base)) {
            $base = preg_replace('/(.*\/).*$/', '$1', $base)  ;
            return $base . $link;
        } else {
            /* Just stick it on the end */
            return $base . $link;
        }
    }

    /**
     * Determine whether we need to apply our xml:base rules
     *
     * Gets us the xml:base data and then processes that with regard
     * to our current link.
     *
     * @param   string
     * @param   DOMElement
     * @return  string
     */
    function addBase($link, $element)
    {
        if (preg_match('/^[A-Za-z]+:\/\//', $link)) {
            return $link;
        }

        return $this->combineBases($element->baseURI, $link);
    }

    /**
     * Get an entry by its position in the feed, starting from zero
     *
     * As well as allowing the items to be iterated over we want to allow
     * users to be able to access a specific entry. This is one of two ways of
     * doing that, the other being by ID.
     * 
     * @param   int $offset
     * @return  XML_Feed_Parser_RSS1Element
     */
    function getEntryByOffset($offset)
    {
        if (! isset($this->entries[$offset])) {
            $entries = $this->model->getElementsByTagName($this->itemElement);
            if ($entries->length > $offset) {
                $xmlBase = $entries->item($offset)->baseURI;
                /** @todo Remove this behaviour - each driver should control this better */
                /** @todo Try to avoid new here */
                $this->entries[$offset] = new $this->itemClass(
                    $entries->item($offset), $this, $xmlBase);
                if ($id = $this->entries[$offset]->id) {
                    $this->idMappings[$id] = $this->entries[$offset];
                }
            } else {
                throw new XML_Feed_Parser_Exception('No entries found');
            }
        }

        return $this->entries[$offset];
    }

    /**
     * Return a date in seconds since epoch.
     *
     * Get a date construct. We use PHP's strtotime to return it as a unix datetime, which
     * is the number of seconds since 1970-01-01 00:00:00.
     * 
     * @link    http://php.net/strtotime
     * @param    string    $method        The name of the date construct we want
     * @param    array     $arguments    Included for compatibility with our __call usage
     * @return    int|false datetime
     */
    protected function getDate($method, $arguments)
    {
        $time = $this->model->getElementsByTagName($method);
        if ($time->length == 0 || empty($time->item(0)->nodeValue)) {
            return false;
        }
        return strtotime($time->item(0)->nodeValue);
    }

    /**
     * Get a text construct. 
     *
     * @param    string    $method    The name of the text construct we want
     * @param    array     $arguments    Included for compatibility with our __call usage
     * @return    string
     */
    protected function getText($method, $arguments = array())
    {
        $tags = $this->model->getElementsByTagName($method);
        if ($tags->length > 0) {
            $value = $tags->item(0)->nodeValue;
            return $this->sanitizer->sanitize($value);
        }
        return false;
    }

    /**
     * Apply various rules to retrieve category data.
     *
     * There is no single way of declaring a category in RSS1/1.1 as there is in RSS2 
     * and  Atom. Instead the usual approach is to use the dublin core namespace to 
     * declare  categories. For example delicious use both: 
     * <dc:subject>PEAR</dc:subject> and: <taxo:topics><rdf:Bag>
     * <rdf:li resource="http://del.icio.us/tag/PEAR" /></rdf:Bag></taxo:topics>
     * to declare a categorisation of 'PEAR'.
     *
     * We need to be sensitive to this where possible.
     *
     * @param    string    $call    for compatibility with our overloading
     * @param   array $arguments - arg 0 is the offset, arg 1 is whether to return as array
     * @return  string|array|false
     */
    protected function getCategory($call, $arguments)
    {
        $categories = $this->model->getElementsByTagName('subject');
        $offset = empty($arguments[0]) ? 0 : $arguments[0];
        $array = empty($arguments[1]) ? false : true;
        if ($categories->length <= $offset) {
            return false;
        }
        if ($array) {
            $list = array();
            foreach ($categories as $category) {
                array_push($list, $this->sanitizer->sanitize($category->nodeValue));
            }
            return $list;
        }
        return $this->sanitizer->sanitize($categories->item($offset)->nodeValue);
    }

    /**
     * Count occurrences of an element
     *
     * This function will tell us how many times the element $type
     * appears at this level of the feed.
     * 
     * @param    string    $type    the element we want to get a count of
     * @return    int
     */
    protected function count($type)
    {
        if ($tags = $this->model->getElementsByTagName($type)) {
            return $tags->length;
        }
        return 0;
    }

    /**
     * Part of our xml:base processing code
     *
     * We need a couple of methods to access XHTML content stored in feeds. 
     * This is because we dereference all xml:base references before returning
     * the element. This method handles the attributes.
     *
     * @param   DOMElement $node    The DOM node we are iterating over
     * @return  string
     */
    function processXHTMLAttributes($node) {
        $return = '';
        foreach ($node->attributes as $attribute) {
            if ($attribute->name == 'src' or $attribute->name == 'href') {
                $attribute->value = $this->addBase(htmlentities($attribute->value, NULL, 'utf-8'), $attribute);
            }
            if ($attribute->name == 'base') {
                continue;
            }
            $return .= $attribute->name . '="' . htmlentities($attribute->value, NULL, 'utf-8') .'" ';
        }
        if (! empty($return)) {
            return ' ' . trim($return);
        }
        return '';
    }

    /**
     * Convert HTML entities based on the current character set.
     * 
     * @param String
     * @return String
     */
    function processEntitiesForNodeValue($node) 
    {
        $current_encoding = $node->ownerDocument->encoding;

        // Charset left blank to trigger autodetection
        $decoded = html_entity_decode($node->nodeValue);
        $value = htmlentities($decoded, null, strtoupper($current_encoding));

        if (function_exists('iconv')) {
            return $this->sanitizer->sanitize(
                iconv(
                    strtoupper($current_encoding),
                    'UTF-8',
                    $value
                )
            );
        } else if (strtoupper($current_encoding) == 'ISO-8859-1') {
            return $this->sanitizer->sanitize(
                utf8_encode($value)
            );
        }

        return $this->sanitizer->sanitize($value);
    }

    /**
     * Part of our xml:base processing code
     *
     * We need a couple of methods to access XHTML content stored in feeds. 
     * This is because we dereference all xml:base references before returning
     * the element. This method recurs through the tree descending from the node
     * and builds our string.
     *
     * @param   DOMElement $node    The DOM node we are processing
     * @return   string
     */
    function traverseNode($node)
    {
        $content = '';

        /* Add the opening of this node to the content */
        if ($node instanceof DOMElement) {
            $content .= '<' . $node->tagName . 
                $this->processXHTMLAttributes($node) . '>';
        }

        /* Process children */
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                $content .= $this->traverseNode($child);
            }
        }

        if ($node instanceof DOMText) {
            $content .= $this->processEntitiesForNodeValue($node);
        }

        /* Add the closing of this node to the content */
        if ($node instanceof DOMElement) {
            $content .= '</' . $node->tagName . '>';
        }

        return $content;
    }

    /**
     * Get content from RSS feeds (atom has its own implementation)
     *
     * The official way to include full content in an RSS1 entry is to use
     * the content module's element 'encoded', and RSS2 feeds often duplicate that.
     * Often, however, the 'description' element is used instead. We will offer that 
     * as a fallback. Atom uses its own approach and overrides this method.
     *
     * @return  string|false
     */
    protected function getContent()
    {
        $options = array('encoded', 'description');
        foreach ($options as $element) {
            $test = $this->model->getElementsByTagName($element);
            if ($test->length == 0) {
                continue;
            }
            if ($test->item(0)->hasChildNodes()) {
                $value = '';
                foreach ($test->item(0)->childNodes as $child) {
                    if ($child instanceof DOMText) {
                        $value .= $this->sanitizer->sanitize($child->nodeValue);
                    } else {
                        $simple = simplexml_import_dom($child);
                        $value .= $simple->asXML();
                    }
                }
                return $value;
            } else if ($test->length > 0) {
                return $this->sanitizer->sanitize($test->item(0)->nodeValue);
            }
        }
        return false;
    }

    /**
     * Checks if this element has a particular child element.
     *
     * @param   String
     * @param   Integer
     * @return  bool
     **/
    function hasKey($name, $offset = 0)
    {
        $search = $this->model->getElementsByTagName($name);
        return $search->length > $offset;
    }

    /**
     * Return an XML serialization of the feed, should it be required. Most 
     * users however, will already have a serialization that they used when 
     * instantiating the object.
     *
     * @return    string    XML serialization of element
     */    
    function __toString()
    {
        $simple = simplexml_import_dom($this->model);
        return $simple->asXML();
    }
    
    /**
     * Get directory holding RNG schemas. Method is based on that 
     * found in Contact_AddressBook.
     *
     * @return string PEAR data directory.
     * @access public
     * @static
     */
    static function getSchemaDir()
    {
        require_once 'PEAR/Config.php';
        $config = new PEAR_Config;
        return $config->get('data_dir') . '/XML_Feed_Parser/schemas';
    }

    public function relaxNGValidate() {
        $dir = self::getSchemaDir();

        $path = $dir . '/' . $this->relax;

        return $this->model->relaxNGValidate($path);
    }
}
