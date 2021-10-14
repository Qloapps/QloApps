<?php

/**
 * Validates file as defined by RFC 1630 and RFC 1738.
 */
class HTMLPurifier_URIScheme_file extends HTMLPurifier_URIScheme
{
    /**
     * Generally file:// URLs are not accessible from most
     * machines, so placing them as an img src is incorrect.
     * @type bool
     */
    public $browsable = false;

    /**
     * Basically the *only* URI scheme for which this is true, since
     * accessing files on the local machine is very common.  In fact,
     * browsers on some operating systems don't understand the
     * authority, though I hear it is used on Windows to refer to
     * network shares.
     * @type bool
     */
    public $may_omit_host = true;

    /**
     * @param HTMLPurifier_URI $uri
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool
     */
    public function doValidate(&$uri, $config, $context)
    {
        // Authentication method is not supported
        $uri->userinfo = null;
        // file:// makes no provisions for accessing the resource
        $uri->port = null;
        // While it seems to work on Firefox, the querystring has
        // no possible effect and is thus stripped.
        $uri->query = null;
        return true;
    }
}

// vim: et sw=4 sts=4
