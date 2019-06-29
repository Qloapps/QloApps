<?php
/**
 * 2007-2017 PrestaShop
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Core_Foundation_Database_EntityManager_QueryBuilder
{
    private $db;

    public function __construct(Core_Foundation_Database_DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function quote($value)
    {
        $escaped = $this->db->escape($value);

        if (is_string($value)) {
            return "'" . $escaped . "'";
        } else {
            return $escaped;
        }
    }

    public function buildWhereConditions($andOrOr, array $conditions)
    {
        $operator = strtoupper($andOrOr);

        if ($operator !== 'AND' && $operator !== 'OR') {
            throw new Core_Foundation_Database_Exception(sprintf('Invalid operator %s - must be "and" or "or".', $andOrOr));
        }

        $parts = array();

        foreach ($conditions as $key => $value) {
            if (is_scalar($value)) {
                $parts[] = $key . ' = ' . $this->quote($value);
            } else {
                $list = array();
                foreach ($value as $item) {
                    $list[] = $this->quote($item);
                }
                $parts[] = $key . ' IN (' . implode(', ', $list) . ')';
            }
        }

        return implode(" $operator ", $parts);
    }
}
