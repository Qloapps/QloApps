<?php

/*
 * 2007-2018 PrestaShop
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
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade\UpgradeTools;

use Db;

class Database
{
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getAllTables()
    {
        $tables = $this->db->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . '%"', true, false);

        $all_tables = array();
        foreach ($tables as $v) {
            $table = array_shift($v);
            $all_tables[$table] = $table;
        }

        return $all_tables;
    }

    /**
     * ToDo: Send tables list instead.
     */
    public function cleanTablesAfterBackup(array $tablesToClean)
    {
        foreach ($tablesToClean as $table) {
            $this->db->execute('DROP TABLE IF EXISTS `' . bqSql($table) . '`');
            $this->db->execute('DROP VIEW IF EXISTS `' . bqSql($table) . '`');
        }
        $this->db->execute('SET FOREIGN_KEY_CHECKS=1');
    }
}
