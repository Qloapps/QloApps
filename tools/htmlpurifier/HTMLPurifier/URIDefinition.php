<?php

class HTMLPurifier_URIDefinition extends HTMLPurifier_Definition
{

    public $type = 'URI';
    protected $filters = array();
    protected $postFilters = array();
    protected $registeredFilters = array();

    /**
     * HTMLPurifier_URI object of the base specified at %URI.Base
     */
    public $base;

    /**
     * String host to consider "home" base, derived off of $base
     */
    public $host;

    /**
     * Name of default scheme based on %URI.DefaultScheme and %URI.Base
     */
    public $defaultScheme;

    public function __construct()
    {
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternal());
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternalResources());
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableResources());
        $this->registerFilter(new HTMLPurifier_URIFilter_HostBlacklist());
        $this->registerFilter(new HTMLPurifier_URIFilter_SafeIframe());
        $this->registerFilter(new HTMLPurifier_URIFilter_MakeAbsolute());
        $this->registerFilter(new HTMLPurifier_URIFilter_Munge());
    }

    public function registerFilter($filter)
    {
        $this->registeredFilters[$filter->name] = $filter;
    }

    public function addFilter($filter, $config)
    {
        $r = $filter->prepare($config);
        if ($r === false) return; // null is ok, for backwards compat
        if ($filter->post) {
            $this->postFilters[$filter->name] = $filter;
        } else {
            $this->filters[$filter->name] = $filter;
        }
    }

    protected function doSetup($config)
    {
        $this->setupMemberVariables($config);
        $this->setupFilters($config);
    }

    protected function setupFilters($config)
    {
        foreach ($this->registeredFilters as $name => $filter) {
            if ($filter->always_load) {
                $this->addFilter($filter, $config);
            } else {
                $conf = $config->get('URI.' . $name);
                if ($conf !== false && $conf !== null) {
                    $this->addFilter($filter, $config);
                }
            }
        }
        unset($this->registeredFilters);
    }

    protected function setupMemberVariables($config)
    {
        $this->host = $config->get('URI.Host');
        $base_uri = $config->get('URI.Base');
        if (!is_null($base_uri)) {
            $parser = new HTMLPurifier_URIParser();
            $this->base = $parser->parse($base_uri);
            $this->defaultScheme = $this->base->scheme;
            if (is_null($this->host)) $this->host = $this->base->host;
        }
        if (is_null($this->defaultScheme)) $this->defaultScheme = $config->get('URI.DefaultScheme');
    }

    public function getDefaultScheme($config, $context)
    {
        return HTMLPurifier_URISchemeRegistry::instance()->getScheme($this->defaultScheme, $config, $context);
    }

    public function filter(&$uri, $config, $context)
    {
        foreach ($this->filters as $name => $f) {
            $result = $f->filter($uri, $config, $context);
            if (!$result) return false;
        }
        return true;
    }

    public function postFilter(&$uri, $config, $context)
    {
        foreach ($this->postFilters as $name => $f) {
            $result = $f->filter($uri, $config, $context);
            if (!$result) return false;
        }
        return true;
    }

}

// vim: et sw=4 sts=4
