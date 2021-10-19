<?php
require_once 'Log.php';
require_once 'Log/null.php';
require_once 'XML/Feed/Parser/Sanitizer.php';

class XML_Feed_Parser_Factory {
    /**
     * A storage space for Namespace URIs.
     * @var array
     */
    private $feedNamespaces = array(
        'rss2' => array(
            'http://backend.userland.com/rss',
            'http://backend.userland.com/rss2',
            'http://blogs.law.harvard.edu/tech/rss'));

    public function __construct(Log $log = null) {
        if ($log === null) {
            $log = new Log_null('', '', array(), null);
        }
        $this->log = $log;
    }

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
    function build(DOMDocument $model, $feed, $strict = false, $suppressWarnings = false, $tidy = false) 
    {
        $options = 0;
        if ($suppressWarnings) {
            $options |= LIBXML_NOWARNING;
            $options |= LIBXML_NOERROR;
        }

        if (empty($feed)) {
            throw new XML_Feed_Parser_Exception('Invalid input: file is empty');
        }

        if (! $model->loadXML($feed, $options)) {
            if (extension_loaded('tidy') && $tidy) {
                $tidy = new tidy;
                $tidy->parseString($feed, 
                    array('input-xml' => true, 'output-xml' => true));
                $tidy->cleanRepair();
                if (! $model->loadXML((string) $tidy)) {
                    throw new XML_Feed_Parser_Exception('Invalid input: this is not ' .
                        'valid XML');
                }
            } else {
                throw new XML_Feed_Parser_Exception('Invalid input: this is not valid XML');
            }
        }


        /* detect feed type */
        $doc_element = $model->documentElement;


        $class = $this->determineClass($doc_element, $suppressWarnings);

        /* Instantiate feed object */
        $feed = new $class($model, $strict);
        $feed->setSanitizer(new XML_Feed_Parser_Unsafe_Sanitizer());

        return $feed;
    }

    public function determineClass($doc_element, $suppressWarnings = false) 
    {
        switch (true) { 
            case ($doc_element->namespaceURI == 'http://www.w3.org/2005/Atom'):
                require_once 'XML/Feed/Parser/Atom.php';
                require_once 'XML/Feed/Parser/AtomElement.php';
                $class = 'XML_Feed_Parser_Atom';
                break;
            case ($doc_element->namespaceURI == 'http://purl.org/atom/ns#'):
                require_once 'XML/Feed/Parser/Atom.php';
                require_once 'XML/Feed/Parser/AtomElement.php';
                $class = 'XML_Feed_Parser_Atom';

                $this->log->warning('Atom 0.3 deprecated, using 1.0 parser which won\'t provide ' .
                    'all options');
                break;
            case ($doc_element->namespaceURI == 'http://purl.org/rss/1.0/' || 
                ($doc_element->hasChildNodes() && $doc_element->childNodes->length > 1 
                && $doc_element->childNodes->item(1)->namespaceURI == 
                'http://purl.org/rss/1.0/')):
                require_once 'XML/Feed/Parser/RSS1.php';
                require_once 'XML/Feed/Parser/RSS1Element.php';
                $class = 'XML_Feed_Parser_RSS1';
                break;
            case ($doc_element->namespaceURI == 'http://purl.org/rss/1.1/' || 
                ($doc_element->hasChildNodes() && $doc_element->childNodes->length > 1 
                && $doc_element->childNodes->item(1)->namespaceURI == 
                'http://purl.org/rss/1.1/')):
                require_once 'XML/Feed/Parser/RSS11.php';
                require_once 'XML/Feed/Parser/RSS11Element.php';
                $class = 'XML_Feed_Parser_RSS11';
                break;
            case (($doc_element->hasChildNodes() && $doc_element->childNodes->length > 1
                && $doc_element->childNodes->item(1)->namespaceURI == 
                'http://my.netscape.com/rdf/simple/0.9/') || 
                $doc_element->namespaceURI == 'http://my.netscape.com/rdf/simple/0.9/'):
                require_once 'XML/Feed/Parser/RSS09.php';
                require_once 'XML/Feed/Parser/RSS09Element.php';
                $class = 'XML_Feed_Parser_RSS09';
                break;
            case ($doc_element->tagName == 'rss' and
                $doc_element->hasAttribute('version') && 
                $doc_element->getAttribute('version') == 0.91):
                $this->log->warning('RSS 0.91 has been superceded by RSS2.0. Using RSS2.0 parser.');
                require_once 'XML/Feed/Parser/RSS2.php';
                require_once 'XML/Feed/Parser/RSS2Element.php';
                $class = 'XML_Feed_Parser_RSS2';
                break;
            case ($doc_element->tagName == 'rss' and
                $doc_element->hasAttribute('version') && 
                $doc_element->getAttribute('version') == 0.92):
                $this->log->warning('RSS 0.92 has been superceded by RSS2.0. Using RSS2.0 parser.');
                require_once 'XML/Feed/Parser/RSS2.php';
                require_once 'XML/Feed/Parser/RSS2Element.php';
                $class = 'XML_Feed_Parser_RSS2';
                break;
            case (in_array($doc_element->namespaceURI, $this->feedNamespaces['rss2'])
                || $doc_element->tagName == 'rss'):
                if (! $doc_element->hasAttribute('version') || 
                    $doc_element->getAttribute('version') != 2) {
                    $this->log->warning('RSS version not specified. Parsing as RSS2.0');
                }
                require_once 'XML/Feed/Parser/RSS2.php';
                require_once 'XML/Feed/Parser/RSS2Element.php';
                $class = 'XML_Feed_Parser_RSS2';
                break;
            default:
                throw new XML_Feed_Parser_Exception('Feed type unknown');
                break;
        }


        return $class;
    }
}
