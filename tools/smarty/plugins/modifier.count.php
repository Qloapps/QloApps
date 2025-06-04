<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifier
 */
/**
 * Smarty count modifier plugin
 * Type:     modifier
 * Name:     count
 * Purpose:  counts all elements in an array or in a Countable object
 * Input:
 *          - Countable|array: array or object to count
 *          - mode: int defaults to 0 for normal count mode, if set to 1 counts recursive
  *
 * @param mixed $arrayOrObject  input array/object
 * @param int $mode       count mode
 *
 * @return int
 */
function smarty_modifier_count($arrayOrObject, $mode = 0)
{
    /*
     * @see https://www.php.net/count
     * > Prior to PHP 8.0.0, if the parameter was neither an array nor an object that implements the Countable interface,
     * > 1 would be returned, unless value was null, in which case 0 would be returned.
     */

    if ($arrayOrObject instanceof Countable || is_array($arrayOrObject)) {
        return count($arrayOrObject, (int) $mode);
    } elseif ($arrayOrObject === null) {
        return 0;
    }
    return 1;
}
