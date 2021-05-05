<?php

/**
 * Foreach Runtime Methods count(), init(), restore()
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Runtime_Foreach
{
    /**
     * Stack of saved variables
     *
     * @var array
     */
    private $stack = array();

    /**
     * Init foreach loop
     *  - save item and key variables, named foreach property data if defined
     *  - init item and key variables, named foreach property data if required
     *  - count total if required
     *
     * @param \Smarty_Internal_Template $tpl
     * @param mixed                     $from       values to loop over
     * @param string                    $item       variable name
     * @param bool                      $needTotal  flag if we need to count values
     * @param null|string               $key        variable name
     * @param null|string               $name       of named foreach
     * @param array                     $properties of named foreach
     *
     * @return mixed $from
     */
    public function init(
        Smarty_Internal_Template $tpl,
        $from,
        $item,
        $needTotal = false,
        $key = null,
        $name = null,
        $properties = array()
    ) {
        $needTotal = $needTotal || isset($properties[ 'total' ]);
        $saveVars = array();
        $total = null;
        if (!is_array($from)) {
            if (is_object($from)) {
                if ($needTotal) {
                    $total = $this->count($from);
                }
            } else {
                settype($from, 'array');
            }
        }
        if (!isset($total)) {
            $total = empty($from) ? 0 : ($needTotal ? count($from) : 1);
        }
        if (isset($tpl->tpl_vars[ $item ])) {
            $saveVars[ 'item' ] = array(
                $item,
                $tpl->tpl_vars[ $item ]
            );
        }
        $tpl->tpl_vars[ $item ] = new Smarty_Variable(null, $tpl->isRenderingCache);
        if ($total === 0) {
            $from = null;
        } else {
            if ($key) {
                if (isset($tpl->tpl_vars[ $key ])) {
                    $saveVars[ 'key' ] = array(
                        $key,
                        $tpl->tpl_vars[ $key ]
                    );
                }
                $tpl->tpl_vars[ $key ] = new Smarty_Variable(null, $tpl->isRenderingCache);
            }
        }
        if ($needTotal) {
            $tpl->tpl_vars[ $item ]->total = $total;
        }
        if ($name) {
            $namedVar = "__smarty_foreach_{$name}";
            if (isset($tpl->tpl_vars[ $namedVar ])) {
                $saveVars[ 'named' ] = array(
                    $namedVar,
                    $tpl->tpl_vars[ $namedVar ]
                );
            }
            $namedProp = array();
            if (isset($properties[ 'total' ])) {
                $namedProp[ 'total' ] = $total;
            }
            if (isset($properties[ 'iteration' ])) {
                $namedProp[ 'iteration' ] = 0;
            }
            if (isset($properties[ 'index' ])) {
                $namedProp[ 'index' ] = -1;
            }
            if (isset($properties[ 'show' ])) {
                $namedProp[ 'show' ] = ($total > 0);
            }
            $tpl->tpl_vars[ $namedVar ] = new Smarty_Variable($namedProp);
        }
        $this->stack[] = $saveVars;
        return $from;
    }

    /**
     * [util function] counts an array, arrayAccess/traversable or PDOStatement object
     *
     * @param mixed $value
     *
     * @return int   the count for arrays and objects that implement countable, 1 for other objects that don't, and 0
     *               for empty elements
     */
    public function count($value)
    {
        if ($value instanceof IteratorAggregate) {
            // Note: getIterator() returns a Traversable, not an Iterator
            // thus rewind() and valid() methods may not be present
            return iterator_count($value->getIterator());
        } elseif ($value instanceof Iterator) {
            return $value instanceof Generator ? 1 : iterator_count($value);
        } elseif ($value instanceof Countable) {
            return count($value);
        } elseif ($value instanceof PDOStatement) {
            return $value->rowCount();
        } elseif ($value instanceof Traversable) {
            return iterator_count($value);
        }
        return count((array)$value);
    }

    /**
     * Restore saved variables
     *
     * will be called by {break n} or {continue n} for the required number of levels
     *
     * @param \Smarty_Internal_Template $tpl
     * @param int                       $levels number of levels
     */
    public function restore(Smarty_Internal_Template $tpl, $levels = 1)
    {
        while ($levels) {
            $saveVars = array_pop($this->stack);
            if (!empty($saveVars)) {
                if (isset($saveVars[ 'item' ])) {
                    $item = &$saveVars[ 'item' ];
                    $tpl->tpl_vars[ $item[ 0 ] ]->value = $item[ 1 ]->value;
                }
                if (isset($saveVars[ 'key' ])) {
                    $tpl->tpl_vars[ $saveVars[ 'key' ][ 0 ] ] = $saveVars[ 'key' ][ 1 ];
                }
                if (isset($saveVars[ 'named' ])) {
                    $tpl->tpl_vars[ $saveVars[ 'named' ][ 0 ] ] = $saveVars[ 'named' ][ 1 ];
                }
            }
            $levels--;
        }
    }
}
