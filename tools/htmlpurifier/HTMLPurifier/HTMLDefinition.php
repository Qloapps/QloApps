<?php

/**
 * Definition of the purified HTML that describes allowed children,
 * attributes, and many other things.
 *
 * Conventions:
 *
 * All member variables that are prefixed with info
 * (including the main $info array) are used by HTML Purifier internals
 * and should not be directly edited when customizing the HTMLDefinition.
 * They can usually be set via configuration directives or custom
 * modules.
 *
 * On the other hand, member variables without the info prefix are used
 * internally by the HTMLDefinition and MUST NOT be used by other HTML
 * Purifier internals. Many of them, however, are public, and may be
 * edited by userspace code to tweak the behavior of HTMLDefinition.
 *
 * @note This class is inspected by Printer_HTMLDefinition; please
 *       update that class if things here change.
 *
 * @warning Directives that change this object's structure must be in
 *          the HTML or Attr namespace!
 */
class HTMLPurifier_HTMLDefinition extends HTMLPurifier_Definition
{

    // FULLY-PUBLIC VARIABLES ---------------------------------------------

    /**
     * Associative array of element names to HTMLPurifier_ElementDef.
     * @type HTMLPurifier_ElementDef[]
     */
    public $info = array();

    /**
     * Associative array of global attribute name to attribute definition.
     * @type array
     */
    public $info_global_attr = array();

    /**
     * String name of parent element HTML will be going into.
     * @type string
     */
    public $info_parent = 'div';

    /**
     * Definition for parent element, allows parent element to be a
     * tag that's not allowed inside the HTML fragment.
     * @type HTMLPurifier_ElementDef
     */
    public $info_parent_def;

    /**
     * String name of element used to wrap inline elements in block context.
     * @type string
     * @note This is rarely used except for BLOCKQUOTEs in strict mode
     */
    public $info_block_wrapper = 'p';

    /**
     * Associative array of deprecated tag name to HTMLPurifier_TagTransform.
     * @type array
     */
    public $info_tag_transform = array();

    /**
     * Indexed list of HTMLPurifier_AttrTransform to be performed before validation.
     * @type HTMLPurifier_AttrTransform[]
     */
    public $info_attr_transform_pre = array();

    /**
     * Indexed list of HTMLPurifier_AttrTransform to be performed after validation.
     * @type HTMLPurifier_AttrTransform[]
     */
    public $info_attr_transform_post = array();

    /**
     * Nested lookup array of content set name (Block, Inline) to
     * element name to whether or not it belongs in that content set.
     * @type array
     */
    public $info_content_sets = array();

    /**
     * Indexed list of HTMLPurifier_Injector to be used.
     * @type HTMLPurifier_Injector[]
     */
    public $info_injector = array();

    /**
     * Doctype object
     * @type HTMLPurifier_Doctype
     */
    public $doctype;



    // RAW CUSTOMIZATION STUFF --------------------------------------------

    /**
     * Adds a custom attribute to a pre-existing element
     * @note This is strictly convenience, and does not have a corresponding
     *       method in HTMLPurifier_HTMLModule
     * @param string $element_name Element name to add attribute to
     * @param string $attr_name Name of attribute
     * @param mixed $def Attribute definition, can be string or object, see
     *             HTMLPurifier_AttrTypes for details
     */
    public function addAttribute($element_name, $attr_name, $def)
    {
        $module = $this->getAnonymousModule();
        if (!isset($module->info[$element_name])) {
            $element = $module->addBlankElement($element_name);
        } else {
            $element = $module->info[$element_name];
        }
        $element->attr[$attr_name] = $def;
    }

    /**
     * Adds a custom element to your HTML definition
     * @see HTMLPurifier_HTMLModule::addElement() for detailed
     *       parameter and return value descriptions.
     */
    public function addElement($element_name, $type, $contents, $attr_collections, $attributes = array())
    {
        $module = $this->getAnonymousModule();
        // assume that if the user is calling this, the element
        // is safe. This may not be a good idea
        $element = $module->addElement($element_name, $type, $contents, $attr_collections, $attributes);
        return $element;
    }

    /**
     * Adds a blank element to your HTML definition, for overriding
     * existing behavior
     * @param string $element_name
     * @return HTMLPurifier_ElementDef
     * @see HTMLPurifier_HTMLModule::addBlankElement() for detailed
     *       parameter and return value descriptions.
     */
    public function addBlankElement($element_name)
    {
        $module  = $this->getAnonymousModule();
        $element = $module->addBlankElement($element_name);
        return $element;
    }

    /**
     * Retrieves a reference to the anonymous module, so you can
     * bust out advanced features without having to make your own
     * module.
     * @return HTMLPurifier_HTMLModule
     */
    public function getAnonymousModule()
    {
        if (!$this->_anonModule) {
            $this->_anonModule = new HTMLPurifier_HTMLModule();
            $this->_anonModule->name = 'Anonymous';
        }
        return $this->_anonModule;
    }

    private $_anonModule = null;

