<?php

/*! @mainpage
 *
 * HTML Purifier is an HTML filter that will take an arbitrary snippet of
 * HTML and rigorously test, validate and filter it into a version that
 * is safe for output onto webpages. It achieves this by:
 *
 *  -# Lexing (parsing into tokens) the document,
 *  -# Executing various strategies on the tokens:
 *      -# Removing all elements not in the whitelist,
 *      -# Making the tokens well-formed,
 *      -# Fixing the nesting of the nodes, and
 *      -# Validating attributes of the nodes; and
 *  -# Generating HTML from the purified tokens.
 *
 * However, most users will only need to interface with the HTMLPurifier
 * and HTMLPurifier_Config.
 */

/*
    HTML Purifier 4.18.0 - Standards Compliant HTML Filtering
    Copyright (C) 2006-2008 Edward Z. Yang

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Facade that coordinates HTML Purifier's subsystems in order to purify HTML.
 *
 * @note There are several points in which configuration can be specified
 *       for HTML Purifier.  The precedence of these (from lowest to
 *       highest) is as follows:
 *          -# Instance: new HTMLPurifier($config)
 *          -# Invocation: purify($html, $config)
 *       These configurations are entirely independent of each other and
 *       are *not* merged (this behavior may change in the future).
 *
 * @todo We need an easier way to inject strategies using the configuration
 *       object.
 */
class HTMLPurifier
{

    /**
     * Version of HTML Purifier.
     * @type string
     */
    public $version = '4.18.0';

    /**
     * Constant with version of HTML Purifier.
     */
    const VERSION = '4.18.0';

    /**
     * Global configuration object.
     * @type HTMLPurifier_Config
     */
    public $config;

    /**
     * Array of extra filter objects to run on HTML,
     * for backwards compatibility.
     * @type HTMLPurifier_Filter[]
     */
    private $filters = array();

    /**
     * Single instance of HTML Purifier.
     * @type HTMLPurifier
     */
    private static $instance;

    /**
     * @type HTMLPurifier_Strategy_Core
     */
    protected $strategy;

    /**
     * @type HTMLPurifier_Generator
     */
    protected $generator;

    /**
     * Resultant context of last run purification.
     * Is an array of contexts if the last called method was purifyArray().
     * @type HTMLPurifier_Context
     */
    public $context;

    /**
     * Initializes the purifier.
     *
     * @param HTMLPurifier_Config|mixed $config Optional HTMLPurifier_Config object
     *                for all instances of the purifier, if omitted, a default
     *                configuration is supplied (which can be overridden on a
     *                per-use basis).
     *                The parameter can also be any type that
     *                HTMLPurifier_Config::create() supports.
     */
    public function __construct($config = null)
    {
        $this->config = HTMLPurifier_Config::create($config);
        $this->strategy = new HTMLPurifier_Strategy_Core();
    }

    /**
     * Adds a filter to process the output. First come first serve
     *
     * @param HTMLPurifier_Filter $filter HTMLPurifier_Filter object
     */
    public function addFilter($filter)
    {
        trigger_error(
            'HTMLPurifier->addFilter() is deprecated, use configuration directives' .
            ' in the Filter namespace or Filter.Custom',
            E_USER_WARNING
        );
        $this->filters[] = $filter;
    }

