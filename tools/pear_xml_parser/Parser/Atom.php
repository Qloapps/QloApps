<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Atom feed class for XML_Feed_Parser
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
 * @version    CVS: $Id: Atom.php 6844 2011-06-03 14:46:51Z dMetzger $
 * @link       http://pear.php.net/package/XML_Feed_Parser/
*/

/**
 * This is the class that determines how we manage Atom 1.0 feeds
 * 
 * How we deal with constructs:
 *  date - return as unix datetime for use with the 'date' function unless specified otherwise
 *  text - return as is. optional parameter will give access to attributes
 *  person - defaults to name, but parameter based access
 *
 * @author    James Stewart <james@jystewart.net>
 * @version    Release: @package_version@
 * @package XML_Feed_Parser
 */
class XML_Feed_Parser_Atom extends XML_Feed_Parser_Type
{
    /**
     * The URI of the RelaxNG schema used to (optionally) validate the feed 
     * @var string
     */
    protected $relax = 'atom.rng';

    /**
     * We're likely to use XPath, so let's keep it global 
     * @var DOMXPath
     */
    public $xpath;

    /**
     * When performing XPath queries we will use this prefix 
     * @var string
     */
    private $xpathPrefix = '//';

    /**
     * The feed type we are parsing 
     * @var string
     */
    public $version = 'Atom 1.0';

    /** 
     * The class used to represent individual items 
     * @var string
     */
    protected $itemClass = 'XML_Feed_Parser_AtomElement';
    
    /** 
     * The element containing entries 
     * @var string
     */
    protected $itemElement = 'entry';

    /**
     * Here we map those elements we're not going to handle individually
     * to the constructs they are. The optional second parameter in the array
     * tells the parser whether to 'fall back' (not apt. at the feed level) or
     * fail if the element is missing. If the parameter is not set, the function
     * will simply return false and leave it to the client to decide what to do.
     * @var array
     */
    protected $map = array(
        'author' => array('Person'),
        'contributor' => array('Person'),
        'icon' => array('Text'),
        'logo' => array('Text'),
        'id' => array('Text', 'fail'),
        'rights' => array('Text'),
        'subtitle' => array('Text'),
        'title' => array('Text', 'fail'),
        'updated' => array('Date', 'fail'),
        'link' => array('Link'),
        'generator' => array('Text'),
        'category' => array('Category'));

    /**
     * Here we provide a few mappings for those very special circumstances in
     * which it makes sense to map back to the RSS2 spec. Key is RSS2 version
     * value is an array consisting of the equivalent in atom and any attributes
     * needed to make the mapping.
     * @var array
     */
    protected $compatMap = array(
        'guid' => array('id'),
        'links' => array('link'),
        'tags' => array('category'),
        'contributors' => array('contributor'));

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

