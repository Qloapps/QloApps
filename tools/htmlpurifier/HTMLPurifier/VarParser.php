<?php

/**
 * Parses string representations into their corresponding native PHP
 * variable type. The base implementation does a simple type-check.
 */
class HTMLPurifier_VarParser
{

    const C_STRING = 1;
    const ISTRING = 2;
    const TEXT = 3;
    const ITEXT = 4;
    const C_INT = 5;
    const C_FLOAT = 6;
    const C_BOOL = 7;
    const LOOKUP = 8;
    const ALIST = 9;
    const HASH = 10;
    const C_MIXED = 11;

    /**
     * Lookup table of allowed types. Mainly for backwards compatibility, but
     * also convenient for transforming string type names to the integer constants.
     */
    public static $types = array(
        'string' => self::C_STRING,
        'istring' => self::ISTRING,
        'text' => self::TEXT,
        'itext' => self::ITEXT,
        'int' => self::C_INT,
        'float' => self::C_FLOAT,
        'bool' => self::C_BOOL,
        'lookup' => self::LOOKUP,
        'list' => self::ALIST,
        'hash' => self::HASH,
        'mixed' => self::C_MIXED
    );

    /**
     * Lookup table of types that are string, and can have aliases or
     * allowed value lists.
     */
    public static $stringTypes = array(
        self::C_STRING => true,
        self::ISTRING => true,
        self::TEXT => true,
        self::ITEXT => true,
    );

    /**
     * Validate a variable according to type.
     * It may return NULL as a valid type if $allow_null is true.
     *
     * @param mixed $var Variable to validate
     * @param int $type Type of variable, see HTMLPurifier_VarParser->types
     * @param bool $allow_null Whether or not to permit null as a value
     * @return string Validated and type-coerced variable
     * @throws HTMLPurifier_VarParserException
     */
    final public function parse($var, $type, $allow_null = false)
    {
        if (is_string($type)) {
            if (!isset(HTMLPurifier_VarParser::$types[$type])) {
                throw new HTMLPurifier_VarParserException("Invalid type '$type'");
            } else {
                $type = HTMLPurifier_VarParser::$types[$type];
            }
        }
        $var = $this->parseImplementation($var, $type, $allow_null);
        if ($allow_null && $var === null) {
            return null;
        }
        // These are basic checks, to make sure nothing horribly wrong
        // happened in our implementations.
        switch ($type) {
            case (self::C_STRING):
            case (self::ISTRING):
            case (self::TEXT):
            case (self::ITEXT):
                if (!is_string($var)) {
                    break;
                }
                if ($type == self::ISTRING || $type == self::ITEXT) {
                    $var = strtolower($var);
                }
                return $var;
            case (self::C_INT):
                if (!is_int($var)) {
                    break;
                }
                return $var;
            case (self::C_FLOAT):
                if (!is_float($var)) {
                    break;
                }
                return $var;
            case (self::C_BOOL):
                if (!is_bool($var)) {
                    break;
                }
                return $var;
            case (self::LOOKUP):
            case (self::ALIST):
            case (self::HASH):
                if (!is_array($var)) {
                    break;
                }
                if ($type === self::LOOKUP) {
                    foreach ($var as $k) {
                        if ($k !== true) {
                            $this->error('Lookup table contains value other than true');
                        }
                    }
                } elseif ($type === self::ALIST) {
                    $keys = array_keys($var);
                    if (array_keys($keys) !== $keys) {
                        $this->error('Indices for list are not uniform');
                    }
                }
                return $var;
            case (self::C_MIXED):
                return $var;
            default:
                $this->errorInconsistent(get_class($this), $type);
        }
        $this->errorGeneric($var, $type);
    }

    /**
     * Actually implements the parsing. Base implementation does not
     * do anything to $var. Subclasses should overload this!
     * @param mixed $var
     * @param int $type
     * @param bool $allow_null
     * @return string
     */
    protected function parseImplementation($var, $type, $allow_null)
    {
        return $var;
    }

    /**
     * Throws an exception.
     * @throws HTMLPurifier_VarParserException
     */
    protected function error($msg)
    {
        throw new HTMLPurifier_VarParserException($msg);
    }

    /**
     * Throws an inconsistency exception.
     * @note This should not ever be called. It would be called if we
     *       extend the allowed values of HTMLPurifier_VarParser without
     *       updating subclasses.
     * @param string $class
     * @param int $type
     * @throws HTMLPurifier_Exception
     */
    protected function errorInconsistent($class, $type)
    {
        throw new HTMLPurifier_Exception(
            "Inconsistency in $class: " . HTMLPurifier_VarParser::getTypeName($type) .
            " not implemented"
        );
    }

    /**
     * Generic error for if a type didn't work.
     * @param mixed $var
     * @param int $type
     */
    protected function errorGeneric($var, $type)
    {
        $vtype = gettype($var);
        $this->error("Expected type " . HTMLPurifier_VarParser::getTypeName($type) . ", got $vtype");
    }

    /**
     * @param int $type
     * @return string
     */
    public static function getTypeName($type)
    {
        static $lookup;
        if (!$lookup) {
            // Lazy load the alternative lookup table
            $lookup = array_flip(HTMLPurifier_VarParser::$types);
        }
        if (!isset($lookup[$type])) {
            return 'unknown';
        }
        return $lookup[$type];
    }
}

// vim: et sw=4 sts=4