    /**
     * Filters an HTML snippet/document to be XSS-free and standards-compliant.
     *
     * @param string $html String of HTML to purify
     * @param HTMLPurifier_Config $config Config object for this operation,
     *                if omitted, defaults to the config object specified during this
     *                object's construction. The parameter can also be any type
     *                that HTMLPurifier_Config::create() supports.
     *
     * @return string Purified HTML
     */
    public function purify($html, $config = null)
    {
        // :TODO: make the config merge in, instead of replace
        $config = $config ? HTMLPurifier_Config::create($config) : $this->config;

        // implementation is partially environment dependant, partially
        // configuration dependant
        $lexer = HTMLPurifier_Lexer::create($config);

        $context = new HTMLPurifier_Context();

        // setup HTML generator
        $this->generator = new HTMLPurifier_Generator($config, $context);
        $context->register('Generator', $this->generator);

        // set up global context variables
        if ($config->get('Core.CollectErrors')) {
            // may get moved out if other facilities use it
            $language_factory = HTMLPurifier_LanguageFactory::instance();
            $language = $language_factory->create($config, $context);
            $context->register('Locale', $language);

            $error_collector = new HTMLPurifier_ErrorCollector($context);
            $context->register('ErrorCollector', $error_collector);
        }

        // setup id_accumulator context, necessary due to the fact that
        // AttrValidator can be called from many places
        $id_accumulator = HTMLPurifier_IDAccumulator::build($config, $context);
        $context->register('IDAccumulator', $id_accumulator);

        $html = HTMLPurifier_Encoder::convertToUTF8($html, $config, $context);

        // setup filters
        $filter_flags = $config->getBatch('Filter');
        $custom_filters = $filter_flags['Custom'];
        unset($filter_flags['Custom']);
        $filters = array();
        foreach ($filter_flags as $filter => $flag) {
            if (!$flag) {
                continue;
            }
            if (strpos($filter, '.') !== false) {
                continue;
            }
            $class = "HTMLPurifier_Filter_$filter";
            $filters[] = new $class;
        }
        foreach ($custom_filters as $filter) {
            // maybe "HTMLPurifier_Filter_$filter", but be consistent with AutoFormat
            $filters[] = $filter;
        }
        $filters = array_merge($filters, $this->filters);
        // maybe prepare(), but later

        for ($i = 0, $filter_size = count($filters); $i < $filter_size; $i++) {
            $html = $filters[$i]->preFilter($html, $config, $context);
        }

        // purified HTML
        $html =
            $this->generator->generateFromTokens(
                // list of tokens
                $this->strategy->execute(
                    // list of un-purified tokens
                    $lexer->tokenizeHTML(
                        // un-purified HTML
                        $html,
                        $config,
                        $context
                    ),
                    $config,
                    $context
                )
            );

        for ($i = $filter_size - 1; $i >= 0; $i--) {
            $html = $filters[$i]->postFilter($html, $config, $context);
        }

        $html = HTMLPurifier_Encoder::convertFromUTF8($html, $config, $context);
        $this->context =& $context;
        return $html;
    }

    /**
     * Filters an array of HTML snippets
     *
     * @param string[] $array_of_html Array of html snippets
     * @param HTMLPurifier_Config $config Optional config object for this operation.
     *                See HTMLPurifier::purify() for more details.
     *
     * @return string[] Array of purified HTML
     */
    public function purifyArray($array_of_html, $config = null)
    {
        $context_array = array();
        $array = array();
        foreach($array_of_html as $key=>$value){
            if (is_array($value)) {
                $array[$key] = $this->purifyArray($value, $config);
            } else {
                $array[$key] = $this->purify($value, $config);
            }
            $context_array[$key] = $this->context;
        }
        $this->context = $context_array;
        return $array;
    }

    /**
     * Singleton for enforcing just one HTML Purifier in your system
     *
     * @param HTMLPurifier|HTMLPurifier_Config $prototype Optional prototype
     *                   HTMLPurifier instance to overload singleton with,
     *                   or HTMLPurifier_Config instance to configure the
     *                   generated version with.
     *
     * @return HTMLPurifier
     */
    public static function instance($prototype = null)
    {
        if (!self::$instance || $prototype) {
            if ($prototype instanceof HTMLPurifier) {
                self::$instance = $prototype;
            } elseif ($prototype) {
                self::$instance = new HTMLPurifier($prototype);
            } else {
                self::$instance = new HTMLPurifier();
            }
        }
        return self::$instance;
    }

    /**
     * Singleton for enforcing just one HTML Purifier in your system
     *
     * @param HTMLPurifier|HTMLPurifier_Config $prototype Optional prototype
     *                   HTMLPurifier instance to overload singleton with,
     *                   or HTMLPurifier_Config instance to configure the
     *                   generated version with.
     *
     * @return HTMLPurifier
     * @note Backwards compatibility, see instance()
     */
    public static function getInstance($prototype = null)
    {
        return HTMLPurifier::instance($prototype);
    }
}

// vim: et sw=4 sts=4