        $this->xpath = new DOMXPath($this->model);
        $this->xpath->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
        $this->numberEntries = $this->count('entry');
    }

    /**
     * Implement retrieval of an entry based on its ID for atom feeds.
     *
     * This function uses XPath to get the entry based on its ID. If DOMXPath::evaluate
     * is available, we also use that to store a reference to the entry in the array
     * used by getEntryByOffset so that method does not have to seek out the entry
     * if it's requested that way.
     * 
     * @param    string    $id    any valid Atom ID.
     * @return    XML_Feed_Parser_AtomElement
     */
    function getEntryById($id)
    {
        if (isset($this->idMappings[$id])) {
            return $this->entries[$this->idMappings[$id]];
        }

        $entries = $this->xpath->query("//atom:entry[atom:id='$id']");

        if ($entries->length > 0) {
            $xmlBase = $entries->item(0)->baseURI;
            $entry = new $this->itemClass($entries->item(0), $this, $xmlBase);
            
            if (in_array('evaluate', get_class_methods($this->xpath))) {
                $offset = $this->xpath->evaluate("count(preceding-sibling::atom:entry)", $entries->item(0));
                $this->entries[$offset] = $entry;
            }

            $this->idMappings[$id] = $entry;

            return $entry;
        }
        
    }

    /**
     * Retrieves data from a person construct.
     *
     * Get a person construct. We default to the 'name' element but allow
     * access to any of the elements.
     * 
     * @param    string    $method    The name of the person construct we want
     * @param    array     $arguments    An array which we hope gives a 'param'
     * @return    string|false
     */
    protected function getPerson($method, $arguments)
    {
        $offset = empty($arguments[0]) ? 0 : $arguments[0];
        $parameter = empty($arguments[1]['param']) ? 'name' : $arguments[1]['param'];
        $section = $this->model->getElementsByTagName($method);
        
        if ($parameter == 'url') {
            $parameter = 'uri';
        }

        if ($section->length <= $offset) {
            return false;
        }

        $param = $section->item($offset)->getElementsByTagName($parameter);
        if ($param->length == 0) {
            return false;
        }
        return $param->item(0)->nodeValue;
    }

    /**
     * Retrieves an element's content where that content is a text construct.
     *
     * Get a text construct. When calling this method, the two arguments
     * allowed are 'offset' and 'attribute', so $parser->subtitle() would
     * return the content of the element, while $parser->subtitle(false, 'type')
     * would return the value of the type attribute.
     *
     * @todo    Clarify overlap with getContent()
     * @param    string    $method    The name of the text construct we want
     * @param    array     $arguments    An array which we hope gives a 'param'
     * @return    string
     */
    protected function getText($method, $arguments)
    {
        $offset = empty($arguments[0]) ? 0: $arguments[0];
        $attribute = empty($arguments[1]) ? false : $arguments[1];
        $tags = $this->model->getElementsByTagName($method);

        if ($tags->length <= $offset) {
            return false;
        }

        $content = $tags->item($offset);

        if (! $content->hasAttribute('type')) {
            $content->setAttribute('type', 'text');
        }
        $type = $content->getAttribute('type');

        if (! empty($attribute) and 
            ! ($method == 'generator' and $attribute == 'name')) {
            if ($content->hasAttribute($attribute)) {
                return $content->getAttribute($attribute);
            } elseif ($attribute == 'href' and $content->hasAttribute('uri')) {
                return $content->getAttribute('uri');
            }
            return false;
        }

        return $this->parseTextConstruct($content);
    }
    
    /**
     * Extract content appropriately from atom text constructs
     *
     * Because of different rules applied to the content element and other text
     * constructs, they are deployed as separate functions, but they share quite
     * a bit of processing. This method performs the core common process, which is
     * to apply the rules for different mime types in order to extract the content.
     *
     * @param   DOMNode $content    the text construct node to be parsed
     * @return String
     * @author James Stewart
     **/
    protected function parseTextConstruct(DOMNode $content)
    {
        if ($content->hasAttribute('type')) {
            $type = $content->getAttribute('type');
        } else {
            $type = 'text';
        }

        if (strpos($type, 'text/') === 0) {
            $type = 'text';
        }

        switch ($type) {
            case 'text':
            case 'html':
                return $content->textContent;
                break;
            case 'xhtml':
                $container = $content->getElementsByTagName('div');
                if ($container->length == 0) {
                    return false;
                }
                $contents = $container->item(0);
                if ($contents->hasChildNodes()) {
                    /* Iterate through, applying xml:base and store the result */
                    $result = '';
                    foreach ($contents->childNodes as $node) {
                        $result .= $this->traverseNode($node);
                    }
                    return $result;
                }
                break;
            case preg_match('@^[a-zA-Z]+/[a-zA-Z+]*xml@i', $type) > 0:
                return $content;
                break;
            case 'application/octet-stream':
            default:
                return base64_decode(trim($content->nodeValue));
                break;
        }
        return false;
    }
    /**
     * Get a category from the entry.
     *
     * A feed or entry can have any number of categories. A category can have the
     * attributes term, scheme and label.
     * 
     * @param    string    $method    The name of the text construct we want
     * @param    array     $arguments    An array which we hope gives a 'param'
     * @return    string
     */
    function getCategory($method, $arguments)
    {
        $offset = empty($arguments[0]) ? 0: $arguments[0];
        $attribute = empty($arguments[1]) ? 'term' : $arguments[1];
        $categories = $this->model->getElementsByTagName('category');
        if ($categories->length <= $offset) {
            $category = $categories->item($offset);
            if ($category->hasAttribute($attribute)) {
                return $category->getAttribute($attribute);
            }
        }
        return false;
    }

    /**
     * This element must be present at least once with rel="feed". This element may be 
     * present any number of further times so long as there is no clash. If no 'rel' is 
     * present and we're asked for one, we follow the example of the Universal Feed
     * Parser and presume 'alternate'.
     *
     * @param    int    $offset    the position of the link within the container
     * @param    string    $attribute    the attribute name required
     * @param    array     an array of attributes to search by
     * @return    string    the value of the attribute
     */
    function getLink($offset = 0, $attribute = 'href', $params = false)
    {
        if (is_array($params) and !empty($params)) {
            $terms = array();
            $alt_predicate = '';
            $other_predicate = '';

            foreach ($params as $key => $value) {
                if ($key == 'rel' && $value == 'alternate') {
                    $alt_predicate = '[not(@rel) or @rel="alternate"]';
                } else {
                    $terms[] = "@$key='$value'";
                }
            }
            if (!empty($terms)) {
                $other_predicate = '[' . join(' and ', $terms) . ']';
            }
            $query =  $this->xpathPrefix . 'atom:link' . $alt_predicate . $other_predicate;
            $links = $this->xpath->query($query);
        } else {
            $links = $this->model->getElementsByTagName('link');
        }
        if ($links->length > $offset) {
            if ($links->item($offset)->hasAttribute($attribute)) {
                $value = $links->item($offset)->getAttribute($attribute);
                if ($attribute == 'href') {
                    $value = $this->addBase($value, $links->item($offset));
                }
                return $value;
            } elseif ($attribute == 'rel') {
                return 'alternate';
            }
        }
        return false;
    }
}

?>