    // PUBLIC BUT INTERNAL VARIABLES --------------------------------------

    /**
     * @type string
     */
    public $type = 'HTML';

    /**
     * @type HTMLPurifier_HTMLModuleManager
     */
    public $manager;

    /**
     * Performs low-cost, preliminary initialization.
     */
    public function __construct()
    {
        $this->manager = new HTMLPurifier_HTMLModuleManager();
    }

    /**
     * @param HTMLPurifier_Config $config
     */
    protected function doSetup($config)
    {
        $this->processModules($config);
        $this->setupConfigStuff($config);
        unset($this->manager);

        // cleanup some of the element definitions
        foreach ($this->info as $k => $v) {
            unset($this->info[$k]->content_model);
            unset($this->info[$k]->content_model_type);
        }
    }

    /**
     * Extract out the information from the manager
     * @param HTMLPurifier_Config $config
     */
    protected function processModules($config)
    {
        if ($this->_anonModule) {
            // for user specific changes
            // this is late-loaded so we don't have to deal with PHP4
            // reference wonky-ness
            $this->manager->addModule($this->_anonModule);
            unset($this->_anonModule);
        }

        $this->manager->setup($config);
        $this->doctype = $this->manager->doctype;

        foreach ($this->manager->modules as $module) {
            foreach ($module->info_tag_transform as $k => $v) {
                if ($v === false) {
                    unset($this->info_tag_transform[$k]);
                } else {
                    $this->info_tag_transform[$k] = $v;
                }
            }
            foreach ($module->info_attr_transform_pre as $k => $v) {
                if ($v === false) {
                    unset($this->info_attr_transform_pre[$k]);
                } else {
                    $this->info_attr_transform_pre[$k] = $v;
                }
            }
            foreach ($module->info_attr_transform_post as $k => $v) {
                if ($v === false) {
                    unset($this->info_attr_transform_post[$k]);
                } else {
                    $this->info_attr_transform_post[$k] = $v;
                }
            }
            foreach ($module->info_injector as $k => $v) {
                if ($v === false) {
                    unset($this->info_injector[$k]);
                } else {
                    $this->info_injector[$k] = $v;
                }
            }
        }
        $this->info = $this->manager->getElements();
        $this->info_content_sets = $this->manager->contentSets->lookup;
    }

