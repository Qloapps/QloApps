<?php

/**
 * Validates name/value pairs in param tags to be used in safe objects. This
 * will only allow name values it recognizes, and pre-fill certain attributes
 * with required values.
 *
 * @note
 *      This class only supports Flash. In the future, Quicktime support
 *      may be added.
 *
 * @warning
 *      This class expects an injector to add the necessary parameters tags.
 */
class HTMLPurifier_AttrTransform_SafeParam extends HTMLPurifier_AttrTransform
{
    /**
     * @type string
     */
    public $name = "SafeParam";

    /**
     * @type HTMLPurifier_AttrDef_URI
     */
    private $uri;

    /**
     * @type HTMLPurifier_AttrDef_Enum
     */
    public $wmode;

    public function __construct()
    {
        $this->uri = new HTMLPurifier_AttrDef_URI(true); // embedded
        $this->wmode = new HTMLPurifier_AttrDef_Enum(array('window', 'opaque', 'transparent'));
    }

    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        // If we add support for other objects, we'll need to alter the
        // transforms.
        switch ($attr['name']) {
            // application/x-shockwave-flash
            // Keep this synchronized with Injector/SafeObject.php
            case 'allowScriptAccess':
                $attr['value'] = 'never';
                break;
            case 'allowNetworking':
                $attr['value'] = 'internal';
                break;
            case 'allowFullScreen':
                if ($config->get('HTML.FlashAllowFullScreen')) {
                    $attr['value'] = ($attr['value'] == 'true') ? 'true' : 'false';
                } else {
                    $attr['value'] = 'false';
                }
                break;
            case 'wmode':
                $attr['value'] = $this->wmode->validate($attr['value'], $config, $context);
                break;
            case 'movie':
            case 'src':
                $attr['name'] = "movie";
                $attr['value'] = $this->uri->validate($attr['value'], $config, $context);
                break;
            case 'flashvars':
                // we're going to allow arbitrary inputs to the SWF, on
                // the reasoning that it could only hack the SWF, not us.
                break;
            // add other cases to support other param name/value pairs
            default:
                $attr['name'] = $attr['value'] = null;
        }
        return $attr;
    }
}

// vim: et sw=4 sts=4
