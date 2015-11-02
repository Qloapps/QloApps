<?php

// OUT OF DATE, NEEDS UPDATING!
// USE XMLWRITER!

class HTMLPurifier_Printer
{

    /**
     * For HTML generation convenience funcs.
     * @type HTMLPurifier_Generator
     */
    protected $generator;

    /**
     * For easy access.
     * @type HTMLPurifier_Config
     */
    protected $config;

    /**
     * Initialize $generator.
     */
    public function __construct()
    {
    }

    /**
     * Give generator necessary configuration if possible
     * @param HTMLPurifier_Config $config
     */
    public function prepareGenerator($config)
    {
        $all = $config->getAll();
        $context = new HTMLPurifier_Context();
        $this->generator = new HTMLPurifier_Generator($config, $context);
    }

    /**
     * Main function that renders object or aspect of that object
     * @note Parameters vary depending on printer
     */
    // function render() {}

    /**
     * Returns a start tag
     * @param string $tag Tag name
     * @param array $attr Attribute array
     * @return string
     */
    protected function start($tag, $attr = array())
    {
        return $this->generator->generateFromToken(
            new HTMLPurifier_Token_Start($tag, $attr ? $attr : array())
        );
    }

    /**
     * Returns an end tag
     * @param string $tag Tag name
     * @return string
     */
    protected function end($tag)
    {
        return $this->generator->generateFromToken(
            new HTMLPurifier_Token_End($tag)
        );
    }

    /**
     * Prints a complete element with content inside
     * @param string $tag Tag name
     * @param string $contents Element contents
     * @param array $attr Tag attributes
     * @param bool $escape whether or not to escape contents
     * @return string
     */
    protected function element($tag, $contents, $attr = array(), $escape = true)
    {
        return $this->start($tag, $attr) .
            ($escape ? $this->escape($contents) : $contents) .
            $this->end($tag);
    }

    /**
     * @param string $tag
     * @param array $attr
     * @return string
     */
    protected function elementEmpty($tag, $attr = array())
    {
        return $this->generator->generateFromToken(
            new HTMLPurifier_Token_Empty($tag, $attr)
        );
    }

    /**
     * @param string $text
     * @return string
     */
    protected function text($text)
    {
        return $this->generator->generateFromToken(
            new HTMLPurifier_Token_Text($text)
        );
    }

    /**
     * Prints a simple key/value row in a table.
     * @param string $name Key
     * @param mixed $value Value
     * @return string
     */
    protected function row($name, $value)
    {
        if (is_bool($value)) {
            $value = $value ? 'On' : 'Off';
        }
        return
            $this->start('tr') . "\n" .
            $this->element('th', $name) . "\n" .
            $this->element('td', $value) . "\n" .
            $this->end('tr');
    }

    /**
     * Escapes a string for HTML output.
     * @param string $string String to escape
     * @return string
     */
    protected function escape($string)
    {
        $string = HTMLPurifier_Encoder::cleanUTF8($string);
        $string = htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        return $string;
    }

    /**
     * Takes a list of strings and turns them into a single list
     * @param string[] $array List of strings
     * @param bool $polite Bool whether or not to add an end before the last
     * @return string
     */
    protected function listify($array, $polite = false)
    {
        if (empty($array)) {
            return 'None';
        }
        $ret = '';
        $i = count($array);
        foreach ($array as $value) {
            $i--;
            $ret .= $value;
            if ($i > 0 && !($polite && $i == 1)) {
                $ret .= ', ';
            }
            if ($polite && $i == 1) {
                $ret .= 'and ';
            }
        }
        return $ret;
    }

    /**
     * Retrieves the class of an object without prefixes, as well as metadata
     * @param object $obj Object to determine class of
     * @param string $sec_prefix Further prefix to remove
     * @return string
     */
    protected function getClass($obj, $sec_prefix = '')
    {
        static $five = null;
        if ($five === null) {
            $five = version_compare(PHP_VERSION, '5', '>=');
        }
        $prefix = 'HTMLPurifier_' . $sec_prefix;
        if (!$five) {
            $prefix = strtolower($prefix);
        }
        $class = str_replace($prefix, '', get_class($obj));
        $lclass = strtolower($class);
        $class .= '(';
        switch ($lclass) {
            case 'enum':
                $values = array();
                foreach ($obj->valid_values as $value => $bool) {
                    $values[] = $value;
                }
                $class .= implode(', ', $values);
                break;
            case 'css_composite':
                $values = array();
                foreach ($obj->defs as $def) {
                    $values[] = $this->getClass($def, $sec_prefix);
                }
                $class .= implode(', ', $values);
                break;
            case 'css_multiple':
                $class .= $this->getClass($obj->single, $sec_prefix) . ', ';
                $class .= $obj->max;
                break;
            case 'css_denyelementdecorator':
                $class .= $this->getClass($obj->def, $sec_prefix) . ', ';
                $class .= $obj->element;
                break;
            case 'css_importantdecorator':
                $class .= $this->getClass($obj->def, $sec_prefix);
                if ($obj->allow) {
                    $class .= ', !important';
                }
                break;
        }
        $class .= ')';
        return $class;
    }
}

// vim: et sw=4 sts=4

