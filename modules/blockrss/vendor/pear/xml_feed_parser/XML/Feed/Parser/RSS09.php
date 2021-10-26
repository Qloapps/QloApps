<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RSS0.9 class for XML_Feed_Parser
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
 * This class handles RSS0.9 feeds.
 * 
 * @author    James Stewart <james@jystewart.net>
 * @version    Release: @package_version@
 * @package XML_Feed_Parser
 * @todo    Find a Relax NG URI we can use
 */
class XML_Feed_Parser_RSS09 extends XML_Feed_Parser_Type
{
    /**
     * The URI of the RelaxNG schema used to (optionally) validate the feed 
     * @var string
     */
    protected $relax = '';

    /**
     * We're likely to use XPath, so let's keep it global
     * @var DOMXPath
     */
    protected $xpath;

    /**
     * The feed type we are parsing 
     * @var string
     */
    public $version = 'RSS 0.9';

    /**
     * The class used to represent individual items 
     * @var string
     */
    protected $itemClass = 'XML_Feed_Parser_RSS09Element';
    
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
        'textinput' => array('TextInput'));

    /**
     * Here we map some elements to their atom equivalents. This is going to be
     * quite tricky to pull off effectively (and some users' methods may vary)
     * but is worth trying. The key is the atom version, the value is RSS2.
     * @var array
     */
    protected $compatMap = array(
        'title' => array('title'),
        'link' => array('link'),
        'subtitle' => array('description'));

    /**
     * We will be working with multiple namespaces and it is useful to 
     * keep them together 
     * @var array
     */
    protected $namespaces = array(
        'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

    /**
     * Our constructor does nothing more than its parent.
     * 
     * @todo    RelaxNG validation
     * @param    DOMDocument    $xml    A DOM object representing the feed
     * @param    bool (optional) $string    Whether or not to validate this feed
     */
    function __construct(DOMDocument $model, $strict = false)
    {
        $this->model = $model;
        $this->setSanitizer(new XML_Feed_Parser_Unsafe_Sanitizer());
        $this->xpath = new DOMXPath($model);
        foreach ($this->namespaces as $key => $value) {
            $this->xpath->registerNamespace($key, $value);
        }            
        $this->numberEntries = $this->count('item');
    }

    /**
     * Included for compatibility -- will not work with RSS 0.9
     *
     * This is not something that will work with RSS0.9 as it does not have
     * clear restrictions on the global uniqueness of IDs.
     *
     * @param    string    $id    any valid ID.
     * @return    false
     */
    function getEntryById($id)
    {
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
                    'link' => $image->getElementsByTagName('link')->item(0)->value,
                    'url' => $image->getElementsByTagName('url')->item(0)->value);
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
            if (empty($results['link']) && 
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
     * Get details of a link from the feed.
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

    /**
     * Not implemented - no available validation.
     */
    public function relaxNGValidate() {
        return true;
    }
}

?>
