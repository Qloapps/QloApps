<?php

/**
 * Represents a pre or post processing filter on HTML Purifier's output
 *
 * Sometimes, a little ad-hoc fixing of HTML has to be done before
 * it gets sent through HTML Purifier: you can use filters to achieve
 * this effect. For instance, YouTube videos can be preserved using
 * this manner. You could have used a decorator for this task, but
 * PHP's support for them is not terribly robust, so we're going
 * to just loop through the filters.
 *
 * Filters should be exited first in, last out. If there are three filters,
 * named 1, 2 and 3, the order of execution should go 1->preFilter,
 * 2->preFilter, 3->preFilter, purify, 3->postFilter, 2->postFilter,
 * 1->postFilter.
 *
 * @note Methods are not declared abstract as it is perfectly legitimate
 *       for an implementation not to want anything to happen on a step
 */

class HTMLPurifier_Filter
{

    /**
     * Name of the filter for identification purposes.
     * @type string
     */
    public $name;

    /**
     * Pre-processor function, handles HTML before HTML Purifier
     * @param string $html
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     */
    public function preFilter($html, $config, $context)
    {
        return $html;
    }

    /**
     * Post-processor function, handles HTML after HTML Purifier
     * @param string $html
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     */
    public function postFilter($html, $config, $context)
    {
        return $html;
    }
}

// vim: et sw=4 sts=4
