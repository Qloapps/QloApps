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
 * @version    CVS: $Id: Parser.php 6844 2011-06-03 14:46:51Z dMetzger $
 * @link       http://pear.php.net/package/XML_Feed_Parser/
 */

/**
 * XML_Feed_Parser_Type is an abstract class required by all of our
 * feed types. It makes sense to load it here to keep the other files
 * clean.
 */
require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/Type.php');

/**
 * We will throw exceptions when errors occur.
 */
require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/Exception.php');

/**
 * This is the core of the XML_Feed_Parser package. It identifies feed types 
 * and abstracts access to them. It is an iterator, allowing for easy access 
 * to the entire feed.
 *
 * @author  James Stewart <james@jystewart.net>
 * @version Release: @package_version@
 * @package XML_Feed_Parser
 */
class XML_Feed_Parser implements Iterator
{
    /**
     * This is where we hold the feed object 
     * @var Object
     */
    private $feed;

    /**
     * To allow for extensions, we make a public reference to the feed model 
     * @var DOMDocument
     */
    public $model;
    
    /**
     * A map between entry ID and offset
     * @var array
     */
    protected $idMappings = array();

    /**
     * A storage space for Namespace URIs.
     * @var array
     */
    private $feedNamespaces = array(
        'rss2' => array(
            'http://backend.userland.com/rss',
            'http://backend.userland.com/rss2',
            'http://blogs.law.harvard.edu/tech/rss'));
    /**
     * Detects feed types and instantiate appropriate objects.
     *
     * Our constructor takes care of detecting feed types and instantiating
     * appropriate classes. For now we're going to treat Atom 0.3 as Atom 1.0
     * but raise a warning. I do not intend to introduce full support for 
     * Atom 0.3 as it has been deprecated, but others are welcome to.
     *
     * @param    string    $feed    XML serialization of the feed
     * @param    bool    $strict    Whether or not to validate the feed
     * @param    bool    $suppressWarnings Trigger errors for deprecated feed types?
     * @param    bool    $tidy    Whether or not to try and use the tidy library on input
     */
    function __construct($feed, $strict = false, $suppressWarnings = false, $tidy = false)
    {
        $this->model = new DOMDocument;
        if (! @$this->model->loadXML($feed)) { /* PrestaShop - no error display */
            if (extension_loaded('tidy') && $tidy) {
                $tidy = new tidy;
                $tidy->parseString($feed, 
                    array('input-xml' => true, 'output-xml' => true));
                $tidy->cleanRepair();
                if (! @$this->model->loadXML((string) $tidy)) {	/* PrestaShop - no error display */
                    throw new XML_Feed_Parser_Exception('Invalid input: this is not ' .
                        'valid XML');
                }
            } else {
                throw new XML_Feed_Parser_Exception('Invalid input: this is not valid XML');
            }

        }

        /* detect feed type */
        $doc_element = $this->model->documentElement;
        $error = false;

        switch (true) {
            case ($doc_element->namespaceURI == 'http://www.w3.org/2005/Atom'):
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/Atom.php');
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/AtomElement.php');
                $class = 'XML_Feed_Parser_Atom';
                break;
            case ($doc_element->namespaceURI == 'http://purl.org/atom/ns#'):
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/Atom.php');
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/AtomElement.php');
                $class = 'XML_Feed_Parser_Atom';
                $error = 'Atom 0.3 deprecated, using 1.0 parser which won\'t provide ' .
                    'all options';
                break;
            case ($doc_element->namespaceURI == 'http://purl.org/rss/1.0/' || 
                ($doc_element->hasChildNodes() && $doc_element->childNodes->length > 1 
                && $doc_element->childNodes->item(1)->namespaceURI == 
                'http://purl.org/rss/1.0/')):
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS1.php');
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS1Element.php');
                $class = 'XML_Feed_Parser_RSS1';
                break;
            case ($doc_element->namespaceURI == 'http://purl.org/rss/1.1/' || 
                ($doc_element->hasChildNodes() && $doc_element->childNodes->length > 1 
                && $doc_element->childNodes->item(1)->namespaceURI == 
                'http://purl.org/rss/1.1/')):
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS11.php');
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS11Element.php');
                $class = 'XML_Feed_Parser_RSS11';
                break;
            case (($doc_element->hasChildNodes() && $doc_element->childNodes->length > 1
                && $doc_element->childNodes->item(1)->namespaceURI == 
                'http://my.netscape.com/rdf/simple/0.9/') || 
                $doc_element->namespaceURI == 'http://my.netscape.com/rdf/simple/0.9/'):
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS09.php');
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS09Element.php');
                $class = 'XML_Feed_Parser_RSS09';
                break;
            case ($doc_element->tagName == 'rss' and
                $doc_element->hasAttribute('version') && 
                $doc_element->getAttribute('version') == 0.91):
                $error = 'RSS 0.91 has been superceded by RSS2.0. Using RSS2.0 parser.';
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS2.php');
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS2Element.php');
                $class = 'XML_Feed_Parser_RSS2';
                break;
            case ($doc_element->tagName == 'rss' and
                $doc_element->hasAttribute('version') && 
                $doc_element->getAttribute('version') == 0.92):
                $error = 'RSS 0.92 has been superceded by RSS2.0. Using RSS2.0 parser.';
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS2.php');
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS2Element.php');
                $class = 'XML_Feed_Parser_RSS2';
                break;
            case (in_array($doc_element->namespaceURI, $this->feedNamespaces['rss2'])
                || $doc_element->tagName == 'rss'):
                if (! $doc_element->hasAttribute('version') || 
                    $doc_element->getAttribute('version') != 2) {
                    $error = 'RSS version not specified. Parsing as RSS2.0';
                }
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS2.php');
                require_once(_PS_PEAR_XML_PARSER_PATH_.'Parser/RSS2Element.php');
                $class = 'XML_Feed_Parser_RSS2';
                break;
            default:
                throw new XML_Feed_Parser_Exception('Feed type unknown');
                break;
        }

        if (! $suppressWarnings && ! empty($error)) {
            trigger_error($error, E_USER_WARNING);
        }

        /* Instantiate feed object */
        $this->feed = new $class($this->model, $strict);
    }

