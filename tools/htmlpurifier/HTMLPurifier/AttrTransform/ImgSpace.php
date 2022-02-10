<?php

/**
 * Pre-transform that changes deprecated hspace and vspace attributes to CSS
 */
class HTMLPurifier_AttrTransform_ImgSpace extends HTMLPurifier_AttrTransform
{
    /**
     * @type string
     */
    protected $attr;

    /**
     * @type array
     */
    protected $css = array(
        'hspace' => array('left', 'right'),
        'vspace' => array('top', 'bottom')
    );

    /**
     * @param string $attr
     */
    public function __construct($attr)
    {
        $this->attr = $attr;
        if (!isset($this->css[$attr])) {
            trigger_error(htmlspecialchars($attr) . ' is not valid space attribute');
        }
    }

    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->attr])) {
            return $attr;
        }

        $width = $this->confiscateAttr($attr, $this->attr);
        // some validation could happen here

        if (!isset($this->css[$this->attr])) {
            return $attr;
        }

        $style = '';
        foreach ($this->css[$this->attr] as $suffix) {
            $property = "margin-$suffix";
            $style .= "$property:{$width}px;";
        }
        $this->prependCSS($attr, $style);
        return $attr;
    }
}

// vim: et sw=4 sts=4
