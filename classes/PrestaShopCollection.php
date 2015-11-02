<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Create a collection of ObjectModel objects
 *
 * @since 1.5.0
 */
class PrestaShopCollectionCore implements Iterator, ArrayAccess, Countable
{
    const LEFT_JOIN = 1;
    const INNER_JOIN = 2;
    const LEFT_OUTER_JOIN = 3;

    /**
     * @var string Object class name
     */
    protected $classname;

    /**
     * @var int
     */
    protected $id_lang;

    /**
     * @var array Object definition
     */
    protected $definition = array();

    /**
     * @var DbQuery
     */
    protected $query;

    /**
     * @var array Collection of objects in an array
     */
    protected $results = array();

    /**
     * @var bool Is current collection already hydrated
     */
    protected $is_hydrated = false;

    /**
     * @var int Collection iterator
     */
    protected $iterator = 0;

    /**
     * @var int Total of elements for iteration
     */
    protected $total;

    /**
     * @var int Page number
     */
    protected $page_number = 0;

    /**
     * @var int Size of a page
     */
    protected $page_size = 0;

    protected $fields = array();
    protected $alias = array();
    protected $alias_iterator = 0;
    protected $join_list = array();
    protected $association_definition = array();

    const LANG_ALIAS = 'l';

    /**
     * @param string $classname
     * @param int $id_lang
     */
    public function __construct($classname, $id_lang = null)
    {
        $this->classname = $classname;
        $this->id_lang = $id_lang;

        $this->definition = ObjectModel::getDefinition($this->classname);
        if (!isset($this->definition['table'])) {
            throw new PrestaShopException('Miss table in definition for class '.$this->classname);
        } elseif (!isset($this->definition['primary'])) {
            throw new PrestaShopException('Miss primary in definition for class '.$this->classname);
        }

        $this->query = new DbQuery();
    }

    /**
     * Join current entity to an associated entity
     *
     * @param string $association Association name
     * @param string $on
     * @param int $type
     * @return PrestaShopCollection
     */
    public function join($association, $on = '', $type = null)
    {
        if (!$association) {
            return;
        }

        if (!isset($this->join_list[$association])) {
            $definition = $this->getDefinition($association);
            $on = '{'.$definition['asso']['complete_field'].'} = {'.$definition['asso']['complete_foreign_field'].'}';
            $type = self::LEFT_JOIN;
            $this->join_list[$association] = array(
                'table' =>    ($definition['is_lang']) ? $definition['table'].'_lang' : $definition['table'],
                'alias' => $this->generateAlias($association),
                'on' => array(),
            );
        }

        if ($on) {
            $this->join_list[$association]['on'][] = $this->parseFields($on);
        }

        if ($type) {
            $this->join_list[$association]['type'] = $type;
        }

        return $this;
    }

    /**
     * Add WHERE restriction on query
     *
     * @param string $field Field name
     * @param string $operator List of operators : =, !=, <>, <, <=, >, >=, like, notlike, regexp, notregexp
     * @param mixed $value
     * @param string $type where|having
     * @return PrestaShopCollection
     */
    public function where($field, $operator, $value, $method = 'where')
    {
        if ($method != 'where' && $method != 'having') {
            throw new PrestaShopException('Bad method argument for where() method (should be "where" or "having")');
        }

        // Create WHERE clause with an array value (IN, NOT IN)
        if (is_array($value)) {
            switch (strtolower($operator)) {
                case '=' :
                case 'in' :
                    $this->query->$method($this->parseField($field).' IN('.implode(', ', $this->formatValue($value, $field)).')');
                break;

                case '!=' :
                case '<>' :
                case 'notin' :
                    $this->query->$method($this->parseField($field).' NOT IN('.implode(', ', $this->formatValue($value, $field)).')');
                break;

                default :
                    throw new PrestaShopException('Operator not supported for array value');
            }
        }
        // Create WHERE clause
        else {
            switch (strtolower($operator)) {
                case '=' :
                case '!=' :
                case '<>' :
                case '>' :
                case '>=' :
                case '<' :
                case '<=' :
                case 'like' :
                case 'regexp' :
                    $this->query->$method($this->parseField($field).' '.$operator.' '.$this->formatValue($value, $field));
                break;

                case 'notlike' :
                    $this->query->$method($this->parseField($field).' NOT LIKE '.$this->formatValue($value, $field));
                break;

                case 'notregexp' :
                    $this->query->$method($this->parseField($field).' NOT REGEXP '.$this->formatValue($value, $field));
                break;

                default :
                    throw new PrestaShopException('Operator not supported');
            }
        }

        return $this;
    }