    /**
     * Sets up stuff based on config. We need a better way of doing this.
     * @param HTMLPurifier_Config $config
     */
    protected function setupConfigStuff($config)
    {
        $block_wrapper = $config->get('HTML.BlockWrapper');
        if (isset($this->info_content_sets['Block'][$block_wrapper])) {
            $this->info_block_wrapper = $block_wrapper;
        } else {
            trigger_error(
                'Cannot use non-block element as block wrapper',
                E_USER_ERROR
            );
        }

        $parent = $config->get('HTML.Parent');
        $def = $this->manager->getElement($parent, true);
        if ($def) {
            $this->info_parent = $parent;
            $this->info_parent_def = $def;
        } else {
            trigger_error(
                'Cannot use unrecognized element as parent',
                E_USER_ERROR
            );
            $this->info_parent_def = $this->manager->getElement($this->info_parent, true);
        }

        // support template text
        $support = "(for information on implementing this, see the support forums) ";

        // setup allowed elements -----------------------------------------

        $allowed_elements = $config->get('HTML.AllowedElements');
        $allowed_attributes = $config->get('HTML.AllowedAttributes'); // retrieve early

        if (!is_array($allowed_elements) && !is_array($allowed_attributes)) {
            $allowed = $config->get('HTML.Allowed');
            if (is_string($allowed)) {
                list($allowed_elements, $allowed_attributes) = $this->parseTinyMCEAllowedList($allowed);
            }
        }

        if (is_array($allowed_elements)) {
            foreach ($this->info as $name => $d) {
                if (!isset($allowed_elements[$name])) {
                    unset($this->info[$name]);
                }
                unset($allowed_elements[$name]);
            }
            // emit errors
            foreach ($allowed_elements as $element => $d) {
                $element = htmlspecialchars($element); // PHP doesn't escape errors, be careful!
                trigger_error("Element '$element' is not supported $support", E_USER_WARNING);
            }
        }

        // setup allowed attributes ---------------------------------------

        $allowed_attributes_mutable = $allowed_attributes; // by copy!
        if (is_array($allowed_attributes)) {
            // This actually doesn't do anything, since we went away from
            // global attributes. It's possible that userland code uses
            // it, but HTMLModuleManager doesn't!
            foreach ($this->info_global_attr as $attr => $x) {
                $keys = array($attr, "*@$attr", "*.$attr");
                $delete = true;
                foreach ($keys as $key) {
                    if ($delete && isset($allowed_attributes[$key])) {
                        $delete = false;
                    }
                    if (isset($allowed_attributes_mutable[$key])) {
                        unset($allowed_attributes_mutable[$key]);
                    }
                }
                if ($delete) {
                    unset($this->info_global_attr[$attr]);
                }
            }

            foreach ($this->info as $tag => $info) {
                foreach ($info->attr as $attr => $x) {
                    $keys = array("$tag@$attr", $attr, "*@$attr", "$tag.$attr", "*.$attr");
                    $delete = true;
                    foreach ($keys as $key) {
                        if ($delete && isset($allowed_attributes[$key])) {
                            $delete = false;
                        }
                        if (isset($allowed_attributes_mutable[$key])) {
                            unset($allowed_attributes_mutable[$key]);
                        }
                    }
                    if ($delete) {
                        if ($this->info[$tag]->attr[$attr]->required) {
                            trigger_error(
                                "Required attribute '$attr' in element '$tag' " .
                                "was not allowed, which means '$tag' will not be allowed either",
                                E_USER_WARNING
                            );
                        }
                        unset($this->info[$tag]->attr[$attr]);
                    }
                }
            }
            // emit errors
            foreach ($allowed_attributes_mutable as $elattr => $d) {
                $bits = preg_split('/[.@]/', $elattr, 2);
                $c = count($bits);
                switch ($c) {
                    case 2:
                        if ($bits[0] !== '*') {
                            $element = htmlspecialchars($bits[0]);
                            $attribute = htmlspecialchars($bits[1]);
                            if (!isset($this->info[$element])) {
                                trigger_error(
                                    "Cannot allow attribute '$attribute' if element " .
                                    "'$element' is not allowed/supported $support"
                                );
                            } else {
                                trigger_error(
                                    "Attribute '$attribute' in element '$element' not supported $support",
                                    E_USER_WARNING
                                );
                            }
                            break;
                        }
                        // otherwise fall through
                    case 1:
                        $attribute = htmlspecialchars($bits[0]);
                        trigger_error(
                            "Global attribute '$attribute' is not ".
                            "supported in any elements $support",
                            E_USER_WARNING
                        );
                        break;
                }
            }
        }

        // setup forbidden elements ---------------------------------------

        $forbidden_elements   = $config->get('HTML.ForbiddenElements');
        $forbidden_attributes = $config->get('HTML.ForbiddenAttributes');

        foreach ($this->info as $tag => $info) {
            if (isset($forbidden_elements[$tag])) {
                unset($this->info[$tag]);
                continue;
            }
            foreach ($info->attr as $attr => $x) {
                if (isset($forbidden_attributes["$tag@$attr"]) ||
                    isset($forbidden_attributes["*@$attr"]) ||
                    isset($forbidden_attributes[$attr])
                ) {
                    unset($this->info[$tag]->attr[$attr]);
                    continue;
                } elseif (isset($forbidden_attributes["$tag.$attr"])) { // this segment might get removed eventually
                    // $tag.$attr are not user supplied, so no worries!
                    trigger_error(
                        "Error with $tag.$attr: tag.attr syntax not supported for " .
                        "HTML.ForbiddenAttributes; use tag@attr instead",
                        E_USER_WARNING
                    );
                }
            }
        }
        foreach ($forbidden_attributes as $key => $v) {
            if (strlen($key) < 2) {
                continue;
            }
            if ($key[0] != '*') {
                continue;
            }
            if ($key[1] == '.') {
                trigger_error(
                    "Error with $key: *.attr syntax not supported for HTML.ForbiddenAttributes; use attr instead",
                    E_USER_WARNING
                );
            }
        }

        // setup injectors -----------------------------------------------------
        foreach ($this->info_injector as $i => $injector) {
            if ($injector->checkNeeded($config) !== false) {
                // remove injector that does not have it's required
                // elements/attributes present, and is thus not needed.
                unset($this->info_injector[$i]);
            }
        }
    }

    /**
     * Parses a TinyMCE-flavored Allowed Elements and Attributes list into
     * separate lists for processing. Format is element[attr1|attr2],element2...
     * @warning Although it's largely drawn from TinyMCE's implementation,
     *      it is different, and you'll probably have to modify your lists
     * @param array $list String list to parse
     * @return array
     * @todo Give this its own class, probably static interface
     */
    public function parseTinyMCEAllowedList($list)
    {
        $list = str_replace(array(' ', "\t"), '', $list);

        $elements = array();
        $attributes = array();

        $chunks = preg_split('/(,|[\n\r]+)/', $list);
        foreach ($chunks as $chunk) {
            if (empty($chunk)) {
                continue;
            }
            // remove TinyMCE element control characters
            if (!strpos($chunk, '[')) {
                $element = $chunk;
                $attr = false;
            } else {
                list($element, $attr) = explode('[', $chunk);
            }
            if ($element !== '*') {
                $elements[$element] = true;
            }
            if (!$attr) {
                continue;
            }
            $attr = substr($attr, 0, strlen($attr) - 1); // remove trailing ]
            $attr = explode('|', $attr);
            foreach ($attr as $key) {
                $attributes["$element.$key"] = true;
            }
        }
        return array($elements, $attributes);
    }
}

// vim: et sw=4 sts=4
