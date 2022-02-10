<?php

/**
 * Validator for the components of a URI for a specific scheme
 */
abstract class HTMLPurifier_URIScheme
{

    /**
     * Scheme's default port (integer). If an explicit port number is
     * specified that coincides with the default port, it will be
     * elided.
     * @type int
     */
    public $default_port = null;

    /**
     * Whether or not URIs of this scheme are locatable by a browser
     * http and ftp are accessible, while mailto and news are not.
     * @type bool
     */
    public $browsable = false;

    /**
     * Whether or not data transmitted over this scheme is encrypted.
     * https is secure, http is not.
     * @type bool
     */
    public $secure = false;

    /**
     * Whether or not the URI always uses <hier_part>, resolves edge cases
     * with making relative URIs absolute
     * @type bool
     */
    public $hierarchical = false;

    /**
     * Whether or not the URI may omit a hostname when the scheme is
     * explicitly specified, ala file:///path/to/file. As of writing,
     * 'file' is the only scheme that browsers support his properly.
     * @type bool
     */
    public $may_omit_host = false;

    /**
     * Validates the components of a URI for a specific scheme.
     * @param HTMLPurifier_URI $uri Reference to a HTMLPurifier_URI object
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool success or failure
     */
    abstract public function doValidate(&$uri, $config, $context);

    /**
     * Public interface for validating components of a URI.  Performs a
     * bunch of default actions. Don't overload this method.
     * @param HTMLPurifier_URI $uri Reference to a HTMLPurifier_URI object
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool success or failure
     */
    public function validate(&$uri, $config, $context)
    {
        if ($this->default_port == $uri->port) {
            $uri->port = null;
        }
        // kludge: browsers do funny things when the scheme but not the
        // authority is set
        if (!$this->may_omit_host &&
            // if the scheme is present, a missing host is always in error
            (!is_null($uri->scheme) && ($uri->host === '' || is_null($uri->host))) ||
            // if the scheme is not present, a *blank* host is in error,
            // since this translates into '///path' which most browsers
            // interpret as being 'http://path'.
            (is_null($uri->scheme) && $uri->host === '')
        ) {
            do {
                if (is_null($uri->scheme)) {
                    if (substr($uri->path, 0, 2) != '//') {
                        $uri->host = null;
                        break;
                    }
                    // URI is '////path', so we cannot nullify the
                    // host to preserve semantics.  Try expanding the
                    // hostname instead (fall through)
                }
                // first see if we can manually insert a hostname
                $host = $config->get('URI.Host');
                if (!is_null($host)) {
                    $uri->host = $host;
                } else {
                    // we can't do anything sensible, reject the URL.
                    return false;
                }
            } while (false);
        }
        return $this->doValidate($uri, $config, $context);
    }
}

// vim: et sw=4 sts=4