    /**
     * Add WHERE restriction on query using real SQL syntax
     *
     * @param string $sql
     * @return PrestaShopCollection
     */
    public function sqlWhere($sql)
    {
        $this->query->where($this->parseFields($sql));
        return $this;
    }

    /**
     * Add HAVING restriction on query
     *
     * @param string $field Field name
     * @param string $operator List of operators : =, !=, <>, <, <=, >, >=, like, notlike, regexp, notregexp
     * @param mixed $value
     * @return PrestaShopCollection
     */
    public function having($field, $operator, $value)
    {
        return $this->where($field, $operator, $value, 'having');
    }

    /**
     * Add HAVING restriction on query using real SQL syntax
     *
     * @param string $sql
     * @return PrestaShopCollection
     */
    public function sqlHaving($sql)
    {
        $this->query->having($this->parseFields($sql));
        return $this;
    }

    /**
     * Add ORDER BY restriction on query
     *
     * @param string $field Field name
     * @param string $order asc|desc
     * @return PrestaShopCollection
     */
    public function orderBy($field, $order = 'asc')
    {
        $order = strtolower($order);
        if ($order != 'asc' && $order != 'desc') {
            throw new PrestaShopException('Order must be asc or desc');
        }
        $this->query->orderBy($this->parseField($field).' '.$order);
        return $this;
    }

    /**
     * Add ORDER BY restriction on query using real SQL syntax
     *
     * @param string $sql
     * @return PrestaShopCollection
     */
    public function sqlOrderBy($sql)
    {
        $this->query->orderBy($this->parseFields($sql));
        return $this;
    }

    /**
     * Add GROUP BY restriction on query
     *
     * @param string $field Field name
     * @return PrestaShopCollection
     */
    public function groupBy($field)
    {
        $this->query->groupBy($this->parseField($field));
        return $this;
    }

    /**
     * Add GROUP BY restriction on query using real SQL syntax
     *
     * @param string $sql
     * @return PrestaShopCollection
     */
    public function sqlGroupBy($sql)
    {
        $this->query->groupBy($this->parseFields($sql));
        return $this;
    }

    /**
     * Launch sql query to create collection of objects
     *
     * @param bool $display_query If true, query will be displayed (for debug purpose)
     * @return PrestaShopCollection
     */
    public function getAll($display_query = false)
    {
        if ($this->is_hydrated) {
            return $this;
        }
        $this->is_hydrated = true;

        $alias = $this->generateAlias();
        //$this->query->select($alias.'.*');
        $this->query->from($this->definition['table'], $alias);

        // If multilang, create association to lang table
        if (!empty($this->definition['multilang'])) {
            $this->join(self::LANG_ALIAS);
            if ($this->id_lang) {
                $this->where(self::LANG_ALIAS.'.id_lang', '=', $this->id_lang);
            }
        }

        // Add join clause
        foreach ($this->join_list as $data) {
            $on = '('.implode(') AND (', $data['on']).')';
            switch ($data['type']) {
                case self::LEFT_JOIN :
                    $this->query->leftJoin($data['table'], $data['alias'], $on);
                break;

                case self::INNER_JOIN :
                    $this->query->innerJoin($data['table'], $data['alias'], $on);
                break;

                case self::LEFT_OUTER_JOIN :
                    $this->query->leftOuterJoin($data['table'], $data['alias'], $on);
                break;
            }
        }

        // All limit clause
        if ($this->page_size) {
            $this->query->limit($this->page_size, $this->page_number * $this->page_size);
        }

        // Shall we display query for debug ?
        if ($display_query) {
            echo $this->query.'<br />';
        }

        $this->results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query);
        if ($this->results && is_array($this->results))
            $this->results = ObjectModel::hydrateCollection($this->classname, $this->results, $this->id_lang);

