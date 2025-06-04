<?php

/**
 * Abstract class for a set of proprietary modules that clean up (tidy)
 * poorly written HTML.
 * @todo Figure out how to protect some of these methods/properties
 */
class HTMLPurifier_HTMLModule_Tidy extends HTMLPurifier_HTMLModule
{
    /**
     * List of supported levels.
     * Index zero is a special case "no fixes" level.
     * @type array
     */
    public $levels = array(0 => 'none', 'light', 'medium', 'heavy');

    /**
     * Default level to place all fixes in.
     * Disabled by default.
     * @type string
     */
    public $defaultLevel = null;

    /**
     * Lists of fixes used by getFixesForLevel().
     * Format is:
     *      HTMLModule_Tidy->fixesForLevel[$level] = array('fix-1', 'fix-2');
     * @type array
     */
    public $fixesForLevel = array(
        'light' => array(),
        'medium' => array(),
        'heavy' => array()
    );

    /**
     * Lazy load constructs the module by determining the necessary
     * fixes to create and then delegating to the populate() function.
     * @param HTMLPurifier_Config $config
     * @todo Wildcard matching and error reporting when an added or
     *       subtracted fix has no effect.
     */
    public function setup($config)
    {
        // create fixes, initialize fixesForLevel
        $fixes = $this->makeFixes();
        $this->makeFixesForLevel($fixes);

        // figure out which fixes to use
        $level = $config->get('HTML.TidyLevel');
        $fixes_lookup = $this->getFixesForLevel($level);

        // get custom fix declarations: these need namespace processing
        $add_fixes = $config->get('HTML.TidyAdd');
        $remove_fixes = $config->get('HTML.TidyRemove');

        foreach ($fixes as $name => $fix) {
            // needs to be refactored a little to implement globbing
            if (isset($remove_fixes[$name]) ||
                (!isset($add_fixes[$name]) && !isset($fixes_lookup[$name]))) {
                unset($fixes[$name]);
            }
        }

        // populate this module with necessary fixes
        $this->populate($fixes);
    }

    /**
     * Retrieves all fixes per a level, returning fixes for that specific
     * level as well as all levels below it.
     * @param string $level level identifier, see $levels for valid values
     * @return array Lookup up table of fixes
     */
    public function getFixesForLevel($level)
    {
        if ($level == $this->levels[0]) {
            return array();
        }
        $activated_levels = array();
        for ($i = 1, $c = count($this->levels); $i < $c; $i++) {
            $activated_levels[] = $this->levels[$i];
            if ($this->levels[$i] == $level) {
                break;
            }
        }
        if ($i == $c) {
            trigger_error(
                'Tidy level ' . htmlspecialchars($level) . ' not recognized',
                E_USER_WARNING
            );
            return array();
        }
        $ret = array();
        foreach ($activated_levels as $level) {
            foreach ($this->fixesForLevel[$level] as $fix) {
                $ret[$fix] = true;
            }
        }
        return $ret;
    }

    /**
     * Dynamically populates the $fixesForLevel member variable using
     * the fixes array. It may be custom overloaded, used in conjunction
     * with $defaultLevel, or not used at all.
     * @param array $fixes
     */
    public function makeFixesForLevel($fixes)
    {
        if (!isset($this->defaultLevel)) {
            return;
        }
        if (!isset($this->fixesForLevel[$this->defaultLevel])) {
            trigger_error(
                'Default level ' . $this->defaultLevel . ' does not exist',
                E_USER_ERROR
            );
            return;
        }
        $this->fixesForLevel[$this->defaultLevel] = array_keys($fixes);
    }

    /**
     * Populates the module with transforms and other special-case code
     * based on a list of fixes passed to it
     * @param array $fixes Lookup table of fixes to activate
     */
    public function populate($fixes)
    {
        foreach ($fixes as $name => $fix) {
            // determine what the fix is for
            list($type, $params) = $this->getFixType($name);
            switch ($type) {
                case 'attr_transform_pre':
                case 'attr_transform_post':
                    $attr = $params['attr'];
                    if (isset($params['element'])) {
                        $element = $params['element'];
                        if (empty($this->info[$element])) {
                            $e = $this->addBlankElement($element);
                        } else {
                            $e = $this->info[$element];
                        }
                    } else {
                        $type = "info_$type";
                        $e = $this;
                    }
                    $e->{$type}[$attr] = $fix;
                    break;
                case 'tag_transform':
                    $this->info_tag_transform[$params['element']] = $fix;
                    break;
                case 'child':
                case 'content_model_type':
                    $element = $params['element'];
                    if (empty($this->info[$element])) {
                        $e = $this->addBlankElement($element);
                    } else {
                        $e = $this->info[$element];
                    }
                    $e->$type = $fix;
                    break;
                default:
                    trigger_error("Fix type $type not supported", E_USER_ERROR);
                    break;
            }
        }
    }

    /**
     * Parses a fix name and determines what kind of fix it is, as well
     * as other information defined by the fix
     * @param $name String name of fix
     * @return array(string $fix_type, array $fix_parameters)
     * @note $fix_parameters is type dependant, see populate() for usage
     *       of these parameters
     */
    public function getFixType($name)
    {
        // parse it
        $property = $attr = null;
        if (strpos($name, '#') !== false) {
            list($name, $property) = explode('#', $name);
        }
        if (strpos($name, '@') !== false) {
            list($name, $attr) = explode('@', $name);
        }

        // figure out the parameters
        $params = array();
        if ($name !== '') {
            $params['element'] = $name;
        }
        if (!is_null($attr)) {
            $params['attr'] = $attr;
        }

        // special case: attribute transform
        if (!is_null($attr)) {
            if (is_null($property)) {
                $property = 'pre';
            }
            $type = 'attr_transform_' . $property;
            return array($type, $params);
        }

        // special case: tag transform
        if (is_null($property)) {
            return array('tag_transform', $params);
        }

        return array($property, $params);

    }

    /**
     * Defines all fixes the module will perform in a compact
     * associative array of fix name to fix implementation.
     * @return array
     */
    public function makeFixes()
    {
        return array();
    }
}

// vim: et sw=4 sts=4