    /**
     * Proxy to allow feed element names to be used as method names
     *
     * For top-level feed elements we will provide access using methods or 
     * attributes. This function simply passes on a request to the appropriate 
     * feed type object.
     *
     * @param   string  $call - the method being called
     * @param   array   $attributes
     */
    function __call($call, $attributes)
    {
        $attributes = array_pad($attributes, 5, false);
        list($a, $b, $c, $d, $e) = $attributes;
        return $this->feed->$call($a, $b, $c, $d, $e);
    }

    /**
     * Proxy to allow feed element names to be used as attribute names
     *
     * To allow variable-like access to feed-level data we use this
     * method. It simply passes along to __call() which in turn passes
     * along to the relevant object.
     *
     * @param   string  $val - the name of the variable required
     */
    function __get($val)
    {
        return $this->feed->$val;
    }

    /**
     * Provides iteration functionality.
     *
     * Of course we must be able to iterate... This function simply increases
     * our internal counter.
     */
    function next()
    {
        if (isset($this->current_item) && 
            $this->current_item <= $this->feed->numberEntries - 1) {
            ++$this->current_item;
        } elseif (! isset($this->current_item)) {
            $this->current_item = 0;
        } else {
            return false;
        }
    }

    /**
     * Return XML_Feed_Type object for current element
     *
     * @return    XML_Feed_Parser_Type Object
     */
    function current()
    {
        return $this->getEntryByOffset($this->current_item);
    }

    /**
     * For iteration -- returns the key for the current stage in the array.
     *
     * @return    int
     */    
    function key()
    {
        return $this->current_item;
    }

    /**
     * For iteration -- tells whether we have reached the 
     * end.
     *
     * @return    bool
     */
    function valid()
    {
        return $this->current_item < $this->feed->numberEntries;
    }

    /**
     * For iteration -- resets the internal counter to the beginning.
     */
    function rewind()
    {
        $this->current_item = 0;
    }

    /**
     * Provides access to entries by ID if one is specified in the source feed.
     *
     * As well as allowing the items to be iterated over we want to allow
     * users to be able to access a specific entry. This is one of two ways of
     * doing that, the other being by offset. This method can be quite slow
     * if dealing with a large feed that hasn't yet been processed as it
     * instantiates objects for every entry until it finds the one needed.
     *
     * @param    string    $id  Valid ID for the given feed format
     * @return    XML_Feed_Parser_Type|false
     */            
    function getEntryById($id)
    {
        if (isset($this->idMappings[$id])) {
            return $this->getEntryByOffset($this->idMappings[$id]);
        }

        /* 
         * Since we have not yet encountered that ID, let's go through all the
         * remaining entries in order till we find it.
         * This is a fairly slow implementation, but it should work.
         */
        return $this->feed->getEntryById($id);
    }

    /**
     * Retrieve entry by numeric offset, starting from zero.
     *
     * As well as allowing the items to be iterated over we want to allow
     * users to be able to access a specific entry. This is one of two ways of
     * doing that, the other being by ID.
     *
     * @param    int    $offset The position of the entry within the feed, starting from 0
     * @return    XML_Feed_Parser_Type|false
     */
    function getEntryByOffset($offset)
    {
        if ($offset < $this->feed->numberEntries) {
            if (isset($this->feed->entries[$offset])) {
                return $this->feed->entries[$offset];
            } else {
                try {
                    $this->feed->getEntryByOffset($offset);
                } catch (Exception $e) {
                    return false;
                }
                $id = $this->feed->entries[$offset]->getID();
                $this->idMappings[$id] = $offset;
                return $this->feed->entries[$offset];
            }
        } else {
            return false;
        }
    }

    /**
     * Retrieve version details from feed type class.
     *
     * @return void
     * @author James Stewart
     */
    function version()
    {
        return $this->feed->version;
    }
    
    /**
     * Returns a string representation of the feed.
     * 
     * @return String
     **/
    function __toString()
    {
        return $this->feed->__toString();
    }
}
?>