        return $this;
    }

    /**
     * Retrieve the first result
     *
     * @return ObjectModel
     */
    public function getFirst()
    {
        $this->getAll();
        if (!count($this)) {
            return false;
        }
        return $this[0];
    }

    /**
     * Get results array
     *
     * @return array
     */
    public function getResults()
    {
        $this->getAll();
        return $this->results;
    }

    /**
     * This method is called when a foreach begin
     *
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->getAll();
        $this->results = array_merge($this->results);
        $this->iterator = 0;
        $this->total = count($this->results);
    }

    /**
     * Get current result
     *
     * @see Iterator::current()
     * @return ObjectModel
     */
    public function current()
    {
        return isset($this->results[$this->iterator]) ? $this->results[$this->iterator] : null;
    }

    /**
     * Check if there is a current result
     *
     * @see Iterator::valid()
     * @return bool
     */
    public function valid()
    {
        return $this->iterator < $this->total;
    }

    /**
     * Get current result index
     *
     * @see Iterator::key()
     * @return int
     */
    public function key()
    {
        return $this->iterator;
    }

    /**
     * Go to next result
     *
     * @see Iterator::next()
     */
    public function next()
    {
        $this->iterator++;
    }

    /**
     * Get total of results
     *
     * @see Countable::count()
     * @return int
     */
    public function count()
    {
        $this->getAll();
        return count($this->results);
    }

    /**
     * Check if a result exist
     *
     * @see ArrayAccess::offsetExists()
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $this->getAll();
        return isset($this->results[$offset]);
    }

    /**
     * Get a result by offset
     *
     * @see ArrayAccess::offsetGet()
     * @param $offset
     * @return ObjectModel
     */
    public function offsetGet($offset)
    {
        $this->getAll();
        if (!isset($this->results[$offset])) {
            throw new PrestaShopException('Unknown offset '.$offset.' for collection '.$this->classname);
        }
        return $this->results[$offset];
    }

    /**
     * Add an element in the collection
     *
     * @see ArrayAccess::offsetSet()
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof $this->classname) {
            throw new PrestaShopException('You cannot add an element which is not an instance of '.$this->classname);
        }

        $this->getAll();
        if (is_null($offset)) {
            $this->results[] = $value;
        } else {
            $this->results[$offset] = $value;
        }
    }

    /**
     * Delete an element from the collection
     *
     * @see ArrayAccess::offsetUnset()
     * @param $offset
     */
    public function offsetUnset($offset)
    {
        $this->getAll();
        unset($this->results[$offset]);
    }

    /**
     * Get definition of an association
     *
     * @param string $association
     * @return array
     */
    protected function getDefinition($association)
    {
        if (!$association) {
            return $this->definition;
        }

        if (!isset($this->association_definition[$association])) {
            $definition = $this->definition;
            $split = explode('.', $association);
            $is_lang = false;
            for ($i = 0, $total_association = count($split); $i < $total_association; $i++) {
                $asso = $split[$i];

                // Check is current association exists in current definition
                if (!isset($definition['associations'][$asso])) {
                    throw new PrestaShopException('Association '.$asso.' not found for class '.$this->definition['classname']);
                }
                $current_def = $definition['associations'][$asso];

                // Special case for lang alias
                if ($asso == self::LANG_ALIAS) {
                    $is_lang = true;
                    break;
                }

                $classname = (isset($current_def['object'])) ? $current_def['object'] : Tools::toCamelCase($asso, true);
                $definition = ObjectModel::getDefinition($classname);
            }

            // Get definition of associated entity and add information on current association
            $current_def['name'] = $asso;
            if (!isset($current_def['object'])) {
                $current_def['object'] = Tools::toCamelCase($asso, true);
            }
            if (!isset($current_def['field'])) {
                $current_def['field'] = 'id_'.$asso;
            }
            if (!isset($current_def['foreign_field'])) {
                $current_def['foreign_field'] = 'id_'.$asso;
            }
            if ($total_association > 1) {
                unset($split[$total_association - 1]);
                $current_def['complete_field'] = implode('.', $split).'.'.$current_def['field'];
            } else {
                $current_def['complete_field'] = $current_def['field'];
            }
            $current_def['complete_foreign_field'] = $association.'.'.$current_def['field'];

            $definition['is_lang'] = $is_lang;
            $definition['asso'] = $current_def;
            $this->association_definition[$association] = $definition;
        } else {
            $definition = $this->association_definition[$association];
        }

        return $definition;
    }

    /**
     * Parse all fields with {field} syntax in a string
     *
     * @param string $str
     * @return string
     */
    protected function parseFields($str)
    {
        preg_match_all('#\{(([a-z0-9_]+\.)*[a-z0-9_]+)\}#i', $str, $m);
        for ($i = 0, $total = count($m[0]); $i < $total; $i++) {
            $str = str_replace($m[0][$i], $this->parseField($m[1][$i]), $str);
        }
        return $str;
    }

    /**
     * Replace a field with its SQL version (E.g. manufacturer.name with a2.name)
     *
     * @param string $field Field name
     * @return string
     */
    protected function parseField($field)
    {
        $info = $this->getFieldInfo($field);
        return $info['alias'].'.`'.$info['name'].'`';
    }

    /**
     * Format a value with the type of the given field
     *
     * @param mixed $value
     * @param string $field Field name
     * @return mixed
     */
    protected function formatValue($value, $field)
    {
        $info = $this->getFieldInfo($field);
        if (is_array($value)) {
            $results = array();
            foreach ($value as $item) {
                $results[] = ObjectModel::formatValue($item, $info['type'], true);
            }
            return $results;
        }
        return ObjectModel::formatValue($value, $info['type'], true);
    }

    /**
     * Obtain some information on a field (alias, name, type, etc.)
     *
     * @param string $field Field name
     * @return array
     */
    protected function getFieldInfo($field)
    {
        if (!isset($this->fields[$field])) {
            $split = explode('.', $field);
            $total = count($split);
            if ($total > 1) {
                $fieldname = $split[$total - 1];
                unset($split[$total - 1]);
                $association = implode('.', $split);
            } else {
                $fieldname = $field;
                $association = '';
            }

            $definition = $this->getDefinition($association);
            if ($association && !isset($this->join_list[$association])) {
                $this->join($association);
            }

            if ($fieldname == $definition['primary'] || (!empty($definition['is_lang']) && $fieldname == 'id_lang')) {
                $type = ObjectModel::TYPE_INT;
            } else {
                // Test if field exists
                if (!isset($definition['fields'][$fieldname])) {
                    throw new PrestaShopException('Field '.$fieldname.' not found in class '.$definition['classname']);
                }

                // Test field validity for language fields
                if (empty($definition['is_lang']) && !empty($definition['fields'][$fieldname]['lang'])) {
                    throw new PrestaShopException('Field '.$fieldname.' is declared as lang field but is used in non multilang context');
                } elseif (!empty($definition['is_lang']) && empty($definition['fields'][$fieldname]['lang'])) {
                    throw new PrestaShopException('Field '.$fieldname.' is not declared as lang field but is used in multilang context');
                }

                $type = $definition['fields'][$fieldname]['type'];
            }

            $this->fields[$field] = array(
                'name' =>            $fieldname,
                'association' =>    $association,
                'alias' =>            $this->generateAlias($association),
                'type' =>            $type,
            );
        }
        return $this->fields[$field];
    }

    /**
     * Set the page number
     *
     * @param int $page_number
     * @return PrestaShopCollection
     */
    public function setPageNumber($page_number)
    {
        $page_number = (int)$page_number;
        if ($page_number > 0) {
            $page_number--;
        }

        $this->page_number = $page_number;
        return $this;
    }

    /**
     * Set the nuber of item per page
     *
     * @param int $page_size
     * @return PrestaShopCollection
     */
    public function setPageSize($page_size)
    {
        $this->page_size = (int)$page_size;
        return $this;
    }

    /**
     * Generate uniq alias from association name
     *
     * @param string $association Use empty association for alias on current table
     * @return string
     */
    protected function generateAlias($association = '')
    {
        if (!isset($this->alias[$association])) {
            $this->alias[$association] = 'a'.$this->alias_iterator++;
        }
        return $this->alias[$association];
    }
}
